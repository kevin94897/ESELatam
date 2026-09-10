/**
 * Página "Nosotros" (page-nosotros.php, Figma 3441-370) — coreografía
 * propia de la página. Se importa perezosamente desde main.ts cuando
 * existe `[data-nosotros]`; los reveals genéricos del theme (data-reveal,
 * data-reveal-header, data-float, data-parallax, data-marquee) ya corrieron
 * antes, acá va solo lo que ninguno de esos módulos cubre:
 *
 *   · Hero: intro por líneas (SplitText + máscara), zoom lento de la foto,
 *     contador del "13 países", halo que sigue al cursor y salida
 *     parallax/fade scrubbed al scrollear.
 *   · Paneles `[data-nos-panel]`: cortina (clip-path) desde un lado + la
 *     foto de adentro asienta desde un zoom.
 *   · Construimos: entrada de la isla (sube, escala y endereza).
 *   · Objetivos: scroll-spy del aside sticky — el chevron viaja al ítem
 *     activo — más parallax y tilt 3D de cada panel.
 *   · Aliados: anillos que se expanden con el scroll, logos que entran en
 *     3D (rotateX) en cascada, isla con tilt que sigue al mouse.
 *   · Método: tabs ESG con autoplay (la barra verde ES el temporizador, un
 *     tween sobre --tab-progress) y crossfade de la foto.
 *   · HDPE: escena de pellets en canvas 2D (llueven, rebotan, se apilan y
 *     se reciclan; el cursor los aparta), chips en cascada y el ícono de
 *     circularidad girando scrubbed con el scroll.
 *
 * Con prefers-reduced-motion todo queda estático y visible (las tabs
 * siguen funcionando con clic, sin autoplay).
 */

import { gsap, ScrollTrigger, SplitText } from '../lib/gsap';

const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const FINE_POINTER = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

// Mismo criterio que scroll-reveals.ts: sin `once` (el auto-kill en pleno
// refresh encoge el array de triggers) — toggleActions anima igual una vez.
const ONCE = { toggleActions: 'play none none none' } as const;

export function initNosotros(root: HTMLElement): void {
  initHero(root);
  initPanels(root);
  initConstruimos(root);
  initObjetivos(root);
  initAliados(root);
  initMetodo(root);
  initHdpe(root);
}

/* ------------------------------------------------------------------ */
/* 1. Hero                                                             */
/* ------------------------------------------------------------------ */

