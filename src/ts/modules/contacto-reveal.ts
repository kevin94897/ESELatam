/**
 * Titular de la sección "Contactemos": cada LETRA (no un degradado parejo)
 * pasa de translúcida a blanco sólido, en cascada, según la posición de
 * scroll. Usa GSAP SplitText para partir el texto en `.char` y un timeline
 * con `stagger` embebido en un ScrollTrigger scrubbed — el scrub hace que
 * la cascada avance letra por letra al bajar y retroceda al subir, sin
 * lógica de reversa manual.
 */

import { gsap, SplitText } from '../lib/gsap';

export function initContactoReveal(): void {
  const heading = document.querySelector<HTMLElement>('[data-contacto-heading]');
  if (!heading) return;

  const split = new SplitText(heading, { type: 'chars', charsClass: 'char' });

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    gsap.set(split.chars, { opacity: 1 });
    return;
  }

  gsap.set(split.chars, { opacity: 0.32 });

  gsap.timeline({
    scrollTrigger: {
      trigger: heading,
      start: 'top 85%',
      end: 'bottom 40%',
      scrub: 0.3,
    },
  }).to(split.chars, {
    opacity: 1,
    // Duración casi nula a propósito: con el 0.8s por defecto del proyecto,
    // ~16 letras quedaban a medio camino a la vez (se veía como un degradado
    // parejo). Así cada letra "salta" de 0.32 a 1 casi de golpe, y lo único
    // gradual es la CASCADA entre letras (vía stagger), no cada letra en sí.
    duration: 0.01,
    stagger: 0.045,
    ease: 'none',
  });
}
