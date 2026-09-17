/**
 * Botón "copiar enlace" de la barra lateral del artículo
 * (template-parts/articulo-aside.php, Figma 3873-10142).
 *
 * El aviso "Enlace copiado al portapapeles" ya viene en el HTML, oculto: acá
 * solo se muestra unos segundos.
 *
 * `navigator.clipboard` SOLO existe en contexto seguro, así que en http://
 * —el entorno local, y cualquier despliegue sin certificado— no está y hay
 * que caer a `execCommand('copy')` sobre un campo temporal. Ese campo tiene
 * que ser seleccionable de verdad: con `opacity: 0` o tamaño cero Chrome se
 * niega a copiar, de ahí el 1×1 fuera de pantalla. Si aun así falla, el
 * botón no miente: no aparece el aviso.
 */

const VISIBLE_MS = 2600;

async function copiar(texto: string): Promise<boolean> {
  try {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(texto);
      return true;
    }
  } catch {
    // Sigue al plan B.
  }

  const campo = document.createElement('textarea');
  campo.value = texto;
  campo.setAttribute('readonly', '');
  campo.style.cssText =
    'position:fixed;top:0;left:0;width:1px;height:1px;padding:0;border:0;outline:0;box-shadow:none;background:transparent;';

  try {
    document.body.appendChild(campo);
    campo.focus();
    campo.select();
    campo.setSelectionRange(0, texto.length); // iOS ignora select() a secas.
    return document.execCommand('copy');
  } catch {
    return false;
  } finally {
    campo.remove();
  }
}

export function initArticuloShare(root: HTMLElement): void {
  const boton = root.querySelector<HTMLButtonElement>('[data-art-copy]');
  if (!boton) return;

  const aviso = root.querySelector<HTMLElement>('[data-art-copiado]');
  let timer = 0;

  boton.addEventListener('click', () => {
    void (async () => {
      const ok = await copiar(boton.dataset.artCopy ?? window.location.href);
      if (!ok || !aviso) return;

      aviso.hidden = false;
      window.clearTimeout(timer);
      timer = window.setTimeout(() => {
        aviso.hidden = true;
      }, VISIBLE_MS);
    })();
  });
}