function initHero(root: HTMLElement): void {
  const hero = root.querySelector<HTMLElement>('[data-nos-hero]');
  if (!hero) return;

  const bgImg = hero.querySelector<HTMLElement>('[data-nos-hero-bg] img');
  const bg = hero.querySelector<HTMLElement>('[data-nos-hero-bg]');
  const content = hero.querySelector<HTMLElement>('[data-nos-hero-content]');
  const title = hero.querySelector<HTMLElement>('[data-nos-hero-title]');
  const desc = hero.querySelector<HTMLElement>('[data-nos-hero-desc]');
  const crumb = hero.querySelector<HTMLElement>('[data-nos-hero-crumb]');
  const glow = hero.querySelector<HTMLElement>('[data-nos-hero-glow]');
  const scrollHint = hero.querySelector<HTMLElement>('[data-nos-hero-scroll]');
  const count = hero.querySelector<HTMLElement>('[data-nos-count]');

  const revealables = [title, desc, crumb, scrollHint].filter((el): el is HTMLElement => el !== null);

  if (REDUCED) {
    gsap.set(revealables, { autoAlpha: 1 });
    return;
  }

  // ---------- Intro ----------

  // Máscara por línea: cada línea sube desde detrás de su propio wrapper
  // (mask: 'lines' los crea con overflow hidden). Al terminar se revierte
  // al HTML original para que el titular vuelva a fluir normal al resizear.
  const split = title ? new SplitText(title, { type: 'lines', mask: 'lines', linesClass: 'nos-hero__line' }) : null;

  const tl = gsap.timeline({
    defaults: { ease: 'power3.out' },
    delay: 0.15,
    onComplete: () => split?.revert(),
  });

  if (bgImg) tl.fromTo(bgImg, { scale: 1.18, autoAlpha: 0 }, { scale: 1, autoAlpha: 1, duration: 2.4, ease: 'power2.out' }, 0);
  if (crumb) tl.fromTo(crumb, { y: -16, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.8 }, 0.5);
  if (title && split) {
    tl.set(title, { autoAlpha: 1 }, 0.6);
    tl.from(split.lines, { yPercent: 110, duration: 1.1, stagger: 0.14 }, 0.6);
  }
  if (desc) tl.fromTo(desc, { y: 24, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.9 }, 1.1);
  if (count) {
    // Cuenta 0 → 13 sobre un proxy (no sobre textContent directo) y escribe
    // el entero en cada frame — evita decimales a mitad del tween.
    const target = Number(count.dataset.nosCount ?? count.textContent ?? 0);
    const proxy = { value: 0 };
    tl.to(proxy, {
      value: target,
      duration: 1.4,
      ease: 'power2.out',
      onUpdate: () => {
        count.textContent = String(Math.round(proxy.value));
      },
    }, 1.2);
  }
  if (scrollHint) {
    tl.fromTo(scrollHint, { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.6 }, 1.6);
    const line = scrollHint.querySelector<HTMLElement>('.nos-hero__scroll-line');
    if (line) {
      gsap.fromTo(line, { scaleY: 0 }, { scaleY: 1, duration: 1.4, ease: 'power2.inOut', repeat: -1, repeatDelay: 0.4, delay: 1.8 });
    }
  }

  // ---------- Salida con el scroll ----------
  // El contenido sube y se apaga mientras el hero sale de pantalla; la foto
  // (el wrapper, no el <img> que ya animó la intro) se queda atrás más lenta.
  const exit = gsap.timeline({
    scrollTrigger: {
      trigger: hero,
      start: 'top top',
      end: 'bottom top',
      scrub: 0.5,
    },
    defaults: { ease: 'none' },
  });
  if (content) exit.to(content, { yPercent: 22, autoAlpha: 0 }, 0);
  if (bg) exit.to(bg, { yPercent: 14, scale: 1.06 }, 0);

  // ---------- Halo que sigue al cursor ----------
  if (glow && FINE_POINTER) {
    const rect = () => hero.getBoundingClientRect();
    const setX = gsap.quickTo(glow, 'x', { duration: 1.2, ease: 'power3.out' });
    const setY = gsap.quickTo(glow, 'y', { duration: 1.2, ease: 'power3.out' });
    // Arranca sobre el titular, a la izquierda-abajo
    const r0 = rect();
    gsap.set(glow, { x: r0.width * 0.28, y: r0.height * 0.7 });
    hero.addEventListener('mousemove', (event) => {
      const r = rect();
      setX(event.clientX - r.left);
      setY(event.clientY - r.top);
    });
  }
}

/* ------------------------------------------------------------------ */
/* Paneles con cortina (clip-path)                                     */
/* ------------------------------------------------------------------ */

const CLIP_FROM: Record<string, string> = {
  left: 'inset(0 100% 0 0)',
  right: 'inset(0 0 0 100%)',
  up: 'inset(100% 0 0 0)',
  down: 'inset(0 0 100% 0)',
};

function initPanels(root: HTMLElement): void {
  if (REDUCED) return;

  root.querySelectorAll<HTMLElement>('[data-nos-panel]').forEach((panel) => {
    const from = CLIP_FROM[panel.dataset.nosPanel ?? 'up'] ?? CLIP_FROM.up;
    const img = panel.querySelector<HTMLElement>('[data-nos-panel-img]');

    const tl = gsap.timeline({
      scrollTrigger: { trigger: panel, start: 'top 82%', ...ONCE },
    });

    tl.fromTo(
      panel,
      { clipPath: from },
      { clipPath: 'inset(0 0 0 0)', duration: 1.3, ease: 'power4.inOut', clearProps: 'clipPath' },
      0
    );
    if (img) {
      tl.fromTo(img, { scale: 1.22 }, { scale: 1, duration: 1.8, ease: 'power3.out', clearProps: 'transform' }, 0);
    }
  });
}

