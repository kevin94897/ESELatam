/**
 * Escena "isla + producto flotante" del banner de catálogo.
 *
 * La flotación continua la resuelve `initFloat()` (mismo `[data-float]`/
 * `[data-float-shadow]` que usan las cards del home) — este módulo se ocupa
 * solo de tres cosas propias de la escena:
 *
 *  1. Entrada al cargar (pedestal → producto activo).
 *  2. Parallax de mouse sobre el WRAPPER del producto — no sobre el <img>,
 *     que ya trae su propio transform de la flotación; son dos tweens de
 *     GSAP en elementos distintos para que no se pisen (mismo criterio que
 *     las hojas de #productos en el home).
 *  3. Product switching: el slider de cards (debajo del crumb, un producto
 *     real por card) salta al producto tocado o deslizado, con una
 *     transición de salida/entrada del producto activo y su texto asociado.
 *     Este módulo crea y controla su PROPIO Embla para ese slider — no pasa
 *     por el initSliders() genérico de slider.ts (la card no lleva
 *     data-embla) porque necesita enganchar su evento `select` al swap real
 *     de contenido, no solo al estado visual `is-active`.
 */

import { gsap } from '../lib/gsap';
import EmblaCarousel from 'embla-carousel';

interface CatalogoSlide {
  title: string;
  desc: string;
  eyebrow: string;
  img: string;
  href: string;
}

