/**
 * Panel de configuración de la ficha de producto (single-producto.php).
 *
 * Un producto del catálogo es una FAMILIA —"Papeleras"— y agrupa a los
 * modelos que la componen: Open Dinova, Campus Goool, Venta… De ahí que el
 * panel tenga tres listas encadenadas, no dos:
 *
 *  1. Modelo — cambia qué capacidades y qué colores existen. Solo aparece
 *     cuando la familia tiene más de uno.
 *  2. Capacidad — las del modelo elegido.
 *  3. Color — los del modelo elegido. Los swatches del panel y las
 *     miniaturas comparten el atributo `data-producto-color`, así que tocar
 *     cualquiera de los dos actualiza el otro.
 *
 * El PHP imprime las píldoras y las muestras de TODOS los modelos y esconde
 * con `hidden` las de los inactivos. Este módulo solo alterna ese atributo:
 * no arma markup, así la ficha sigue siendo correcta sin JavaScript y no hay
 * dos fuentes de verdad para el mismo panel.
 *
 * La foto sale de `colores[i].imgs[capacidad]` y cae a `colores[i].img`
 * cuando esa combinación no tiene foto propia. Por eso el cambio de imagen
 * vive en una sola función `render()`: las tres listas alteran el mismo
 * estado y cualquiera de ellas puede cambiar la pieza que se ve.
 *
 * La flotación continua de la foto la sigue resolviendo initFloat()
 * (`[data-float]`), igual que en el banner de catálogo — acá solo se cambia
 * el `src`, nunca el transform del <img>, para no pisar ese tween.
 */

import { gsap } from '../lib/gsap';

interface ProductoColor {
  nombre: string;
  color: string;
  img: string;
  /** Fotos por capacidad ("120L" → URL). Vacío si el modelo no las tiene. */
  imgs?: Record<string, string>;
}

interface ProductoLitraje {
  valor: string;
  default?: boolean;
}

interface ProductoModelo {
  nombre: string;
  descripcion?: string;
  litrajes: ProductoLitraje[];
  colores: ProductoColor[];
}

