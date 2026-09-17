/**
 * Selector de color dentro de las tarjetas del carrusel de la portada
 * (front-page.php, sección "Productos").
 *
 * Cada tarjeta es una CAPACIDAD del producto y sus swatches
 * (`[data-card-color]`) son los colores que tienen foto para esa capacidad.
 * Al tocar uno cambia la foto de la tarjeta; la capacidad no se toca.
 *
 * Un solo listener delegado en el documento, no uno por swatch: el carrusel
 * duplica slides para cerrar el loop (product-carousel.ts) y el filtro por
 * producto los saca y los vuelve a poner (product-filter.ts), así que los
 * nodos van y vienen. Con delegación no hace falta re-enganchar nada.
 *
 * El cambio se aplica a la tarjeta tocada Y a sus copias del loop (mismo
 * `data-producto` + `data-capacidad`): si no, al girar el carrusel la copia
 * mostraría todavía el color anterior.
 */

interface ColorElegido {
  img: string;
  nombre: string;
}

/** Deja la tarjeta en el color `index`, sin tocar la capacidad. */
function pintar(card: HTMLElement, index: number, color: ColorElegido): void {
  card.querySelectorAll<HTMLElement>('[data-card-color]').forEach((swatch, i) => {
    const on = i === index;
    swatch.classList.toggle('is-active', on);
    swatch.setAttribute('aria-pressed', on ? 'true' : 'false');
  });

  const img = card.querySelector<HTMLImageElement>('[data-card-img]');
  if (!img || color.img === '') return;
  if (new URL(color.img, location.href).href === img.src) return;

  // La foto de cada color es un archivo aparte: asignando el src directo, el
  // <img> queda en blanco mientras baja. Se precarga y se cambia recién
  // cuando está lista, así la tarjeta nunca parpadea en vacío. El transform
  // del <img> no se toca — es de la flotación de float.ts.
  const previa = new Image();
  previa.decoding = 'async';
  previa.addEventListener('load', () => {
    img.src = color.img;
    img.alt = `${img.dataset.cardAlt ?? ''} ${color.nombre}`.trim();
  });
  previa.src = color.img;
}

export function initProductCardColors(): void {
  document.addEventListener('click', (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;

    const swatch = target.closest<HTMLElement>('[data-card-color]');
    if (!swatch || swatch.classList.contains('is-active')) return;

    const card = swatch.closest<HTMLElement>('.product-card');
    if (!card) return;

    const swatches = Array.from(card.querySelectorAll<HTMLElement>('[data-card-color]'));
    const index = swatches.indexOf(swatch);
    const color: ColorElegido = {
      img: swatch.dataset.colorImg ?? '',
      nombre: swatch.dataset.colorName ?? '',
    };

    const hermanas = Array.from(card.parentElement?.children ?? []).filter(
      (el): el is HTMLElement =>
        el instanceof HTMLElement &&
        el.dataset.producto === card.dataset.producto &&
        el.dataset.capacidad === card.dataset.capacidad
    );

    (hermanas.length > 0 ? hermanas : [card]).forEach((gemela) => pintar(gemela, index, color));
  });
}
