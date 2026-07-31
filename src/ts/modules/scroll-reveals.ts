/**
 * Scroll reveals genéricos.
 *
 * `data-reveal="up|down|left|right|fade"` — el elemento se anima al entrar
 * en viewport con un blur-up moderno. `data-reveal-delay="0.2"` para retrasar.
 *
 * `data-reveal-stagger` — contenedor cuyos hijos directos entran en cascada.
 */

import { gsap } from '../lib/gsap';

type Direction = 'up' | 'down' | 'left' | 'right' | 'fade';

const OFFSET = 48;

const FROM: Record<Direction, gsap.TweenVars> = {
  up:    { y: OFFSET, autoAlpha: 0 },
  down:  { y: -OFFSET, autoAlpha: 0 },
  left:  { x: OFFSET, autoAlpha: 0 },
  right: { x: -OFFSET, autoAlpha: 0 },
  fade:  { autoAlpha: 0 },
};

export function initScrollReveals(): void {
  // Con reduce-motion el timeline global queda congelado: no aplicamos
  // los `gsap.from` para que el contenido quede visible.
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    return;
  }

  document.querySelectorAll<HTMLElement>('[data-reveal]').forEach((el) => {
    const dir = (el.dataset.reveal || 'up') as Direction;
    const delay = Number(el.dataset.revealDelay ?? 0);
    const from = FROM[dir] ?? FROM.up;

    gsap.from(el, {
      ...from,
      filter: 'blur(8px)',
      duration: 1,
      ease: 'power3.out',
      delay,
      clearProps: 'filter,transform',
      scrollTrigger: {
        trigger: el,
        start: 'top 85%',
        once: true,
      },
    });
  });

  document.querySelectorAll<HTMLElement>('[data-reveal-stagger]').forEach((group) => {
    gsap.from(group.children, {
      y: OFFSET,
      autoAlpha: 0,
      filter: 'blur(8px)',
      duration: 1,
      ease: 'power3.out',
      stagger: 0.12,
      clearProps: 'filter,transform',
      scrollTrigger: {
        trigger: group,
        start: 'top 82%',
        once: true,
      },
    });
  });
}
