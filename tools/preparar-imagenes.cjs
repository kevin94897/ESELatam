/**
 * Optimiza los renders del cliente y deja todo listo para subir a la
 * biblioteca de medios de WordPress.
 *
 *   node tools/preparar-imagenes.cjs [--salida <dir>] [--solo <slug>]
 *
 * POR QUÉ
 * -------
 * Los archivos que entrega el cliente pesan entre 30 KB y 28 MB, vienen en
 * lienzos de todos los tamaños y con el producto a distinta escala dentro del
 * cuadro. En la ficha el <img> se dibuja a una ALTURA FIJA, así que sin
 * normalizar un balde de 3 L y una compostera de 660 L se verían iguales.
 *
 * Qué hace con cada imagen:
 *
 *  - Renders de producto (PNG con transparencia): recorta al contenido, lo
 *    re-escala según el volumen del modelo —la dimensión lineal va con la
 *    RAÍZ CÚBICA del volumen, que es como crecen los cuerpos de verdad— y lo
 *    apoya sobre una línea de base común en un lienzo cuadrado. Así, dentro de
 *    un mismo producto, cambiar de capacidad se nota; y entre productos
 *    distintos todos quedan parados a la misma altura.
 *  - Fotos de ambiente (JPG, sin transparencia): solo se reducen de tamaño.
 *
 * No escribe nada dentro del theme: la salida va a un directorio de trabajo
 * que después consume tools/seed-catalogo.php. Las imágenes viven en la
 * biblioteca de medios, no en el repositorio.
 */

'use strict';

const { execFileSync } = require('node:child_process');
const fs = require('node:fs');
const path = require('node:path');

const RAIZ = path.resolve(__dirname, '..');
const MANIFIESTO = path.join(__dirname, 'catalogo-manifiesto.json');

// Lienzo de salida para los renders de producto.
const LIENZO = 1200;
const ALTO_MAX = 1040; // alto de contenido del producto más grande del catálogo
const ALTO_MIN = 520; // piso: un balde de 3 L no puede quedar invisible
// Volúmenes de referencia del catálogo, en litros: el más chico (Olimax 3 L)
// y el más grande (Bagio L, 3 m³). Entre ambos se interpola la altura.
const VOL_MIN = 3;
const VOL_MAX = 3000;
const ANCHO_MAX = 1140; // los cuerpos anchos no deben tocar el borde del lienzo
const BASE = 30; // margen bajo la base del producto
const CALIDAD = 86;

// Fotos de ambiente: solo se achican.
const FOTO_ANCHO = 1800;
const FOTO_CALIDAD = 82;

const argv = process.argv.slice(2);
const opt = (nombre, porDefecto) => {
  const i = argv.indexOf(nombre);
  return i >= 0 && argv[i + 1] ? argv[i + 1] : porDefecto;
};

const SALIDA = path.resolve(opt('--salida', path.join(RAIZ, '.tmp-imagenes')));
const SOLO = opt('--solo', '');

const manifiesto = JSON.parse(fs.readFileSync(MANIFIESTO, 'utf8'));
const ORIGEN = manifiesto._origen;

/**
 * Alto de contenido para un volumen dado, en píxeles.
 *
 * Interpola por raíz cúbica —los cuerpos crecen en tres dimensiones, así que
 * la altura va con la raíz cúbica del volumen— entre el producto más chico y
 * el más grande del catálogo. Sin esto, escalando cada producto por separado,
 * un balde de cocina salía tan alto como un contenedor soterrado.
 */
function altoPara(litros) {
  const v = Math.min(VOL_MAX, Math.max(VOL_MIN, litros));
  const t = (Math.cbrt(v) - Math.cbrt(VOL_MIN)) / (Math.cbrt(VOL_MAX) - Math.cbrt(VOL_MIN));
  return Math.round(ALTO_MIN + (ALTO_MAX - ALTO_MIN) * t);
}

function magick(args) {
  execFileSync('magick', args, { stdio: ['ignore', 'pipe', 'pipe'] });
}

