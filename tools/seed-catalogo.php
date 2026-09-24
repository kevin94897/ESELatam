<?php
/**
 * Sube a la biblioteca de medios las imágenes ya optimizadas por
 * tools/preparar-imagenes.cjs y crea o completa los productos del catálogo.
 *
 *   node tools/preparar-imagenes.cjs --salida C:/ruta/temporal
 *   cd app/public
 *   php wp-content/themes/ese-latam/tools/seed-catalogo.php --imagenes C:/ruta/temporal
 *
 * Las imágenes viven SOLO en la biblioteca de medios: ni este script ni el
 * preparador escriben nada dentro del theme.
 *
 * Es IDEMPOTENTE. Cada adjunto lleva la meta `_ese_media_key` y cada producto
 * la meta `_ese_producto_key`; volver a correrlo actualiza en lugar de
 * duplicar. Un producto que ya exista con el mismo slug —cargado a mano desde
 * wp-admin— se reutiliza en vez de crear otro.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if ('cli' !== PHP_SAPI) {
    exit("Solo por línea de comandos.\n");
}

$ese_wp_load = getcwd() . '/wp-load.php';
if (! file_exists($ese_wp_load)) {
    exit("Ejecutalo desde app/public (no se encontró wp-load.php).\n");
}
require $ese_wp_load;

if (! function_exists('update_field')) {
    exit("Los campos personalizados no están activos.\n");
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

/* ------------------------------------------------------------------ *
 * Argumentos
 * ------------------------------------------------------------------ */

/** Lee un argumento --nombre valor de la línea de comandos. */
function ese_arg(string $nombre, string $default = ''): string {
    global $argv;
    $i = array_search('--' . $nombre, $argv, true);
    return false !== $i && isset($argv[$i + 1]) ? (string) $argv[$i + 1] : $default;
}

$ESE_DIR_IMG = rtrim(str_replace('\\', '/', ese_arg('imagenes')), '/');
$ESE_SECO    = in_array('--seco', $argv, true); // simulacro: no escribe nada

if ('' === $ESE_DIR_IMG || ! is_dir($ESE_DIR_IMG)) {
    exit("Falta --imagenes <directorio> (el que generó preparar-imagenes.cjs).\n");
}

$ESE_INDICE = $ESE_DIR_IMG . '/indice.json';
if (! file_exists($ESE_INDICE)) {
    exit("No se encontró indice.json en $ESE_DIR_IMG. Corré antes preparar-imagenes.cjs.\n");
}

$ese_indice     = json_decode((string) file_get_contents($ESE_INDICE), true);
// __DIR__ y no get_template_directory(): en esta instalación conviven dos
// copias del theme (ESELatam es la activa, ese-latam es la otra) y el
// manifiesto tiene que leerse desde la copia donde está este script, no
// desde la que WordPress tenga activada.
$ese_manifiesto = json_decode(
    (string) file_get_contents(__DIR__ . '/catalogo-manifiesto.json'),
    true
);

if (! is_array($ese_indice) || ! is_array($ese_manifiesto)) {
    exit("No se pudo leer el índice o el manifiesto.\n");
}

/* ------------------------------------------------------------------ *
 * Medios
 * ------------------------------------------------------------------ */

/**
 * Sube un archivo a la biblioteca de medios una sola vez.
 *
 * @return int ID del adjunto, 0 si falla.
 */
function ese_media(string $ruta, string $clave, string $titulo, string $alt): int {
    global $ESE_SECO;

    // Huella del archivo de origen: si el render se regeneró (otra escala,
    // otro recorte), el adjunto existente quedó viejo y hay que reemplazarlo.
    // Sin esto, volver a correr el pipeline subía nada y el sitio seguía
    // mostrando la versión anterior.
    $huella = file_exists($ruta) ? md5_file($ruta) : '';

    $existe = get_posts([
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_ese_media_key',
        'meta_value'     => $clave,
    ]);

    if (! empty($existe)) {
        $id      = (int) $existe[0];
        $path    = get_attached_file($id);
        $igual   = (string) get_post_meta($id, '_ese_media_hash', true) === $huella;
        if ($path && file_exists($path) && $igual) {
            // Mismo archivo: solo se refresca el texto alternativo.
            if (! $ESE_SECO) {
                update_post_meta($id, '_wp_attachment_image_alt', $alt);
            }
            return $id;
        }
        // Cambió el render, o el archivo ya no está en uploads: se reemplaza.
        if (! $ESE_SECO) {
            wp_delete_attachment($id, true);
        }
    }

    if (! file_exists($ruta)) {
        fwrite(STDERR, "  !! no existe: $ruta\n");
        return 0;
    }
    if ($ESE_SECO) {
        return -1;
    }

    $uploads = wp_upload_dir();
    $destino = $uploads['path'] . '/' . wp_unique_filename($uploads['path'], basename($ruta));

    if (! copy($ruta, $destino)) {
        fwrite(STDERR, "  !! no se pudo copiar: $ruta\n");
        return 0;
    }

    $id = wp_insert_attachment([
        'post_mime_type' => wp_check_filetype($destino)['type'] ?: 'image/webp',
        'post_title'     => $titulo,
        'post_status'    => 'inherit',
    ], $destino);

    if (is_wp_error($id) || 0 === $id) {
        fwrite(STDERR, "  !! falló el alta: $ruta\n");
        return 0;
    }

    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $destino));
    update_post_meta($id, '_wp_attachment_image_alt', $alt);
    update_post_meta($id, '_ese_media_key', $clave);
    update_post_meta($id, '_ese_media_hash', $huella);

    return (int) $id;
}