/* ------------------------------------------------------------------ */
/* 2. Construimos para el futuro                                       */
/* ------------------------------------------------------------------ */

function initConstruimos(root: HTMLElement): void {
  if (REDUCED) return;

  const island = root.querySelector<HTMLElement>('[data-nos-island]');
  if (!island) return;

  // Este wrapper es exclusivo de la entrada: el parallax (padre) y la
  // flotación (hijo <img>) tocan `y` en sus propios elementos, así no se
  // pisan entre sí.
  gsap.from(island, {
    y: 140,
    scale: 0.72,
    rotate: -8,
    autoAlpha: 0,
    duration: 1.7,
    ease: 'expo.out',
    scrollTrigger: { trigger: island, start: 'top 95%', ...ONCE },
  });
}

/* ------------------------------------------------------------------ */
/* 3. Objetivos con propósito                                          */
/* ------------------------------------------------------------------ */

function initObjetivos(root: HTMLElement): void {
  const section = root.querySelector<HTMLElement>('[data-nos-objetivos]');
  if (!section) return;

  const links = Array.from(section.querySelectorAll<HTMLAnchorElement>('[data-nos-obj-link]'));
  const chevron = section.querySelector<HTMLElement>('[data-nos-obj-chevron]');
  const cards = Array.from(section.querySelectorAll<HTMLElement>('[data-nos-obj]'));
  if (!links.length || !cards.length) return;

  let current = 0;

  // El chevron vive absoluto en el nav (top 0) y viaja en `y` hasta quedar
  // centrado con el link activo. Se re-mide en cada refresh de ScrollTrigger
  // (resize, fuente cargada) porque los offsets cambian con la tipografía.
  const moveChevron = (index: number, animate: boolean): void => {
    if (!chevron) return;
    const link = links[index];
    const y = link.offsetTop + (link.offsetHeight - chevron.offsetHeight) / 2;
    if (!animate || REDUCED) {
      gsap.set(chevron, { y });
      return;
    }
    gsap.to(chevron, { y, duration: 0.55, ease: 'back.out(1.7)', overwrite: true });
    // Pequeño "empujón" hacia el texto al llegar
    gsap.fromTo(chevron, { x: -6 }, { x: 0, duration: 0.5, ease: 'power2.out', overwrite: 'auto' });
  };

  const setActive = (index: number, animate = true): void => {
    if (index === current && animate) return;
    current = index;
    links.forEach((link, i) => link.classList.toggle('is-active', i === index));
    moveChevron(index, animate);
  };

  setActive(0, false);
  ScrollTrigger.addEventListener('refresh', () => moveChevron(current, false));
  if ('fonts' in document) {
    document.fonts.ready.then(() => moveChevron(current, false));
  }

  // Scroll-spy: cada objetivo es "activo" mientras cruza la franja central
  // del viewport. Sin reduce-motion igual corre (solo cambia el estado del
  // nav, no hay movimiento de contenido).
  cards.forEach((card, i) => {
    ScrollTrigger.create({
      trigger: card,
      start: 'top 55%',
      end: 'bottom 55%',
      onToggle: (self) => {
        if (self.isActive) setActive(i);
      },
    });
  });

  if (REDUCED) return;

  cards.forEach((card) => {
    const panel = card.querySelector<HTMLElement>('[data-nos-obj-panel]');
    const img = card.querySelector<HTMLElement>('[data-nos-obj-img]');
    if (!panel) return;

    // Entrada: el panel se "asienta" (escala + sube) al entrar en viewport
    gsap.from(panel, {
      y: 56,
      scale: 0.94,
      autoAlpha: 0,
      duration: 1.2,
      ease: 'power3.out',
      scrollTrigger: { trigger: panel, start: 'top 85%', ...ONCE },
    });

    // Parallax interno de la foto (tiene 116% de alto para no dejar bordes)
    if (img) {
      gsap.fromTo(
        img,
        { yPercent: -6 },
        {
          yPercent: 6,
          ease: 'none',
          scrollTrigger: { trigger: panel, start: 'top bottom', end: 'bottom top', scrub: 0.4 },
        }
      );
    }

    // Tilt 3D siguiendo al cursor (solo desktop con mouse)
    if (FINE_POINTER) {
      const rotX = gsap.quickTo(panel, 'rotationX', { duration: 0.6, ease: 'power3.out' });
      const rotY = gsap.quickTo(panel, 'rotationY', { duration: 0.6, ease: 'power3.out' });
      gsap.set(panel, { transformPerspective: 1400 });
      panel.addEventListener('mousemove', (event) => {
        const r = panel.getBoundingClientRect();
        const dx = (event.clientX - r.left) / r.width - 0.5;
        const dy = (event.clientY - r.top) / r.height - 0.5;
        rotX(-dy * 6);
        rotY(dx * 8);
      });
      panel.addEventListener('mouseleave', () => {
        rotX(0);
        rotY(0);
      });
    }
  });
}

