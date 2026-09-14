/**
 * Buscador global: el ícono de lupa del nav-pill (`[data-site-search-open]`)
 * abre un overlay (`[data-site-search]`, en header.php) con el campo de
 * búsqueda y chips de sugerencia. El formulario envía al archivo del CPT
 * (/catalogo/?s=), que ya pinta los resultados con la grilla real.
 *
 *  - Abre con animación GSAP (backdrop en fade, panel baja y se asienta) y
 *    deja el foco en el input; cierra con Esc, click fuera, la X o el mismo
 *    ícono, devolviendo el foco al botón que lo abrió.
 *  - Bloquea el scroll de la página mientras está abierto (mismo criterio
 *    que el panel móvil en header.ts).
 *  - Trap de foco mínimo: Tab desde el último control vuelve al primero.
 *  - Con prefers-reduced-motion abre/cierra sin animar.
 */

import { gsap } from '../lib/gsap';

export function initSearchOverlay(): void {
  const overlay = document.querySelector<HTMLElement>('[data-site-search]');
  if (!overlay) return;

  const triggers = Array.from(document.querySelectorAll<HTMLElement>('[data-site-search-open]'));
  const panel = overlay.querySelector<HTMLElement>('.site-search__panel');
  const backdrop = overlay.querySelector<HTMLElement>('.site-search__backdrop');
  const input = overlay.querySelector<HTMLInputElement>('.site-search__input');
  const closers = Array.from(overlay.querySelectorAll<HTMLElement>('[data-site-search-close]'));
  if (!triggers.length || !panel || !backdrop || !input) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  let isOpen = false;
  let lastTrigger: HTMLElement | null = null;

  const setExpanded = (open: boolean): void => {
    triggers.forEach((trigger) => trigger.setAttribute('aria-expanded', String(open)));
  };

  const open = (trigger: HTMLElement | null): void => {
    if (isOpen) return;
    isOpen = true;
    lastTrigger = trigger;

    overlay.hidden = false;
    document.documentElement.style.overflow = 'hidden';
    setExpanded(true);

    gsap.killTweensOf([backdrop, panel]);
    if (prefersReducedMotion) {
      gsap.set([backdrop, panel], { autoAlpha: 1, y: 0, scale: 1 });
    } else {
      gsap.fromTo(backdrop, { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.3, ease: 'power2.out' });
      gsap.fromTo(
        panel,
        { autoAlpha: 0, y: -18, scale: 0.98 },
        { autoAlpha: 1, y: 0, scale: 1, duration: 0.45, ease: 'power3.out' }
      );
    }

    // Recién se le quitó `hidden`: el foco va en el frame siguiente, cuando
    // el input ya es enfocable.
    requestAnimationFrame(() => {
      input.focus();
      input.select();
    });
  };

  const close = (): void => {
    if (!isOpen) return;
    isOpen = false;

    setExpanded(false);
    document.documentElement.style.overflow = '';

    const finish = (): void => {
      overlay.hidden = true;
      lastTrigger?.focus();
    };

    gsap.killTweensOf([backdrop, panel]);
    if (prefersReducedMotion) {
      gsap.set([backdrop, panel], { autoAlpha: 0 });
      finish();
      return;
    }
    gsap.to(panel, { autoAlpha: 0, y: -12, scale: 0.98, duration: 0.25, ease: 'power2.in' });
    gsap.to(backdrop, { autoAlpha: 0, duration: 0.25, ease: 'power2.in', onComplete: finish });
  };

  triggers.forEach((trigger) => {
    trigger.addEventListener('click', () => {
      if (isOpen) close();
      else open(trigger);
    });
  });
  closers.forEach((closer) => closer.addEventListener('click', close));

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && isOpen) close();
  });

  overlay.addEventListener('keydown', (event) => {
    if (event.key !== 'Tab') return;
    const focusables = Array.from(
      overlay.querySelectorAll<HTMLElement>('input, button, a[href]')
    );
    if (!focusables.length) return;
    const first = focusables[0];
    const last = focusables[focusables.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  });
}
