/**
 * Header fijo: entrada animada, estado "scrolled" (fondo oscuro translúcido)
 * y toggle del menú móvil.
 */

import { gsap } from '../lib/gsap';

export function initHeader(): void {
  const header = document.querySelector<HTMLElement>('[data-header]');
  if (!header) return;

  // Entrada: logo y pill bajan con un pequeño stagger.
  // En la front-page el hero controla la entrada del header (aparece con scroll).
  const heroControlsHeader = document.querySelector('[data-hero]') !== null;
  if (!heroControlsHeader && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const inner = header.querySelector<HTMLElement>('.site-header__inner');
    if (inner) {
      gsap.from(inner.children, {
        y: -28,
        autoAlpha: 0,
        duration: 0.9,
        ease: 'power3.out',
        stagger: 0.12,
        delay: 0.1,
        clearProps: 'all',
      });
    }
  }

  const onScroll = (): void => {
    header.classList.toggle('is-scrolled', window.scrollY > 32);
  };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  const toggle = header.querySelector<HTMLButtonElement>('[data-nav-toggle]');
  const panel = header.querySelector<HTMLElement>('[data-nav-panel]');
  if (!toggle || !panel) return;

  toggle.addEventListener('click', () => {
    const open = header.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', String(open));
  });

  // Cierra el panel al navegar con un link del menú
  panel.addEventListener('click', (event) => {
    if ((event.target as HTMLElement).closest('a')) {
      header.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
    }
  });
}