/* ------------------------------------------------------------------ */
/* 4. Nuestros aliados                                                 */
/* ------------------------------------------------------------------ */

function initAliados(root: HTMLElement): void {
  if (REDUCED) return;

  const section = root.querySelector<HTMLElement>('[data-nos-aliados]');
  if (!section) return;

  // Anillos: se expanden y encienden a medida que la sección entra —
  // scrubbed, así retroceden al subir como el resto de la página.
  const rings = section.querySelectorAll<HTMLElement>('[data-nos-ring]');
  if (rings.length) {
    gsap.fromTo(
      rings,
      { scale: 0.55, opacity: 0 },
      {
        scale: 1,
        opacity: 1,
        ease: 'none',
        stagger: 0.12,
        scrollTrigger: { trigger: section, start: 'top 85%', end: 'center center', scrub: 0.6 },
      }
    );
  }

  // Logos: entran en 3D, "cayendo" hacia atrás desde su base, en cascada.
  const grid = section.querySelector<HTMLElement>('[data-nos-logos]');
  const logos = section.querySelectorAll<HTMLElement>('[data-nos-logo]');
  if (grid && logos.length) {
    gsap.from(logos, {
      rotationX: -75,
      y: 48,
      autoAlpha: 0,
      transformOrigin: '50% 100%',
      transformPerspective: 900,
      duration: 1.1,
      ease: 'power3.out',
      stagger: { each: 0.07, from: 'start' },
      clearProps: 'transform',
      scrollTrigger: { trigger: grid, start: 'top 82%', ...ONCE },
    });
  }

  // Isla: entra desde abajo y, en desktop, se inclina siguiendo al mouse
  // sobre toda la sección (la flotación continua es data-float en el <img>).
  const island = section.querySelector<HTMLElement>('[data-nos-aliados-island]');
  const tilt = section.querySelector<HTMLElement>('[data-nos-aliados-tilt]');
  if (island) {
    gsap.from(island, {
      y: 120,
      scale: 0.8,
      autoAlpha: 0,
      duration: 1.6,
      ease: 'expo.out',
      scrollTrigger: { trigger: island, start: 'top 95%', ...ONCE },
    });
  }
  if (tilt && FINE_POINTER) {
    gsap.set(tilt, { transformPerspective: 1200 });
    const rotX = gsap.quickTo(tilt, 'rotationX', { duration: 1, ease: 'power3.out' });
    const rotY = gsap.quickTo(tilt, 'rotationY', { duration: 1, ease: 'power3.out' });
    section.addEventListener('mousemove', (event) => {
      const r = section.getBoundingClientRect();
      const dx = (event.clientX - r.left) / r.width - 0.5;
      const dy = (event.clientY - r.top) / r.height - 0.5;
      rotX(-dy * 10);
      rotY(dx * 14);
    });
    section.addEventListener('mouseleave', () => {
      rotX(0);
      rotY(0);
    });
  }
}

