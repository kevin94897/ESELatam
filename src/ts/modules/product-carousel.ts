/**
 * Carrusel de productos con efecto coverflow (Swiper vanilla).
 *
 * Foco central con profundidad 3D continua: los slides se alejan y encogen
 * de forma fluida mientras se arrastra (no por escalones), con loop,
 * autoplay suave, navegación con nuestras flechas y paginación clickeable.
 * Parámetros del coverflow tomados de la referencia (rotate 0, stretch 0,
 * depth 100, modifier 2.5).
 */

import Swiper from 'swiper';
import { Autoplay, EffectCoverflow, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-coverflow';

export function initProductCarousel(): void {
  const el = document.querySelector<HTMLElement>('[data-product-carousel]');
  if (!el) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const root = el.closest<HTMLElement>('.productos') ?? document.body;

  new Swiper(el, {
    modules: [EffectCoverflow, Autoplay, Navigation, Pagination],
    effect: 'coverflow',
    grabCursor: true,
    centeredSlides: true,
    loop: true,
    slidesPerView: 'auto',
    slideToClickedSlide: true,
    speed: 600,
    coverflowEffect: {
      rotate: 0,
      stretch: 0,
      depth: 100,
      modifier: 2.5,
      slideShadows: false,
    },
    autoplay: prefersReducedMotion
      ? false
      : { delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: true },
    navigation: {
      prevEl: root.querySelector<HTMLElement>('[data-carousel-prev]'),
      nextEl: root.querySelector<HTMLElement>('[data-carousel-next]'),
    },
    pagination: {
      el: root.querySelector<HTMLElement>('.productos__pagination'),
      clickable: true,
    },
  });
}
