/**
 * Tabs genéricas con paneles, autoplay opcional y prev/next.
 *
 * Generaliza el patrón de residuos-selector.ts / proceso-steps.ts para los
 * bloques nuevos que solo necesitan "un botón activa un panel":
 *   · Desafíos (Solución por sector): 2 filtros → 2 grillas.
 *   · Certificaciones en detalle: 9 tiles → 9 fichas.
 *   · Valida certificados: 4 pasos con barra de progreso → 4 tarjetas.
 *   · Economía circular: 5 nodos del ciclo → 5 leyendas, con prev/next.
 *
 * Markup:
 *   <div data-tabs [data-tabs-autoplay="6000"]>
 *     … <button data-tab> ×N  (mismo orden que los paneles)
 *     … <div data-tab-panel> ×N
 *     … [<button data-tabs-prev>] [<button data-tabs-next>]
 *
 * Estados que pone el módulo (el CSS de cada bloque decide cómo se ven):
 *   · tab activa: `.is-active` + aria-selected
 *   · panel activo: `.is-active`; el siguiente: `.is-next` (para los
 *     "mazos" que muestran asomando la próxima tarjeta)
 *   · `--tab-progress` (0→1) en la tab activa mientras corre el autoplay.
 *
 * El panel entrante hace un pequeño fade+subida con GSAP; con
 * prefers-reduced-motion cambia de golpe y no hay autoplay.
 */

import { gsap } from '../lib/gsap';

const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initTabPanels(): void {
  document.querySelectorAll<HTMLElement>('[data-tabs]').forEach(setup);
}

function setup(root: HTMLElement): void {
  const tabs = Array.from(root.querySelectorAll<HTMLElement>('[data-tab]'));
  const panels = Array.from(root.querySelectorAll<HTMLElement>('[data-tab-panel]'));
  if (!tabs.length || !panels.length) return;

  const interval = Number(root.dataset.tabsAutoplay);
  const autoplay = interval > 0 && !REDUCED;

  let current = Math.max(0, tabs.findIndex((t) => t.classList.contains('is-active')));
  let hovered = false;
  let inView = false;
  let progressTween: gsap.core.Tween | undefined;

  const eligible = (): boolean => inView && !hovered && !document.hidden;

  const sync = (): void => {
    if (!progressTween) return;
    if (eligible()) progressTween.play();
    else progressTween.pause();
  };

  const startProgress = (index: number): void => {
    progressTween?.kill();
    progressTween = undefined;
    if (!autoplay) return;
    progressTween = gsap.fromTo(
      tabs[index],
      { '--tab-progress': 0 },
      {
        '--tab-progress': 1,
        duration: interval / 1000,
        ease: 'none',
        onComplete: () => activate((index + 1) % tabs.length),
      }
    );
    if (!eligible()) progressTween.pause();
  };

  const paint = (index: number): void => {
    tabs.forEach((tab, i) => {
      const active = i === index;
      tab.classList.toggle('is-active', active);
      tab.setAttribute('aria-selected', String(active));
      if (!active) gsap.set(tab, { '--tab-progress': 0 });
    });
    panels.forEach((panel, i) => {
      panel.classList.toggle('is-active', i === index);
      panel.classList.toggle('is-next', i === (index + 1) % panels.length && panels.length > 1);
    });
  };

  const activate = (index: number, animate = true): void => {
    if (index === current && animate) {
      // Clic sobre la activa: reinicia su cuenta (mismo criterio que
      // residuos-selector.ts).
      startProgress(index);
      return;
    }
    current = index;
    paint(index);
    startProgress(index);

    const panel = panels[index];
    if (!panel || REDUCED || !animate) return;
    gsap.fromTo(panel, { autoAlpha: 0, y: 14 }, { autoAlpha: 1, y: 0, duration: 0.5, ease: 'power2.out', overwrite: true, clearProps: 'opacity,visibility,transform' });
  };

  tabs.forEach((tab, i) => tab.addEventListener('click', () => activate(i)));
  root.querySelector<HTMLElement>('[data-tabs-prev]')?.addEventListener('click', () => activate((current - 1 + tabs.length) % tabs.length));
  root.querySelector<HTMLElement>('[data-tabs-next]')?.addEventListener('click', () => activate((current + 1) % tabs.length));

  // Estado inicial coherente aunque el HTML no lo trajera completo.
  paint(current);

  if (!autoplay) return;

  startProgress(current);

  new IntersectionObserver((entries) => {
    inView = entries.some((entry) => entry.isIntersecting);
    sync();
  }, { threshold: 0.25 }).observe(root);

  if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    root.addEventListener('mouseenter', () => { hovered = true; sync(); });
    root.addEventListener('mouseleave', () => { hovered = false; sync(); });
  }
  root.addEventListener('focusin', () => { hovered = true; sync(); });
  root.addEventListener('focusout', () => { hovered = false; sync(); });
  document.addEventListener('visibilitychange', sync);
}
