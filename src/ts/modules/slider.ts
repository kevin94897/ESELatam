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

export function initSliders(): void {
  document.querySelectorAll<HTMLElement>('[data-embla]').forEach((root) => {
    const viewport = root.querySelector<HTMLElement>('[data-embla-viewport]');
    if (!viewport) return;

    // data-embla-contain="false": cada slide es un snap alcanzable (necesario
    // cuando el contador/estado activo debe llegar hasta el último slide).
    const embla = EmblaCarousel(viewport, {
      align: 'start',
      containScroll: root.dataset.emblaContain === 'false' ? false : 'trimSnaps',
    });

    setupButtons(root, embla);
    setupDots(root, embla);
    setupCounter(root, embla);
    setupActiveSlide(embla);
  });
}