/* ------------------------------------------------------------------ */
/* 6. Método Circulogic — tabs ESG                                     */
/* ------------------------------------------------------------------ */

function initMetodo(root: HTMLElement): void {
  const section = root.querySelector<HTMLElement>('[data-nos-metodo]');
  if (!section) return;

  const tabs = Array.from(section.querySelectorAll<HTMLButtonElement>('[data-nos-metodo-tab]'));
  const media = section.querySelector<HTMLElement>('[data-nos-metodo-media]');
  const img = section.querySelector<HTMLImageElement>('[data-nos-metodo-img]');
  const leaf = section.querySelector<HTMLElement>('[data-nos-metodo-leaf]');
  if (!tabs.length || !media || !img) return;

  const interval = Number(section.dataset.nosMetodoAutoplay);
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

  // La barra verde ES el temporizador (mismo patrón que residuos-selector):
  // un tween sobre --tab-progress que al completar avanza a la siguiente.
  const startProgress = (tab: HTMLButtonElement): void => {
    progressTween?.kill();
    progressTween = undefined;
    if (!autoplay) {
      gsap.set(tab, { '--tab-progress': 1 });
      return;
    }
    progressTween = gsap.fromTo(
      tab,
      { '--tab-progress': 0 },
      {
        '--tab-progress': 1,
        duration: interval / 1000,
        ease: 'none',
        onComplete: () => activate(tabs[(tabs.indexOf(tab) + 1) % tabs.length]),
      }
    );
    if (!eligible()) progressTween.pause();
  };

  const activate = (tab: HTMLButtonElement): void => {
    if (tab.classList.contains('is-active')) return;

    tabs.forEach((t) => {
      const active = t === tab;
      t.classList.toggle('is-active', active);
      t.setAttribute('aria-selected', String(active));
      if (!active) gsap.set(t, { '--tab-progress': 0 });
    });
    startProgress(tab);

    const next = tab.dataset.img ?? '';
    if (next && img.getAttribute('src') !== next) {
      if (REDUCED) {
        img.setAttribute('src', next);
      } else {
        gsap.to(media, {
          autoAlpha: 0,
          scale: 1.05,
          duration: 0.35,
          ease: 'power2.in',
          overwrite: true,
          onComplete: () => {
            img.setAttribute('src', next);
            gsap.to(media, { autoAlpha: 1, scale: 1, duration: 0.7, ease: 'power2.out' });
          },
        });
      }
    }

    if (leaf && !REDUCED) {
      gsap.fromTo(leaf, { rotation: -25, scale: 0.6 }, { rotation: 0, scale: 1, duration: 0.9, ease: 'back.out(2)', overwrite: true });
    }
  };

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => activate(tab));
  });

  const tabsWrap = tabs[0].parentElement ?? section;
  tabsWrap.addEventListener('mouseenter', () => { hovered = true; sync(); });
  tabsWrap.addEventListener('mouseleave', () => { hovered = false; sync(); });
  tabsWrap.addEventListener('focusin', () => { hovered = true; sync(); });
  tabsWrap.addEventListener('focusout', () => { hovered = false; sync(); });
  document.addEventListener('visibilitychange', sync);

  ScrollTrigger.create({
    trigger: section,
    start: 'top 75%',
    end: 'bottom 25%',
    onToggle: (self) => {
      inView = self.isActive;
      sync();
    },
  });

  // Estado inicial: la primera tab ya está activa en el HTML; solo arranca
  // su cuenta (queda pausada hasta que la sección entre en pantalla).
  startProgress(tabs[0]);
}

