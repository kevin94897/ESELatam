/**
 * Marquee de texto gigante ligado al scroll (Figma node 3287-283).
 *
 * Cualquier `[data-marquee]` (el track, con su contenido repetido para dar
 * cobertura) se desplaza horizontalmente scrubbed mientras su sección cruza
 * el viewport — mismo lenguaje que el resto de animaciones de la página.
 * Con `data-marquee="right"` invierte el sentido.
 */

import { gsap } from '../lib/gsap';

export function initMarquees(): void {
  // Con reduce-motion el texto queda estático (posición del diseño)
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    return;
  }

  document.querySelectorAll<HTMLElement>('[data-marquee]').forEach((track) => {
    const direction = track.dataset.marquee === 'right' ? 1 : -1;

    gsap.fromTo(
      track,
      { xPercent: direction * -12 },
      {
        xPercent: direction * 12,
        ease: 'none',
        scrollTrigger: {
          trigger: track.parentElement ?? track,
          start: 'top bottom',
          end: 'bottom top',
          scrub: 0.4,
        },
      }
    );
  });
}
