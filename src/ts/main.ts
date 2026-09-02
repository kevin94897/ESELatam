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
import { initHeader } from './modules/header';
import { initSliders } from './modules/slider';
import { initMarquees } from './modules/marquee';
import { initParallax } from './modules/parallax';
import { initProductCarousel } from './modules/product-carousel';
import { initHeroCta } from './modules/hero-cta';
import { initContactoReveal } from './modules/contacto-reveal';
import { initResiduosSelector } from './modules/residuos-selector';

function bootstrap(): void {
  initGsap();
  initSmoothScroll();
  initHeader();
  initScrollReveals();
  initSliders();
  initMarquees();
  initParallax();
  initProductCarousel();
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

  // Lazy-load Three.js solo si la página tiene un canvas 3D
  const has3d = document.querySelector<HTMLElement>('[data-three-scene]');
  if (has3d) {
    void import('./modules/three-scene').then(({ initThreeScene }) => {
      initThreeScene(has3d);
    });
  }

  // Distribuidores: globo 3D (Three.js + GSAP), solo si la sección existe
  const globeSection = document.querySelector<HTMLElement>('[data-globe]');
  if (globeSection) {
    void import('./modules/distribuidores').then(({ initDistribuidores }) => {
      initDistribuidores(globeSection);
    });
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', bootstrap, { once: true });
} else {
  bootstrap();
}
