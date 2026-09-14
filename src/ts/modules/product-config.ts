/**
 * Panel de configuración de la ficha de producto (single-producto.php).
 *
 * Dos controles sobre datos reales del CPT, y una sola foto que depende de
 * LOS DOS a la vez:
 *
 *  1. Color — los swatches del panel izquierdo y las miniaturas de la
 *     derecha son DOS vistas del mismo estado: comparten el atributo
 *     `data-producto-color` (índice dentro de `data-colors`), así que tocar
 *     cualquiera de los dos actualiza el otro.
 *  2. Litraje — marca la píldora activa y sincroniza el valor de la card
 *     "Volumen" de la sección de especificaciones.
 *
 * La foto sale de `colors[i].imgs[litraje]` y cae a `colors[i].img` cuando
 * ese producto no tiene una foto propia por capacidad (ver el repeater
 * "Fotos por litraje" en inc/acf-productos.php). Por eso el swap vive en una
 * sola función `render()` en vez de estar duplicado en cada control: un
 * contenedor de 80L y uno de 360L del mismo color son piezas distintas, así
 * que cambiar la capacidad también cambia la imagen.
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
  /** Fotos por litraje ("120L" → URL). Vacío si el producto no las tiene. */
  imgs?: Record<string, string>;
}

export function initProductConfig(root: HTMLElement): void {
  let colors: ProductoColor[] = [];
  try {
    colors = JSON.parse(root.dataset.colors ?? '[]');
  } catch {
    colors = [];
  }

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const mainImg = root.querySelector<HTMLImageElement>('[data-producto-img]');
  const colorEls = Array.from(root.querySelectorAll<HTMLElement>('[data-producto-color]'));
  const litrajeEls = Array.from(root.querySelectorAll<HTMLElement>('[data-producto-litraje]'));
  // "Capacidad" en la barra de atributos del hero y la card "Volumen" de la
  // sección de especificaciones (fuera de `root`): todos los que haya.
  const volumenEls = Array.from(document.querySelectorAll<HTMLElement>('[data-producto-volumen]'));
  const colorNameEl = root.querySelector<HTMLElement>('[data-producto-color-name]');

  // Estado inicial: lo que el PHP ya dejó marcado con .is-active, para que
  // el módulo no pueda arrancar desincronizado del HTML servido.
  let activeColor = Math.max(
    0,
    colorEls.findIndex((el) => el.classList.contains('is-active'))
  );
  let activeLitraje =
    litrajeEls.find((el) => el.classList.contains('is-active'))?.dataset.productoLitraje ?? '';

  const imageFor = (colorIndex: number, litraje: string): string => {
    const color = colors[colorIndex];
    if (!color) return '';
    return color.imgs?.[litraje] || color.img || '';
  };

  // Las fotos de las otras variantes son archivos aparte: sin precargar, el
  // fade-in arranca con el <img> todavía vacío y se ve un parpadeo. Se
  // piden en idle, después del LCP (misma técnica que product-island.ts).
  const preload = (): void => {
    const seen = new Set<string>();
    colors.forEach((color) => {
      [color.img, ...Object.values(color.imgs ?? {})].forEach((url) => {
        if (!url || seen.has(url)) return;
        seen.add(url);
        const img = new Image();
        img.decoding = 'async';
        img.src = url;
      });
    });
  };
  if (typeof window.requestIdleCallback === 'function') {
    window.requestIdleCallback(preload, { timeout: 3000 });
  } else {
    window.setTimeout(preload, 1500);
  }

  let swapping = false;
  let pending: string | null = null;

  /** Cambia la foto principal al par (color, litraje) activo. */
  const render = (): void => {
    if (!mainImg) return;

    const src = imageFor(activeColor, activeLitraje);
    const alt = colors[activeColor]?.nombre ?? mainImg.alt;
    if (!src) return;

    // Comparar contra el src YA RESUELTO por el navegador (absoluto): las
    // URLs del JSON también lo son, así que un cambio de litraje que no
    // cambia de archivo no dispara una animación en vano.
    if (new URL(src, location.href).href === mainImg.src) return;

    if (prefersReducedMotion) {
      mainImg.src = src;
      mainImg.alt = alt;
      return;
    }

    // Un cambio durante el crossfade no se descarta: queda pendiente y se
    // aplica al terminar (clics rápidos entre colores no se pierden).
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
            // Mientras se reponía pudo elegirse otra variante.
            render();
          },
        });
      },
    });
  };

  // ---------- Color ----------

  const selectColor = (index: number): void => {
    if (!colors[index] || index === activeColor) return;
    activeColor = index;

    // Swatches y miniaturas comparten el atributo: un solo recorrido deja
    // los dos grupos en el mismo estado.
    colorEls.forEach((el) => {
      el.classList.toggle('is-active', Number(el.dataset.productoColor) === index);
    });
    if (colorNameEl) colorNameEl.textContent = colors[index].nombre;

    render();
  };

  colorEls.forEach((el) => {
    el.addEventListener('click', () => selectColor(Number(el.dataset.productoColor)));
  });

  // ---------- Litraje ----------

  const selectLitraje = (el: HTMLElement): void => {
    const litraje = el.dataset.productoLitraje;
    if (!litraje || litraje === activeLitraje) return;
    activeLitraje = litraje;

    litrajeEls.forEach((other) => other.classList.toggle('is-active', other === el));
    volumenEls.forEach((target) => {
      target.textContent = litraje;
    });

    render();
  };

  litrajeEls.forEach((el) => {
    el.addEventListener('click', () => selectLitraje(el));
  });
}
