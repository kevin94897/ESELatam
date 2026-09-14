/**
 * Buscador móvil "expanding dock" (menú móvil, header.php).
 *
 * Réplica en GSAP del ExpandingSearchDock (React + framer-motion) del
 * brief: en reposo hay un botón redondo con la lupa; al tocarlo el botón se
 * encoge hasta desaparecer y en su lugar el formulario se abre desde 48px
 * hasta su ancho final con un resorte (`back.out`), el vidrio pasa de blur
 * 0 a 12px y la X de cierre aparece escalando. Cerrar invierte todo, limpia
 * el campo y devuelve el foco al botón. Esc cierra; si el panel móvil se
 * cierra con el buscador abierto, el dock vuelve solo al estado de ícono
 * (sin animar, el panel ya se está desvaneciendo).
 *
 * Con prefers-reduced-motion los estados cambian de golpe.
 */

import { gsap } from '../lib/gsap';

const COLLAPSED_WIDTH = 48;

export function initMobileSearch(): void {
  const root = document.querySelector<HTMLElement>('[data-mobile-search]');
  if (!root) return;

  const toggle = root.querySelector<HTMLButtonElement>('[data-mobile-search-open]');
  const form = root.querySelector<HTMLFormElement>('[data-mobile-search-form]');
  const close = root.querySelector<HTMLButtonElement>('[data-mobile-search-close]');
  const input = form?.querySelector<HTMLInputElement>('input[type="search"]');
  const header = root.closest<HTMLElement>('.site-header');
  if (!toggle || !form || !close || !input) return;

  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  let expanded = false;
  let busy = false;

  const setState = (open: boolean): void => {
    expanded = open;
    toggle.setAttribute('aria-expanded', String(open));
    root.classList.toggle('is-expanded', open);
  };

  // Ancho final: el del contenedor (el dock ocupa min(420px, 100%)).
  const targetWidth = (): number => root.getBoundingClientRect().width;

  const expand = (): void => {
    if (expanded || busy) return;
    setState(true);

    if (reduced) {
      toggle.hidden = true;
      form.hidden = false;
      input.focus();
      return;
    }

    busy = true;
    // El atributo `hidden` tiene que salir ANTES de animar: el preflight de
    // Tailwind lo pinta con `display: none !important`, contra el que un
    // `display: flex` inline de GSAP no puede. Arranca invisible igual
    // (autoAlpha 0) hasta que el tween lo abre.
    form.hidden = false;
    const tl = gsap.timeline({
      onComplete: () => {
        busy = false;
        input.focus();
      },
    });

    tl.to(toggle, { scale: 0, autoAlpha: 0, duration: 0.18, ease: 'power2.in' })
      .set(toggle, { display: 'none' })
      .set(form, { display: 'flex', width: COLLAPSED_WIDTH, autoAlpha: 0, backdropFilter: 'blur(0px)' })
      .to(form, {
        width: targetWidth,
        autoAlpha: 1,
        backdropFilter: 'blur(12px)',
        duration: 0.6,
        ease: 'back.out(1.4)',
      })
      // La X entra escalando cuando el campo ya casi tiene su ancho.
      .fromTo(close, { scale: 0 }, { scale: 1, duration: 0.35, ease: 'back.out(2)' }, '-=0.35');
  };

  const collapse = (animate = true): void => {
    if (!expanded) return;
    setState(false);
    input.value = '';

    if (reduced || !animate) {
      gsap.killTweensOf([toggle, form, close]);
      gsap.set([toggle, form, close], { clearProps: 'all' });
      form.hidden = true;
      toggle.hidden = false;
      busy = false;
      return;
    }

    busy = true;
    const tl = gsap.timeline({
      onComplete: () => {
        busy = false;
        gsap.set([toggle, form, close], { clearProps: 'all' });
        form.hidden = true;
        toggle.hidden = false;
        toggle.focus();
      },
    });

    tl.to(close, { scale: 0, duration: 0.15, ease: 'power2.in' })
      .to(form, {
        width: COLLAPSED_WIDTH,
        autoAlpha: 0,
        backdropFilter: 'blur(0px)',
        duration: 0.35,
        ease: 'power3.in',
      }, '<')
      .set(form, { display: 'none' })
      .set(toggle, { display: 'inline-flex', scale: 0, autoAlpha: 0 })
      .to(toggle, { scale: 1, autoAlpha: 1, duration: 0.35, ease: 'back.out(1.8)' });
  };

  toggle.addEventListener('click', expand);
  close.addEventListener('click', () => collapse());

  input.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      event.preventDefault();
      collapse();
    }
  });

  // Sin texto no hay nada que buscar: se queda en el campo en vez de
  // mandar a /catalogo/?s= vacío.
  form.addEventListener('submit', (event) => {
    if (!input.value.trim()) {
      event.preventDefault();
      input.focus();
    }
  });

  // Si el panel móvil se cierra (hamburguesa, o clic en un link del menú)
  // con el buscador abierto, el dock vuelve al ícono para la próxima vez.
  if (header && typeof MutationObserver === 'function') {
    new MutationObserver(() => {
      if (!header.classList.contains('is-open')) collapse(false);
    }).observe(header, { attributes: true, attributeFilter: ['class'] });
  }
}
