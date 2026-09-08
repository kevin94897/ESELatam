/**
 * Hover del CTA primario del hero (`.hero-cta`) — réplica del botón de
 * integratedbio.com: el color/escala/flecha los resuelve CSS (ver
 * `.hero-cta*` en main.css); lo único que anima este módulo es la FORMA de
 * dos `<path>` reales vía GSAP MorphSVGPlugin — la esquina que empalma el
 * label con el chip, y el propio chip ("blob") de la flecha.
 *
 * `.hero-cta__arrow`'s hover-shape es el espejo vertical del path en reposo
 * (la punta inclinada pasa de abajo-izquierda a arriba-izquierda); el
 * corner hace el movimiento complementario. Reduce-motion ya está cubierto
 * globalmente (transition-duration forzado a 0.01ms + `gsap.globalTimeline`
 * en timeScale(0) en `initGsap()`), así que no hace falta duplicar esa
 * lógica aquí.
 */

import { gsap } from '../lib/gsap';

const PATHS = {
  corner: {
    default: 'M0 0h5.63c7.808 0 13.536 7.337 11.642 14.91l-6.09 24.359A11.527 11.527 0 0 1 0 48V0Z',
    hover: 'M0 0c5.29 0 9.9 3.6 11.183 8.731l6.09 24.359C19.165 40.663 13.437 48 5.63 48H0V0Z',
  },
  blob: {
    default: 'M73 50C73 54.4183 69.4183 58 65 58H11.4975C5.92468 58 2.05929 52.4447 3.99626 47.2194L19.5652 5.21938C20.7282 2.08215 23.7206 0 27.0664 0H65C69.4183 0 73 3.58172 73 8V50Z',
    hover: 'M73 8C73 3.5817 69.4183 0 65 0H11.4975C5.92468 0 2.05929 5.5553 3.99626 10.7806L19.5652 52.7806C20.7282 55.9179 23.7206 58 27.0664 58H65C69.4183 58 73 54.4183 73 50V8Z',
  },
} as const;

type MorphState = keyof typeof PATHS.corner;

export function initHeroCta(): void {
  // Mismo guard que el hover CSS de `.hero-cta` (main.css, `hover: hover` +
  // `pointer: fine`): sin él, el primer TAP en táctil disparaba el morph a
  // 'hover' (el JS no tiene equivalente a `:hover` que se suelte solo al
  // levantar el dedo) mientras el color/escala en CSS sí quedaba protegido
  // — un estado a medio camino que solo se corregía tocando otra parte de
  // la pantalla.
  if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;

  document.querySelectorAll<HTMLAnchorElement>('.hero-cta').forEach((cta) => {
    const cornerPath = cta.querySelector<SVGPathElement>('.hero-cta__corner path');
    const blobPath = cta.querySelector<SVGPathElement>('.hero-cta__arrow path');
    if (!cornerPath || !blobPath) return;

    const to = (state: MorphState): void => {
      gsap.to(cornerPath, { morphSVG: PATHS.corner[state], duration: 0.3, ease: 'power2.out', overwrite: true });
      gsap.to(blobPath, { morphSVG: PATHS.blob[state], duration: 0.3, ease: 'power2.out', overwrite: true });
    };

    cta.addEventListener('mouseenter', () => to('hover'));
    cta.addEventListener('mouseleave', () => to('default'));
  });
}
