/**
 * Header fijo: entrada animada, estado "scrolled" (fondo oscuro translúcido),
 * reveal del logo desde detrás del nav-pill —scrubbed con el scroll, no un
 * snap instantáneo— y toggle del menú móvil.
 */

import { gsap, ScrollTrigger } from '../lib/gsap';

const DESKTOP_QUERY = '(min-width: 64rem)'; // mismo breakpoint que .nav-pill
const SCROLL_THRESHOLD = 32;

export function initHeader(): void {
  const header = document.querySelector<HTMLElement>('[data-header]');
  if (!header) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Debe ir ANTES de crear la tween de entrada: fija la x inicial del
  // nav-pill (centrado) vía gsap.set. Si la entrada (de abajo) se creara
  // primero, GSAP cachearía x=0 como valor base del nav-pill en ese momento
  // y lo re-impondría en cada frame mientras anima `y`, pisando la x fijada
  // después — el nav-pill jamás llegaría a centrarse.
  initLogoReveal(header, prefersReducedMotion);

  // Entrada: nav-pill y toggle bajan con un pequeño stagger al cargar.
  // El logo queda excluido a propósito: su opacidad la controla en exclusiva
  // el reveal por scroll de abajo — animarla desde dos sitios a la vez
  // (misma propiedad, dos timelines) generaría un tira y afloja impredecible.
  // En la front-page el hero controla la entrada de todo el header (aparece con scroll).
  const heroControlsHeader = document.querySelector('[data-hero]') !== null;
  if (!heroControlsHeader && !prefersReducedMotion) {
    const inner = header.querySelector<HTMLElement>('.site-header__inner');
    const entranceEls = inner?.querySelectorAll<HTMLElement>(':scope > *:not(.site-logo)');
    if (entranceEls?.length) {
      gsap.from(entranceEls, {
        y: -28,
        autoAlpha: 0,
        duration: 0.9,
        ease: 'power3.out',
        stagger: 0.12,
        delay: 0.1,
        // Sin clearProps: aunque GSAP admite tokens como 'y' para limpiar
        // solo un eje, en la práctica sigue reescribiendo el `transform`
        // completo del elemento y pisa la `x` que fija initLogoReveal (mismo
        // transform compuesto, único CSS `transform`). El resto de props que
        // toca esta tween (y en 0, opacity en 1) no bloquea nada después.
      });
    }
  }

  // Solo gobierna el fondo translúcido tras el header (blur) — el logo ya
  // no depende de este umbral, ver initLogoReveal.
  const onScroll = (): void => {
    header.classList.toggle('is-scrolled', window.scrollY > SCROLL_THRESHOLD);
  };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  const toggle = header.querySelector<HTMLButtonElement>('[data-nav-toggle]');
  const panel = header.querySelector<HTMLElement>('[data-nav-panel]');
  if (!toggle || !panel) return;

  // Bloquea el scroll de la página mientras el overlay está abierto
  const setOpen = (open: boolean): void => {
    header.classList.toggle('is-open', open);
    toggle.setAttribute('aria-expanded', String(open));
    document.documentElement.style.overflow = open ? 'hidden' : '';
  };

  toggle.addEventListener('click', () => {
    setOpen(!header.classList.contains('is-open'));
  });

  // Cierra el panel al navegar con un link del menú
  panel.addEventListener('click', (event) => {
    if ((event.target as HTMLElement).closest('a')) {
      setOpen(false);
    }
  });
}

// Distancia de scroll (px) que tarda en completarse la transición — cuanto
// más grande, más lento/gradual se recogen el logo y el nav-pill al centro.
const REVEAL_DISTANCE = 600;

/**
 * Al tope de la página el header está en su layout normal: logo visible a la
 * izquierda y nav-pill a la derecha. Scrubbed con ScrollTrigger sobre
 * REVEAL_DISTANCE px: al bajar, el nav-pill viaja al centro mientras el logo
 * se encoge y desvanece hacia ese mismo centro — queda "escondido detrás"
 * del pill. Reversible 1:1 con el scroll: al volver arriba, reaparece.
 */
function initLogoReveal(header: HTMLElement, prefersReducedMotion: boolean): void {
  const logo = header.querySelector<HTMLElement>('.site-logo');
  const navPill = header.querySelector<HTMLElement>('.nav-pill');
  const inner = header.querySelector<HTMLElement>('.site-header__inner');

  if (!logo || !navPill || !inner || prefersReducedMotion) {
    if (logo) gsap.set(logo, { autoAlpha: 1 });
    return;
  }

  let offsets = { navDx: 0, logoDx: 0 };

  // Mide con los transforms reseteados (si no, arrastraríamos el offset de
  // la medición anterior) la distancia para centrar el nav-pill en el header
  // y la distancia para que el logo quede justo sobre ese centro.
  const measure = (): void => {
    gsap.set([logo, navPill], { x: 0 });
    const containerRect = inner.getBoundingClientRect();
    const logoRect = logo.getBoundingClientRect();
    const navRect = navPill.getBoundingClientRect();

    const navCenteredLeft = containerRect.left + (containerRect.width - navRect.width) / 2;
    const navCenteredCenterX = navCenteredLeft + navRect.width / 2;

    offsets = {
      navDx: navCenteredLeft - navRect.left,
      logoDx: navCenteredCenterX - (logoRect.left + logoRect.width / 2),
    };
  };

  // matchMedia crea/destruye automáticamente el setup de cada breakpoint al
  // cruzarlo (mata sus ScrollTriggers y tweens) — evita gestionar a mano el
  // salto entre "hay nav-pill tras el que esconder el logo" (desktop) y "no
  // lo hay, el logo va siempre visible" (móvil).
  const mm = gsap.matchMedia();

  mm.add(DESKTOP_QUERY, () => {
    measure();

    const tl = gsap.timeline({
      defaults: { ease: 'none' },
      scrollTrigger: {
        trigger: document.documentElement,
        start: 'top top',
        end: '+=' + REVEAL_DISTANCE,
        scrub: 0.3,
        invalidateOnRefresh: true,
      },
    });

    // Valores "to" function-based: con invalidateOnRefresh se re-miden
    // en cada refresh (resize), sin necesidad de reconstruir el timeline.
    tl.fromTo(navPill, { x: 0 }, { x: () => offsets.navDx }, 0);
    tl.fromTo(
      logo,
      { x: 0, scale: 1, autoAlpha: 1 },
      { x: () => offsets.logoDx, scale: 0.75, autoAlpha: 0 },
      0
    );

    ScrollTrigger.addEventListener('refreshInit', measure);

    // Se ejecuta automáticamente al salir de este breakpoint (matchMedia).
    return () => {
      ScrollTrigger.removeEventListener('refreshInit', measure);
    };
  });

  // Móvil: no hay nav-pill tras el cual esconder el logo — va siempre visible.
  mm.add('(max-width: 63.9375rem)', () => {
    gsap.set([logo, navPill], { x: 0, scale: 1, autoAlpha: 1 });
  });
}
