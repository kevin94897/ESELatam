/**
 * Globo 3D interactivo de la sección Distribuidores.
 *
 * Textura realista de la Tierra (día + specular + normal, assets oficiales
 * de los ejemplos de Three.js) con tinte de marca aplicado vía luces de
 * color en vez de un shader propio — más simple y sostenible.
 *
 * Dos formas de interacción, que conviven:
 *  - Arrastre libre con el cursor/dedo: rota el globo como una esfera real,
 *    con inercia al soltar (decae hasta detenerse).
 *  - `focusCountry(slug)`, disparado desde la lista de países: gira el
 *    grupo con GSAP hasta que el país señalado queda de frente a cámara.
 * Cualquiera de las dos interrumpe a la otra.
 *
 * En reposo el globo no da la vuelta completa: hace un BARRIDO pendular
 * sobre la franja de longitudes donde están los países conectados (de
 * México a Uruguay, con margen), así el frente nunca se queda mostrando
 * océano o continentes sin marcadores. El barrido arranca unos segundos
 * después de la última interacción y se funde suavemente desde la
 * orientación en la que quedó el globo.
 */

import {
  Scene,
  PerspectiveCamera,
  WebGLRenderer,
  Color,
  Mesh,
  SphereGeometry,
  MeshPhongMaterial,
  MeshBasicMaterial,
  DirectionalLight,
  HemisphereLight,
  TextureLoader,
  Group,
  Vector3,
  Vector2,
  Quaternion,
  Clock,
  Raycaster,
} from 'three';
import { gsap } from '../lib/gsap';

// CAMERA_Z se eligió para que la esfera (RADIUS) quepa entera dentro del
// frustum del canvas con margen: el semi-ángulo aparente de la esfera
// (asin(RADIUS / CAMERA_Z)) debe quedar por debajo del semi-FOV de cámara,
// si no la esfera se recorta contra los bordes del canvas.
const RADIUS = 1.6;
// 4.9 → semi-ángulo aparente 19.1° frente al semi-FOV de 21°: la esfera
// llena ~91% del canvas (con 5.2 llenaba ~85%). Más cerca y se recorta.
const CAMERA_Z = 4.9;
const NAV_DURATION = 1.8;

// Barrido en reposo: la longitud de frente oscila (seno) entre los extremos
// de los países conectados ± margen. Con 22 s por ida y vuelta sobre ~60°
// la velocidad pico ronda 0.15 rad/s — el triple del giro continuo anterior
// (0.05 rad/s), que se sentía detenido. La latitud también respira un poco.
const IDLE_PERIOD = 22; // s por ciclo completo (ida y vuelta)
const IDLE_LNG_MARGIN = 2; // grados de aire más allá del país más al oeste / este
const IDLE_LAT_AMPLITUDE = 5; // grados de vaivén vertical
const IDLE_LAT_PERIOD = 9; // s
const IDLE_BLEND = 2.2; // 1/s — cuánto "persigue" el globo al barrido tras una interacción
const IDLE_RESUME_DELAY = 2.5; // s de espera tras soltar / tras llegar a un país
const DRAG_SENSITIVITY = 0.006; // rad por px arrastrado
const INERTIA_DAMPING_PER_SEC = 0.06; // fracción de velocidad que sobrevive cada segundo
const INERTIA_MIN_SPEED = 0.001; // rad/s por debajo del cual se detiene la inercia

const Y_AXIS = new Vector3(0, 1, 0);
const X_AXIS = new Vector3(1, 0, 0);

export interface GlobeCountry {
  slug: string;
  name: string;
  lat: number;
  lng: number;
}

export interface GlobeHandle {
  focusCountry: (slug: string) => void;
  destroy: () => void;
}

export interface GlobeTextures {
  map: string;
  specularMap: string;
  normalMap: string;
}

