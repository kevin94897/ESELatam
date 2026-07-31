/**
 * Escena Three.js base.
 * Se monta sobre cualquier elemento `data-three-scene`.
 * Se carga de forma perezosa desde main.ts para no pesar en páginas sin 3D.
 */

import {
  Scene,
  PerspectiveCamera,
  WebGLRenderer,
  Color,
  Mesh,
  IcosahedronGeometry,
  MeshStandardMaterial,
  DirectionalLight,
  AmbientLight,
  Clock,
} from 'three';

// Colores del design system consolidado:
// fondo Primary (Azul Marino) + mesh Secondary (Azul Cielo)
const BG_COLOR = 0x001e61;   // --color-primary
const MESH_COLOR = 0x0091d1; // --color-secondary

export function initThreeScene(container: HTMLElement): () => void {
  const scene = new Scene();
  scene.background = new Color(BG_COLOR);

  const camera = new PerspectiveCamera(
    45,
    container.clientWidth / container.clientHeight,
    0.1,
    100,
  );
  camera.position.set(0, 0, 5);

  const renderer = new WebGLRenderer({ antialias: true, alpha: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setSize(container.clientWidth, container.clientHeight);
  container.appendChild(renderer.domElement);

  const geometry = new IcosahedronGeometry(1.4, 1);
  const material = new MeshStandardMaterial({
    color: MESH_COLOR,
    roughness: 0.4,
    metalness: 0.2,
    flatShading: true,
  });
  const mesh = new Mesh(geometry, material);
  scene.add(mesh);

  const dirLight = new DirectionalLight(0xffffff, 1);
  dirLight.position.set(3, 3, 4);
  scene.add(dirLight);
  scene.add(new AmbientLight(0xffffff, 0.4));

  const clock = new Clock();
  let rafId = 0;

  const tick = (): void => {
    const dt = clock.getDelta();
    mesh.rotation.x += dt * 0.2;
    mesh.rotation.y += dt * 0.3;
    renderer.render(scene, camera);
    rafId = requestAnimationFrame(tick);
  };
  tick();

  const onResize = (): void => {
    const w = container.clientWidth;
    const h = container.clientHeight;
    renderer.setSize(w, h);
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
  };
  window.addEventListener('resize', onResize);

  // Función de cleanup por si se necesita destruir la escena
  return () => {
    cancelAnimationFrame(rafId);
    window.removeEventListener('resize', onResize);
    geometry.dispose();
    material.dispose();
    renderer.dispose();
    container.removeChild(renderer.domElement);
  };
}
