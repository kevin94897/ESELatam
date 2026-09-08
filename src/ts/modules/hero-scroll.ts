/**
 * Hero con animación de scroll (front-page):
 *  - Intro al cargar (sin scroll): el titular aparece centrado con máscara por
 *    línea, viaja a su posición de layout (a la izquierda), entran menú y
 *    lede/CTA/card, y recién al final emerge la isla — no está desde la carga.
 *  - Con el scroll: el video se reproduce scrubbed (suavizado con lerp) y el
 *    titular y la isla derivan hacia abajo con parallax (velocidades
 *    distintas) hasta disolverse entre las nubes de la sección siguiente.
 *  - En el último viewport del pin, la sección siguiente (coronada por las
 *    nubes, ver .hero-next__clouds) se desliza por encima del hero.
 */

import { gsap, ScrollTrigger } from '../lib/gsap';

// Duración del pin en viewports. La sección siguiente (adelantada exactamente
// la altura del hero) entra deslizándose durante el ÚLTIMO viewport de scroll
// del pin: su frente son las nubes que la coronan, y llega al top justo al
// liberarse el pin. SWEEP_START = 1 - 1/PIN_VIEWPORTS.
const PIN_VIEWPORTS = 3.5;
const VIDEO_LERP = 0.12;

// Coreografía del pin (fracciones del progreso total)
const SWEEP_START = 1 - 1 / PIN_VIEWPORTS;      // ~0.71 — la sección empieza a barrer
const VIDEO_END = SWEEP_START + 0.15;           // el video muere durante el barrido

