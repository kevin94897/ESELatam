/**
 * Entrada coreografiada de la sección Distribuidores (front-page.php,
 * Figma 3323-55). Reemplaza a los reveals genéricos (data-reveal /
 * data-reveal-header) que tenía la sección: acá todo va en UN timeline con
 * tiempos absolutos, para que el orden se lea como una sola escena:
 *
 *   0.00  anillos: se expanden desde el centro, uno tras otro
 *   0.15  globo: aparece escalando y "enderezándose" (rotación -14° → 0)
 *   0.35  kicker → título por líneas (máscara, SplitText) → bajada
 *   0.70  riel: el contador cuenta 0 → 13 y las pastillas caen en cascada
 *   0.95  tarjeta del país activo: sube y se asienta
 *
 * Corre en el bootstrap (no es perezoso como el globo 3D): es liviano y
 * necesita armar el ScrollTrigger antes de que el usuario llegue. El globo
 * en sí puede seguir cargándose (Three.js) mientras el wrapper ya anima —
 * el canvas aparece dentro de un contenedor que ya está en su sitio.
 *
 * Con prefers-reduced-motion no se anima nada y todo queda visible.
 */

import { gsap, SplitText } from '../lib/gsap';

export function initDistribuidoresIntro(): void {
  const section = document.querySelector<HTMLElement>('[data-globe]');
  if (!section) return;

  const rings = section.querySelectorAll<HTMLElement>('.distribuidores__rings span');
  const globe = section.querySelector<HTMLElement>('[data-globe-canvas]');
  const kicker = section.querySelector<HTMLElement>('.distribuidores__intro .type-kicker');
  const title = section.querySelector<HTMLElement>('.distribuidores__title');
  const desc = section.querySelector<HTMLElement>('.distribuidores__desc');
  const railHead = section.querySelector<HTMLElement>('.distribuidores__rail-head');
  const count = section.querySelector<HTMLElement>('.distribuidores__rail-count');
  const pills = section.querySelectorAll<HTMLElement>('.country-pill');
  const select = section.querySelector<HTMLElement>('.distribuidores__select');
  const panel = section.querySelector<HTMLElement>('.distribuidores__panel');

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  // Título por líneas con máscara (mismo recurso que el hero de Nosotros).
  // Se revierte al terminar para que el <h2> vuelva a su HTML original.
  const split = title ? new SplitText(title, { type: 'lines', mask: 'lines' }) : null;

  const tl = gsap.timeline({
    defaults: { ease: 'power3.out', duration: 1 },
    scrollTrigger: {
      trigger: section,
      start: 'top 70%',
      // Sin `once`: mismo criterio que scroll-reveals.ts (el auto-kill en
      // pleno refresh encoge el array de triggers). Anima igual una vez.
      toggleActions: 'play none none none',
    },
    onComplete: () => split?.revert(),
  });

  // ---------- Fondo: anillos y globo ----------
  if (rings.length) {
    tl.from(rings, { scale: 0.35, autoAlpha: 0, duration: 1.6, ease: 'expo.out', stagger: 0.14 }, 0);
  }
  if (globe) {
    tl.from(globe, { scale: 0.55, rotation: -14, autoAlpha: 0, duration: 1.7, ease: 'expo.out' }, 0.15);
  }

  // ---------- Intro (columna izquierda) ----------
  if (kicker) tl.from(kicker, { y: 20, autoAlpha: 0, duration: 0.7 }, 0.35);
  if (split) {
    tl.from(split.lines, { yPercent: 110, duration: 1.1, stagger: 0.12 }, 0.45);
  } else if (title) {
    tl.from(title, { y: 32, autoAlpha: 0 }, 0.45);
  }
  if (desc) tl.from(desc, { y: 24, autoAlpha: 0, duration: 0.8 }, 0.8);

  // ---------- Riel de países (columna derecha) ----------
  if (railHead) tl.from(railHead, { y: 16, autoAlpha: 0, duration: 0.7 }, 0.7);
  if (count) {
    // Cuenta 0 → N sobre un proxy y escribe el entero en cada frame.
    const target = Number(count.textContent ?? 0);
    const proxy = { value: 0 };
    tl.to(proxy, {
      value: target,
      duration: 1.2,
      ease: 'power2.out',
      onUpdate: () => {
        count.textContent = String(Math.round(proxy.value));
      },
    }, 0.75);
  }
  if (pills.length) {
    tl.from(pills, { x: 28, autoAlpha: 0, duration: 0.7, stagger: 0.05, clearProps: 'transform' }, 0.85);
  }
  if (select) tl.from(select, { y: 16, autoAlpha: 0, duration: 0.7 }, 0.85);

  // ---------- Tarjeta del país activo ----------
  if (panel) tl.from(panel, { y: 48, scale: 0.96, autoAlpha: 0, duration: 1.1 }, 0.95);
}
