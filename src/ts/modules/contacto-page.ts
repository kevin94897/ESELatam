/**
 * Página de Contacto (page-contacto.php, Figma 3941-8369).
 *
 * Dos comportamientos, ambos progresivos — la página funciona sin este
 * módulo, que solo agrega la capa de animación y el envío sin recarga:
 *
 *  1. Acordeón de preguntas frecuentes. El markup son `<details>` nativos:
 *     abrir/cerrar ya funciona solo. Acá se le suma la animación de altura
 *     (hay que retrasar el cierre real del `<details>` hasta que termine el
 *     tween, si no el navegador oculta el panel de golpe) y la regla de
 *     "una abierta a la vez".
 *
 *  2. Envío del formulario por fetch a admin-ajax (ver inc/contacto.php).
 *     Valida en el cliente lo mismo que valida el servidor —que es quien
 *     manda—, marca los campos con error y pinta el estado en la tarjeta.
 *     Sin JS el `<form>` postea normal y vuelve con `?contacto=ok|error`.
 */

import { gsap } from '../lib/gsap';

const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initContactoPage(root: HTMLElement): void {
  initFaq(root);
  initForm(root);
}

/* ------------------------------------------------------------------ */
/* Acordeón de FAQ                                                     */
/* ------------------------------------------------------------------ */

function initFaq(root: HTMLElement): void {
  const list = root.querySelector<HTMLElement>('[data-ctc-faq]');
  if (!list) return;

  const items = Array.from(list.querySelectorAll<HTMLDetailsElement>('[data-ctc-faq-item]'));
  if (!items.length) return;

  const panelOf = (item: HTMLDetailsElement): HTMLElement | null =>
    item.querySelector<HTMLElement>('[data-ctc-faq-panel]');

  const open = (item: HTMLDetailsElement): void => {
    const panel = panelOf(item);
    item.open = true;
    if (!panel || REDUCED) return;
    gsap.fromTo(
      panel,
      { height: 0, autoAlpha: 0 },
      { height: 'auto', autoAlpha: 1, duration: 0.5, ease: 'power3.out', overwrite: true }
    );
  };

  const close = (item: HTMLDetailsElement): void => {
    const panel = panelOf(item);
    if (!panel || REDUCED) {
      item.open = false;
      return;
    }
    gsap.to(panel, {
      height: 0,
      autoAlpha: 0,
      duration: 0.35,
      ease: 'power2.inOut',
      overwrite: true,
      // El `<details>` sigue abierto durante el tween: recién al terminar se
      // cierra de verdad y se limpian los estilos inline, para que la
      // próxima apertura vuelva a medir la altura real del contenido.
      onComplete: () => {
        item.open = false;
        gsap.set(panel, { clearProps: 'height,opacity,visibility' });
      },
    });
  };

  items.forEach((item) => {
    const summary = item.querySelector<HTMLElement>('summary');
    if (!summary) return;

    summary.addEventListener('click', (event) => {
      // El toggle nativo se hace acá a mano para poder animarlo.
      event.preventDefault();

      if (item.open) {
        close(item);
        return;
      }

      items.forEach((other) => {
        if (other !== item && other.open) close(other);
      });
      open(item);
    });
  });
}

/* ------------------------------------------------------------------ */
/* Formulario                                                          */
/* ------------------------------------------------------------------ */

const REQUERIDOS = ['nombre', 'email', 'telefono', 'pais', 'sector', 'mensaje'] as const;

interface RespuestaContacto {
  ok?: boolean;
  mensaje?: string;
  errores?: Record<string, string>;
}

function initForm(root: HTMLElement): void {
  const form = root.querySelector<HTMLFormElement>('[data-contacto-form]');
  if (!form) return;

  // OJO: `form.action` NO sirve acá. El formulario tiene un campo oculto
  // llamado `action` (lo exige admin-ajax) y el acceso por nombre a los
  // controles pisa la propiedad nativa: `form.action` devuelve ese <input>,
  // no la URL, y el fetch terminaba posteando contra la página actual.
  const endpoint = form.getAttribute('action') ?? '';

  const status = form.querySelector<HTMLElement>('[data-ctc-status]');
  const submit = form.querySelector<HTMLButtonElement>('[data-ctc-submit]');
  const label = form.querySelector<HTMLElement>('.ctc-submit__text');
  const labelDefault = label?.textContent ?? '';

  const control = (name: string): HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement | null =>
    form.querySelector(`[name="${name}"]`);

  const setError = (name: string, mensaje: string): void => {
    const el = control(name);
    const box = form.querySelector<HTMLElement>(`[data-ctc-error="${name}"]`);
    el?.closest('.ctc-field')?.classList.toggle('has-error', Boolean(mensaje));
    el?.setAttribute('aria-invalid', mensaje ? 'true' : 'false');
    if (!box) return;
    box.textContent = mensaje;
    box.hidden = !mensaje;
  };

  const clearErrors = (): void => {
    form.querySelectorAll<HTMLElement>('[data-ctc-error]').forEach((box) => {
      box.textContent = '';
      box.hidden = true;
    });
    form.querySelectorAll<HTMLElement>('.ctc-field').forEach((field) => field.classList.remove('has-error'));
  };

  const setStatus = (mensaje: string, ok: boolean): void => {
    if (!status) return;
    status.textContent = mensaje;
    status.classList.toggle('is-ok', ok);
    status.classList.toggle('is-error', !ok);
    status.classList.add('is-visible');
    if (!REDUCED) {
      gsap.fromTo(status, { y: 8, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.4, ease: 'power2.out' });
    }
  };

  // Al corregir un campo marcado, el error se limpia solo — no hay que
  // reenviar el formulario para que desaparezca.
  form.addEventListener('input', (event) => {
    const target = event.target as HTMLElement;
    const name = target.getAttribute('name');
    if (name && target.closest('.ctc-field.has-error')) setError(name, '');
  });

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    clearErrors();

    // Misma validación que el servidor: campos obligatorios + formato de
    // correo. El servidor la repite igual, esto solo evita el viaje.
    let primerError: string | null = null;
    REQUERIDOS.forEach((name) => {
      const el = control(name);
      if (!el || el.value.trim()) return;
      setError(name, 'Completa este campo.');
      primerError = primerError ?? name;
    });

    const email = control('email');
    if (!primerError && email && email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
      setError('email', 'Escribe un correo válido.');
      primerError = 'email';
    }

    if (primerError) {
      setStatus('Revisa los campos marcados.', false);
      control(primerError)?.focus();
      return;
    }

    const data = new FormData(form);
    data.append('ajax', '1');

    if (submit) submit.disabled = true;
    if (label) label.textContent = 'Enviando...';

    void fetch(endpoint, { method: 'POST', body: data, credentials: 'same-origin' })
      .then(async (response) => {
        const payload = (await response.json().catch(() => ({}))) as RespuestaContacto;

        if (payload.ok) {
          form.reset();
          setStatus(payload.mensaje ?? '¡Gracias! Recibimos tu mensaje.', true);
          return;
        }

        Object.entries(payload.errores ?? {}).forEach(([name, mensaje]) => setError(name, mensaje));
        setStatus(payload.mensaje ?? 'No pudimos enviar tu mensaje.', false);
      })
      .catch(() => {
        setStatus('No pudimos enviar tu mensaje. Revisa tu conexión e inténtalo de nuevo.', false);
      })
      .finally(() => {
        if (submit) submit.disabled = false;
        if (label) label.textContent = labelDefault;
      });
  });
}
