/**
 * Orquesta la sección Distribuidores: lee los países desde el DOM (marcado
 * en front-page.php), monta el globo 3D (globe-scene.ts) y conecta la lista
 * de países de la derecha con él — en ambos sentidos.
 *
 * La lista se comporta como un acordeón de un solo panel: al elegir un país
 * (desde la lista, o haciendo clic en su marcador sobre el globo), ese pasa
 * a ser el activo/expandido (los demás se cierran), el panel scrolleable se
 * centra automáticamente sobre él, y el globo gira y señala ese país.
 */

import type { GlobeCountry, GlobeHandle } from './globe-scene';
import { gsap } from '../lib/gsap';

// Debe igualar la duración de `transition: grid-template-rows` en
// `.distributor-card` (main.css) — se espera a que el acordeón (la tarjeta
// que se abre Y la que se cierra) asiente su layout antes de medir dónde
// centrar el scroll; medir a mitad de esa transición da una posición
// todavía afectada por la altura vieja de la tarjeta que se está cerrando.
const ACCORDION_TRANSITION_MS = 500;

/** Centra `card` verticalmente dentro de `wrap` (el panel scrolleable). */
function centerCardInWrap(card: HTMLElement, wrap: HTMLElement): void {
  const wrapRect = wrap.getBoundingClientRect();
  const cardRect = card.getBoundingClientRect();
  const cardTopWithinWrap = cardRect.top - wrapRect.top + wrap.scrollTop;
  const targetCenter = cardTopWithinWrap + cardRect.height / 2;

  const maxScrollTop = Math.max(0, wrap.scrollHeight - wrap.clientHeight);
  const targetScrollTop = Math.max(0, Math.min(targetCenter - wrap.clientHeight / 2, maxScrollTop));

  gsap.to(wrap, { scrollTop: targetScrollTop, duration: 0.6, ease: 'power2.inOut', overwrite: true });
}

export function initDistribuidores(section: HTMLElement): void {
  const canvasHost = section.querySelector<HTMLElement>('[data-globe-canvas]');
  const listWrap = section.querySelector<HTMLElement>('.distribuidores__list-wrap');
  const cards = Array.from(section.querySelectorAll<HTMLButtonElement>('[data-country]'));
  if (!canvasHost || !listWrap || !cards.length) return;

  const countries: GlobeCountry[] = cards.map((card) => ({
    slug: card.dataset.countrySlug ?? '',
    name: card.querySelector('.distributor-card__name')?.textContent?.trim() ?? card.dataset.countrySlug ?? '',
    lat: Number(card.dataset.lat),
    lng: Number(card.dataset.lng),
  }));

  const defaultSlug = cards.find((c) => c.classList.contains('is-active'))?.dataset.countrySlug;

  void import('./globe-scene').then(({ initGlobeScene }) => {
    let handle: GlobeHandle;

    // Punto único de selección: lo dispara tanto un clic en la lista como
    // un clic en el marcador del país sobre el globo.
    const selectCountry = (slug: string): void => {
      const card = cards.find((c) => c.dataset.countrySlug === slug);
      if (!card || card.classList.contains('is-active')) return;

      cards.forEach((c) => c.classList.toggle('is-active', c === card));
      handle.focusCountry(slug); // el globo reacciona de inmediato

      // El centrado espera a que el acordeón (abrir + cerrar el anterior)
      // asiente su layout — si no, mide contra una altura todavía vieja.
      window.setTimeout(() => centerCardInWrap(card, listWrap), ACCORDION_TRANSITION_MS);
    };

    handle = initGlobeScene(
      canvasHost,
      countries,
      {
        map: canvasHost.dataset.earthMap ?? '',
        specularMap: canvasHost.dataset.earthSpecular ?? '',
        normalMap: canvasHost.dataset.earthNormal ?? '',
      },
      defaultSlug,
      selectCountry,
    );

    cards.forEach((card) => {
      card.addEventListener('click', () => {
        const slug = card.dataset.countrySlug;
        if (slug) selectCountry(slug);
      });
    });
  });
}
