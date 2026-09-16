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

  // Si el usuario prefiere reducir movimiento, las animaciones se vuelven
  // instantáneas: todo tween llega a su estado final en milisegundos.
  //
  // OJO: NO usar timeScale(0). Congelar el timeline global también anula
  // los gsap.set() (son tweens de duración 0 y con el timeline parado no
  // llegan a renderizar) con los que cada módulo deja visible el estado
  // final bajo reduce-motion — el hero de la home y los .nos-hero de las
  // internas quedaban con visibility:hidden para siempre. Los bucles
  // (float, marquee, parallax, scrubs) ya se saltan por su cuenta: cada
  // módulo consulta la media query antes de crearlos.
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReducedMotion) {
    gsap.globalTimeline.timeScale(100);
  }

  registered = true;
}

export { gsap, ScrollTrigger, MorphSVGPlugin, SplitText };
