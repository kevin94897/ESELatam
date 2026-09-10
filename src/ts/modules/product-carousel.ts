/**
 * Carrusel de productos con efecto coverflow (Swiper vanilla).
 *
 * Foco central con profundidad 3D continua: los slides se alejan y encogen
 * de forma fluida mientras se arrastra (no por escalones), con loop,
 * autoplay suave, navegación con nuestras flechas, teclado y paginación
 * clickeable.
 *
 * Varias instancias por página, cada una con sus parámetros en `data-*`
 * sobre el propio `.swiper` (los valores por defecto son los del slider de
 * la home, Figma 3287-243: rotate 0, stretch 0, depth 100, modifier 2.5):
 *
 *   data-carousel-visible   slides al frente (impar; 5 por defecto)
 *   data-carousel-rotate    grados de giro (rotateY) de la primera vecina
 *   data-carousel-depth     px que retrocede (translateZ) la primera vecina
 *   data-carousel-modifier  multiplicador de ambos por paso de distancia
 *   data-carousel-loop      "false" desactiva el loop (pocos slides)
 *
 * Con rotate 44 / modifier 1 se obtiene el coverflow "inclinado" clásico
 * (cada vecina gira 44° y retrocede `depth`px): es el mismo lenguaje que el
 * componente React de referencia de "Soluciones recomendadas"
 * (rotate 44, depth 0.6·ancho, fade por distancia), resuelto con la
 * dependencia que ya tenemos en vez de sumar React. La única diferencia es
 * que Swiper aplica giro y retroceso de forma lineal con la distancia (la
 * referencia los suaviza con un exponente 0.56); a dos pasos del centro las
 * cards ya van desvanecidas (is-edge), así que no se nota.
 *
 * Flechas y paginación se buscan dentro de la raíz `[data-carousel-root]`
 * más cercana (o `.productos`, la raíz histórica de la home), así dos
 * carruseles en la misma página no se pisan los controles.
 *
 * Solo quedan al frente los slides cercanos al centro (`data-carousel-visible`).
 * El resto sigue en el DOM y en el loop, apenas oculto: el coverflow reparte
 * TODOS los slides en abanico y, con muchos productos, los lejanos se apilan
 * unos sobre otros en los bordes.
 */

