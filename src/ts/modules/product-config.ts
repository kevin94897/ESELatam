/**
 * Panel de configuración de la ficha de producto (single-producto.php).
 *
 * Dos controles, ambos sobre datos reales del CPT:
 *
 *  1. Color — los swatches del panel izquierdo y las miniaturas de la
 *     derecha son DOS vistas del mismo estado: comparten el atributo
 *     `data-producto-color` (índice dentro de `data-colors`), así que tocar
 *     cualquiera de los dos actualiza el otro y hace el crossfade de la foto
 *     principal.
 *  2. Litraje — marca la píldora activa y sincroniza el valor de la card
 *     "Volumen" de la sección de especificaciones.
 *
 * La flotación continua de la foto la sigue resolviendo initFloat()
 * (`[data-float]`), igual que en el banner de catálogo — acá solo se cambia
 * el `src`, nunca el transform del <img>, para no pisar ese tween.
 */

import { gsap } from '../lib/gsap';

interface ProductoColor {
  nombre: string;
  color: string;
  img: string;
}

export function initProductConfig(root: HTMLElement): void {
  let colors: ProductoColor[] = [];
  try {
    colors = JSON.parse(root.dataset.colors ?? '[]');
  } catch {
    colors = [];
  }

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const mainImg = root.querySelector<HTMLImageElement>('[data-producto-img]');
  const colorEls = Array.from(root.querySelectorAll<HTMLElement>('[data-producto-color]'));
  const litrajeEls = Array.from(root.querySelectorAll<HTMLElement>('[data-producto-litraje]'));
  // "Capacidad" en la barra de atributos del hero y la card "Volumen" de la
  // sección de especificaciones (fuera de `root`): todos los que haya.
  const volumenEls = Array.from(document.querySelectorAll<HTMLElement>('[data-producto-volumen]'));
  const colorNameEl = root.querySelector<HTMLElement>('[data-producto-color-name]');

  // ---------- Color ----------

  if (mainImg && colors.length > 0) {
    let activeColor = 0;
    let swapping = false;

    const selectColor = (index: number): void => {
      if (swapping || index === activeColor) return;
      const color = colors[index];
      if (!color) return;

      activeColor = index;
      // Swatches y miniaturas comparten el atributo: un solo recorrido deja
      // los dos grupos en el mismo estado.
      colorEls.forEach((el) => {
        el.classList.toggle('is-active', Number(el.dataset.productoColor) === index);
      });
      if (colorNameEl) colorNameEl.textContent = color.nombre;

      if (prefersReducedMotion) {
        mainImg.src = color.img;
        mainImg.alt = color.nombre;
        return;
      }

      swapping = true;
      gsap.to(mainImg, {
        autoAlpha: 0,
        scale: 0.94,
        duration: 0.22,
        ease: 'power2.in',
        onComplete: () => {
          mainImg.src = color.img;
          mainImg.alt = color.nombre;
          gsap.to(mainImg, {
            autoAlpha: 1,
            scale: 1,
            duration: 0.4,
            ease: 'back.out(1.5)',
            onComplete: () => {
              swapping = false;
            },
          });
        },
      });
    };

    colorEls.forEach((el) => {
      el.addEventListener('click', () => selectColor(Number(el.dataset.productoColor)));
    });
  }

  // ---------- Litraje ----------

  litrajeEls.forEach((el) => {
    el.addEventListener('click', () => {
      litrajeEls.forEach((other) => other.classList.toggle('is-active', other === el));
      const litraje = el.dataset.productoLitraje;
      if (litraje) {
        volumenEls.forEach((target) => {
          target.textContent = litraje;
        });
      }
    });
  });
}
