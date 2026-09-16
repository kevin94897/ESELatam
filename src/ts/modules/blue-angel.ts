/**
 * "Estándar Blue Angel" (template-parts/blue-angel.php, Figma 3807-5473):
 * carrusel en arco, a imagen de la sección "Economía circular" del sitio
 * anterior (contenedoresdebasura-ese.com#economia-circular).
 *
 *  - Desktop: hasta 5 pasos visibles repartidos sobre un arco concéntrico
 *    con la banda de progreso; el del centro va grande y es el activo. Los
 *    demás pasos del ciclo esperan, ocultos, más allá de los extremos.
 *  - Mobile (< 48rem): 3 en fila, el del centro más grande; sin arco.
 *  - Prev/next rotan el ciclo (con wrap); clic o Enter en un paso lo trae al
 *    centro; deslizar en táctil también rota. Autoplay cada N ms (atributo
 *    data-bangel-autoplay) solo mientras la sección está en viewport, y se
 *    apaga para siempre a la primera interacción del usuario — igual que la
 *    referencia. Con prefers-reduced-motion no hay autoplay.
 *  - El texto bajo el arco es el nombre del paso activo; la banda de
 *    progreso (stroke-dasharray) y su marcador "»" avanzan con el índice.
 *
 * Todo el movimiento es CSS (transition en transform/opacity): acá solo se
 * calculan posiciones y se escriben transforms.
 */

const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const MOBILE = window.matchMedia('(width < 48rem)');

// Geometría del arco del SVG (viewBox 1000×340): centro y radio de la banda.
const ARC_VB_W = 1000;
const ARC_VB_H = 340;
const ARC_CY = 585;
const ARC_WIDTH_RATIO = 0.68; // ancho de la banda respecto del escenario (Figma: 990/1458)

// Ángulos (en grados, desde el eje X hacia arriba) de los 5 puestos visibles.
const DESKTOP_VISIBLE = 5;
const EDGE_ANGLE = 35;

