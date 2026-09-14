/**
 * Escena "isla + producto flotante" del banner de catálogo.
 *
 * La flotación continua la resuelve `initFloat()` (mismo `[data-float]`/
 * `[data-float-shadow]` que usan las cards del home) — este módulo se ocupa
 * solo de lo propio de la escena:
 *
 *  1. Entrada al cargar (pedestal → producto activo).
 *  2. Parallax de mouse sobre el WRAPPER del producto — no sobre el <img>,
 *     que ya trae su propio transform de la flotación; son dos tweens de
 *     GSAP en elementos distintos para que no se pisen (mismo criterio que
 *     las hojas de #productos en el home).
 *  3. Product switching: goTo(i) es la ÚNICA puerta de entrada — cards,
 *     dots, flechas, autoplay y swipe táctil la llaman directo con un
 *     índice de producto. El Embla del strip de cards solo "sigue" al
 *     producto activo (syncEmbla) y aporta el deslizar con el dedo/mouse
 *     sobre el strip; nunca es la fuente de verdad. Antes las flechas
 *     movían Embla y esperaban su `select`: con containScroll trimSnaps
 *     hay menos snaps que cards (el último agrupa varias), así que el
 *     último producto era inalcanzable con las flechas y en mobile —donde
 *     el strip está display:none— Embla ni siquiera tiene medidas.
 *     Un goTo() durante la transición no se descarta: queda pendiente y se
 *     ejecuta al terminar (clicks rápidos en la flecha ya no se pierden).
 */

import { gsap } from '../lib/gsap';
import EmblaCarousel, { type EmblaCarouselType } from 'embla-carousel';

interface CatalogoSlide {
  title: string;
  desc: string;
  eyebrow: string;
  img: string;
  thumb?: string;
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

  const dotEls = Array.from(root.querySelectorAll<HTMLElement>('[data-catalogo-dot]'));
  const progressBarEl = root.querySelector<HTMLElement>('[data-catalogo-progress-bar]');
  const prevBtn = root.querySelector<HTMLButtonElement>('[data-catalogo-slider-prev]');
  const nextBtn = root.querySelector<HTMLButtonElement>('[data-catalogo-slider-next]');
  const textEls = [eyebrowEl, titleEl, descEl].filter((el): el is HTMLElement => el !== null);

  let activeIndex = 0;
  let switching = false;
  let pendingIndex: number | null = null;

  const wrap = (index: number): number => ((index % slides.length) + slides.length) % slides.length;

  // Las fotos 'large' de los otros productos se pedían recién al cambiar:
  // el fade-in arrancaba con el <img> todavía vacío. Se precalientan en
  // idle (después del LCP), así el swap ya las tiene en caché.
  const preload = (): void => {
    slides.forEach((slide) => {
      const img = new Image();
      img.decoding = 'async';
      img.src = slide.img;
    });
  };
  if (typeof window.requestIdleCallback === 'function') {
    window.requestIdleCallback(preload, { timeout: 3000 });
  } else {
    window.setTimeout(preload, 1500);
  }

  // Embla del strip de cards. En mobile el strip está display:none (sus
  // slides miden 0) y con trimSnaps un snap puede agrupar varias cards:
  // por eso se traduce índice de producto → snap que lo contiene, y se
  // marca `emblaSyncing` para que el `select` que dispara scrollTo() no
  // vuelva a entrar en goTo().
  let embla: EmblaCarouselType | null = null;
  let emblaSyncing = false;

  const syncEmbla = (index: number): void => {
    if (!embla) return;
    const registry = embla.internalEngine().slideRegistry;
    const snap = registry.findIndex((group) => group.includes(index));
    if (snap < 0 || snap === embla.selectedScrollSnap()) return;
    emblaSyncing = true;
    embla.scrollTo(snap);
    emblaSyncing = false;
  };

  const speed = prefersReducedMotion ? 0 : 1;

  const goTo = (nextIndex: number): void => {
    const resolvedIndex = wrap(nextIndex);
    if (switching) {
      pendingIndex = resolvedIndex;
      return;
    }
    if (resolvedIndex === activeIndex) return;
    switching = true;
    activeIndex = resolvedIndex;
    const slide = slides[activeIndex];

    slideEls.forEach((el, i) => el.classList.toggle('is-active', i === activeIndex));
    dotEls.forEach((el, i) => el.classList.toggle('is-active', i === activeIndex));
    if (progressBarEl) {
      progressBarEl.style.width = `${((activeIndex + 1) / slides.length) * 100}%`;
    }
    syncEmbla(activeIndex);

    const tl = gsap.timeline({
      defaults: { ease: 'power2.inOut' },
      onComplete: () => {
        switching = false;
        if (pendingIndex !== null) {
          const next = pendingIndex;
          pendingIndex = null;
          goTo(next);
        }
      },
    });

    tl.to(mainWrap, { autoAlpha: 0, scale: 0.85, y: 30, duration: 0.35 * speed, ease: 'power2.in' }, 0);
    if (textEls.length) tl.to(textEls, { autoAlpha: 0, y: -10, duration: 0.25 * speed, ease: 'power2.in' }, 0);

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

    tl.to(mainWrap, { autoAlpha: 1, scale: 1, y: 0, duration: 0.6 * speed, ease: 'back.out(1.6)' });
    if (textEls.length) tl.to(textEls, { autoAlpha: 1, y: 0, duration: 0.4 * speed }, '<');
  };

