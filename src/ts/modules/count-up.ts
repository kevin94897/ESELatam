/**
 * Contadores: `[data-count-to="500"]` cuenta desde 0 hasta ese número la
 * primera vez que entra en viewport. `data-count-suffix="+"` agrega un
 * sufijo fijo (500+). El texto inicial del HTML ya es el valor final, así
 * que sin JS o con reduce-motion se ve el número de una.
 */

import { gsap } from '../lib/gsap';

export function initCountUp(): void {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  document.querySelectorAll<HTMLElement>('[data-count-to]').forEach((el) => {
    const target = Number(el.dataset.countTo);
    if (!Number.isFinite(target)) return;
    const suffix = el.dataset.countSuffix ?? '';
    const proxy = { value: 0 };

    gsap.to(proxy, {
      value: target,
      duration: 1.6,
      ease: 'power2.out',
      onUpdate: () => {
        el.textContent = String(Math.round(proxy.value)) + suffix;
      },
      scrollTrigger: {
        trigger: el,
        start: 'top 85%',
        toggleActions: 'play none none none',
      },
    });
  });
}
