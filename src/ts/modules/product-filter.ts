/**
 * Selector de producto del carrusel de la portada (front-page.php, sección
 * "Productos").
 *
 * Cada pastilla (`[data-producto-filtro]`) es uno de los productos
 * destacados, y las tarjetas del slider son sus VARIANTES: un slide por cada
 * par color × capacidad con foto, marcado con `data-producto`. Elegir una
 * pastilla deja en el carrusel solo las variantes de ese producto.
 *
 * Los slides del resto SALEN del DOM en vez de ocultarse con CSS: el
 * coverflow reparte TODOS los slides en el abanico y Swiper los mide para
 * centrar y para el loop, así que unos cuantos ocultos dentro del wrapper
 * desplazarían el centro y dejarían huecos en la navegación. Por eso el
 * módulo guarda la lista completa aparte, escribe en el wrapper solo el
 * subconjunto activo y vuelve a montar el carrusel (el número de slides
 * cambia los clones del loop y cuántas vecinas se ven, que
 * product-carousel.ts calcula al montar).
 *
 * Sin JS no se filtra nada y quedan todas las variantes de todos los
 * productos a la vista: el mismo contenido, sin recortar.
 */

import { initFloat } from './float';
import { mountProductCarousel, unmountProductCarousel } from './product-carousel';

function initFilterBar(bar: HTMLElement): void {
  const root =
    bar.closest<HTMLElement>('[data-carousel-root]') ?? bar.closest<HTMLElement>('.productos');
  const carousel = root?.querySelector<HTMLElement>('[data-product-carousel]');
  const wrapper = carousel?.querySelector<HTMLElement>('.swiper-wrapper');
  if (!carousel || !wrapper) return;

  const buttons = Array.from(bar.querySelectorAll<HTMLElement>('[data-producto-filtro]'));
  if (buttons.length === 0) return;

  // La lista completa se toma antes de montar Swiper: después el wrapper
  // puede tener copias para el loop (`[data-carousel-clone]`), que no son
  // slides reales y no deben entrar acá.
  const slides = Array.from(wrapper.children) as HTMLElement[];

  /**
   * @param mount false en el arranque: el wrapper se deja con el subconjunto
   *   activo y el montaje lo hace initProductCarousel() (ver main.ts), sin
   *   crear y destruir una instancia en vano.
   */
  const apply = (slug: string, mount: boolean): void => {
    const match = slides.filter((slide) => slide.dataset.producto === slug);
    // Un producto sin tarjetas no debería existir (siempre tiene al menos la
    // variante de reserva), pero si llegara a pasar es mejor el set completo
    // que un carrusel vacío.
    const next = match.length > 0 ? match : slides;

    if (mount) unmountProductCarousel(carousel);
    wrapper.replaceChildren(...next);
    if (!mount) return;

    mountProductCarousel(carousel);
    // Las copias del loop son nodos nuevos: les toca su propia flotación.
    initFloat(wrapper);
  };

  buttons.forEach((button) => {
    button.addEventListener('click', () => {
      if (button.classList.contains('is-active')) return;

      buttons.forEach((other) => {
        const on = other === button;
        other.classList.toggle('is-active', on);
        other.setAttribute('aria-selected', on ? 'true' : 'false');
      });

      apply(button.dataset.productoFiltro ?? '', true);
    });
  });

  // Estado inicial: el que el PHP ya dejó marcado, para no arrancar
  // desincronizado del HTML servido.
  const active = buttons.find((button) => button.classList.contains('is-active')) ?? buttons[0];
  apply(active.dataset.productoFiltro ?? '', false);
}

export function initProductFilter(): void {
  document.querySelectorAll<HTMLElement>('[data-producto-filtros]').forEach(initFilterBar);
}
