/**
 * Configuración central de GSAP.
 * Registra plugins una sola vez y expone la instancia lista para usar.
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { MorphSVGPlugin } from 'gsap/MorphSVGPlugin';
import { SplitText } from 'gsap/SplitText';

let registered = false;

export function initGsap(): void {
  if (registered) return;

  gsap.registerPlugin(ScrollTrigger, MorphSVGPlugin, SplitText);

  gsap.defaults({
    ease: 'power2.out',
    duration: 0.8,
  });

  // Si el usuario prefiere reducir movimiento, desactivamos animaciones
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReducedMotion) {
    gsap.globalTimeline.timeScale(0);
  }

  registered = true;
}

export { gsap, ScrollTrigger, MorphSVGPlugin, SplitText };
