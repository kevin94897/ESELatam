/**
 * ESE Latam — Entry point
 *
 * Este archivo se carga en todas las plantillas. Módulos pesados como
 * Three.js se cargan de forma perezosa según lo que exista en la página.
 */

import '../css/main.css';
import { initGsap } from './lib/gsap';
import { initSmoothScroll } from './modules/smooth-scroll';
import { initScrollReveals } from './modules/scroll-reveals';
import { initSectionHeaders } from './modules/section-headers';
import { initHeader } from './modules/header';
import { initSliders } from './modules/slider';
import { initMarquees } from './modules/marquee';
import { initParallax } from './modules/parallax';
import { initProductCarousel } from './modules/product-carousel';
import { initFloat } from './modules/float';
import { initHeroCta } from './modules/hero-cta';
import { initContactoReveal } from './modules/contacto-reveal';
import { initResiduosSelector } from './modules/residuos-selector';

function bootstrap(): void {
  initGsap();
  initSmoothScroll();
  initHeader();
  initScrollReveals();
  initSectionHeaders();
  initSliders();
  initMarquees();
  initParallax();
  initProductCarousel();
  // Después del carrusel: si Swiper duplicó slides para el loop, sus copias ya
  // están en el DOM y también reciben la flotación.
  initFloat();
  initHeroCta();
  initContactoReveal();
  initResiduosSelector();

  // Hero con scroll-animación (solo front-page)
  const hero = document.querySelector<HTMLElement>('[data-hero]');
  if (hero) {
    void import('./modules/hero-scroll').then(({ initHeroScroll }) => {
      initHeroScroll(hero);
    });
  }

  // Escena "isla + productos flotantes" del banner de catálogo
  const productIsland = document.querySelector<HTMLElement>('[data-product-island]');
  if (productIsland) {
    void import('./modules/product-island').then(({ initProductIsland }) => {
      initProductIsland(productIsland);
    });
  }

  // Panel de configuración de la ficha de producto (single-producto.php)
  const productConfig = document.querySelector<HTMLElement>('[data-product-config]');
  if (productConfig) {
    void import('./modules/product-config').then(({ initProductConfig }) => {
      initProductConfig(productConfig);
    });
  }

  // Selector de "Pruebas de rigurosidad" (single-producto.php)
  if (document.querySelector('[data-pruebas]')) {
    void import('./modules/pruebas-selector').then(({ initPruebasSelector }) => {
      initPruebasSelector();
    });
  }

  // Lazy-load Three.js solo si la página tiene un canvas 3D
  const has3d = document.querySelector<HTMLElement>('[data-three-scene]');
  if (has3d) {
    void import('./modules/three-scene').then(({ initThreeScene }) => {
      initThreeScene(has3d);
    });
  }

  // Distribuidores: globo 3D (Three.js + texturas, ~1.1MB) — la sección está
  // varias pantallas abajo del fold, así que se difiere su import hasta que
  // esté por entrar en viewport. Sin esto compite por ancho de banda con el
  // contenido crítico del hero desde el primer instante de la carga.
  const globeSection = document.querySelector<HTMLElement>('[data-globe]');
  if (globeSection) {
    if (typeof IntersectionObserver === 'function') {
      const observer = new IntersectionObserver(
        (entries, obs) => {
          if (entries.some((entry) => entry.isIntersecting)) {
            obs.disconnect();
            void import('./modules/distribuidores').then(({ initDistribuidores }) => {
              initDistribuidores(globeSection);
            });
          }
        },
        { rootMargin: '200px' }
      );
      observer.observe(globeSection);
    } else {
      void import('./modules/distribuidores').then(({ initDistribuidores }) => {
        initDistribuidores(globeSection);
      });
    }
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', bootstrap, { once: true });
} else {
  bootstrap();
}