/* ------------------------------------------------------------------ */
/* 7. HDPE — chips, ícono y escena de pellets                          */
/* ------------------------------------------------------------------ */

function initHdpe(root: HTMLElement): void {
  const section = root.querySelector<HTMLElement>('[data-nos-hdpe]');
  if (!section) return;

  const canvas = section.querySelector<HTMLCanvasElement>('[data-nos-pellets]');
  if (canvas && canvas.parentElement) initPellets(canvas, canvas.parentElement, section);

  if (REDUCED) return;

  const chips = section.querySelector<HTMLElement>('[data-nos-chips]');
  if (chips && chips.children.length) {
    gsap.from(chips.children, {
      y: 18,
      scale: 0.9,
      autoAlpha: 0,
      duration: 0.7,
      ease: 'back.out(1.6)',
      stagger: 0.08,
      clearProps: 'transform',
      scrollTrigger: { trigger: chips, start: 'top 88%', ...ONCE },
    });
  }

  // El ícono de circularidad gira una vuelta completa mientras las cards
  // cruzan el viewport — reversible con el scroll, como todo lo demás.
  const spin = section.querySelector<HTMLElement>('.nos-hdpe__card-icon--spin svg');
  if (spin) {
    gsap.fromTo(
      spin,
      { rotation: 0 },
      {
        rotation: 360,
        ease: 'none',
        scrollTrigger: { trigger: spin, start: 'top bottom', end: 'bottom top', scrub: 0.5 },
      }
    );
  }
}

/* ---------- Escena de pellets (canvas 2D) ----------
 * Pellets de HDPE (los granos con que se fabrican los contenedores) que
 * llueven desde arriba, rebotan contra un piso en perspectiva y se apilan
 * en un montículo. Cada uno vive un rato apilado, se apaga y vuelve a
 * caer: el ciclo de reciclaje en loop. El cursor los aparta (repulsión con
 * resorte de vuelta a su lugar). Corre en el ticker de GSAP solo mientras
 * la escena está en pantalla.
 */

type Pellet = {
  hx: number; // posición "hogar" en el piso
  hy: number;
  x: number;
  y: number;
  vx: number;
  vy: number;
  r: number;
  color: string;
  depth: number; // 0 (lejos) → 1 (cerca): tamaño y orden de dibujo
  state: 'fall' | 'rest' | 'fade';
  bounces: number;
  restAt: number; // segundos del reloj interno en que se apiló
  ttl: number; // segundos apilado antes de reciclarse
  alpha: number;
  delay: number; // segundos antes de empezar a caer
};

const PELLET_COLORS = ['#0091d1', '#8eb952', '#e85d28', '#ffd27d', '#33a7da', '#b0ddf1', '#ffffff', '#8eb952', '#0091d1'];
const PELLET_COUNT = 300;
const GRAVITY = 1400; // px/s²

