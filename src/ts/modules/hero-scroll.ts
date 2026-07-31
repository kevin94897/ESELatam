/**
 * Hero con animación de scroll (front-page):
 *  - Primera pantalla: solo el titular centrado sobre el video (sin menú ni bottom).
 *  - Con el scroll, el titular viaja a su posición de layout (izquierda/arriba)
 *    y van entrando el menú, el lede/CTA/card y la isla flotante.
 *  - El video se reproduce scrubbed según el progreso (suavizado con lerp).
 *  - En el último viewport del pin, la sección siguiente (coronada por las
 *    nubes, ver .hero-next__clouds) se desliza por encima del hero.
 */

import { gsap, ScrollTrigger } from '../lib/gsap';

// Duración del pin en viewports. La sección siguiente (adelantada exactamente
// la altura del hero) entra deslizándose durante el ÚLTIMO viewport de scroll
// del pin: su frente son las nubes que la coronan, y llega al top justo al
// liberarse el pin. SWEEP_START = 1 - 1/PIN_VIEWPORTS.
const PIN_VIEWPORTS = 2.5;
const VIDEO_LERP = 0.12;

// Coreografía del pin (fracciones del progreso total)
const TITLE_TRAVEL_END = 0.16;                  // el titular llega a su posición
const ELEMENTS_IN = 0.14;                       // entran menú y hero__bottom
const ISLAND_IN = 0.2;                          // la isla emerge
const SWEEP_START = 1 - 1 / PIN_VIEWPORTS;      // 0.6 — la sección empieza a barrer
const VIDEO_END = SWEEP_START + 0.15;           // el video muere durante el barrido