export function initProductIsland(root: HTMLElement): void {
  let slides: CatalogoSlide[] = [];
  try {
    slides = JSON.parse(root.dataset.slides ?? '[]');
  } catch {
    slides = [];
  }
  if (slides.length === 0) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  const scene = root.querySelector<HTMLElement>('.catalogo-banner__scene');
  const pedestal = root.querySelector<HTMLElement>('.catalogo-banner__pedestal');
  const mainWrap = root.querySelector<HTMLElement>('.catalogo-banner__product--main');
  const mainImg = mainWrap?.querySelector<HTMLImageElement>('[data-product-main-img]') ?? null;

  const eyebrowEl = root.querySelector<HTMLElement>('.catalogo-banner__eyebrow');
  const titleEl = root.querySelector<HTMLElement>('.catalogo-banner__title');
  const descEl = root.querySelector<HTMLElement>('.catalogo-banner__desc');
  const watermarkEl = root.querySelector<HTMLElement>('.catalogo-banner__watermark');
  const ctaEl = root.querySelector<HTMLAnchorElement>('.hero-cta');
  const counterEl = root.querySelector<HTMLElement>('[data-catalogo-counter]');
  const sliderRoot = root.querySelector<HTMLElement>('[data-catalogo-slider]');
  const sliderViewport = sliderRoot?.querySelector<HTMLElement>('[data-embla-viewport]') ?? null;
  const slideEls = Array.from(root.querySelectorAll<HTMLElement>('[data-catalogo-goto]'));

  // ---------- Entrada al cargar ----------

  if (prefersReducedMotion) {
    gsap.set([pedestal, mainWrap].filter(Boolean) as HTMLElement[], { autoAlpha: 1 });
  } else {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
    if (pedestal) tl.from(pedestal, { autoAlpha: 0, y: 30, duration: 0.8 }, 0);
    if (mainWrap) {
      tl.from(mainWrap, { autoAlpha: 0, y: 80, scale: 0.8, duration: 1, ease: 'back.out(1.4)' }, 0.25);
    }
  }

  // ---------- Parallax de mouse ----------

  if (!prefersReducedMotion && canHover && scene && mainWrap) {
    const x = gsap.quickTo(mainWrap, 'x', { duration: 0.6, ease: 'power3.out' });
    const y = gsap.quickTo(mainWrap, 'y', { duration: 0.6, ease: 'power3.out' });

    scene.addEventListener('pointermove', (e) => {
      const rect = scene.getBoundingClientRect();
      const nx = (e.clientX - rect.left) / rect.width - 0.5;
      const ny = (e.clientY - rect.top) / rect.height - 0.5;
      x(nx * 24);
      y(ny * 24);
    });

    scene.addEventListener('pointerleave', () => {
      x(0);
      y(0);
    });
  }

  // ---------- Tilt 3D en las cards del slider (mouse) ----------
  //
  // rotationX/rotationY son los nombres que usa GSAP para esas props de
  // transform (no rotateX/rotateY, el nombre literal de CSS) — quickTo()
  // da inercia suave al seguir el mouse, con GSAP (ya instalado), sin
  // sumar una librería de animación aparte.

  if (!prefersReducedMotion && canHover) {
    slideEls.forEach((slide) => {
      const frame = slide.querySelector<HTMLElement>('.catalogo-banner__shop-card-frame');
      if (!frame) return;

      const setRotateX = gsap.quickTo(frame, 'rotationX', { duration: 0.4, ease: 'power3.out' });
      const setRotateY = gsap.quickTo(frame, 'rotationY', { duration: 0.4, ease: 'power3.out' });

      frame.addEventListener('pointermove', (e) => {
        const rect = frame.getBoundingClientRect();
        const px = (e.clientX - rect.left) / rect.width;
        const py = (e.clientY - rect.top) / rect.height;
        setRotateX(-(py - 0.5) * 16);
        setRotateY((px - 0.5) * 16);
      });

      frame.addEventListener('pointerleave', () => {
        setRotateX(0);
        setRotateY(0);
      });
    });
  }

  // ---------- Product switching ----------

  if (slides.length <= 1 || !mainWrap || !mainImg) return;

  let activeIndex = 0;
  let switching = false;

  const goTo = (nextIndex: number): void => {
    if (switching) return;
    const resolvedIndex = ((nextIndex % slides.length) + slides.length) % slides.length;
    if (resolvedIndex === activeIndex) return;
    switching = true;
    activeIndex = resolvedIndex;
    const slide = slides[activeIndex];

    const dotEls = Array.from(root.querySelectorAll<HTMLElement>('[data-catalogo-dot]'));
    const progressBarEl = root.querySelector<HTMLElement>('[data-catalogo-progress-bar]');

    slideEls.forEach((el, i) => el.classList.toggle('is-active', i === activeIndex));
    dotEls.forEach((el, i) => el.classList.toggle('is-active', i === activeIndex));
    if (progressBarEl) {
      progressBarEl.style.width = `${((activeIndex + 1) / slides.length) * 100}%`;
    }

    const textEls = [eyebrowEl, titleEl, descEl].filter((el): el is HTMLElement => el !== null);

    const tl = gsap.timeline({
      defaults: { ease: 'power2.inOut' },
      onComplete: () => {
        switching = false;
      },
    });

    tl.to(mainWrap, { autoAlpha: 0, scale: 0.85, y: 30, duration: 0.35, ease: 'power2.in' }, 0);
    if (textEls.length) tl.to(textEls, { autoAlpha: 0, y: -10, duration: 0.25, ease: 'power2.in' }, 0);

    tl.call(() => {
      mainImg.src = slide.img;
      mainImg.alt = slide.title;
      if (eyebrowEl) eyebrowEl.textContent = slide.eyebrow;
      if (titleEl) titleEl.textContent = slide.title;
      if (descEl) descEl.textContent = slide.desc;
      if (watermarkEl) watermarkEl.textContent = slide.title;
      if (ctaEl) ctaEl.href = slide.href;
      if (counterEl) counterEl.textContent = String(activeIndex + 1).padStart(2, '0');
    });

    tl.to(mainWrap, { autoAlpha: 1, scale: 1, y: 0, duration: 0.6, ease: 'back.out(1.6)' });
    if (textEls.length) tl.to(textEls, { autoAlpha: 1, y: 0, duration: 0.4 }, '<');
  };

  // Slider de productos: Embla propio (no data-embla — ver comentario del
  // header) para poder enganchar `select` (deslizar) al mismo goTo() que
  // usa el click directo sobre una card.
  if (sliderViewport && slideEls.length > 1) {
    const embla = EmblaCarousel(sliderViewport, { align: 'start', containScroll: 'trimSnaps', loop: true });
    embla.on('select', () => goTo(embla.selectedScrollSnap()));
    slideEls.forEach((el, i) => {
      // scrollTo() por si la card no está del todo visible (la trae a
      // vista); goTo() directo porque, con pocas cards, todas caben en el
      // viewport del slider y Embla no tiene ningún punto de scroll real
      // que alcanzar — su propio evento `select` nunca llegaría a disparar
      // para ese índice. goTo() ya ignora llamadas redundantes (mismo
      // índice ya activo), así que no hay riesgo de duplicar el swap si
      // ambos (scrollTo → select, y este goTo directo) terminan coincidiendo.
      el.addEventListener('click', () => {
        embla.scrollTo(i);
        goTo(i);
      });
    });

    const dotEls = Array.from(root.querySelectorAll<HTMLElement>('[data-catalogo-dot]'));
    dotEls.forEach((dot, i) => {
      dot.addEventListener('click', () => {
        embla.scrollTo(i);
        goTo(i);
      });
    });

    // Mismas flechas que .sectores__controls (embla__arrow--solid), acá
    // debajo del slider — mismo patrón de habilitar/deshabilitar en los
    // extremos que setupButtons() en slider.ts, pero sobre este Embla propio.
    const prevBtn = root.querySelector<HTMLButtonElement>('[data-catalogo-slider-prev]');
    const nextBtn = root.querySelector<HTMLButtonElement>('[data-catalogo-slider-next]');
    prevBtn?.addEventListener('click', () => embla.scrollPrev());
    nextBtn?.addEventListener('click', () => embla.scrollNext());

    const updateArrows = (): void => {
      if (prevBtn) prevBtn.disabled = !embla.canScrollPrev();
      if (nextBtn) nextBtn.disabled = !embla.canScrollNext();
    };
    embla.on('select', updateArrows).on('reInit', updateArrows);
    updateArrows();

    // ---------- Autoplay ----------
    // Avanza al siguiente producto cada AUTOPLAY_MS ms.
    // Se pausa en cualquier interacción sobre la sección completa
    // (hover de mouse o inicio de touch) y se reanuda al salir.
    // Respeta prefers-reduced-motion: no arranca si está activo.

    if (!prefersReducedMotion) {
      const AUTOPLAY_MS = 4000;
      let timer: ReturnType<typeof setInterval> | null = null;

      const advance = (): void => {
        const next = ((activeIndex + 1) % slides.length);
        embla.scrollTo(next);
        goTo(next);
      };

      const start = (): void => {
        if (timer !== null) return;
        timer = setInterval(advance, AUTOPLAY_MS);
      };

      const stop = (): void => {
        if (timer === null) return;
        clearInterval(timer);
        timer = null;
      };

      // Pausa al interactuar con el banner completo.
      root.addEventListener('mouseenter', stop);
      root.addEventListener('touchstart', stop, { passive: true });

      // Reanuda al dejar el banner.
      root.addEventListener('mouseleave', start);
      root.addEventListener('touchend', start, { passive: true });

      // Pausa también cuando el usuario hace click en una flecha o card
      // (stop + restart da sensación de "recién elegiste — espera un poco").
      const restartOnInteraction = (): void => { stop(); start(); };
      prevBtn?.addEventListener('click', restartOnInteraction);
      nextBtn?.addEventListener('click', restartOnInteraction);
      slideEls.forEach((el) => el.addEventListener('click', restartOnInteraction));

      // Arranca.
      start();
    }
  }
}
