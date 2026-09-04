/**
 * Animación de entrada para los headers de sección.
 *
 * `data-reveal-header` en el contenedor del header y el módulo resuelve solo
 * sus tres partes (todas opcionales), en cascada:
 *
 *   1. el título   → entra hacia la derecha  (arranca corrido a la izquierda)
 *   2. la bajada   → entra hacia la izquierda (arranca corrida a la derecha)
 *   3. el kicker   → entra hacia arriba
 *
 * Las partes se buscan por convención, no por clase de sección, así el mismo
 * atributo sirve en cualquier header con esta estructura:
 *   · título → el `h2` (todas las secciones lo usan, con .type-h2 o su propia
 *     clase de título)
 *   · bajada → el primer `p` que no sea el kicker; `[data-reveal-desc]` manda
 *     si hay que desambiguar
 *   · kicker → `.type-kicker`
 * Se usa querySelector (no `:scope >`) a propósito: en varias secciones el
 * kicker y el título van envueltos en un div intermedio.
 *
 * Mismo lenguaje visual que scroll-reveals.ts (blur-up + power3.out) para que
 * se sienta parte del mismo sistema; los tramos se solapan en vez de esperar a
 * que termine el anterior, que es lo que hace que la cascada se sienta fluida.
 */

import { gsap } from '../lib/gsap';

const OFFSET = 48;
const DURATION = 0.9;

export function initSectionHeaders(): void {
  // Igual que el resto de reveals: sin animación, todo visible.
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  document.querySelectorAll<HTMLElement>('[data-reveal-header]').forEach((header) => {
    const title = header.querySelector<HTMLElement>('h2');
    const desc = header.querySelector<HTMLElement>('[data-reveal-desc]')
      ?? header.querySelector<HTMLElement>('p:not(.type-kicker)');
    const kicker = header.querySelector<HTMLElement>('.type-kicker');

    if (!title && !desc && !kicker) return;

    const tl = gsap.timeline({
      defaults: {
        duration: DURATION,
        ease: 'power3.out',
        filter: 'blur(8px)',
        autoAlpha: 0,
        // Sin transform ni filter inline al terminar: si quedaran, bloquearían
        // hovers y otras animaciones propias del elemento.
        clearProps: 'filter,transform',
      },
      scrollTrigger: {
        trigger: header,
        start: 'top 85%',
        // No `once: true`: al auto-matarse (kill -> _triggers.splice) mientras
        // otro trigger se refresca, el array se encoge en pleno bucle y
        // ScrollTrigger lee un índice vacío. Esto anima igual una sola vez.
        toggleActions: 'play none none none',
      },
    });

    // Los tiempos son absolutos (0, .18, .36) y no relativos: así el orden se
    // mantiene aunque falte alguna de las tres partes.
    if (title) tl.from(title, { x: -OFFSET }, 0);
    if (desc) tl.from(desc, { x: OFFSET }, 0.18);
    if (kicker) tl.from(kicker, { y: OFFSET }, 0.36);
  });
}