  if (sliderViewport && slideEls.length > 1) {
    const instance = EmblaCarousel(sliderViewport, { align: 'start', containScroll: 'trimSnaps', loop: true });
    embla = instance;
    // Deslizar el strip con el dedo/mouse: primera card del snap alcanzado.
    instance.on('select', () => {
      if (emblaSyncing) return;
      const group = instance.internalEngine().slideRegistry[instance.selectedScrollSnap()];
      if (group?.length) goTo(group[0]);
    });
  }

  slideEls.forEach((el, i) => el.addEventListener('click', () => goTo(i)));
  dotEls.forEach((dot, i) => dot.addEventListener('click', () => goTo(i)));
  // Circular: con 2+ productos nunca hay extremo muerto, así que las
  // flechas no se deshabilitan.
  prevBtn?.addEventListener('click', () => goTo(activeIndex - 1));
  nextBtn?.addEventListener('click', () => goTo(activeIndex + 1));

  // ---------- Swipe táctil sobre la escena (mobile) ----------
  // En desktop la escena tiene pointer-events:none (no tapa el texto); en
  // mobile el CSS la habilita con touch-action:pan-y, así el scroll vertical
  // sigue siendo del navegador y solo el gesto horizontal cambia de producto.

  if (scene) {
    let startX = 0;
    let startY = 0;
    let tracking = false;

    scene.addEventListener('pointerdown', (e) => {
      if (e.pointerType === 'mouse') return;
      tracking = true;
      startX = e.clientX;
      startY = e.clientY;
    }, { passive: true });

    scene.addEventListener('pointerup', (e) => {
      if (!tracking) return;
      tracking = false;
      const dx = e.clientX - startX;
      const dy = e.clientY - startY;
      if (Math.abs(dx) < 40 || Math.abs(dx) < Math.abs(dy) * 1.5) return;
      goTo(activeIndex + (dx < 0 ? 1 : -1));
    });

    scene.addEventListener('pointercancel', () => {
      tracking = false;
    });
  }

  // ---------- Autoplay ----------
  // Avanza cada AUTOPLAY_MS. Se detiene mientras el mouse está sobre el
  // banner, mientras hay un dedo apoyado, con la pestaña oculta o con el
  // banner fuera de viewport (si no, seguía cargando fotos 'large' mientras
  // el usuario ya estaba abajo en la grilla). Va por pointer events con
  // pointerType: con mouseenter/mouseleave, un tap en móvil disparaba el
  // mouseenter sintético (sin mouseleave después) y el autoplay moría en
  // el primer toque. Respeta prefers-reduced-motion: no arranca.

  if (!prefersReducedMotion) {
    const AUTOPLAY_MS = 4000;
    let timer: ReturnType<typeof setInterval> | null = null;
    let hovering = false;
    let pressing = false;
    let inView = true;

    const stop = (): void => {
      if (timer === null) return;
      clearInterval(timer);
      timer = null;
    };

    const update = (): void => {
      const shouldRun = inView && !document.hidden && !hovering && !pressing;
      if (shouldRun && timer === null) {
        timer = setInterval(() => goTo(activeIndex + 1), AUTOPLAY_MS);
      } else if (!shouldRun) {
        stop();
      }
    };

    // Tras elegir a mano, el siguiente tick vuelve a contar desde cero
    // ("recién elegiste — espera un poco").
    const restart = (): void => {
      stop();
      update();
    };

    root.addEventListener('pointerenter', (e) => {
      if (e.pointerType !== 'mouse') return;
      hovering = true;
      update();
    });
    root.addEventListener('pointerleave', (e) => {
      if (e.pointerType !== 'mouse') return;
      hovering = false;
      update();
    });
    root.addEventListener('pointerdown', (e) => {
      if (e.pointerType === 'mouse') return;
      pressing = true;
      update();
    }, { passive: true });
    const release = (e: PointerEvent): void => {
      if (e.pointerType === 'mouse') return;
      pressing = false;
      restart();
    };
    root.addEventListener('pointerup', release);
    root.addEventListener('pointercancel', release);

    document.addEventListener('visibilitychange', update);

    if (typeof IntersectionObserver === 'function') {
      new IntersectionObserver(
        (entries) => {
          inView = entries.some((entry) => entry.isIntersecting);
          update();
        },
        { threshold: 0.2 }
      ).observe(root);
    }

    [prevBtn, nextBtn, ...slideEls, ...dotEls].forEach((el) => el?.addEventListener('click', restart));

    update();
  }
}
