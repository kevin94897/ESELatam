/**
 * Orquesta la sección Distribuidores: lee los países desde el DOM (marcado
 * en front-page.php), monta el globo 3D (globe-scene.ts) y conecta el riel
 * de países con él — en ambos sentidos.
 *
 * Elegir un país (desde el riel, o haciendo clic en su marcador sobre el
 * globo) marca su pastilla, hace crossfade a su panel y gira el globo hacia
 * él. Los paneles ya están todos en el DOM, apilados en la misma celda de
 * grilla (ver .distribuidores__panel-stack en main.css): acá solo se alterna
 * la clase — el fundido y el alto estable los resuelve el CSS, sin medir ni
 * esperar transiciones desde JS.
 */

import type { GlobeCountry, GlobeHandle } from './globe-scene';

export function initDistribuidores(section: HTMLElement): void {
  const canvasHost = section.querySelector<HTMLElement>('[data-globe-canvas]');
  const pills = Array.from(section.querySelectorAll<HTMLButtonElement>('[data-country]'));
  const panels = Array.from(section.querySelectorAll<HTMLElement>('[data-country-panel]'));
  if (!canvasHost || !pills.length) return;

  const countries: GlobeCountry[] = pills.map((pill) => ({
    slug: pill.dataset.countrySlug ?? '',
    name: pill.querySelector('.country-pill__name')?.textContent?.trim() ?? pill.dataset.countrySlug ?? '',
    lat: Number(pill.dataset.lat),
    lng: Number(pill.dataset.lng),
  }));

  const defaultSlug = pills.find((p) => p.classList.contains('is-active'))?.dataset.countrySlug;

  void import('./globe-scene').then(({ initGlobeScene }) => {
    let handle: GlobeHandle;

    // Punto único de selección: lo dispara tanto un clic en el riel como un
    // clic en el marcador del país sobre el globo.
    const selectCountry = (slug: string): void => {
      const pill = pills.find((p) => p.dataset.countrySlug === slug);
      if (!pill || pill.classList.contains('is-active')) return;

      pills.forEach((p) => {
        const isActive = p === pill;
        p.classList.toggle('is-active', isActive);
        p.setAttribute('aria-selected', String(isActive));
      });

      panels.forEach((panel) => {
        panel.classList.toggle('is-active', panel.dataset.countryPanel === slug);
      });

      handle.focusCountry(slug);
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

    pills.forEach((pill) => {
      pill.addEventListener('click', () => {
        const slug = pill.dataset.countrySlug;
        if (slug) selectCountry(slug);
      });
    });
  });
}
