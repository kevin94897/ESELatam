/**
 * Flotación continua e independiente del scroll.
 *
 * `[data-float]` sube y baja en bucle con un vaivén suave (sine.inOut), como
 * si el objeto levitara. Ajustable con `data-float-distance` (px, def. 12) y
 * `data-float-duration` (s, def. 3).
 *
 * `[data-float-shadow]` (opcional, hermano del elemento que flota) entra en el
 * MISMO timeline: encoge y se aclara cuando el objeto sube. Compartir timeline
 * —en vez de crear uno propio— es lo que garantiza que sombra y objeto no se
 * desfasen con el tiempo.
 *
 * OJO: el elemento no debe traer un `transform` propio desde CSS (centrarlo
 * con márgenes, no con translateX(-50%)); GSAP fijaría ese porcentaje en px al
 * leer la matriz computada. Ver .product-card__img en main.css.
 */

import { gsap } from '../lib/gsap';

/**
 * @param scope Dónde buscar `[data-float]`. Por defecto todo el documento; el
 *   filtro de categorías de la portada (product-filter.ts) pasa el wrapper del
 *   carrusel para animar solo los slides que acaba de montar.
 */
export function initFloat(scope: ParentNode = document): void {
  // Movimiento perpetuo es justo lo que reduce-motion pide evitar.
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  scope.querySelectorAll<HTMLElement>('[data-float]').forEach((el) => {
    // Un slide que vuelve a entrar al carrusel al cambiar de filtro ya trae
    // su timeline: un segundo tween sobre la misma `y` se pelearía con el
    // primero y la flotación quedaría temblando.
    if (el.dataset.floatReady !== undefined) return;
    el.dataset.floatReady = '';

    const distance = Number(el.dataset.floatDistance ?? 12);
    const duration = Number(el.dataset.floatDuration ?? 3);
    const shadow = el.parentElement?.querySelector<HTMLElement>('[data-float-shadow]');

    const tl = gsap.timeline({
      repeat: -1,
      yoyo: true,
      defaults: { duration, ease: 'sine.inOut' },
    });

    tl.to(el, { y: -distance }, 0);
    if (shadow) tl.to(shadow, { scaleX: 0.82, opacity: 0.55 }, 0);

    // Desfase inicial aleatorio: si todas las tarjetas flotaran al unísono se
    // vería mecánico. progress() (no delay) para que ya arranquen repartidas.
    tl.progress(Math.random());
  });
}
