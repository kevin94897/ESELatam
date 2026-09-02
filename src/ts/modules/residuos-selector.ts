/**
 * Selector de servicios de "Residuos Inteligentes" (Educar/Segregar/
 * Transformar): un clic en una tarjeta la activa (las demás se desactivan,
 * ver estilos `.residuos__tab.is-active` en main.css) y el panel de la
 * derecha (foto + título + descripción) hace un crossfade con GSAP hacia el
 * contenido de esa tarjeta, leído de sus `data-*`.
 */

import { gsap } from '../lib/gsap';

export function initResiduosSelector(): void {
  const root = document.querySelector<HTMLElement>('[data-service-selector]');
  if (!root) return;

  const tabs = Array.from(root.querySelectorAll<HTMLButtonElement>('[data-service-tab]'));
  const media = root.querySelector<HTMLElement>('[data-service-media]');
  const img = root.querySelector<HTMLImageElement>('[data-service-img]');
  const titleEl = root.querySelector<HTMLElement>('[data-service-title]');
  const descEl = root.querySelector<HTMLElement>('[data-service-desc]');
  if (!tabs.length || !media || !img || !titleEl || !descEl) return;

  const panelEls = [media, titleEl, descEl];

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      if (tab.classList.contains('is-active')) return;

      tabs.forEach((t) => t.classList.toggle('is-active', t === tab));

      const { title = '', desc = '', img: nextImg = '' } = tab.dataset;

      gsap.to(panelEls, {
        autoAlpha: 0,
        y: 12,
        duration: 0.25,
        ease: 'power2.in',
        overwrite: true,
        onComplete: () => {
          img.src = nextImg;
          titleEl.textContent = title;
          descEl.textContent = desc;
          gsap.fromTo(panelEls,
            { autoAlpha: 0, y: 12 },
            { autoAlpha: 1, y: 0, duration: 0.4, ease: 'power2.out', stagger: 0.05, clearProps: 'transform' },
          );
        },
      });
    });
  });
}
