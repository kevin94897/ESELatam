/**
 * Selector de "Pruebas de rigurosidad" (single-producto.php, Figma 3694-6617).
 *
 * A diferencia de residuos-selector.ts, acá las 6 tarjetas ya muestran su
 * propio título+descripción completos (solo se atenúan cuando no están
 * activas — ver `.pruebas__card` en main.css): no hay un panel aparte cuyo
 * contenido haya que reemplazar. El clic solo mueve el estado `.is-active`
 * de una tarjeta a otra, tipo acordeón de una sola tarjeta "abierta" a la vez.
 *
 * El panel de video del centro es un placeholder decorativo por ahora (no
 * hay asset de video por prueba todavía), así que no reacciona al clic.
 */

export function initPruebasSelector(): void {
  const root = document.querySelector<HTMLElement>('[data-pruebas]');
  if (!root) return;

  const cards = Array.from(root.querySelectorAll<HTMLButtonElement>('[data-pruebas-card]'));
  if (!cards.length) return;

  cards.forEach((card) => {
    card.addEventListener('click', () => {
      if (card.classList.contains('is-active')) return;
      cards.forEach((c) => c.classList.toggle('is-active', c === card));
    });
  });
}
