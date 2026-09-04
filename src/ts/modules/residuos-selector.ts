/**
 * Selector de servicios de "Residuos Inteligentes" (Educar/Segregar/
 * Transformar): un clic en una tarjeta la activa (las demás se desactivan,
 * ver estilos `.residuos__tab.is-active` en main.css) y el panel de la
 * derecha (foto + título + descripción) hace un crossfade con GSAP hacia el
 * contenido de esa tarjeta, leído de sus `data-*`.
 *
 * `data-service-autoplay="<ms>"` en la raíz hace que las tabs se recorran
 * solas en bucle. Solo corre mientras la sección está en pantalla y se pausa
 * con hover/foco o si la pestaña del navegador queda oculta; un clic manual
 * reinicia la cuenta. Con prefers-reduced-motion no arranca (el clic sigue
 * funcionando igual).
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

  const activate = (tab: HTMLButtonElement): void => {
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
  };

  // ---------- Autoplay (opt-in vía data-service-autoplay) ----------

  const interval = Number(root.dataset.serviceAutoplay);
  const autoplay = Boolean(interval) && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  let timer: number | undefined;
  let hovered = false;
  let inView = false;

  const advance = (): void => {
    const current = tabs.findIndex((t) => t.classList.contains('is-active'));
    activate(tabs[(current + 1) % tabs.length]);
  };

  // Una sola compuerta para todas las razones de pausa, así no se solapan
  // varios setInterval ni queda uno vivo al salir de pantalla.
  const sync = (): void => {
    const shouldRun = autoplay && inView && !hovered && !document.hidden;
    if (shouldRun && timer === undefined) {
      timer = window.setInterval(advance, interval);
    } else if (!shouldRun && timer !== undefined) {
      clearInterval(timer);
      timer = undefined;
    }
  };

  const restart = (): void => {
    if (timer !== undefined) {
      clearInterval(timer);
      timer = undefined;
    }
    sync();
  };

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      activate(tab);
      // Tras elegir a mano, la siguiente tab espera el intervalo completo.
      restart();
    });
  });

  if (autoplay) {
    new IntersectionObserver((entries) => {
      inView = entries.some((entry) => entry.isIntersecting);
      sync();
    }, { threshold: 0.25 }).observe(root);

    root.addEventListener('mouseenter', () => { hovered = true; sync(); });
    root.addEventListener('mouseleave', () => { hovered = false; sync(); });
    root.addEventListener('focusin', () => { hovered = true; sync(); });
    root.addEventListener('focusout', () => { hovered = false; sync(); });
    document.addEventListener('visibilitychange', sync);
  }
}