export function initBlueAngel(root: HTMLElement): void {
  const stage = root.querySelector<HTMLElement>('[data-bangel-stage]');
  const items = Array.from(root.querySelectorAll<HTMLElement>('[data-bangel-item]'));
  const label = root.querySelector<HTMLElement>('[data-bangel-label]');
  const prev = root.querySelector<HTMLButtonElement>('[data-bangel-prev]');
  const next = root.querySelector<HTMLButtonElement>('[data-bangel-next]');
  const progress = root.querySelector<SVGPathElement>('[data-bangel-progress]');
  const marker = root.querySelector<SVGGElement>('[data-bangel-marker]');
  const arc = root.querySelector<SVGSVGElement>('[data-bangel-arc]');
  if (!stage || items.length < 2) return;

  const n = items.length;
  // Índice del paso que ocupa el PRIMER puesto visible; el activo es el del medio.
  let first = 0;
  const initialActive = items.findIndex((el) => el.classList.contains('is-active'));
  const visibleCount = (): number => (MOBILE.matches ? Math.min(3, n) : Math.min(DESKTOP_VISIBLE, n));
  const middle = (): number => Math.floor(visibleCount() / 2);
  if (initialActive > -1) first = (initialActive - middle() + n) % n;

  let busy = false;
  let userTouched = false;
  let timer = 0;
  let inView = false;
  const autoplayMs = Number(root.dataset.bangelAutoplay ?? 0);
  const progressLength = progress?.getTotalLength() ?? 0;

  stage.classList.add('is-js');

  /* ------------------------------------------------------------------ */
  /* Layout                                                              */
  /* ------------------------------------------------------------------ */

  const place = (el: HTMLElement, x: number, y: number, scale: number, visible: boolean, z: number): void => {
    el.style.transform = `translate3d(${x.toFixed(1)}px, ${y.toFixed(1)}px, 0) scale(${scale.toFixed(3)})`;
    el.style.zIndex = String(z);
    el.classList.toggle('is-hidden', !visible);
    el.setAttribute('aria-hidden', visible ? 'false' : 'true');
    const btn = el.querySelector<HTMLButtonElement>('button');
    if (btn) btn.tabIndex = visible ? 0 : -1;
  };

  const layoutDesktop = (): void => {
    const W = stage.clientWidth;
    const H = stage.clientHeight;
    const vis = visibleCount();
    const mid = middle();

    // Círculo concéntrico con la banda del SVG (que está pegada abajo).
    const s = (W * ARC_WIDTH_RATIO) / ARC_VB_W;
    const cx = W / 2;
    const cy = H - ARC_VB_H * s + ARC_CY * s;

    const base = W * 0.16;
    stage.style.setProperty('--bangel-item', `${base.toFixed(1)}px`);
    const activeScale = 2;
    // Radio tal que el paso activo (el más alto) entre en el escenario.
    const R = cy - (base * activeScale) / 2 - W * 0.012;

    const span = 180 - EDGE_ANGLE * 2;
    const step = vis > 1 ? span / (vis - 1) : 0;

    items.forEach((el, i) => {
      const v = (i - first + n) % n;
      if (v < vis) {
        const deg = 180 - EDGE_ANGLE - step * v;
        const a = (deg * Math.PI) / 180;
        const scale = v === mid ? activeScale : v === 0 || v === vis - 1 ? 1.2 : 1;
        place(el, cx + R * Math.cos(a) - base / 2, cy - R * Math.sin(a) - base / 2, scale, true, v === mid ? 10 : 5 - Math.abs(v - mid));
      } else {
        // Los que esperan: justo más allá del extremo por el que van a entrar.
        const fromRight = v - vis < (n - vis) / 2;
        const deg = fromRight ? EDGE_ANGLE - 22 : 180 - EDGE_ANGLE + 22;
        const a = (deg * Math.PI) / 180;
        place(el, cx + R * Math.cos(a) - base / 2, cy - R * Math.sin(a) - base / 2, 0.6, false, 1);
      }
    });
  };

  const layoutMobile = (): void => {
    const W = stage.clientWidth;
    const H = stage.clientHeight;
    const vis = visibleCount();
    const mid = middle();
    const base = Math.min(W * 0.3, 150);
    stage.style.setProperty('--bangel-item', `${base.toFixed(1)}px`);
    const gap = base * 0.12;
    const pitch = base + gap;
    const cy = H * 0.42;

    items.forEach((el, i) => {
      const v = (i - first + n) % n;
      if (v < vis) {
        const x = W / 2 + (v - mid) * pitch - base / 2;
        place(el, x, cy - base / 2, v === mid ? 1.35 : 1, true, v === mid ? 10 : 5);
      } else {
        const fromRight = v - vis < (n - vis) / 2;
        const x = fromRight ? W + base * 0.2 : -base * 1.2;
        place(el, x, cy - base / 2, 0.7, false, 1);
      }
    });
  };

  const updateChrome = (): void => {
    const activeIndex = (first + middle()) % n;
    items.forEach((el, i) => el.classList.toggle('is-active', i === activeIndex));

    const text = items[activeIndex].dataset.bangelName ?? '';
    if (label && label.textContent?.trim() !== text) {
      label.classList.add('is-switching');
      window.setTimeout(() => {
        label.textContent = text;
        label.classList.remove('is-switching');
      }, REDUCED ? 0 : 180);
    }

    // Progreso del ciclo: 0 en el primer paso, 1 en el último.
    if (progress && progressLength) {
      const p = n > 1 ? activeIndex / (n - 1) : 1;
      const len = Math.max(progressLength * p, 0.001);
      progress.style.strokeDasharray = `${len} ${progressLength}`;
      if (marker) {
        const pt = progress.getPointAtLength(len);
        const ahead = progress.getPointAtLength(Math.min(progressLength, len + 1));
        const behind = progress.getPointAtLength(Math.max(0, len - 1));
        const angle = (Math.atan2(ahead.y - behind.y, ahead.x - behind.x) * 180) / Math.PI;
        marker.setAttribute('transform', `translate(${pt.x.toFixed(2)} ${pt.y.toFixed(2)}) rotate(${angle.toFixed(2)})`);
      }
    }
  };

  const layout = (): void => {
    if (MOBILE.matches) layoutMobile();
    else layoutDesktop();
    updateChrome();
  };

  /* ------------------------------------------------------------------ */
  /* Navegación                                                          */
  /* ------------------------------------------------------------------ */

  const rotate = (delta: number, fromUser = true): void => {
    if (busy || delta === 0) return;
    busy = true;
    first = (first + delta + n * 10) % n;
    layout();
    window.setTimeout(() => { busy = false; }, REDUCED ? 0 : 450);
    if (fromUser) stopAutoplay(true);
  };

  const goTo = (index: number): void => {
    const v = (index - first + n) % n;
    rotate(v - middle());
  };

  prev?.addEventListener('click', () => rotate(-1));
  next?.addEventListener('click', () => rotate(1));

  items.forEach((el) => {
    const btn = el.querySelector<HTMLButtonElement>('[data-bangel-go]');
    btn?.addEventListener('click', () => goTo(Number(btn.dataset.bangelGo)));
  });

  // Deslizar en táctil / arrastrar con mouse
  let startX: number | null = null;
  stage.addEventListener('pointerdown', (e) => { startX = e.clientX; });
  stage.addEventListener('pointerup', (e) => {
    if (startX === null) return;
    const dx = e.clientX - startX;
    startX = null;
    if (Math.abs(dx) > 40) rotate(dx < 0 ? 1 : -1);
  });
  stage.addEventListener('pointercancel', () => { startX = null; });

  /* ------------------------------------------------------------------ */
  /* Autoplay                                                            */
  /* ------------------------------------------------------------------ */

  const stopAutoplay = (forGood = false): void => {
    if (forGood) userTouched = true;
    if (timer) { window.clearInterval(timer); timer = 0; }
  };

  const startAutoplay = (): void => {
    if (REDUCED || userTouched || !autoplayMs || timer || !inView) return;
    timer = window.setInterval(() => rotate(1, false), autoplayMs);
  };

  if (autoplayMs && !REDUCED) {
    if (typeof IntersectionObserver === 'function') {
      new IntersectionObserver((entries) => {
        inView = entries.some((en) => en.isIntersecting);
        if (inView) startAutoplay();
        else stopAutoplay();
      }, { threshold: 0.35 }).observe(root);
    } else {
      inView = true;
      startAutoplay();
    }
    // Mientras el usuario está encima, el ciclo espera; si no tocó nada, sigue al salir.
    stage.addEventListener('pointerenter', () => stopAutoplay());
    stage.addEventListener('pointerleave', () => startAutoplay());
    stage.addEventListener('focusin', () => stopAutoplay());
    stage.addEventListener('focusout', () => startAutoplay());
  }

  /* ------------------------------------------------------------------ */
  /* Arranque                                                            */
  /* ------------------------------------------------------------------ */

  // Primera colocación sin transición (para que no "vuelen" desde el origen).
  stage.classList.add('is-settling');
  layout();
  requestAnimationFrame(() => requestAnimationFrame(() => stage.classList.remove('is-settling')));

  if (typeof ResizeObserver === 'function') {
    new ResizeObserver(() => layout()).observe(stage);
  } else {
    window.addEventListener('resize', layout);
  }
  MOBILE.addEventListener('change', layout);
  arc?.setAttribute('data-ready', 'true');
}
