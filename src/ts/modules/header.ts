/**
 * Header fijo ("barra de vidrio", ver main.css): entrada animada, progreso
 * continuo oscuro→claro/compacto ligado al scroll (--hdr-scroll), pastilla
 * que se desliza bajo el link con hover/foco y toggle del menú móvil.
 */

import { gsap } from '../lib/gsap';

// Píxeles de scroll en los que la barra pasa de vidrio oscuro a blanco.
const SCROLL_RANGE = 200;

export function initHeader(): void {
  const header = document.querySelector<HTMLElement>('[data-header]');
  if (!header) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Entrada: la cápsula baja al cargar. En la front-page el hero controla la
  // entrada de todo el header (aparece al final de la intro, hero-scroll.ts).
  const heroControlsHeader = document.querySelector('[data-hero]') !== null;
  if (!heroControlsHeader && !prefersReducedMotion) {
    gsap.from(header, {
      y: -24,
      autoAlpha: 0,
      duration: 0.9,
      ease: 'power3.out',
      delay: 0.1,
      clearProps: 'transform',
    });
  }

  // Progreso oscuro→claro (0..1) escrito en --hdr-scroll; el CSS interpola
  // de ahí color, alto y logo. Va suavizado con un quickTo sobre un proxy:
  // un giro brusco de rueda salta 100px de golpe y sin esto la barra
  // cambiaría de un cuadro a otro. Con reduce-motion se escribe directo.
  const progress = { p: 0 };
  const write = (): void => {
    header.style.setProperty('--hdr-scroll', progress.p.toFixed(3));
  };
  const setProgress = prefersReducedMotion
    ? (value: number): void => { progress.p = value; write(); }
    : gsap.quickTo(progress, 'p', { duration: 0.45, ease: 'power2.out', onUpdate: write });
  const onScroll = (): void => {
    setProgress(Math.min(1, Math.max(0, window.scrollY / SCROLL_RANGE)));
  };
  progress.p = Math.min(1, Math.max(0, window.scrollY / SCROLL_RANGE));
  write();
  window.addEventListener('scroll', onScroll, { passive: true });

  const navPill = header.querySelector<HTMLElement>('.nav-pill');
  if (navPill) initNavIndicator(navPill, prefersReducedMotion);

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

/**
 * Pastilla que se desliza bajo el link de primer nivel con hover o foco:
 * aparece bajo el primero que se toca y desde ahí viaja (quickTo en x y
 * ancho) al siguiente; al salir de la fila se desvanece y la próxima vez
 * vuelve a aparecer en el sitio, sin viajar desde el último.
 *
 * Solo los links de primer nivel: los del submenú desplegable (.sub-menu)
 * también son `.nav-pill__list a`, pero viven en un panel vertical.
 */
function initNavIndicator(navPill: HTMLElement, reduced: boolean): void {
  const indicator = navPill.querySelector<HTMLElement>('.nav-pill__indicator');
  const list = navPill.querySelector<HTMLElement>('.nav-pill__list');
  if (!indicator || !list) return;

  const links = Array.from(list.querySelectorAll<HTMLElement>(':scope > li > a'));
  if (!links.length) return;

  const setX = gsap.quickTo(indicator, 'x', { duration: 0.35, ease: 'power3.out' });
  const setY = gsap.quickTo(indicator, 'y', { duration: 0.35, ease: 'power3.out' });
  const setW = gsap.quickTo(indicator, 'width', { duration: 0.35, ease: 'power3.out' });
  let parked = true; // true = oculta; la siguiente aparición no anima el viaje

  const show = (link: HTMLElement): void => {
    const pill = navPill.getBoundingClientRect();
    const rect = link.getBoundingClientRect();
    const x = rect.left - pill.left;
    const y = rect.top - pill.top;
    if (parked || reduced) {
      gsap.set(indicator, { x, y, width: rect.width });
      parked = false;
    } else {
      setX(x);
      setY(y);
      setW(rect.width);
    }
    gsap.to(indicator, { autoAlpha: 1, duration: 0.2, overwrite: 'auto' });
  };

  const hide = (): void => {
    gsap.to(indicator, {
      autoAlpha: 0,
      duration: 0.25,
      overwrite: 'auto',
      onComplete: () => { parked = true; },
    });
  };

  links.forEach((link) => {
    link.addEventListener('mouseenter', () => show(link));
    link.addEventListener('focus', () => show(link));
  });
  list.addEventListener('mouseleave', hide);
  list.addEventListener('focusout', (event) => {
    if (!list.contains(event.relatedTarget as Node | null)) hide();
  });
}
