/**
 * Índice de las páginas legales (page-legal.php).
 *
 * Solo marca en el índice la sección que se está leyendo, con el mismo
 * criterio que el aside de "Objetivos con propósito": activa mientras cruza
 * la franja alta del viewport.
 *
 * El clic no se toca acá. Los enlaces son anclas normales y ya las mueve
 * initSmoothScroll (modules/smooth-scroll.ts) con la inercia de Lenis,
 * respetando el `scroll-margin-top` que el CSS le pone a cada sección para
 * que el título no quede bajo la cabecera fija. Sin JS o con reduce-motion,
 * el salto nativo del navegador también lo respeta.
 */

import { ScrollTrigger } from '../lib/gsap';

export function initLegal(root: HTMLElement): void {
  const links = Array.from(root.querySelectorAll<HTMLAnchorElement>('[data-legal-link]'));
  const secciones = Array.from(root.querySelectorAll<HTMLElement>('[data-legal-seccion]'));
  if (!links.length || !secciones.length) return;

  let actual = -1;

  const activar = (index: number): void => {
    if (index === actual) return;
    actual = index;
    links.forEach((link, i) => link.classList.toggle('is-active', i === index));
  };

  // Si se llega con un ancla en la URL, esa manda; si no, la primera.
  const inicial = secciones.findIndex((s) => `#${s.id}` === window.location.hash);
  activar(inicial >= 0 ? inicial : 0);

  secciones.forEach((seccion, i) => {
    ScrollTrigger.create({
      trigger: seccion,
      start: 'top 40%',
      end: 'bottom 40%',
      onToggle: (self) => {
        if (self.isActive) activar(i);
      },
    });
  });
}