/** Devuelve el ID del término, creándolo si hace falta. */
function ese_termino(string $nombre, string $taxonomia): int {
    $t = get_term_by('name', $nombre, $taxonomia);
    if ($t instanceof WP_Term) {
        return $t->term_id;
    }
    $nuevo = wp_insert_term($nombre, $taxonomia);
    return is_wp_error($nuevo) ? 0 : (int) $nuevo['term_id'];
}

/* ------------------------------------------------------------------ *
 * 1. Subir todas las imágenes
 * ------------------------------------------------------------------ */

echo "== Subiendo imágenes a la biblioteca de medios\n";

$ese_ids   = [];   // archivo optimizado => ID de adjunto
$ese_nuevo = 0;
$ese_ya    = 0;

foreach ($ese_indice['generadas'] as $ese_img) {
    $archivo = (string) $ese_img['archivo'];
    $clave   = 'cat-' . pathinfo($archivo, PATHINFO_FILENAME);

    $antes = get_posts([
        'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1,
        'fields' => 'ids', 'meta_key' => '_ese_media_key', 'meta_value' => $clave,
    ]);

    $id = ese_media(
        $ESE_DIR_IMG . '/' . $archivo,
        $clave,
        (string) $ese_img['titulo'],
        (string) $ese_img['alt']
    );

    if ($id > 0) {
        $ese_ids[$archivo] = $id;
        empty($antes) ? $ese_nuevo++ : $ese_ya++;
    }
}

echo "   nuevas: $ese_nuevo   ya estaban: $ese_ya   total: " . count($ese_ids) . "\n\n";

/* ------------------------------------------------------------------ *
 * 2. Crear o completar los productos
 * ------------------------------------------------------------------ */

echo "== Productos\n";

/*
 * Mapa producto => color => litraje (o 'base') => ID de adjunto.
 *
 * Se arma con los datos que el propio indice.json trae por cada archivo, en
 * vez de volver a calcular el nombre acá: la normalización de acentos de PHP
 * y la de Node no coinciden ("Marrón" salía marr-on en una y marron en la
 * otra), y cualquier diferencia dejaba el producto sin fotos en silencio.
 */
$ese_mapa = [];
foreach ($ese_indice['generadas'] as $ese_img) {
    if (empty($ese_img['producto'])) {
        continue;
    }
    $id = $ese_ids[(string) $ese_img['archivo']] ?? 0;
    if (! $id) {
        continue;
    }
    $clave = $ese_img['litraje'] ?? 'base';
    $ese_mapa[(string) $ese_img['producto']]
             [(string) ($ese_img['modelo'] ?? '')]
             [(string) $ese_img['color']]
             [(string) $clave] = $id;
}