/** Convierte lat/lng a un punto sobre la esfera (convención estándar de mapeo equirectangular). */
function latLngToVector3(lat: number, lng: number, radius: number): Vector3 {
  const phi = (90 - lat) * (Math.PI / 180);
  const theta = (lng + 180) * (Math.PI / 180);
  return new Vector3(
    -radius * Math.sin(phi) * Math.cos(theta),
    radius * Math.cos(phi),
    radius * Math.sin(phi) * Math.sin(theta),
  );
}

/** Cuaternión absoluto que deja el punto `local` (fijo en espacio del grupo) mirando a +Z. */
function quaternionFacingCamera(local: Vector3): Quaternion {
  const rotY = Math.atan2(-local.x, local.z);
  const qY = new Quaternion().setFromAxisAngle(Y_AXIS, rotY);

  const rXZ = Math.sqrt(local.x * local.x + local.z * local.z);
  const rotX = Math.atan2(local.y, rXZ);
  const qX = new Quaternion().setFromAxisAngle(X_AXIS, rotX);

  return qX.multiply(qY); // aplica qY primero, luego qX
}

/** Lat/lng (grados) del punto del globo que hoy mira a cámara (+Z de mundo). */
function facingLatLng(groupQuat: Quaternion): { lat: number; lng: number } {
  const local = new Vector3(0, 0, 1).applyQuaternion(groupQuat.clone().invert()).normalize();
  const lat = 90 - (Math.acos(Math.max(-1, Math.min(1, local.y))) * 180) / Math.PI;
  let lng = (Math.atan2(local.z, -local.x) * 180) / Math.PI - 180;
  if (lng < -180) lng += 360;
  return { lat, lng };
}