import Swiper from 'swiper';
import type { SwiperOptions } from 'swiper/types';
import { Autoplay, EffectCoverflow, Keyboard, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-coverflow';

/** watchSlidesProgress (que coverflow activa solo) escribe `progress` en cada slide. */
type ProgressSlide = HTMLElement & { progress?: number };

/** `data-*` numérico con fallback; un valor no numérico cae al default. */
function numberAttr(value: string | undefined, fallback: number): number {
  if (value === undefined || value === '') return fallback;
  const parsed = Number(value);
  return Number.isFinite(parsed) ? parsed : fallback;
}

function initCarousel(el: HTMLElement): void {
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  // Swiper implementa pauseOnMouseEnter con mouseenter/mouseleave reales: en
  // táctil un tap puede disparar el mouseenter sintético sin un mouseleave
  // que lo suelte después, dejando el autoplay pausado para siempre.
  const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  const root =
    el.closest<HTMLElement>('[data-carousel-root]') ??
    el.closest<HTMLElement>('.productos') ??
    document.body;

  const visible = numberAttr(el.dataset.carouselVisible, 5);
  const rotate = numberAttr(el.dataset.carouselRotate, 0);
  const depth = numberAttr(el.dataset.carouselDepth, 100);
  const modifier = numberAttr(el.dataset.carouselModifier, 2.5);
  const loop = el.dataset.carouselLoop !== 'false';

  const paginationEl =
    root.querySelector<HTMLElement>('[data-carousel-pagination]') ??
    root.querySelector<HTMLElement>('.productos__pagination');

  // Swiper (desde la v9) no clona slides para el loop: si hay menos slides
  // que los que caben a la vista (`slidesPerView: 'auto'` → unas 3 cards)
  // lo desactiva con un warning y la navegación queda muerta al llegar al
  // final. Es el caso real mientras el catálogo crece (hoy 3 productos), así
  // que el set se duplica hasta superar los visibles — copias marcadas y
  // ocultas a lectores de pantalla — y el anillo se cierra igual. Los
  // clones también reciben la flotación (initFloat corre después, ver
  // main.ts) y sus chips enlazan a la misma ficha que el original.
  const wrapper = el.querySelector<HTMLElement>('.swiper-wrapper');
  const realSlides = wrapper ? (Array.from(wrapper.children) as HTMLElement[]) : [];
  const realCount = realSlides.length;
  const minForLoop = visible + 1;
  let cloned = false;
  if (loop && wrapper && realCount >= 2 && realCount < minForLoop) {
    while (wrapper.children.length < minForLoop) {
      realSlides.forEach((slide) => {
        const copy = slide.cloneNode(true) as HTMLElement;
        copy.setAttribute('aria-hidden', 'true');
        copy.dataset.carouselClone = '';
        wrapper.appendChild(copy);
      });
    }
    cloned = true;
  }

  // Impar: uno al centro y (visible - 1) / 2 a cada lado — pero nunca más
  // vecinas que productos distintos haya: con 3 productos y 5 visibles el
  // mismo producto asomaría dos veces (una por lado), así que se muestran
  // solo 3 hasta que el catálogo alcance para los 5 del diseño.
  const reach = Math.min((visible - 1) / 2, Math.max(0, Math.floor((realCount - 1) / 2)));

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

  const options: SwiperOptions = {
    on: {
      // setTranslate cubre init, navegación, autoplay y arrastre; el fade lo
      // suaviza la transición que ya declara .swiper-slide de cada sección.
      setTranslate: updateVisible,
    },
    modules: [EffectCoverflow, Autoplay, Navigation, Pagination, Keyboard],
    effect: 'coverflow',
    grabCursor: true,
    centeredSlides: true,
    loop,
    slidesPerView: 'auto',
    // Un poco de aire entre slides: sin esto, la vecina se metía ~27px
    // debajo de la activa (el ancho de la card ya deja muy poco margen
    // dentro del propio .swiper) y el degradado de máscara (main.css) tenía
    // menos overlap que disimular.
    spaceBetween: 16,
    slideToClickedSlide: true,
    speed: 600,
    coverflowEffect: {
      rotate,
      stretch: 0,
      depth,
      modifier,
      slideShadows: false,
    },
    autoplay: prefersReducedMotion
      ? false
      : { delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: canHover },
    navigation: {
      prevEl: root.querySelector<HTMLElement>('[data-carousel-prev]'),
      nextEl: root.querySelector<HTMLElement>('[data-carousel-next]'),
    },
    // Arranca apagado: se enciende/apaga más abajo según el carrusel esté a
    // la vista. No se usa `onlyInViewport` de Swiper a propósito: en la v14
    // compara la posición en el DOCUMENTO (elementOffset suma scrollY) con
    // el alto de la VENTANA, así que cualquier carrusel más abajo del primer
    // viewport ignora las flechas del teclado en silencio.
    keyboard: { enabled: false, onlyInViewport: false },
  };

  // Sin contenedor de bullets no se registra la paginación: Swiper con
  // `el: null` no falla, pero tampoco hace falta cargarle el módulo.
  if (paginationEl) {
    options.pagination = { el: paginationEl, clickable: true };
  }

  // Con clones, Swiper pintaría un bullet por slide (copias incluidas) y
  // marcaría como activo el del índice del clon. Acá los bullets siguen
  // contando productos reales: se ocultan los sobrantes y el activo es el
  // producto (realIndex módulo reales). El click en un bullet ya llega al
  // original (slideToLoop(i) con i < reales), sin nada que interceptar.
  if (cloned && paginationEl) {
    const syncBullets = (swiper: Swiper): void => {
      const bullets = paginationEl.querySelectorAll<HTMLElement>('.swiper-pagination-bullet');
      const current = swiper.realIndex % realCount;
      bullets.forEach((bullet, index) => {
        bullet.style.display = index >= realCount ? 'none' : '';
        bullet.classList.toggle('swiper-pagination-bullet-active', index === current);
      });
    };
    options.on = { ...options.on, paginationRender: syncBullets, paginationUpdate: syncBullets };
  }

  const swiper = new Swiper(el, options);

  // Teclado solo con el carrusel a la vista (misma compuerta que el autoplay
  // de residuos-selector.ts): así dos carruseles en la misma página no se
  // pelean las flechas, y uno fuera de pantalla no se mueve a ciegas.
  if (typeof IntersectionObserver === 'function') {
    new IntersectionObserver(
      (entries) => {
        if (entries.some((entry) => entry.isIntersecting)) swiper.keyboard.enable();
        else swiper.keyboard.disable();
      },
      { threshold: 0.25 }
    ).observe(el);
  } else {
    swiper.keyboard.enable();
  }
}

export function initProductCarousel(): void {
  document.querySelectorAll<HTMLElement>('[data-product-carousel]').forEach(initCarousel);
}