foreach ($ese_manifiesto['productos'] as $ese_p) {
    $slug = (string) $ese_p['slug'];

    // ---- Post ----
    $existe = get_posts([
        'post_type' => 'producto', 'post_status' => 'any', 'posts_per_page' => 1,
        'fields' => 'ids', 'meta_key' => '_ese_producto_key', 'meta_value' => $slug,
    ]);
    if (empty($existe)) {
        $existe = get_posts([
            'post_type' => 'producto', 'post_status' => 'any', 'posts_per_page' => 1,
            'fields' => 'ids', 'name' => $slug,
        ]);
    }

    $postarr = [
        'post_type'   => 'producto',
        'post_status' => 'publish',
        'post_title'  => (string) $ese_p['titulo'],
        'post_name'   => $slug,
    ];
    if (! empty($existe)) {
        $postarr['ID'] = (int) $existe[0];
    }

    if ($ESE_SECO) {
        $id = empty($existe) ? 0 : (int) $existe[0];
    } else {
        $r  = empty($existe) ? wp_insert_post($postarr, true) : wp_update_post($postarr, true);
        if (is_wp_error($r)) {
            fwrite(STDERR, "  !! {$ese_p['titulo']}: " . $r->get_error_message() . "\n");
            continue;
        }
        $id = (int) $r;
        update_post_meta($id, '_ese_producto_key', $slug);
    }

    // ---- Modelos de la familia ----
    //
    // Los tres repetidores de abajo son PLANOS y llevan una columna "modelo":
    // así el cliente ve una tabla por concepto en vez de tablas anidadas, y
    // el theme filtra por modelo al pintar la ficha.
    $modelos  = [];
    $litrajes = [];
    $colores  = [];
    $fotos    = [];
    $thumb    = 0;

    foreach ($ese_p['modelos'] as $m) {
        $nombre = (string) $m['nombre'];

        $modelos[] = [
            'nombre'      => $nombre,
            'descripcion' => (string) ($m['descripcion'] ?? ''),
        ];

        foreach ($m['litrajes'] as $l) {
            $litrajes[] = [
                'modelo'         => $nombre,
                'valor'          => (string) $l['valor'],
                'predeterminado' => ! empty($l['default']),
            ];
        }

        foreach ($m['colores'] as $c) {
            $img = $ese_mapa[$slug][$nombre][(string) $c['nombre']]['base'] ?? 0;
            $colores[] = [
                'modelo' => $nombre,
                'nombre' => (string) $c['nombre'],
                'color'  => (string) $c['hex'],
                'imagen' => $img ?: '',
            ];
            // La destacada del producto es la primera foto del primer modelo.
            if (0 === $thumb && $img) {
                $thumb = $img;
            }
        }

        foreach ($m['fotos'] ?? [] as $f) {
            $img = $ese_mapa[$slug][$nombre][(string) $f['color']][(string) $f['litraje']] ?? 0;
            if ($img) {
                $fotos[] = [
                    'modelo'  => $nombre,
                    'color'   => (string) $f['color'],
                    'litraje' => (string) $f['litraje'],
                    'imagen'  => $img,
                ];
            }
        }
    }

    if (! $ESE_SECO && $id > 0) {
        if ($thumb) {
            set_post_thumbnail($id, $thumb);
        }
        wp_set_object_terms($id, [ese_termino((string) $ese_p['categoria'], 'producto_categoria')], 'producto_categoria');

        update_field('descripcion_corta', (string) $ese_p['descripcion'], $id);
        update_field('enlace_compra', [
            'title'  => __('Comprar', 'ese-latam'),
            'url'    => home_url('/contacto/'),
            'target' => '',
        ], $id);
        update_field('modelos', $modelos, $id);
        update_field('litrajes', $litrajes, $id);
        update_field('colores', $colores, $id);
        update_field('fotos', $fotos, $id);
        update_field('caracteristicas', $ese_p['caracteristicas'] ?? [], $id);
    }

    printf(
        "  #%-4s %-28s %-24s %d modelos · %d colores · %d capacidades · %d fotos%s\n",
        $id ?: '—',
        $ese_p['titulo'],
        $ese_p['categoria'],
        count($modelos),
        count($colores),
        count($litrajes),
        count($fotos),
        $thumb ? '' : '   (SIN destacada)'
    );
}

/* ------------------------------------------------------------------ *
 * 3. Productos sembrados que ya no están en el manifiesto
 *
 * Al pasar de "un producto por modelo" a "un producto por carpeta", los 23
 * productos sueltos anteriores quedaron huérfanos. Solo se mandan a la
 * papelera los que creó este script (llevan `_ese_producto_key`): lo que el
 * cliente haya cargado a mano no se toca.
 * ------------------------------------------------------------------ */

$ese_vigentes = array_column($ese_manifiesto['productos'], 'slug');
$ese_sembrados = get_posts([
    'post_type'      => 'producto',
    'post_status'    => 'any',
    'posts_per_page' => -1,
    'meta_key'       => '_ese_producto_key',
]);

$ese_papelera = 0;
foreach ($ese_sembrados as $ese_viejo) {
    $clave = (string) get_post_meta($ese_viejo->ID, '_ese_producto_key', true);
    if ('' === $clave || in_array($clave, $ese_vigentes, true)) {
        continue;
    }
    if (! $ESE_SECO) {
        wp_trash_post($ese_viejo->ID);
    }
    $ese_papelera++;
    echo "  a la papelera: #{$ese_viejo->ID} {$ese_viejo->post_title}\n";
}
if ($ese_papelera > 0) {
    echo "  ($ese_papelera productos de la estructura anterior)\n";
}

/* ------------------------------------------------------------------ *
 * 4. Resumen
 * ------------------------------------------------------------------ */

echo "\n== Catálogo completo\n";
$q = new WP_Query([
    'post_type' => 'producto', 'posts_per_page' => -1, 'post_status' => 'any',
    'orderby' => 'title', 'order' => 'ASC',
]);
$sin_foto = 0;
foreach ($q->posts as $p) {
    $foto = function_exists('ese_latam_producto_foto') ? ese_latam_producto_foto($p->ID) : '';
    if ('' === $foto) {
        $sin_foto++;
    }
    $t = get_the_terms($p->ID, 'producto_categoria');
    printf(
        "  #%-4d %-32s %-24s %s\n",
        $p->ID,
        $p->post_title,
        is_array($t) && $t ? $t[0]->name : '—',
        '' !== $foto ? 'con foto' : 'SIN FOTO'
    );
}
echo "\nproductos: " . count($q->posts) . "   sin foto: $sin_foto\n";
if ($ESE_SECO) {
    echo "\n(simulacro: no se escribió nada)\n";
}