export function initGlobeScene(
  container: HTMLElement,
  countries: GlobeCountry[],
  textures: GlobeTextures,
  defaultSlug?: string,
  onCountryClick?: (slug: string) => void,
): GlobeHandle {
  const scene = new Scene();

  const camera = new PerspectiveCamera(
    42,
    container.clientWidth / Math.max(container.clientHeight, 1),
    0.1,
    100,
  );
  camera.position.set(0, 0, CAMERA_Z);

  const renderer = new WebGLRenderer({ antialias: true, alpha: true });
  // Táctil: DPR 1.5 y esfera de 48 segmentos — mitad de píxeles a sombrear
  // en pantallas de DPR 3 sin diferencia visible en un globo de ~460px.
  const coarse = window.matchMedia('(pointer: coarse)').matches;
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, coarse ? 1.5 : 2));
  renderer.setSize(container.clientWidth, container.clientHeight);
  container.appendChild(renderer.domElement);

  const loader = new TextureLoader();
  const dayMap = loader.load(textures.map);
  const specularMap = loader.load(textures.specularMap);
  const normalMap = loader.load(textures.normalMap);

  const group = new Group();
  scene.add(group);

  const geometry = new SphereGeometry(RADIUS, coarse ? 48 : 64, coarse ? 48 : 64);
  const material = new MeshPhongMaterial({
    map: dayMap,
    specularMap,
    normalMap,
    // Relieve suave: la textura cartoon (tools/globo-cartoon.py) ya trae
    // el color plano; el normal map solo insinúa las cordilleras.
    normalScale: new Vector2(0.45, 0.45),
    specular: new Color(0x2a6a94),
    shininess: 16,
  });
  const globeMesh = new Mesh(geometry, material);
  group.add(globeMesh);

  // Tinte de marca vía luces de color en vez de shader propio, pensado para
  // que quede LUMINOSO: un hemisphere light (celeste arriba / navy abajo)
  // como base pareja en vez de un ambient plano y oscuro, más un key light
  // frontal blanco-celeste que da brillo sin generar un punto especular
  // duro (shininess bajo = reflejo amplio y suave, no un "glare").
  // Hemisferio casi neutro: con la textura cartoon, el navy de abajo que
  // usaba la textura realista teñía los verdes de turquesa.
  scene.add(new HemisphereLight(0xf4fbff, 0x7fb6e6, 1.7));
  const keyLight = new DirectionalLight(0xffffff, 1.5);
  keyLight.position.set(2.5, 2, 5);
  scene.add(keyLight);
  const rimLight = new DirectionalLight(0x8eb952, 0.5);
  rimLight.position.set(-4, 1, -2);
  scene.add(rimLight);

  // Marcadores: punto sólido + halo translúcido por país, hijos del grupo
  // para heredar su rotación junto con la esfera.
  const dots = new Map<string, Mesh>();
  const halos = new Map<string, Mesh>();
  const ACTIVE_COLOR = new Color(0x8eb952);
  const IDLE_COLOR = new Color(0xffffff);

  countries.forEach((c) => {
    const pos = latLngToVector3(c.lat, c.lng, RADIUS * 1.01);

    const dot = new Mesh(
      new SphereGeometry(0.026, 12, 12),
      new MeshBasicMaterial({ color: IDLE_COLOR.clone() }),
    );
    dot.position.copy(pos);
    dot.userData.slug = c.slug;
    group.add(dot);
    dots.set(c.slug, dot);

    // Radio más generoso que el punto visible: además de brillar en el país
    // activo, sirve de área de impacto más cómoda para el hover del tooltip.
    const halo = new Mesh(
      new SphereGeometry(0.065, 12, 12),
      new MeshBasicMaterial({ color: 0x8eb952, transparent: true, opacity: 0 }),
    );
    halo.position.copy(pos);
    halo.userData.slug = c.slug;
    group.add(halo);
    halos.set(c.slug, halo);
  });

  const setActiveMarker = (slug: string): void => {
    dots.forEach((dot, key) => {
      const isActive = key === slug;
      const mat = dot.material as MeshBasicMaterial;
      const targetColor = isActive ? ACTIVE_COLOR : IDLE_COLOR;
      gsap.to(mat.color, { r: targetColor.r, g: targetColor.g, b: targetColor.b, duration: 0.4, overwrite: true });
      gsap.to(dot.scale, {
        x: isActive ? 1.7 : 1,
        y: isActive ? 1.7 : 1,
        z: isActive ? 1.7 : 1,
        duration: 0.5,
        ease: 'back.out(2)',
        overwrite: true,
      });
    });
    halos.forEach((halo, key) => {
      const mat = halo.material as MeshBasicMaterial;
      gsap.to(mat, { opacity: key === slug ? 0.45 : 0, duration: 0.4, overwrite: true });
    });
  };

  const navProxy = { t: 0 };
  let startQuat = group.quaternion.clone();
  // true mientras el tween de focusCountry manda sobre el quaternion: en ese
  // lapso el giro automático se detiene (si no, el slerp lo pisaría cada
  // cuadro y el país quedaría "temblando").
  let navigating = false;
  const autoSpin = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ---------- Barrido en reposo ----------
  // Franja de longitudes/latitudes que cubre a todos los países conectados.
  const lngs = countries.map((c) => c.lng);
  const lats = countries.map((c) => c.lat);
  const idleLngMin = Math.min(...lngs) - IDLE_LNG_MARGIN;
  const idleLngMax = Math.max(...lngs) + IDLE_LNG_MARGIN;
  const idleLngCenter = (idleLngMin + idleLngMax) / 2;
  const idleLngHalf = (idleLngMax - idleLngMin) / 2;
  const idleLatCenter = (Math.min(...lats) + Math.max(...lats)) / 2;
  let idleTime = 0; // s dentro del ciclo del seno
  let idlePhase = 0; // fase inicial, alineada con la orientación al retomar
  let idleWait = 0; // s que faltan para retomar el barrido
  let idleArmed = false; // true una vez alineada la fase tras la última interacción
  const idleTarget = new Quaternion();

  // Pausa el barrido y deja programada su vuelta, alineada con donde quede el globo.
  const pauseIdle = (delay = IDLE_RESUME_DELAY): void => {
    idleWait = delay;
    idleArmed = false;
  };

  // Elige la fase del seno para que lng(t) arranque en la longitud que hoy
  // mira a cámara (acotada a la franja) y avance hacia el extremo más lejano
  // — así la retoma es continua, sin salto.
  const armIdle = (): void => {
    const { lng } = facingLatLng(group.quaternion);
    const u = Math.max(-1, Math.min(1, (lng - idleLngCenter) / idleLngHalf));
    idlePhase = u > 0 ? Math.PI - Math.asin(u) : Math.asin(u);
    idleTime = 0;
    idleArmed = true;
  };

  // Velocidad angular (rad/s) de la inercia tras soltar el arrastre —
  // consultada y decaída cuadro a cuadro en el loop de render.
  const inertia = { x: 0, y: 0 };

  const focusCountry = (slug: string): void => {
    const country = countries.find((c) => c.slug === slug);
    if (!country) return;

    setActiveMarker(slug);

    // El clic en la lista tiene prioridad: corta cualquier arrastre/inercia
    // en curso para que no compitan por el quaternion del grupo.
    inertia.x = 0;
    inertia.y = 0;

    const targetQuat = quaternionFacingCamera(latLngToVector3(country.lat, country.lng, 1));
    startQuat = group.quaternion.clone();
    navProxy.t = 0;

    gsap.killTweensOf(navProxy);
    navigating = true;
    gsap.to(navProxy, {
      t: 1,
      duration: NAV_DURATION,
      ease: 'power2.inOut',
      onUpdate: () => group.quaternion.slerpQuaternions(startQuat, targetQuat, navProxy.t),
      // onInterrupt cubre el killTweensOf de un arrastre o de otra selección.
      onComplete: () => { navigating = false; pauseIdle(); },
      onInterrupt: () => { navigating = false; },
    });
    pauseIdle(NAV_DURATION + IDLE_RESUME_DELAY);
  };

  // Orientación inicial: sin animación, ya mirando al país activo por defecto.
  const initialSlug = defaultSlug ?? countries[0]?.slug;
  if (initialSlug) {
    const initial = countries.find((c) => c.slug === initialSlug);
    if (initial) {
      group.quaternion.copy(quaternionFacingCamera(latLngToVector3(initial.lat, initial.lng, 1)));
      setActiveMarker(initialSlug);
    }
  }

  // ---------- Arrastre libre (trackball) + inercia ----------
  const canvasEl = renderer.domElement;
  canvasEl.style.touchAction = 'none';
  canvasEl.style.cursor = 'grab';

  let dragging = false;
  let lastX = 0;
  let lastY = 0;
  let lastMoveTime = 0;
  let downX = 0;
  let downY = 0;
  const CLICK_MOVE_THRESHOLD = 6; // px — por debajo de esto, un pointerdown+up cuenta como clic, no arrastre

  // ---------- Tooltip al hacer hover sobre un marcador ----------
  const tooltipEl = document.createElement('div');
  tooltipEl.className = 'globe-tooltip';
  tooltipEl.setAttribute('role', 'status');
  container.appendChild(tooltipEl);

  const raycaster = new Raycaster();
  const pointerNdc = new Vector2();
  const markerWorldPos = new Vector3();
  const haloMeshes = Array.from(halos.values());
  let hoveredSlug: string | null = null;

  const slugAtClientPoint = (clientX: number, clientY: number): string | null => {
    const rect = canvasEl.getBoundingClientRect();
    pointerNdc.x = ((clientX - rect.left) / rect.width) * 2 - 1;
    pointerNdc.y = -((clientY - rect.top) / rect.height) * 2 + 1;

    raycaster.setFromCamera(pointerNdc, camera);
    const hit = raycaster.intersectObjects(haloMeshes, false)[0];
    return (hit?.object.userData.slug as string | undefined) ?? null;
  };

  const updateTooltip = (clientX: number, clientY: number): void => {
    const slug = slugAtClientPoint(clientX, clientY);

    if (slug !== hoveredSlug) {
      hoveredSlug = slug;
      canvasEl.style.cursor = slug ? 'pointer' : 'grab';
      if (slug) {
        const country = countries.find((c) => c.slug === slug);
        tooltipEl.textContent = country?.name ?? slug;
        tooltipEl.classList.add('is-visible');
      } else {
        tooltipEl.classList.remove('is-visible');
      }
    }

    if (slug) {
      const rect = canvasEl.getBoundingClientRect();
      const marker = dots.get(slug);
      marker?.getWorldPosition(markerWorldPos);
      const ndc = markerWorldPos.clone().project(camera);
      const px = (ndc.x * 0.5 + 0.5) * rect.width;
      const py = (-ndc.y * 0.5 + 0.5) * rect.height;
      tooltipEl.style.transform = `translate(${px}px, ${py}px) translate(-50%, -140%)`;
    }
  };

  const clearTooltip = (): void => {
    hoveredSlug = null;
    tooltipEl.classList.remove('is-visible');
    // No pisar el cursor 'grabbing': esto también se llama al iniciar un
    // arrastre (justo después de fijarlo), donde debe quedar como está.
    if (!dragging) canvasEl.style.cursor = 'grab';
  };

  const applyRotation = (deltaX: number, deltaY: number): void => {
    // Ejes de MUNDO (no locales): así arrastrar siempre gira "hacia donde
    // apunta el cursor" sin importar la orientación actual del globo.
    const qYaw = new Quaternion().setFromAxisAngle(Y_AXIS, deltaX * DRAG_SENSITIVITY);
    const qPitch = new Quaternion().setFromAxisAngle(X_AXIS, deltaY * DRAG_SENSITIVITY);
    group.quaternion.premultiply(qYaw).premultiply(qPitch);
  };

  const onPointerDown = (event: PointerEvent): void => {
    dragging = true;
    lastX = event.clientX;
    lastY = event.clientY;
    downX = event.clientX;
    downY = event.clientY;
    lastMoveTime = performance.now();
    inertia.x = 0;
    inertia.y = 0;
    gsap.killTweensOf(navProxy); // el arrastre interrumpe una navegación en curso
    pauseIdle();
    canvasEl.style.cursor = 'grabbing';
    canvasEl.setPointerCapture(event.pointerId);
    clearTooltip(); // arrastrando no tiene sentido mostrar el hint de hover
  };

  const onPointerMove = (event: PointerEvent): void => {
    if (!dragging) {
      updateTooltip(event.clientX, event.clientY);
      return;
    }
    const now = performance.now();
    const dt = Math.max((now - lastMoveTime) / 1000, 1 / 120);
    const dx = event.clientX - lastX;
    const dy = event.clientY - lastY;

    applyRotation(dx, dy);

    // Velocidad instantánea, para que la inercia siga con el mismo "impulso"
    // que traía el gesto justo antes de soltar.
    inertia.y = dx * DRAG_SENSITIVITY / dt;
    inertia.x = dy * DRAG_SENSITIVITY / dt;

    lastX = event.clientX;
    lastY = event.clientY;
    lastMoveTime = now;
  };

  const endDrag = (event: PointerEvent): void => {
    if (!dragging) return;
    dragging = false;
    pauseIdle();
    canvasEl.style.cursor = 'grab';
    if (canvasEl.hasPointerCapture(event.pointerId)) {
      canvasEl.releasePointerCapture(event.pointerId);
    }
  };

  const onPointerUp = (event: PointerEvent): void => {
    if (!dragging) return;
    // Si el gesto apenas se movió, es un clic (no un arrastre) — si cayó
    // sobre un marcador, se comporta igual que elegirlo en la lista.
    const moved = Math.hypot(event.clientX - downX, event.clientY - downY);
    endDrag(event);
    if (moved > CLICK_MOVE_THRESHOLD || !onCountryClick) return;

    const slug = slugAtClientPoint(event.clientX, event.clientY);
    if (slug) onCountryClick(slug);
  };

  canvasEl.addEventListener('pointerdown', onPointerDown);
  canvasEl.addEventListener('pointermove', onPointerMove);
  canvasEl.addEventListener('pointerup', onPointerUp);
  canvasEl.addEventListener('pointercancel', endDrag);
  canvasEl.addEventListener('pointerleave', clearTooltip);

  // Sin dibujar mientras la sección está fuera de pantalla (o la pestaña
  // oculta): el loop sigue barato (solo actualiza el quaternion) y el GPU
  // descansa — en móvil es lo que más batería consumía de toda la página.
  let isVisible = true;
  let inViewport = true;
  const syncVisible = (): void => { isVisible = inViewport && document.visibilityState === 'visible'; };
  if (typeof IntersectionObserver === 'function') {
    new IntersectionObserver((entries) => {
      inViewport = entries.some((e) => e.isIntersecting);
      syncVisible();
    }, { rootMargin: '120px' }).observe(container);
  }
  document.addEventListener('visibilitychange', syncVisible);

  let rafId = 0;
  const clock = new Clock();
  const render = (): void => {
    const dt = clock.getDelta();

    if (!dragging && (Math.abs(inertia.x) > INERTIA_MIN_SPEED || Math.abs(inertia.y) > INERTIA_MIN_SPEED)) {
      applyRotation((inertia.y / DRAG_SENSITIVITY) * dt, (inertia.x / DRAG_SENSITIVITY) * dt);
      const decay = Math.pow(INERTIA_DAMPING_PER_SEC, dt);
      inertia.x *= decay;
      inertia.y *= decay;
    }

    // Barrido pendular en reposo sobre la franja de países conectados. Tras
    // una interacción espera IDLE_RESUME_DELAY (y a que muera la inercia),
    // alinea la fase con la orientación actual y desde ahí "persigue" el
    // objetivo con un slerp suavizado — sin saltos ni tirones.
    const inertiaAlive = Math.abs(inertia.x) > INERTIA_MIN_SPEED || Math.abs(inertia.y) > INERTIA_MIN_SPEED;
    if (autoSpin && !dragging && !navigating && !inertiaAlive) {
      if (idleWait > 0) {
        idleWait -= dt;
      } else {
        if (!idleArmed) armIdle();
        idleTime += dt;
        const lng = idleLngCenter + idleLngHalf * Math.sin(idlePhase + (idleTime * 2 * Math.PI) / IDLE_PERIOD);
        const lat = idleLatCenter + IDLE_LAT_AMPLITUDE * Math.sin((idleTime * 2 * Math.PI) / IDLE_LAT_PERIOD);
        idleTarget.copy(quaternionFacingCamera(latLngToVector3(lat, lng, 1)));
        group.quaternion.slerp(idleTarget, 1 - Math.exp(-dt * IDLE_BLEND));
      }
    }

    if (isVisible) renderer.render(scene, camera);
    rafId = requestAnimationFrame(render);
  };
  render();

  const onResize = (): void => {
    const w = container.clientWidth;
    const h = container.clientHeight;
    if (!w || !h) return;
    renderer.setSize(w, h);
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
  };
  window.addEventListener('resize', onResize);

  const destroy = (): void => {
    cancelAnimationFrame(rafId);
    window.removeEventListener('resize', onResize);
    canvasEl.removeEventListener('pointerdown', onPointerDown);
    canvasEl.removeEventListener('pointermove', onPointerMove);
    canvasEl.removeEventListener('pointerup', onPointerUp);
    canvasEl.removeEventListener('pointercancel', endDrag);
    canvasEl.removeEventListener('pointerleave', clearTooltip);
    tooltipEl.remove();
    gsap.killTweensOf(navProxy);
    geometry.dispose();
    material.dispose();
    dayMap.dispose();
    specularMap.dispose();
    normalMap.dispose();
    dots.forEach((dot) => {
      dot.geometry.dispose();
      (dot.material as MeshBasicMaterial).dispose();
    });
    halos.forEach((halo) => {
      halo.geometry.dispose();
      (halo.material as MeshBasicMaterial).dispose();
    });
    renderer.dispose();
    container.removeChild(renderer.domElement);
  };

  return { focusCountry, destroy };
}
