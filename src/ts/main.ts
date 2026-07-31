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

function bootstrap(): void {
  initGsap();
  initSmoothScroll();
  initHeader();
  initScrollReveals();
  initSliders();

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
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', bootstrap, { once: true });
} else {
  bootstrap();
}
