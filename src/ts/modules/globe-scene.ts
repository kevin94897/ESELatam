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
const CAMERA_Z = 5.2;
const NAV_DURATION = 1.8;

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
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setSize(container.clientWidth, container.clientHeight);
  container.appendChild(renderer.domElement);

  const loader = new TextureLoader();
  const dayMap = loader.load(textures.map);
  const specularMap = loader.load(textures.specularMap);
  const normalMap = loader.load(textures.normalMap);

  const group = new Group();
  scene.add(group);

  const geometry = new SphereGeometry(RADIUS, 64, 64);
  const material = new MeshPhongMaterial({
    map: dayMap,
    specularMap,
    normalMap,
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
  scene.add(new HemisphereLight(0xe4f4ff, 0x3a72c4, 2.6));
  const keyLight = new DirectionalLight(0xffffff, 2.1);
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
    gsap.to(navProxy, {
      t: 1,
      duration: NAV_DURATION,
      ease: 'power2.inOut',
      onUpdate: () => group.quaternion.slerpQuaternions(startQuat, targetQuat, navProxy.t),
    });
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

    renderer.render(scene, camera);
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
