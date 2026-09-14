/**
 * Pasos de "Entendemos tu operación" (template-parts/sectores-proceso.php).
 *
 * Mismo patrón que residuos-selector.ts: un clic en un paso lo activa y la
 * cabecera de la tarjeta (número, titular en dos renglones, bajada) hace un
 * crossfade hacia el contenido leído de sus `data-*`; si el paso trae foto,
 * el fondo también cambia con fundido.
 *
 * `data-proceso-autoplay="<ms>"` en la raíz hace que los pasos roten solos.
 * El avance ES un tween de GSAP sobre `--tab-progress` (0→1) en el paso
 * activo — su barra inferior (.proceso__step-bar) lo lee como scaleX — y
 * solo corre mientras la sección está en pantalla, sin hover/foco y con la
 * pestaña visible. Con prefers-reduced-motion no hay autoplay ni fundidos:
 * el clic sigue cambiando el contenido, de golpe.
 *
 * No se reutiliza residuos-selector.ts porque aquel escribe el título como
 * texto plano y acá el titular tiene dos tramos (light + bold) que deben
 * seguir siendo dos elementos.
 */

import { gsap } from '../lib/gsap';

const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initProcesoSteps(root: HTMLElement): void {
  const steps = Array.from(root.querySelectorAll<HTMLButtonElement>('[data-proceso-step]'));
  const panel = root.querySelector<HTMLElement>('[data-proceso-panel]');
  const numEl = root.querySelector<HTMLElement>('[data-proceso-num]');
  const lightEl = root.querySelector<HTMLElement>('[data-proceso-light]');
  const boldEl = root.querySelector<HTMLElement>('[data-proceso-bold]');
  const descEl = root.querySelector<HTMLElement>('[data-proceso-desc]');
  const bg = root.querySelector<HTMLElement>('[data-proceso-bg]');
  const img = root.querySelector<HTMLImageElement>('[data-proceso-img]');
  if (!steps.length || !panel || !numEl || !lightEl || !boldEl || !descEl) return;

  const interval = Number(root.dataset.procesoAutoplay);
  const autoplay = interval > 0 && !REDUCED;

  let hovered = false;
  let inView = false;
  let progressTween: gsap.core.Tween | undefined;

  const eligible = (): boolean => inView && !hovered && !document.hidden;

  const sync = (): void => {
    if (!progressTween) return;
    if (eligible()) progressTween.play();
    else progressTween.pause();
  };

  const startProgress = (step: HTMLButtonElement): void => {
    progressTween?.kill();
    progressTween = undefined;
    if (!autoplay) return;
    progressTween = gsap.fromTo(
      step,
      { '--tab-progress': 0 },
      {
        '--tab-progress': 1,
        duration: interval / 1000,
        ease: 'none',
        onComplete: () => activate(steps[(steps.indexOf(step) + 1) % steps.length]),
      }
    );
    if (!eligible()) progressTween.pause();
  };

  const write = (step: HTMLButtonElement): void => {
    numEl.textContent = step.dataset.num ?? '';
    lightEl.textContent = step.dataset.light ?? '';
    boldEl.textContent = step.dataset.bold ?? '';
    descEl.textContent = step.dataset.desc ?? '';
  };

  const activate = (step: HTMLButtonElement): void => {
    if (step.classList.contains('is-active')) return;

    steps.forEach((s) => {
      const active = s === step;
      s.classList.toggle('is-active', active);
      s.setAttribute('aria-selected', String(active));
      if (!active) gsap.set(s, { '--tab-progress': 0 });
    });
    startProgress(step);

    const nextImg = step.dataset.img ?? '';
    const swapImg = Boolean(img && nextImg && img.getAttribute('src') !== nextImg);

    if (REDUCED) {
      write(step);
      if (swapImg && img) img.setAttribute('src', nextImg);
      return;
    }

    gsap.to(panel, {
      autoAlpha: 0,
      y: 12,
      duration: 0.25,
      ease: 'power2.in',
      overwrite: true,
      onComplete: () => {
        write(step);
        gsap.fromTo(panel, { autoAlpha: 0, y: 12 }, { autoAlpha: 1, y: 0, duration: 0.45, ease: 'power2.out', clearProps: 'transform' });
      },
    });

    if (swapImg && bg && img) {
      gsap.to(bg, {
        autoAlpha: 0,
        duration: 0.4,
        ease: 'power2.in',
        overwrite: true,
        onComplete: () => {
          img.setAttribute('src', nextImg);
          gsap.to(bg, { autoAlpha: 1, duration: 0.8, ease: 'power2.out' });
        },
      });
    }
  };

  steps.forEach((step) => {
    step.addEventListener('click', () => {
      // Clic sobre el paso ya activo: reinicia su cuenta en vez de dejarla
      // donde iba (mismo criterio que residuos-selector.ts).
      const wasActive = step.classList.contains('is-active');
      activate(step);
      if (wasActive) startProgress(step);
    });
  });

  if (!autoplay) return;

  startProgress(steps.find((s) => s.classList.contains('is-active')) ?? steps[0]);

  new IntersectionObserver((entries) => {
    inView = entries.some((entry) => entry.isIntersecting);
    sync();
  }, { threshold: 0.25 }).observe(root);

  // En táctil un tap dispara mouseenter sin mouseleave garantizado después:
  // `hovered` quedaría en true para siempre y el autoplay no volvería.
  if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    root.addEventListener('mouseenter', () => { hovered = true; sync(); });
    root.addEventListener('mouseleave', () => { hovered = false; sync(); });
  }
  root.addEventListener('focusin', () => { hovered = true; sync(); });
  root.addEventListener('focusout', () => { hovered = false; sync(); });
  document.addEventListener('visibilitychange', sync);
}