function initPellets(canvas: HTMLCanvasElement, scene: HTMLElement, section: HTMLElement): void {
  const ctx = canvas.getContext('2d');
  if (!ctx) return;

  let w = 0;
  let h = 0;
  let floor = { cx: 0, cy: 0, rx: 0, ry: 0 };
  const pellets: Pellet[] = [];
  const mouse = { x: -9999, y: -9999, active: false };
  let running = false;
  let lastTime = 0;
  // Reloj propio (segundos) que solo avanza mientras el loop corre: si se
  // usara performance.now(), al llegar a la sección después de un rato toda
  // la pila habría "vencido" a la vez y se reciclaría de golpe.
  let clock = 0;

  const rand = (min: number, max: number): number => min + Math.random() * (max - min);

  const spawn = (p: Pellet | null, immediate: boolean): Pellet => {
    // Punto de aterrizaje: dentro de la elipse del piso, más denso al centro
    const a = Math.random() * Math.PI * 2;
    const d = Math.sqrt(Math.random()) * 0.92;
    const hx = floor.cx + Math.cos(a) * floor.rx * d;
    const hy = floor.cy + Math.sin(a) * floor.ry * d;
    const depth = (hy - (floor.cy - floor.ry)) / (floor.ry * 2); // 0 arriba (lejos) → 1 abajo (cerca)
    const next: Pellet = {
      hx,
      hy,
      x: hx + rand(-10, 10),
      y: immediate ? hy : -rand(20, h * 0.5),
      vx: 0,
      vy: 0,
      r: rand(2.2, 3.6) * (0.65 + depth * 0.6),
      color: PELLET_COLORS[Math.floor(Math.random() * PELLET_COLORS.length)],
      depth,
      state: immediate ? 'rest' : 'fall',
      bounces: 0,
      // Los de la pila inicial nacen con parte de su vida ya consumida, así
      // se reciclan repartidos en el tiempo y no todos juntos.
      restAt: immediate ? clock - rand(0, 10) : clock,
      ttl: rand(9, 20),
      alpha: 1,
      delay: immediate ? 0 : rand(0, 6),
    };
    if (p) Object.assign(p, next);
    return p ?? next;
  };

  const resize = (): void => {
    const rect = scene.getBoundingClientRect();
    if (!rect.width || !rect.height) return;
    w = rect.width;
    h = rect.height;
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    canvas.width = Math.round(w * dpr);
    canvas.height = Math.round(h * dpr);
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    floor = { cx: w * 0.5, cy: h * 0.74, rx: w * 0.3, ry: h * 0.12 };
    // Reposiciona los apilados relativo al nuevo piso
    pellets.forEach((p) => {
      if (p.state !== 'fall') spawn(p, true);
    });
  };

  const drawFloor = (): void => {
    // Rejilla en perspectiva: verticales que convergen a un punto de fuga y
    // horizontales que se comprimen hacia el horizonte.
    const horizon = h * 0.5;
    const vpX = w * 0.5;
    ctx.save();
    ctx.strokeStyle = 'rgba(255,255,255,0.05)';
    ctx.lineWidth = 1;
    for (let i = -8; i <= 8; i++) {
      ctx.beginPath();
      ctx.moveTo(vpX + i * 6, horizon);
      ctx.lineTo(vpX + i * (w * 0.16), h + 2);
      ctx.stroke();
    }
    for (let k = 1; k <= 9; k++) {
      const t = (k / 9) ** 2.2;
      const y = horizon + (h - horizon) * t;
      ctx.globalAlpha = 0.25 + t * 0.75;
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.lineTo(w, y);
      ctx.stroke();
    }
    ctx.restore();

    // Brillo bajo el montículo
    const g = ctx.createRadialGradient(floor.cx, floor.cy, 0, floor.cx, floor.cy, floor.rx * 1.2);
    g.addColorStop(0, 'rgba(0,145,209,0.16)');
    g.addColorStop(1, 'rgba(0,145,209,0)');
    ctx.save();
    ctx.translate(floor.cx, floor.cy);
    ctx.scale(1, floor.ry / floor.rx);
    ctx.translate(-floor.cx, -floor.cy);
    ctx.fillStyle = g;
    ctx.fillRect(0, floor.cy - floor.rx * 1.5, w, floor.rx * 3);
    ctx.restore();
  };

  const step = (dt: number): void => {
    const rx = floor.rx;
    for (const p of pellets) {
      if (p.state === 'fall') {
        if (p.delay > 0) {
          p.delay -= dt;
          continue;
        }
        p.vy += GRAVITY * dt;
        p.y += p.vy * dt;
        p.x += p.vx * dt;
        if (p.y >= p.hy) {
          p.y = p.hy;
          if (p.bounces < 2 && p.vy > 120) {
            p.vy = -p.vy * rand(0.25, 0.4);
            p.vx = rand(-30, 30);
            p.bounces++;
          } else {
            p.vy = 0;
            p.vx = 0;
            p.state = 'rest';
            p.restAt = clock;
            p.x = p.hx;
          }
        }
      } else if (p.state === 'rest') {
        // Repulsión del cursor + resorte de vuelta al hogar
        if (mouse.active) {
          const dx = p.x - mouse.x;
          const dy = (p.y - mouse.y) * (rx / floor.ry) * 0.5; // compensa la elipse
          const dist = Math.hypot(dx, dy) || 1;
          const radius = 70;
          if (dist < radius) {
            const f = (1 - dist / radius) * 900;
            p.vx += (dx / dist) * f * dt;
            p.vy += (dy / dist) * f * dt * (floor.ry / rx);
          }
        }
        p.vx += (p.hx - p.x) * 18 * dt;
        p.vy += (p.hy - p.y) * 18 * dt;
        p.vx *= 0.86;
        p.vy *= 0.86;
        p.x += p.vx * dt;
        p.y += p.vy * dt;
        if (clock - p.restAt > p.ttl) p.state = 'fade';
      } else {
        p.alpha -= dt * 1.4;
        if (p.alpha <= 0) spawn(p, false);
      }
    }
  };

  const draw = (): void => {
    ctx.clearRect(0, 0, w, h);
    drawFloor();
    // Los más lejanos (depth bajo) primero para que los cercanos queden encima
    const sorted = pellets.slice().sort((a, b) => a.depth - b.depth);
    for (const p of sorted) {
      if (p.state === 'fall' && p.delay > 0) continue;
      ctx.globalAlpha = p.alpha * (0.7 + p.depth * 0.3);
      ctx.fillStyle = p.color;
      ctx.beginPath();
      ctx.ellipse(p.x, p.y, p.r, p.r * 0.85, 0, 0, Math.PI * 2);
      ctx.fill();
      // Brillo especular
      ctx.globalAlpha = p.alpha * 0.35;
      ctx.fillStyle = '#ffffff';
      ctx.beginPath();
      ctx.arc(p.x - p.r * 0.3, p.y - p.r * 0.35, p.r * 0.3, 0, Math.PI * 2);
      ctx.fill();
    }
    ctx.globalAlpha = 1;
  };

  const tick = (): void => {
    const now = performance.now();
    const dt = Math.min((now - lastTime) / 1000, 0.05);
    lastTime = now;
    clock += dt;
    step(dt);
    draw();
  };

  const start = (): void => {
    if (running) return;
    running = true;
    lastTime = performance.now();
    gsap.ticker.add(tick);
  };
  const stop = (): void => {
    if (!running) return;
    running = false;
    gsap.ticker.remove(tick);
  };

  resize();
  // Escena inicial: dos tercios ya apilados (así no arranca vacía), el resto
  // llueve escalonado durante los primeros segundos.
  for (let i = 0; i < PELLET_COUNT; i++) {
    pellets.push(spawn(null, i < PELLET_COUNT * 0.66));
  }
  draw();

  if (REDUCED) return; // cuadro estático, sin loop

  if (typeof ResizeObserver === 'function') {
    new ResizeObserver(() => {
      resize();
      draw();
    }).observe(scene);
  } else {
    window.addEventListener('resize', resize);
  }

  if (FINE_POINTER) {
    scene.addEventListener('mousemove', (event) => {
      const r = scene.getBoundingClientRect();
      mouse.x = event.clientX - r.left;
      mouse.y = event.clientY - r.top;
      mouse.active = true;
    });
    scene.addEventListener('mouseleave', () => {
      mouse.active = false;
    });
  }

  ScrollTrigger.create({
    trigger: section,
    start: 'top bottom',
    end: 'bottom top',
    onToggle: (self) => {
      if (self.isActive && !document.hidden) start();
      else stop();
    },
  });
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) stop();
    else if (ScrollTrigger.isInViewport(scene)) start();
  });
}
