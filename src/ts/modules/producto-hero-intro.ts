/**
 * Entrada coreografiada del hero de la ficha de producto
 * (template-parts/producto-hero.php). Reemplaza a los reveals genéricos
 * (data-reveal / data-reveal-stagger) que tenía la sección: todo va en UN
 * timeline con tiempos absolutos, para que se lea como una sola escena:
 *
 *   0.00  escena: el foco de luz se enciende y los anillos se expanden
 *   0.15  isla: sube desde abajo y se asienta
 *   0.35  producto: cae desde arriba con un pequeño rebote y su sombra
 *         aparece al tocar la isla
 *   0.25  breadcrumb → kicker → título por líneas (máscara) → bajada →
 *         grupos de configuración → acciones, en cascada
 *   1.40  indicador de scroll
 *
 * La foto del producto lleva su propia flotación (data-float en el <img>):
 * acá se anima el wrapper `.producto-hero__product`, nunca el <img>, para
 * no pisar ese tween. Mismo criterio con la isla: el wrapper, no la foto.
 *
 * Con JS presente, el CSS (`.producto-hero.is-js`) esconde las partes hasta
 * que este timeline las revela; sin JS todo queda visible desde el HTML.
 * Con prefers-reduced-motion se muestra todo de golpe.
 */

import { gsap, SplitText } from '../lib/gsap';

export function initProductoHeroIntro(hero: HTMLElement): void {
  const crumb = hero.querySelector<HTMLElement>('.producto-hero__crumb');
  const kicker = hero.querySelector<HTMLElement>('.producto-hero__kicker');
  const title = hero.querySelector<HTMLElement>('.producto-hero__title');
  const desc = hero.querySelector<HTMLElement>('.producto-hero__desc');
  const groups = hero.querySelectorAll<HTMLElement>('.producto-hero__group');
  const actions = hero.querySelector<HTMLElement>('.producto-hero__actions');
  const spot = hero.querySelector<HTMLElement>('.producto-hero__spot');
  const ring = hero.querySelector<HTMLElement>('.producto-hero__ring');
  const pedestal = hero.querySelector<HTMLElement>('.producto-hero__pedestal');
  const product = hero.querySelector<HTMLElement>('.producto-hero__product');
  const shadow = hero.querySelector<HTMLElement>('.producto-hero__product .product-card__shadow');
  const scrollHint = hero.querySelector<HTMLElement>('.producto-hero__scroll-hint');

  const all = [crumb, kicker, title, desc, ...groups, actions, spot, ring, pedestal, product, scrollHint]
    .filter((el): el is HTMLElement => el !== null);

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    gsap.set(all, { autoAlpha: 1 });
    return;
  }

  // Título por líneas con máscara (mismo recurso que Nosotros/Distribuidores).
  // Se revierte al terminar para que el <h1> vuelva a su HTML original.
  const split = title ? new SplitText(title, { type: 'lines', mask: 'lines' }) : null;

  const tl = gsap.timeline({
    defaults: { ease: 'power3.out', duration: 0.9 },
    delay: 0.1,
    onComplete: () => split?.revert(),
  });

  // ---------- Escena ----------
  if (spot) tl.fromTo(spot, { autoAlpha: 0, scale: 0.6 }, { autoAlpha: 1, scale: 1, duration: 1.6, ease: 'power2.out' }, 0);
  if (ring) tl.fromTo(ring, { autoAlpha: 0, scale: 0.4 }, { autoAlpha: 1, scale: 1, duration: 1.6, ease: 'expo.out' }, 0.05);
  if (pedestal) {
    tl.fromTo(pedestal, { autoAlpha: 0, y: 90, scale: 0.9 }, { autoAlpha: 1, y: 0, scale: 1, duration: 1.3, ease: 'expo.out' }, 0.15);
  }
  if (product) {
    // Cae desde arriba y rebota apenas al "tocar" la isla (back.out), con la
    // sombra apareciendo justo en ese momento.
    tl.fromTo(product, { autoAlpha: 0, y: -70, scale: 0.92 }, { autoAlpha: 1, y: 0, scale: 1, duration: 1.2, ease: 'back.out(1.2)' }, 0.35);
    if (shadow) tl.fromTo(shadow, { autoAlpha: 0, scaleX: 0.5 }, { autoAlpha: 1, scaleX: 1, duration: 0.6 }, 0.95);
  }

  // ---------- Panel de configuración ----------
  if (crumb) tl.fromTo(crumb, { autoAlpha: 0, y: -14 }, { autoAlpha: 1, y: 0, duration: 0.7 }, 0.25);
  if (kicker) tl.fromTo(kicker, { autoAlpha: 0, y: 16 }, { autoAlpha: 1, y: 0, duration: 0.7 }, 0.4);
  if (title && split) {
    tl.set(title, { autoAlpha: 1 }, 0.5);
    tl.from(split.lines, { yPercent: 110, duration: 1.05, stagger: 0.12 }, 0.5);
  } else if (title) {
    tl.fromTo(title, { autoAlpha: 0, y: 30 }, { autoAlpha: 1, y: 0 }, 0.5);
  }
  if (desc) tl.fromTo(desc, { autoAlpha: 0, y: 20 }, { autoAlpha: 1, y: 0, duration: 0.8 }, 0.8);
  if (groups.length) {
    tl.fromTo(groups, { autoAlpha: 0, y: 24 }, { autoAlpha: 1, y: 0, duration: 0.8, stagger: 0.14 }, 0.95);
  }
  if (actions) tl.fromTo(actions, { autoAlpha: 0, y: 24 }, { autoAlpha: 1, y: 0, duration: 0.8 }, 1.25);

  // ---------- Indicador de scroll ----------
  if (scrollHint) tl.fromTo(scrollHint, { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.6 }, 1.4);
}