export function initHeroScroll(section: HTMLElement): void {
  const video = section.querySelector<HTMLVideoElement>('[data-hero-video]');
  const island = section.querySelector<HTMLElement>('[data-hero-island]');
  const title = section.querySelector<HTMLElement>('.hero__title');
  const titleLines = section.querySelectorAll<HTMLElement>('[data-hero-line]');
  const revealEls = section.querySelectorAll<HTMLElement>('[data-hero-reveal]');
  const header = document.querySelector<HTMLElement>('[data-header]');

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (prefersReducedMotion) {
    // Sin animación: estado final legible, todo visible, video en su primer frame.
    const staticEls = [island, title, ...revealEls].filter((el): el is HTMLElement => el !== null);
    gsap.set(staticEls, { autoAlpha: 1 });
    video?.pause();
    return;
  }

  // ---------- Intro al cargar: titular centrado, líneas con máscara ----------

  // Desplazamiento del titular desde su posición de layout hasta el centro
  // del viewport. Se mide SIN transform aplicado (y se re-mide en cada
  // refresh para sobrevivir a resizes).
  let titleDx = 0;
  let titleDy = 0;

  const measureTitle = (): void => {
    if (!title) return;
    gsap.set(title, { x: 0, y: 0, scale: 1 });
    const r = title.getBoundingClientRect();
    titleDx = window.innerWidth / 2 - (r.left + r.width / 2);
    titleDy = window.innerHeight / 2 - (r.top + r.height / 2);
  };

  measureTitle();
  ScrollTrigger.addEventListener('refreshInit', measureTitle);

  if (title) {
    gsap.set(title, { x: titleDx, y: titleDy, scale: 1.08, autoAlpha: 1 });
  }

  const intro = gsap.timeline({ defaults: { ease: 'power3.out' }, delay: 0.15 });

  // El video asienta desde un ligero zoom
  intro.from(video ?? [], { scale: 1.12, duration: 2, ease: 'power2.out' }, 0);

  // Titular: cada línea sube desde detrás de su máscara
  intro.from(titleLines, { yPercent: 115, duration: 1.1, stagger: 0.14 }, 0.2);

  // Flotación continua de la isla (independiente del scroll)
  const islandImg = island?.querySelector('img');
  if (islandImg) {
    gsap.to(islandImg, {
      y: -18,
      duration: 2.8,
      ease: 'sine.inOut',
      yoyo: true,
      repeat: -1,
    });
  }

  // Solape de la sección siguiente: se adelanta EXACTAMENTE la altura real
  // del hero (no 100svh, que puede diferir con min-height, zoom o svh≠viewport)
  // para que llegue al top del viewport justo cuando el pin termina.
  // Solo se aplica con pin activo; sin JS o con reduce-motion, flujo normal.
  const next = document.querySelector<HTMLElement>('.hero-next');
  const setOverlap = (): void => {
    // minHeight = altura del hero: la sección debe cubrirlo por completo
    // (si fuera más corta, el hero asomaría entre la sección y el footer).
    if (next) gsap.set(next, { marginTop: -section.offsetHeight, minHeight: section.offsetHeight });
  };
  setOverlap();
  ScrollTrigger.addEventListener('refreshInit', setOverlap);

  let scrollProgress = 0;

  const tl = gsap.timeline({
    defaults: { ease: 'none' },
    scrollTrigger: {
      trigger: section,
      start: 'top top',
      // Distancia explícita en px de viewport (no % del trigger): así la
      // entrada de la sección siguiente coincide 1:1 con el fin del pin.
      end: () => '+=' + window.innerHeight * PIN_VIEWPORTS,
      scrub: true,
      pin: true,
      anticipatePin: 1,
      invalidateOnRefresh: true,
      onUpdate: (self) => {
        scrollProgress = self.progress;
      },
    },
  });

  // 1) El titular viaja del centro a su posición de layout
  // (from function-based: con invalidateOnRefresh se re-evalúa tras un resize)
  if (title) {
    tl.fromTo(
      title,
      { x: () => titleDx, y: () => titleDy, scale: 1.08 },
      { x: 0, y: 0, scale: 1, duration: TITLE_TRAVEL_END, ease: 'power1.inOut' },
      0
    );
  }

  // 2) El menú baja y entra
  if (header) {
    tl.fromTo(
      header,
      { yPercent: -120, autoAlpha: 0 },
      { yPercent: 0, autoAlpha: 1, duration: 0.08, ease: 'power1.out' },
      ELEMENTS_IN
    );
  }

  // 3) Lede, CTA y card entran escalonados
  if (revealEls.length) {
    tl.fromTo(
      revealEls,
      { y: 48, autoAlpha: 0 },
      { y: 0, autoAlpha: 1, duration: 0.12, stagger: 0.04, ease: 'power1.out' },
      ELEMENTS_IN + 0.02
    );
  }

  // 4) La isla emerge y queda asentada antes de que empiece el barrido
  if (island) {
    tl.fromTo(
      island,
      { yPercent: 55, autoAlpha: 0, scale: 0.85 },
      { yPercent: 0, autoAlpha: 1, scale: 1, duration: SWEEP_START - ISLAND_IN - 0.02, ease: 'power1.out' },
      ISLAND_IN
    );
  }

  // Relleno hasta 1.0: el scrub normaliza la duración total del timeline al
  // rango de scroll, así que sin esto los tweens se estirarían hasta el final.
  // El tramo SWEEP_START→1 es donde la sección siguiente barre el hero.
  tl.to({}, { duration: 1 - SWEEP_START }, SWEEP_START);

  // Video scrubbed: el currentTime persigue el progreso del scroll con lerp
  if (video) {
    video.pause();
    let current = 0;

    const scrubVideo = (): void => {
      const duration = video.duration;
      // No encolar un seek nuevo mientras el anterior sigue en curso
      if (!duration || Number.isNaN(duration) || video.seeking) return;

      // El video completa su recorrido durante el barrido de la sección
      const target = Math.min(1, scrollProgress / VIDEO_END) * duration * 0.999;
      current += (target - current) * VIDEO_LERP;

      if (Math.abs(video.currentTime - current) > 1 / 60) {
        video.currentTime = current;
      }
    };

    gsap.ticker.add(scrubVideo);

    ScrollTrigger.addEventListener('refreshInit', () => {
      // Mantiene el frame correcto tras un resize
      current = scrollProgress * (video.duration || 0);
    });
  }

  // El margen negativo de .hero-next cambió el layout: recalcula las
  // posiciones de los triggers creados antes (reveals de secciones).
  ScrollTrigger.refresh();
}