export function initHeroScroll(section: HTMLElement): void {
  const video = section.querySelector<HTMLVideoElement>('[data-hero-video]');

  // El video tiene preload="none" (ver front-page.php) para no competirle
  // ancho de banda a la imagen LCP (la isla) durante el primer paint. Recién
  // acá, con el navegador ya libre, se dispara la descarga real — a tiempo
  // para que el scrub tenga datos cuando el usuario empiece a scrollear.
  const loadVideo = (): void => video?.load();
  if (typeof window.requestIdleCallback === 'function') {
    window.requestIdleCallback(loadVideo, { timeout: 2000 });
  } else {
    setTimeout(loadVideo, 300);
  }

  const island = section.querySelector<HTMLElement>('[data-hero-island]');
  const titleWrap = section.querySelector<HTMLElement>('[data-hero-title-wrap]');
  const bottom = section.querySelector<HTMLElement>('[data-hero-bottom]');
  const title = section.querySelector<HTMLElement>('.hero__title');
  const card = section.querySelector<HTMLElement>('.hero-card');
  const titleLines = section.querySelectorAll<HTMLElement>('[data-hero-line]');
  const revealEls = section.querySelectorAll<HTMLElement>('[data-hero-reveal]');
  const header = document.querySelector<HTMLElement>('[data-header]');
  // .hero__title-settled (ver main.css) es un duplicado estático de este
  // titular, visible solo en mobile, dentro de .hero__intro justo arriba
  // del lede — no hace falta capturarlo acá: ya trae `data-hero-reveal`, así
  // que `revealEls` (abajo) lo agarra solo. En mobile .hero__title de arriba
  // queda como el adorno puro de la intro (centrado, grande) y termina
  // ocultándose del todo en vez de viajar a una posición asentada; ese
  // duplicado es el que realmente queda en el layout final. En desktop no
  // existe visualmente (display:none) y .hero__title sigue siendo el único.
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // En mobile `.hero__title` ocupa TODO el ancho de contenido de
  // `.hero__content` (el padding lateral es simétrico, no hay nada que lo
  // angoste) — su CAJA ya cae centrada en el viewport de por sí, así que
  // centrar la caja (lo que hace titleDx) no mueve nada visible: el texto,
  // más angosto y con text-align:left, seguía pegado al borde izquierdo de
  // esa caja ya centrada. Acá sí hace falta centrar el TEXTO (no solo la
  // caja), y solo durante la intro — el layout asentado usa .hero__title-settled.
  const isMobile = window.matchMedia('(width < 48rem)').matches;

  if (prefersReducedMotion) {
    // Sin animación: estado final legible, todo visible, video en su primer frame.
    // En mobile el titular real es .hero__title-settled (ya incluido en
    // revealEls) — el de arriba (.hero__title) se queda oculto en vez de
    // mostrarse duplicado.
    const staticEls = [island, card, ...revealEls].filter((el): el is HTMLElement => el !== null);
    gsap.set(staticEls, { autoAlpha: 1 });
    if (title) gsap.set(title, { autoAlpha: isMobile ? 0 : 1 });
    video?.pause();
    return;
  }

  // ---------- Intro al cargar (toda la coreografía de entrada) ----------

  // Estado final directo, sin animación (para cargas con scroll restaurado).
  // clearProps en los reveals: un transform inline residual de la intro
  // podría interferir con animaciones propias de cada elemento después
  // (p. ej. el hover del hero-cta, que anima el `d` de su SVG conector).
  const finishIntroState = (): void => {
    if (title) gsap.set(title, isMobile ? { autoAlpha: 0 } : { x: 0, y: 0, scale: 1, autoAlpha: 1 });
    if (island) gsap.set(island, { autoAlpha: 1 });
    if (header) gsap.set(header, { yPercent: 0, autoAlpha: 1 });
    if (revealEls.length) gsap.set(revealEls, { autoAlpha: 1, clearProps: 'transform' });
  };

  // La intro solo tiene sentido si la página arranca al inicio del hero.
  // Con scroll restaurado (reload a mitad de página) o navegación con ancla,
  // el titular "viajaría" por un viewport que ya no es el suyo — se salta.
  const startsAtTop = window.scrollY <= 8 && window.location.hash === '';

  if (startsAtTop) {
    // .hero__title-wrap es display:none en mobile (ver main.css) — sin
    // animación de entrada ahí, el titular real es .hero__title-settled,
    // que entra con el resto de revealEls en el paso 5. Medir/animar el
    // original igual (con su ancestro display:none) daría un
    // getBoundingClientRect() en cero y una escena inútil.
    if (!isMobile && title) {
      // Desplazamiento del titular desde su posición de layout hasta el centro
      // del viewport, medido del DOM antes de aplicar cualquier transform.
      const r = title.getBoundingClientRect();
      const titleDx = window.innerWidth / 2 - (r.left + r.width / 2);
      const titleDy = window.innerHeight / 2 - (r.top + r.height / 2);
      // scale 1.2: el font-size CSS es el tamaño ASENTADO (reducido); la
      // escala compensa para que centrado se vea al tamaño grande original
      // y "reduzca ligeramente" al viajar a su posición (scale → 1).
      gsap.set(title, { x: titleDx, y: titleDy, scale: 1.2, autoAlpha: 1 });
    }

    // El menú arranca oculto arriba; el intro lo hace entrar.
    if (header) {
      gsap.set(header, { yPercent: -120, autoAlpha: 0 });
    }

    const intro = gsap.timeline({ defaults: { ease: 'power3.out' }, delay: 0.15 });

    // 1) El video asienta desde un ligero zoom
    intro.from(video ?? [], { scale: 1.12, duration: 2, ease: 'power2.out' }, 0);

    // 2) y 3) Solo desktop: el titular centrado sube tras su máscara y
    // luego viaja del centro a su posición de layout, a la izquierda. En
    // mobile .hero__title-wrap es display:none (ver main.css) — no hay
    // nada que animar; el titular que se ve ahí es .hero__title-settled,
    // que entra plano con el resto de revealEls en el paso 5. Los pasos
    // siguientes quedan en sus mismas posiciones absolutas de todos modos
    // (2.15, 2.25, 2.55), así que saltarse este tramo en mobile no
    // desarma la coreografía del resto — solo dejan de correr en el vacío.
    if (!isMobile) {
      intro.from(titleLines, { yPercent: 115, duration: 1.1, stagger: 0.14 }, 0.2);
      intro.to(title ?? [], { x: 0, y: 0, scale: 1, duration: 1.1, ease: 'power3.inOut' }, 1.5);
    }

    // 4) El menú baja y entra
    intro.to(header ?? [], { yPercent: 0, autoAlpha: 1, duration: 0.8 }, 2.15);

    // 5) Lede, CTA y card entran escalonados (clearProps: sin transform
    // inline residual que bloquee los hovers CSS, como el settle del CTA)
    intro.fromTo(
      revealEls,
      { y: 40, autoAlpha: 0 },
      { y: 0, autoAlpha: 1, duration: 0.9, stagger: 0.12, clearProps: 'transform' },
      2.25
    );

    // 6) Recién ahora aparece la isla: no está desde la carga, entra cuando el
    // titular ya aterrizó a la izquierda (arranca 0.05s antes de que termine
    // ese viaje, lo justo para que encadene en vez de sentirse un corte). Es
    // una decisión de diseño deliberada (la isla no debe verse hasta que el
    // título se asiente a la izquierda) a costa del LCP: con la isla oculta
    // hasta ISLAND_IN, el navegador mide el Largest Contentful Paint recién
    // ahí (o en el reveal de texto del paso 5 si pinta antes) — unos 4-6s en
    // conexión lenta, en vez de los <2s posibles si la isla fuera visible
    // desde el primer frame. Aceptado a cambio del efecto de entrada.
    //
    // Anima `y` en px (no yPercent): el parallax de scroll usa yPercent en el
    // mismo wrapper y ambas capas de transform componen sin pisarse. `scale` y
    // `rotation` son libres — el timeline de scroll no las toca.
    const ISLAND_IN = 2.55;

    intro.fromTo(
      island ?? [],
      { y: 170, scale: 0.84, autoAlpha: 0 },
      { y: 0, scale: 1, autoAlpha: 1, duration: 1.7, ease: 'expo.out' },
      ISLAND_IN
    );

    // La inclinación asienta más lenta que la subida: ese desfase es lo que da
    // la sensación de que la isla "encuentra" su flotación, en vez de aterrizar
    // rígida de una pieza.
    intro.fromTo(
      island ?? [],
      { rotation: -5 },
      { rotation: 0, duration: 2.4, ease: 'power2.out' },
      ISLAND_IN
    );

    // Si el navegador restaura el scroll tarde, o el usuario sale del hero
    // durante la intro, se completa de inmediato para que no se vea el titular
    // volando por la página ya scrolleada.
    const onEarlyScroll = (): void => {
      if (window.scrollY > window.innerHeight * 0.5) {
        intro.progress(1);
        window.removeEventListener('scroll', onEarlyScroll);
      }
    };
    window.addEventListener('scroll', onEarlyScroll, { passive: true });
    intro.eventCallback('onComplete', () => {
      window.removeEventListener('scroll', onEarlyScroll);
    });
  } else {
    finishIntroState();
  }

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
  // Solo el margen: NO hace falta forzar ningún min-height (ni acá ni en la
  // sección siguiente) para que el hero quede tapado. Lo que sincroniza el
  // barrido es este margen; al liberarse el pin, .hero-next y todo lo que
  // sigue son un flujo continuo y opaco (bg-white + z-10) por encima del
  // hero, así que no hay hueco que rellenar. Forzar altura solo reservaba
  // scroll de más y dejaba un vacío visible en la sección a la que se le
  // aplicara.
  const next = document.querySelector<HTMLElement>('.hero-next');
  const setOverlap = (): void => {
    if (next) gsap.set(next, { marginTop: -section.offsetHeight });
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
      onUpdate: (self) => {
        scrollProgress = self.progress;
      },
    },
  });

  // El titular "retrocede" al scrollear (efecto homy.framer.media): encoge y
  // pierde opacidad en el primer tramo (con clamp) mientras deriva hacia abajo
  // con parallax, y termina de disolverse cuando las nubes del barrido lo
  // alcanzan. Va sobre el WRAPPER para no chocar con el h1 de la intro.
  if (titleWrap) {
    tl.fromTo(
      titleWrap,
      { scale: 1, opacity: 1 },
      { scale: 0.88, opacity: 0.75, duration: 0.2, ease: 'power1.out' },
      0
    );
    tl.fromTo(
      titleWrap,
      { y: 0 },
      { y: 190, duration: SWEEP_START + 0.12, ease: 'none' },
      0
    );
    // Disolución entre las nubes que suben con la sección
    tl.to(titleWrap, { opacity: 0, duration: 0.16, ease: 'none' }, SWEEP_START - 0.02);
  }

  // hero__bottom (lede, CTA y card) retrocede con el mismo lenguaje que el
  // titular, con un leve desfase para dar profundidad. Anclado a su borde
  // inferior para que al encoger no se despegue del pie del viewport.
  // Va sobre el WRAPPER: la intro anima a sus hijos ([data-hero-reveal])
  // individualmente y compartir target crearía conflicto de transforms.
  if (bottom) {
    gsap.set(bottom, { transformOrigin: 'center bottom' });
    tl.fromTo(
      bottom,
      { scale: 1, opacity: 1 },
      { scale: 0.94, opacity: 0.75, duration: 0.22, ease: 'power1.out' },
      0.04
    );
    tl.fromTo(
      bottom,
      { y: 0 },
      { y: 120, duration: SWEEP_START, ease: 'none' },
      0
    );
  }

  // La hero-card no entra con la intro de carga: aparece scrubbed en el
  // primer tramo del pin (y se re-oculta al volver arriba). Sus props no
  // chocan con el wrapper hero__bottom, que anima en otro elemento.
  if (card) {
    tl.fromTo(
      card,
      { y: 64, autoAlpha: 0 },
      { y: 0, autoAlpha: 1, duration: 0.14, ease: 'power1.out' },
      0.05
    );
  }

  // La isla (visible desde la carga) deriva hacia abajo con parallax — más
  // lenta que el titular, para dar profundidad — y se disuelve entre las
  // nubes del barrido. yPercent en el wrapper: compone con el `y` px de la
  // intro y con la flotación (que va en el <img> hijo) sin conflictos.
  // La disolución va sobre el <img>: un .to() en timeline scrubeado captura
  // su valor inicial al CREARSE, y el wrapper estaba aún en opacity 0 por la
  // intro — quedaría como tween 0→0. El img siempre parte de opacity 1.
  if (island) {
    tl.fromTo(
      island,
      { yPercent: 0 },
      { yPercent: 14, duration: SWEEP_START + 0.12, ease: 'none' },
      0
    );
    if (islandImg) {
      tl.to(islandImg, { autoAlpha: 0, duration: 0.2, ease: 'none' }, SWEEP_START - 0.06);
    }
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
  // posiciones de los triggers creados antes (reveals, marquee…).
  // sort() primero: el orden de refresh por defecto es el de CREACIÓN, y los
  // triggers creados antes que este pin (módulos síncronos del bootstrap) se
  // medirían sin sumar su distancia de pin — sus start/end quedarían ~3800px
  // adelantados. Ordenados por posición en el documento, el offset del pin
  // se propaga correctamente a todo lo que está debajo.
  ScrollTrigger.sort();
  ScrollTrigger.refresh();
}
