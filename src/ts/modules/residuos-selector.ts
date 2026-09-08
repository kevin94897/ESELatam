/**
 * Selector de servicios de "Residuos Inteligentes" (Educar/Segregar/
 * Transformar): un clic en una tarjeta la activa (las demás se desactivan,
 * ver estilos `.residuos__tab.is-active` en main.css) y el panel de la
 * derecha (foto + título + descripción) hace un crossfade con GSAP hacia el
 * contenido de esa tarjeta, leído de sus `data-*`.
 *
 * `data-service-autoplay="<ms>"` en la raíz hace que las tabs se recorran
 * solas en bucle. El propio avance ES un tween de GSAP sobre la custom
 * property `--tab-progress` (0→100 en `interval` ms) — no un setInterval
 * aparte: así el anillo de progreso de `.residuos__tab.is-active::after`
 * (main.css) nunca se desincroniza del avance real, y pausar/reanudar es
 * solo `pause()`/`play()` sobre ese mismo tween. Solo corre mientras la
 * sección está en pantalla y se pausa con hover/foco o si la pestaña del
 * navegador queda oculta; un clic manual reinicia la cuenta. Con
 * prefers-reduced-motion no arranca (el clic sigue funcionando igual).
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

  // ---------- Autoplay (opt-in vía data-service-autoplay) ----------

  const interval = Number(root.dataset.serviceAutoplay);
  const autoplay = Boolean(interval) && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  let hovered = false;
  let inView = false;
  let progressTween: gsap.core.Tween | undefined;

  // El propio tween ES el temporizador: al llegar a 100 dispara el avance.
  // Matar el tween anterior (en vez de dejarlo terminar) es lo que hace que
  // un clic manual "reinicie la cuenta" en vez de sumarse a la que ya corría.
  const startProgress = (tab: HTMLButtonElement): void => {
    progressTween?.kill();
    if (!autoplay) return;
    progressTween = gsap.fromTo(
      tab,
      { '--tab-progress': 0 },
      {
        '--tab-progress': 100,
        duration: interval / 1000,
        ease: 'none',
        onComplete: () => {
          const current = tabs.indexOf(tab);
          activate(tabs[(current + 1) % tabs.length]);
        },
      }
    );
    // Arranca ya pausado si la sección todavía no calificaba para correr
    // (fuera de vista, con hover, con la pestaña oculta); sync() lo destraba.
    if (!(inView && !hovered && !document.hidden)) progressTween.pause();
  };

  const activate = (tab: HTMLButtonElement): void => {
    if (tab.classList.contains('is-active')) return;

    tabs.forEach((t) => t.classList.toggle('is-active', t === tab));
    startProgress(tab);

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

  // Una sola compuerta para todas las razones de pausa, así no queda el
  // tween corriendo de fondo con la sección fuera de vista o la pestaña oculta.
  const sync = (): void => {
    if (!progressTween) return;
    const shouldRun = autoplay && inView && !hovered && !document.hidden;
    if (shouldRun) progressTween.play();
    else progressTween.pause();
  };

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      // Clic sobre la tab ya activa: igual reinicia su propia cuenta en vez
      // de dejarla en el punto donde iba (mismo criterio que antes con
      // setInterval). Se mide ANTES de activate(): si la tab era nueva,
      // activate() ya llama a startProgress() por su cuenta — llamarlo de
      // nuevo acá lo mataría y reiniciaría dos veces por el mismo clic.
      const wasActive = tab.classList.contains('is-active');
      activate(tab);
      if (wasActive) startProgress(tab);
    });
  });

  if (autoplay) {
    startProgress(tabs[tabs.findIndex((t) => t.classList.contains('is-active'))] ?? tabs[0]);

    new IntersectionObserver((entries) => {
      inView = entries.some((entry) => entry.isIntersecting);
      sync();
    }, { threshold: 0.25 }).observe(root);

    // Igual que en slider.ts: en táctil un tap dispara mouseenter sin un
    // mouseleave garantizado después — `hovered` quedaba en true para
    // siempre y el autoplay no volvía a correr en esa sesión. El tap sobre
    // una tab ya reinicia su cuenta más arriba, así que no hace falta un
    // sustituto táctil para esta pausa "por intención de mouse".
    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
      root.addEventListener('mouseenter', () => { hovered = true; sync(); });
      root.addEventListener('mouseleave', () => { hovered = false; sync(); });
    }
    root.addEventListener('focusin', () => { hovered = true; sync(); });
    root.addEventListener('focusout', () => { hovered = false; sync(); });
    document.addEventListener('visibilitychange', sync);
  }
}
