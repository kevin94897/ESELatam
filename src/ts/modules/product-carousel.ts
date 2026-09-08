/**
 * Carrusel de productos con efecto coverflow (Swiper vanilla).
 *
 * Foco central con profundidad 3D continua: los slides se alejan y encogen
 * de forma fluida mientras se arrastra (no por escalones), con loop,
 * autoplay suave, navegación con nuestras flechas y paginación clickeable.
 * Parámetros del coverflow tomados de la referencia (rotate 0, stretch 0,
 * depth 100, modifier 2.5).
 *
 * Solo quedan al frente los slides cercanos al centro (`data-carousel-visible`,
 * 5 por defecto). El resto sigue en el DOM y en el loop, apenas oculto: el
 * coverflow reparte TODOS los slides en abanico y, con muchos productos, los
 * lejanos se apilan unos sobre otros en los bordes.
 */

import Swiper from 'swiper';
import { Autoplay, EffectCoverflow, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-coverflow';

/** watchSlidesProgress (que coverflow activa solo) escribe `progress` en cada slide. */
type ProgressSlide = HTMLElement & { progress?: number };

export function initProductCarousel(): void {
  const el = document.querySelector<HTMLElement>('[data-product-carousel]');
  if (!el) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  // Swiper implementa pauseOnMouseEnter con mouseenter/mouseleave reales: en
  // táctil un tap puede disparar el mouseenter sintético sin un mouseleave
  // que lo suelte después, dejando el autoplay pausado para siempre.
  const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  const root = el.closest<HTMLElement>('.productos') ?? document.body;

  // Impar: uno al centro y (visible - 1) / 2 a cada lado.
  const visible = Number(el.dataset.carouselVisible) || 5;
  const reach = (visible - 1) / 2;

  // `progress` es 0 en el slide activo y ±1, ±2… según se aleja del centro
  // (fraccionario mientras se arrastra), así que redondearlo da la distancia
  // en pasos. Los valores de opacidad de cada nivel viven en main.css, junto
  // a los de .swiper-slide / -active; acá solo se marca en qué nivel cae.
  const updateVisible = (swiper: Swiper): void => {
    swiper.slides.forEach((slide) => {
      const step = Math.round(Math.abs((slide as ProgressSlide).progress ?? 0));
      // step > 0 evita que con visible=1 el propio activo quede de "extremo".
      slide.classList.toggle('is-edge', step > 0 && step === reach);
      slide.classList.toggle('is-hidden', step > reach);
    });
  };

  new Swiper(el, {
    on: {
      // setTranslate cubre init, navegación, autoplay y arrastre; el fade lo
      // suaviza la transición que ya declara .productos .swiper-slide.
      setTranslate: updateVisible,
    },
    modules: [EffectCoverflow, Autoplay, Navigation, Pagination],
    effect: 'coverflow',
    grabCursor: true,
    centeredSlides: true,
    loop: true,
    slidesPerView: 'auto',
    // Un poco de aire entre slides: sin esto, la vecina se metía ~27px
    // debajo de la activa (el ancho de la card ya deja muy poco margen
    // dentro del propio .swiper) y el degradado de máscara (main.css) tenía
    // menos overlap que disimular.
    spaceBetween: 16,
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
      : { delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: canHover },
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