/** Convierte un texto a un slug apto para nombre de archivo. */
function slugificar(texto) {
  return texto
    .normalize('NFD')
    .replace(/[̀-ͯ]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
}

/** Ruta absoluta de un archivo original, o null si no está. */
function origen(carpeta, archivo) {
  const p = path.join(ORIGEN, carpeta, archivo);
  return fs.existsSync(p) ? p : null;
}

fs.mkdirSync(SALIDA, { recursive: true });

const faltantes = [];
const generadas = [];

/* ------------------------------------------------------------------ *
 * 1. Renders de producto
 * ------------------------------------------------------------------ */

for (const producto of manifiesto.productos) {
  if (SOLO && producto.slug !== SOLO) continue;

  process.stdout.write(`  ${producto.titulo}\n`);

  // Un producto es una FAMILIA y sus modelos van dentro: cada uno trae su
  // propia gama de capacidades y colores, porque una papelera Campus Goool
  // no comparte carta con una Open Dinova.
  for (const modelo of producto.modelos) {
    // Litraje por defecto: el que representa al modelo en la ficha.
    const litrosDefecto =
      modelo.litrajes.find((l) => l.default)?.litros ?? modelo.litrajes[0].litros;

    // Hay piezas cuyo TAMAÑO FÍSICO no depende de su capacidad: una papelera
    // de 50 L montada sobre poste mide más de un metro, y escalada por
    // volumen salía diminuta al lado de una compostera de 660 L. Para esas,
    // el manifiesto declara un `volumen_visual` que se usa solo para la
    // escala; la progresión entre capacidades del modelo se mantiene.
    const alturaBase = modelo.volumen_visual ? altoPara(modelo.volumen_visual) : null;

    // Cada combinación (color, litraje) que hay que generar.
    const combinaciones = [];

    for (const foto of modelo.fotos ?? []) {
      combinaciones.push({ ...foto, tipo: 'foto' });
    }
    // La "foto por defecto" de cada color: el respaldo cuando una capacidad
    // no tiene foto propia.
    for (const color of modelo.colores) {
      combinaciones.push({
        color: color.nombre,
        litraje: null,
        archivo: color.archivo,
        recorte: color.recorte,
        tipo: 'color',
      });
    }

    for (const combo of combinaciones) {
      const src = origen(producto.carpeta, combo.archivo);
      if (!src) {
        faltantes.push(`${producto.slug}/${modelo.nombre}: ${producto.carpeta}/${combo.archivo}`);
        continue;
      }

      const litrosCombo = combo.litraje
        ? (modelo.litrajes.find((l) => l.valor === combo.litraje)?.litros ?? litrosDefecto)
        : litrosDefecto;
      const alto = alturaBase
        ? Math.round(alturaBase * Math.cbrt(litrosCombo / litrosDefecto))
        : altoPara(litrosCombo);

      const sufijo = combo.litraje ? slugificar(combo.litraje) : 'base';
      const nombre =
        `${producto.slug}-${slugificar(modelo.nombre)}-${slugificar(combo.color)}-${sufijo}.webp`;
      const destino = path.join(SALIDA, nombre);

      // Algunos renders traen recuadros de detalle en el mismo cuadro; el
      // manifiesto declara qué parte conservar antes de recortar al contenido.
      const recorte = combo.recorte ? ['-crop', combo.recorte, '+repage'] : [];

      magick([
        src,
        ...recorte,
        '-trim', '+repage',
        '-resize', `${ANCHO_MAX}x${alto}`,
        '-background', 'none',
        '-gravity', 'south', '-splice', `0x${BASE}`,
        '-gravity', 'center', '-extent', `${LIENZO}x${LIENZO}`,
        '-define', 'webp:lossless=false', '-quality', String(CALIDAD),
        destino,
      ]);

      generadas.push({
        archivo: nombre,
        producto: producto.slug,
        modelo: modelo.nombre,
        color: combo.color,
        litraje: combo.litraje,
        tipo: combo.tipo,
        titulo: `${modelo.nombre}${combo.litraje ? ' ' + combo.litraje : ''} — ${combo.color}`,
        alt: `${modelo.nombre}${combo.litraje ? ' de ' + combo.litraje : ''} en color ${combo.color.toLowerCase()}`,
        origen: `${producto.carpeta}/${combo.archivo}`,
      });
    }

    process.stdout.write(
      `     ${modelo.nombre.padEnd(28)} ${modelo.colores.length} colores x ${modelo.litrajes.length} litrajes\n`
    );
  }
}

/* ------------------------------------------------------------------ *
 * 2. Fotos sueltas (ambiente, detalles, cartas de color)
 * ------------------------------------------------------------------ */

if (!SOLO) {
  for (const [carpeta, archivos] of Object.entries(manifiesto._sueltas)) {
    if (carpeta.startsWith('_')) continue;
    for (const archivo of archivos) {
      const src = origen(carpeta, archivo);
      if (!src) {
        faltantes.push(`sueltas: ${carpeta}/${archivo}`);
        continue;
      }
      const nombre = `suelta-${slugificar(carpeta)}-${slugificar(path.parse(archivo).name)}.webp`;
      const destino = path.join(SALIDA, nombre);

      // Si tiene transparencia se conserva; si no, se aplana sobre blanco.
      const tieneAlfa =
        execFileSync('magick', ['identify', '-format', '%A', src]).toString().trim() !== 'Undefined';

      const args = [src, '-resize', `${FOTO_ANCHO}x${FOTO_ANCHO}>`];
      if (tieneAlfa) args.push('-background', 'none');
      args.push('-quality', String(FOTO_CALIDAD), destino);
      magick(args);

      generadas.push({
        archivo: nombre,
        producto: null,
        color: null,
        litraje: null,
        tipo: 'suelta',
        titulo: path.parse(archivo).name.replace(/[_-]+/g, ' ').trim(),
        alt: path.parse(archivo).name.replace(/[_-]+/g, ' ').trim(),
        origen: `${carpeta}/${archivo}`,
      });
    }
  }
}

/* ------------------------------------------------------------------ */

fs.writeFileSync(
  path.join(SALIDA, 'indice.json'),
  JSON.stringify({ generadas, faltantes }, null, 2),
  'utf8'
);

const pesoSalida = generadas.reduce(
  (t, g) => t + fs.statSync(path.join(SALIDA, g.archivo)).size,
  0
);

console.log(`\nGeneradas: ${generadas.length}`);
console.log(`Salida   : ${SALIDA}`);
console.log(`Peso     : ${(pesoSalida / 1048576).toFixed(1)} MB`);
if (faltantes.length) {
  console.log(`\nFALTAN ${faltantes.length} archivos del manifiesto:`);
  faltantes.forEach((f) => console.log(`  !! ${f}`));
  process.exitCode = 1;
}
