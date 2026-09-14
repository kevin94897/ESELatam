/**
 * Submenú desplegable del nav-pill (`.menu-item-has-children`, hoy
 * "Sectores"). Con mouse lo abre/cierra el :hover de CSS (main.css); este
 * módulo cubre lo que CSS no puede:
 *
 *  - aria-expanded sincronizado para lectores de pantalla.
 *  - Táctil sin hover (tablet ≥64rem, donde el pill sí se muestra): el
 *    primer toque abre el panel, el segundo navega.
 *  - Teclado: ArrowDown sobre el link abre y entra al primer ítem; Esc y
 *    perder el foco lo cierran; click fuera también.
 */

export function initNavSubmenu(): void {
  const parents = Array.from(
    document.querySelectorAll<HTMLElement>('.nav-pill__list .menu-item-has-children')
  );
  if (!parents.length) return;

  const hoverable = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  const setOpen = (item: HTMLElement, open: boolean): void => {
    item.classList.toggle('is-open', open);
    item.querySelector<HTMLElement>(':scope > a')?.setAttribute('aria-expanded', String(open));
  };

  const closeAll = (except?: HTMLElement): void => {
    parents.forEach((item) => {
      if (item !== except) setOpen(item, false);
    });
  };

  parents.forEach((item) => {
    const link = item.querySelector<HTMLAnchorElement>(':scope > a');
    if (!link) return;

    link.setAttribute('aria-haspopup', 'true');
    if (!link.hasAttribute('aria-expanded')) link.setAttribute('aria-expanded', 'false');

    // Sin hover real, el link padre necesita dos toques: abrir, luego ir.
    link.addEventListener('click', (event) => {
      if (hoverable || item.classList.contains('is-open')) return;
      event.preventDefault();
      closeAll(item);
      setOpen(item, true);
    });

    link.addEventListener('keydown', (event) => {
      if (event.key !== 'ArrowDown') return;
      event.preventDefault();
      setOpen(item, true);
      item.querySelector<HTMLElement>('.sub-menu a')?.focus();
    });

    // Con mouse el :hover ya lo muestra; acá solo se refleja en aria.
    if (hoverable) {
      item.addEventListener('mouseenter', () => setOpen(item, true));
      item.addEventListener('mouseleave', () => setOpen(item, false));
    }

    item.addEventListener('focusout', (event) => {
      const next = event.relatedTarget as Node | null;
      if (!next || !item.contains(next)) setOpen(item, false);
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeAll();
  });

  document.addEventListener('pointerdown', (event) => {
    const target = event.target as Node;
    parents.forEach((item) => {
      if (!item.contains(target)) setOpen(item, false);
    });
  });
}
