/**
 * Sliders con Embla.
 *
 * Estructura esperada:
 *   <div data-embla>
 *     <div data-embla-viewport> <div class="embla__container"> slides… </div> </div>
 *     <button data-embla-prev> / <button data-embla-next>   (opcionales)
 *     <div data-embla-dots>                                  (opcional)
 *     <span data-embla-current> / <span data-embla-total>    (opcionales)
 *   </div>
 *
 * El slide seleccionado recibe la clase `is-active` (para estados visuales).
 */

import EmblaCarousel from 'embla-carousel';
import type { EmblaCarouselType } from 'embla-carousel';

function setupButtons(root: HTMLElement, embla: EmblaCarouselType): void {
  const prev = root.querySelector<HTMLButtonElement>('[data-embla-prev]');
  const next = root.querySelector<HTMLButtonElement>('[data-embla-next]');
  if (!prev && !next) return;

  prev?.addEventListener('click', () => embla.scrollPrev());
  next?.addEventListener('click', () => embla.scrollNext());

  const update = (): void => {
    if (prev) prev.disabled = !embla.canScrollPrev();
    if (next) next.disabled = !embla.canScrollNext();
  };

  embla.on('select', update).on('reInit', update);
  update();
}

function setupDots(root: HTMLElement, embla: EmblaCarouselType): void {
  const wrap = root.querySelector<HTMLElement>('[data-embla-dots]');
  if (!wrap) return;

  let dots: HTMLButtonElement[] = [];

  const build = (): void => {
    wrap.innerHTML = '';
    dots = embla.scrollSnapList().map((_, index) => {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'embla__dot';
      dot.setAttribute('aria-label', `Ir al slide ${index + 1}`);
      dot.addEventListener('click', () => embla.scrollTo(index));
      wrap.appendChild(dot);
      return dot;
    });
  };

  const update = (): void => {
    const selected = embla.selectedScrollSnap();
    dots.forEach((dot, index) => dot.classList.toggle('is-active', index === selected));
  };

  embla.on('reInit', () => { build(); update(); });
  embla.on('select', update);
  build();
  update();
}

function pad(n: number): string {
  return String(n).padStart(2, '0');
}

function setupCounter(root: HTMLElement, embla: EmblaCarouselType): void {
  const current = root.querySelector<HTMLElement>('[data-embla-current]');
  const total = root.querySelector<HTMLElement>('[data-embla-total]');
  if (!current && !total) return;

  const update = (): void => {
    if (current) current.textContent = pad(embla.selectedScrollSnap() + 1);
    if (total) total.textContent = '/' + pad(embla.scrollSnapList().length);
  };

  embla.on('select', update).on('reInit', update);
  update();
}

function setupActiveSlide(embla: EmblaCarouselType): void {
  const update = (): void => {
    const selected = embla.selectedScrollSnap();
    embla.slideNodes().forEach((slide, index) => {
      slide.classList.toggle('is-active', index === selected);
    });
  };

  embla.on('select', update).on('reInit', update);
  update();
}

/**
 * Autoplay opt-in vía `data-embla-autoplay="<ms>"` en la raíz. Avanza en
 * loop, se pausa con hover/foco/drag/pestaña oculta y respeta
 * prefers-reduced-motion. `data-embla-progress` (opcional) es la barra que
 * refleja el tiempo restante hasta el próximo avance (transform: scaleX,
 * animada a mano con rAF para poder pausarla/reanudarla sin saltos).
 */
function setupAutoplay(root: HTMLElement, embla: EmblaCarouselType): void {
  const interval = Number(root.dataset.emblaAutoplay);
  if (!interval) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const bar = root.querySelector<HTMLElement>('[data-embla-progress]');

  let rafId: number | null = null;
  let start: number | null = null;
  let elapsed = 0;
  let hovered = false;
  let pointerDown = false;

  const setProgress = (value: number): void => {
    if (bar) bar.style.transform = `scaleX(${value})`;
  };

  const tick = (timestamp: number): void => {
    if (start === null) start = timestamp - elapsed;
    elapsed = timestamp - start;
    const progress = Math.min(1, elapsed / interval);
    setProgress(progress);

    if (progress >= 1) {
      rafId = null;
      if (embla.canScrollNext()) embla.scrollNext();
      else embla.scrollTo(0);
      return;
    }
    rafId = requestAnimationFrame(tick);
  };

  const stop = (): void => {
    if (rafId !== null) {
      cancelAnimationFrame(rafId);
      rafId = null;
    }
  };

  const play = (): void => {
    if (rafId !== null || hovered || pointerDown || document.hidden) return;
    rafId = requestAnimationFrame(tick);
  };

  const reset = (): void => {
    stop();
    start = null;
    elapsed = 0;
    setProgress(0);
    play();
  };

  embla.on('select', reset);
  embla.on('pointerDown', () => { pointerDown = true; stop(); });
  embla.on('pointerUp', () => { pointerDown = false; play(); });

  root.addEventListener('mouseenter', () => { hovered = true; stop(); });
  root.addEventListener('mouseleave', () => { hovered = false; play(); });
  root.addEventListener('focusin', () => { hovered = true; stop(); });
  root.addEventListener('focusout', () => { hovered = false; play(); });

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) stop();
    else play();
  });

  reset();
}

export function initSliders(): void {
  document.querySelectorAll<HTMLElement>('[data-embla]').forEach((root) => {
    const viewport = root.querySelector<HTMLElement>('[data-embla-viewport]');
    if (!viewport) return;

    // data-embla-loop="true": carril infinito (vuelve al primer slide desde
    // el último y viceversa). containScroll no aplica con loop activo — Embla
    // lo ignora y avisa por consola si se manda igual, así que no se pasa.
    const loop = root.dataset.emblaLoop === 'true';

    // data-embla-contain="false": cada slide es un snap alcanzable (necesario
    // cuando el contador/estado activo debe llegar hasta el último slide).
    // duration algo mayor que el default (25): glide más fluido, menos brusco.
    //
    // Nota: el crecimiento de la card activa (sectores) ocurre en un elemento
    // position:absolute *dentro* del slide (ver .sector-card en main.css) — el
    // slide que Embla mide nunca cambia de tamaño, así que no hace falta tocar
    // watchResize ni forzar reInit: no hay resize que interrumpa el scroll.
    const embla = EmblaCarousel(viewport, {
      align: 'start',
      duration: 28,
      loop,
      ...(loop ? {} : { containScroll: root.dataset.emblaContain === 'false' ? false : 'trimSnaps' }),
    });

    setupButtons(root, embla);
    setupDots(root, embla);
    setupCounter(root, embla);
    setupActiveSlide(embla);
    setupAutoplay(root, embla);
  });
}
