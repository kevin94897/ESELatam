/**
 * Página "Encuentra un distribuidor" (page-distribuidores.php, Figma
 * 3952-9273). Dos mejoras progresivas sobre una página que ya funciona
 * sola (el filtro corre en el servidor vía ?q= y ?pais=, y el mapa arranca
 * en el primer distribuidor):
 *
 *  1. Filtro en vivo: al escribir o cambiar de país se ocultan/muestran las
 *     tarjetas sin recargar, se actualiza el contador y se sincroniza la URL
 *     (replaceState) para que el enlace siga siendo compartible.
 *  2. El mapa sigue a la tarjeta activa: clic (o Enter/Espacio) en una
 *     tarjeta la marca y cambia el `src` del embed de Google Maps por la
 *     dirección de ese distribuidor. Los enlaces de la tarjeta (tel, mail,
 *     web, WhatsApp) siguen siendo enlaces normales.
 */

export function initDistribuidoresPage(root: HTMLElement): void {
  const form = root.querySelector<HTMLFormElement>('[data-dst-filters]');
  const input = root.querySelector<HTMLInputElement>('#dst-q');
  const select = root.querySelector<HTMLSelectElement>('#dst-pais');
  const items = Array.from(root.querySelectorAll<HTMLElement>('[data-dst-item]'));
  const count = root.querySelector<HTMLElement>('[data-dst-count]');
  const empty = root.querySelector<HTMLElement>('[data-dst-empty]');
  const reset = root.querySelector<HTMLAnchorElement>('[data-dst-reset]');
  const frame = root.querySelector<HTMLIFrameElement>('[data-dst-map-frame]');
  const cards = Array.from(root.querySelectorAll<HTMLElement>('[data-dst-card]'));

  /* ---------- Mapa que sigue a la tarjeta activa ---------- */

  const activate = (card: HTMLElement): void => {
    if (card.classList.contains('is-active')) return;
    cards.forEach((c) => c.classList.toggle('is-active', c === card));
    const query = card.dataset.dstMapa;
    if (frame && query) {
      frame.src = `https://www.google.com/maps?q=${encodeURIComponent(query)}&z=14&hl=es&output=embed`;
    }
  };

  cards.forEach((card) => {
    card.addEventListener('click', (event) => {
      // Un clic sobre un enlace de la tarjeta es para ese enlace, no para el mapa.
      if ((event.target as HTMLElement).closest('a')) return;
      activate(card);
    });
    card.addEventListener('keydown', (event) => {
      if (event.target !== card) return;
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        activate(card);
      }
    });
  });

  /* ---------- Filtro en vivo ---------- */

  if (!form || !input || !select) return;

  // Con JS el select ya no recarga: filtra acá. El atributo era el fallback sin JS.
  select.removeAttribute('onchange');

  const normalize = (value: string): string => value.trim().toLowerCase();

  const apply = (): void => {
    const q = normalize(input.value);
    const pais = select.value;
    let visibles = 0;
    let first: HTMLElement | null = null;

    items.forEach((item) => {
      const ok = (pais === '' || item.dataset.dstPais === pais)
        && (q === '' || (item.dataset.dstBuscar ?? '').includes(q));
      item.hidden = !ok;
      if (ok) {
        visibles++;
        if (!first) first = item.querySelector<HTMLElement>('[data-dst-card]');
      }
    });

    if (count) count.textContent = String(visibles);
    if (empty) empty.hidden = visibles > 0;

    // Si la tarjeta activa quedó oculta, el mapa salta a la primera visible.
    const active = cards.find((c) => c.classList.contains('is-active'));
    if (first && (!active || active.closest<HTMLElement>('[data-dst-item]')?.hidden)) {
      activate(first);
    }

    // URL compartible sin recargar.
    const params = new URLSearchParams();
    if (q) params.set('q', input.value.trim());
    if (pais) params.set('pais', pais);
    const search = params.toString();
    window.history.replaceState(null, '', `${window.location.pathname}${search ? `?${search}` : ''}`);
  };

  let timer = 0;
  input.addEventListener('input', () => {
    window.clearTimeout(timer);
    timer = window.setTimeout(apply, 120);
  });
  select.addEventListener('change', apply);
  form.addEventListener('submit', (event) => {
    event.preventDefault();
    apply();
  });

  reset?.addEventListener('click', (event) => {
    event.preventDefault();
    input.value = '';
    select.value = '';
    apply();
  });
}
