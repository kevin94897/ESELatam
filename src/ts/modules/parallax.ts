/**
 * Parallax vertical scrubbed con el scroll.
 *
 * Cualquier `[data-parallax]` se traslada en yPercent mientras su contenedor
 * cruza el viewport: al bajar el scroll el elemento asciende, y al subir
 * desciende — reversible 1:1 con el scroll, como el resto de la página.
 *
 * Rango configurable: `data-parallax-from` / `data-parallax-to` (yPercent).
 */

import { gsap } from '../lib/gsap';

export function initParallax(): void {
  // Con reduce-motion el elemento queda estático en su posición de layout
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    return;
  }

  document.querySelectorAll<HTMLElement>('[data-parallax]').forEach((el) => {
    const from = Number(el.dataset.parallaxFrom ?? 30);
    const to = Number(el.dataset.parallaxTo ?? -30);

    gsap.fromTo(
      el,
      { yPercent: from },
      {
        yPercent: to,
        ease: 'none',
        scrollTrigger: {
          trigger: el.parentElement ?? el,
          // clamp(): comprime el rango a lo realmente scrolleable — sin esto,
          // un elemento al final del documento (p. ej. el footer) tendría su
          // 'bottom top' más allá del scroll máximo y el recorrido del
          // parallax quedaría truncado a una fracción.
          start: 'clamp(top bottom)',
          end: 'clamp(bottom top)',
          scrub: 0.4,
        },
      }
    );
  });
}