export function initProductConfig(root: HTMLElement): void {
  let modelos: ProductoModelo[] = [];
  try {
    modelos = JSON.parse(root.dataset.modelos ?? '[]');
  } catch {
    modelos = [];
  }
  if (modelos.length === 0) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const mainImg = root.querySelector<HTMLImageElement>('[data-producto-img]');
  const modeloEls = Array.from(
    root.querySelectorAll<HTMLElement>('.producto-hero__model[data-producto-modelo]')
  );
  const litrajeEls = Array.from(root.querySelectorAll<HTMLElement>('[data-producto-litraje]'));
  const colorEls = Array.from(root.querySelectorAll<HTMLElement>('[data-producto-color]'));
  // "Capacidad" en la barra de atributos del hero y la card "Volumen" de la
  // sección de especificaciones (fuera de `root`): todos los que haya.
  const volumenEls = Array.from(document.querySelectorAll<HTMLElement>('[data-producto-volumen]'));
  const colorNameEl = root.querySelector<HTMLElement>('[data-producto-color-name]');
  const modeloNameEl = root.querySelector<HTMLElement>('[data-producto-modelo-nombre]');
  const coloresEl = document.querySelector<HTMLElement>('[data-producto-colores]');
  const descEl = root.querySelector<HTMLElement>('.producto-hero__desc');
  const descOriginal = descEl?.textContent ?? '';

  const idx = (el: HTMLElement, attr: string): number => Number(el.dataset[attr] ?? -1);

  // Estado inicial: lo que el PHP ya dejó marcado, para que el módulo no
  // pueda arrancar desincronizado del HTML servido.
  let activeModel = Math.max(0, modeloEls.findIndex((el) => el.classList.contains('is-active')));
  let activeColor = 0;

  const modeloActual = (): ProductoModelo => modelos[activeModel] ?? modelos[0];

  const litrajePorDefecto = (m: ProductoModelo): string =>
    m.litrajes.find((l) => l.default)?.valor ?? m.litrajes[0]?.valor ?? '';

  let activeLitraje = litrajePorDefecto(modeloActual());

  // Las fotos de las otras variantes son archivos aparte: sin precargar, el
  // fade-in arranca con el <img> todavía vacío y se ve un parpadeo. Se piden
  // en idle, después del LCP (misma técnica que product-island.ts).
  const preload = (): void => {
    const vistas = new Set<string>();
    modelos.forEach((m) =>
      m.colores.forEach((c) => {
        [c.img, ...Object.values(c.imgs ?? {})].forEach((url) => {
          if (!url || vistas.has(url)) return;
          vistas.add(url);
          const img = new Image();
          img.decoding = 'async';
          img.src = url;
        });
      })
    );
  };
  if (typeof window.requestIdleCallback === 'function') {
    window.requestIdleCallback(preload, { timeout: 3000 });
  } else {
    window.setTimeout(preload, 1500);
  }

  let swapping = false;
  let pending: string | null = null;

  /** Cambia la foto principal a la combinación (modelo, color, capacidad) activa. */
  const render = (): void => {
    if (!mainImg) return;

    const color = modeloActual().colores[activeColor];
    if (!color) return;

    const src = color.imgs?.[activeLitraje] || color.img || '';
    if (!src) return;

    // Comparar contra el src YA RESUELTO por el navegador (absoluto): las
    // URLs del JSON también lo son, así que un cambio que no cambia de
    // archivo no dispara una animación en vano.
    if (new URL(src, location.href).href === mainImg.src) return;

    const alt = color.nombre;

    if (prefersReducedMotion) {
      mainImg.src = src;
      mainImg.alt = alt;
      return;
    }

    // Un cambio durante el crossfade no se descarta: queda pendiente y se
    // aplica al terminar (clics rápidos no se pierden).
    if (swapping) {
      pending = src;
      return;
    }

    swapping = true;
    gsap.to(mainImg, {
      autoAlpha: 0,
      scale: 0.94,
      duration: 0.22,
      ease: 'power2.in',
      onComplete: () => {
        mainImg.src = pending ?? src;
        mainImg.alt = alt;
        pending = null;
        gsap.to(mainImg, {
          autoAlpha: 1,
          scale: 1,
          duration: 0.4,
          ease: 'back.out(1.5)',
          onComplete: () => {
            swapping = false;
            render(); // por si se eligió otra variante mientras reponía
          },
        });
      },
    });
  };

  /** Deja visibles solo las píldoras y las muestras del modelo activo. */
  const sincronizarVisibles = (): void => {
    litrajeEls.forEach((el) => {
      el.hidden = idx(el, 'productoModelo') !== activeModel;
    });
    colorEls.forEach((el) => {
      const suyo = idx(el, 'productoModelo');
      // Las miniaturas de la escena no declaran modelo: siempre visibles.
      el.hidden = suyo >= 0 && suyo !== activeModel;
    });
  };

  const marcarLitraje = (): void => {
    litrajeEls.forEach((el) => {
      el.classList.toggle(
        'is-active',
        idx(el, 'productoModelo') === activeModel && el.dataset.productoLitraje === activeLitraje
      );
    });
    volumenEls.forEach((el) => {
      el.textContent = activeLitraje;
    });
  };

  const marcarColor = (): void => {
    colorEls.forEach((el) => {
      const suyo = idx(el, 'productoModelo');
      const mismoModelo = suyo < 0 || suyo === activeModel;
      el.classList.toggle('is-active', mismoModelo && idx(el, 'productoColor') === activeColor);
    });
    if (colorNameEl) {
      colorNameEl.textContent = modeloActual().colores[activeColor]?.nombre ?? '';
    }
  };

  // ---------- Modelo ----------

  const seleccionarModelo = (i: number): void => {
    if (!modelos[i] || i === activeModel) return;
    activeModel = i;

    const m = modeloActual();
    activeColor = 0;
    activeLitraje = litrajePorDefecto(m);

    modeloEls.forEach((el) => {
      el.classList.toggle('is-active', idx(el, 'productoModelo') === activeModel);
    });
    if (modeloNameEl) modeloNameEl.textContent = m.nombre;
    if (coloresEl) {
      const n = m.colores.length;
      coloresEl.textContent = `${n} ${n === 1 ? 'disponible' : 'disponibles'}`;
    }
    // La descripción del modelo, cuando la tiene, reemplaza a la del producto.
    if (descEl) descEl.textContent = m.descripcion || descOriginal;

    sincronizarVisibles();
    marcarLitraje();
    marcarColor();
    render();
  };

  modeloEls.forEach((el) => {
    el.addEventListener('click', () => seleccionarModelo(idx(el, 'productoModelo')));
  });

  // ---------- Capacidad ----------

  litrajeEls.forEach((el) => {
    el.addEventListener('click', () => {
      const valor = el.dataset.productoLitraje;
      if (!valor || valor === activeLitraje) return;
      activeLitraje = valor;
      marcarLitraje();
      render();
    });
  });

  // ---------- Color ----------

  colorEls.forEach((el) => {
    el.addEventListener('click', () => {
      const i = idx(el, 'productoColor');
      if (i < 0 || i === activeColor || !modeloActual().colores[i]) return;
      activeColor = i;
      marcarColor();
      render();
    });
  });

  // Estado coherente desde el primer frame.
  sincronizarVisibles();
  marcarLitraje();
  marcarColor();
}
