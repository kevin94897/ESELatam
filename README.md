# ESE Latam — WordPress Theme

Theme corporativo de ESE Latam, alineado 1:1 al design system del Figma (`ESE-LATAM-WEB`).

## Stack

- **WordPress** (theme clásico PHP 8.1+)
- **Vite 5** como build tool (`base: ''` relativo — requerido para que los chunks dinámicos resuelvan dentro de `/wp-content/themes/`)
- **TypeScript** en modo estricto
- **Tailwind CSS v4** con tokens ESE Latam en `@theme`
- **GSAP** (+ ScrollTrigger) para animaciones de scroll
- **Lenis** para smooth scroll inercial (sincronizado con el ticker de GSAP)
- **Embla Carousel** para sliders
- **Three.js** para 3D (carga perezosa)
- **Plus Jakarta Sans** como tipografía oficial

## Estructura

```
ese-latam/
├── style.css              # Header requerido por WordPress
├── functions.php          # Bootstrap del theme
├── index.php              # Fallback template
├── front-page.php         # Home: hero animado + sección Sectores (slider)
├── page-nosotros.php      # Página Nosotros (Figma 3441-370), ver abajo
├── page-contacto.php      # Página Contacto (Figma 3941-8369), ver abajo
├── header.php / footer.php
├── inc/
│   ├── setup.php          # Supports, menús (+ fallback del menú), sidebars
│   ├── contacto.php       # Datos de contacto, página /contacto y envío del form
│   └── enqueue.php        # Integración con manifest de Vite
├── assets/
│   ├── icons/             # SVGs exportados del Figma (CTA, chevron, search)
│   ├── imgs/              # Logo, isla, nubes, fotos de sectores
│   └── video/             # Video del hero (codificado all-intra, ver abajo)
├── src/
│   ├── css/main.css       # Tailwind v4 + tokens + componentes (hero, nav, slider…)
│   └── ts/
│       ├── main.ts        # Entry point
│       ├── lib/gsap.ts    # Setup central de GSAP
│       └── modules/
│           ├── hero-scroll.ts    # Coreografía del hero (pin + scrub)
│           ├── smooth-scroll.ts  # Lenis + integración ScrollTrigger
│           ├── header.ts         # Header fijo: scrolled + menú móvil
│           ├── slider.ts         # Sliders Embla (flechas, dots, contador)
│           ├── scroll-reveals.ts # Reveals genéricos por data-attributes
│           ├── nosotros.ts       # Coreografía de la página Nosotros (lazy)
│           ├── contacto-page.ts  # FAQ + envío del formulario (lazy)
│           └── three-scene.ts
├── vite.config.ts
├── tsconfig.json
└── package.json
```

## Instalación

```bash
cd wp-content/themes/ese-latam
npm install
```

## Desarrollo

1. Crear archivo marcador `.vite-dev` en la raíz del theme:
   ```bash
   touch .vite-dev
   ```
2. Correr Vite:
   ```bash
   npm run dev
   ```
3. Levantar WordPress. Los assets se sirven desde `http://localhost:5173` con HMR.

## Producción

```bash
rm -f .vite-dev
npm run build
```

Vite genera `dist/.vite/manifest.json` y `enqueue.php` encola los archivos con hash.

> ⚠️ **No agregar `?ver=` a los assets de Vite** (`enqueue.php` pasa `null` como
> versión a propósito). Un query string haría que los chunks dinámicos importen
> el módulo `main` con otra URL y se ejecute dos veces, rompiendo ScrollTrigger.

---

## 🏠 Home — coreografía del hero

`front-page.php` + `src/ts/modules/hero-scroll.ts`.

**Intro al cargar** (timeline de entrada, no depende del scroll):

1. El video asienta desde un ligero zoom; el titular aparece **centrado** con
   reveal de máscara por línea.
2. El titular viaja a su posición de layout (la distancia se mide del DOM, no
   hay valores mágicos).
3. Entran el menú, el lede, el CTA y la card de stats, escalonados.

**Scroll** (hero pineado durante `PIN_VIEWPORTS` = 2.5 viewports):

- El video se reproduce scrubbed durante todo el pin.
- **6 → 58%**: la isla emerge (con flotación continua en loop).
- **60 → 100%**: la sección siguiente — coronada por las nubes
  (`.hero-next__clouds`) — barre el hero y llega al top exactamente al
  liberarse el pin.

El **video se reproduce scrubbed** con el scroll (lerp suavizado). Para que el
seeking sea fluido debe estar codificado **all-intra** (cada frame keyframe):

```bash
ffmpeg -i input.mp4 -vf "scale=1920:-2" -c:v libx264 -crf 24 -g 1 \
  -keyint_min 1 -pix_fmt yuv420p -movflags +faststart -an hero-banner-video.mp4
```

La sección que sigue al hero lleva la clase **`.hero-next`**: el módulo le
aplica `margin-top` negativo y `min-height` iguales a la altura real del hero
(re-medidos en cada refresh) para el solape exacto. Sin JS o con
`prefers-reduced-motion` todo queda estático, visible y en flujo normal.

---

## 👥 Página Nosotros

`page-nosotros.php` — WordPress la aplica sola a la página con slug
`nosotros` (también se puede asignar como plantilla "Nosotros" desde el
editor). Estilos con prefijo `.nos-*` en `main.css`; animaciones en
`src/ts/modules/nosotros.ts`, que `main.ts` importa perezosamente cuando
existe `[data-nosotros]`. Assets propios en `assets/imgs/nosotros/`.

Secciones y sus efectos GSAP:

- **Hero**: titular por líneas (SplitText + máscara), zoom lento de la foto,
  contador del "13 países", halo que sigue al cursor, salida scrubbed.
- **Construimos**: paneles con cortina `[data-nos-panel="left|right|up"]`
  (clip-path + zoom de la foto), isla con entrada + parallax + flotación.
- **Objetivos**: aside sticky con scroll-spy (el chevron viaja al ítem
  activo), paneles con parallax interno y tilt 3D al mouse.
- **Aliados**: anillos que se expanden con el scroll, logos que entran en
  3D en cascada, isla con tilt que sigue al mouse.
- **Método Circulogic**: tabs ESG con autoplay (`data-nos-metodo-autoplay`,
  la barra verde es el temporizador) y crossfade de la foto.
- **HDPE**: escena de pellets en canvas 2D (llueven, se apilan, se
  reciclan; el cursor los aparta) y chips en cascada.
- **Contactemos**: `template-parts/contacto.php` acepta `$args`
  (`heading`, `heading_strong`, `desc`, `bg`, `class`, …) para pisar el copy.

---

## ✉️ Página Contacto

`page-contacto.php` — el theme crea la página con slug `contacto` una sola
vez (`inc/contacto.php`), así que la URL existe sin tocar wp-admin. Estilos
con prefijo `.ctc-*`; animaciones en `src/ts/modules/contacto-page.ts`.

Tres bloques, cada uno un template-part autocontenido:

- **Formulario de asesoría** (`contacto-form.php`): campos del Figma, con
  Sector y País como listas cerradas y Producto de interés poblado desde el
  CPT `producto`. Al costado, la tarjeta de marca y los cuatro datos de
  contacto.
- **Sede central** (`contacto-sede.php`): dirección + mapa de Google
  embebido (`output=embed`, sin API key).
- **Preguntas frecuentes** (`contacto-faq.php`): acordeón de `<details>`
  nativos; el JS solo anima la altura y deja una abierta a la vez.

### Envío del formulario

Sin plugin: `inc/contacto.php` registra `wp_ajax(_nopriv)_ese_latam_contacto`,
valida, sanitiza y despacha con `wp_mail` al `admin_email` (filtrable con
`ese_latam_contacto_destino`), con `Reply-To` de quien escribe. Trae honeypot
y nonce. Con JS el envío es por `fetch`; sin JS el `<form>` postea normal y
vuelve con `?contacto=ok|error`.

> ⚠️ En el JS **no** se puede usar `form.action`: el formulario tiene un
> campo oculto llamado `action` (lo exige admin-ajax) y el acceso por nombre
> a los controles pisa la propiedad nativa. Hay que leer el atributo con
> `form.getAttribute('action')`.

Los datos de contacto (teléfono, correo, dirección, horario) salen de
`ese_latam_contacto_datos()`, filtrable con `ese_latam_contacto_datos`.

---

## 🎠 Sliders (Embla)

`src/ts/modules/slider.ts` inicializa cualquier bloque con esta estructura:

```html
<div data-embla data-embla-contain="false">   <!-- contain false: cada slide es snap -->
  <div class="embla__viewport" data-embla-viewport>
    <div class="embla__container"> <div class="embla__slide">…</div> … </div>
  </div>
  <button data-embla-prev>…</button>            <!-- opcional, maneja disabled -->
  <button data-embla-next>…</button>
  <div data-embla-dots></div>                   <!-- opcional, dots generados -->
  <span data-embla-current>01</span>            <!-- opcional, contador 01/08 -->
  <span data-embla-total>/08</span>
</div>
```

El slide seleccionado recibe la clase `is-active` (la sección Sectores la usa
para crecer la card y revelar descripción + chip).

---

## 🎬 Animaciones de scroll

- **Lenis** (`smooth-scroll.ts`): scroll inercial global, anclas internas
  suaves. Se desactiva con `prefers-reduced-motion`.
- **Reveals** (`scroll-reveals.ts`):
  ```html
  <h2 data-reveal="up">Título</h2>
  <p data-reveal="fade" data-reveal-delay="0.2">Párrafo</p>
  <div data-reveal-stagger> <div>…</div> <div>…</div> </div>  <!-- hijos en cascada -->
  ```
  Direcciones: `up` | `down` | `left` | `right` | `fade`. Blur-up + `power3.out`.

---

## 🎨 Design System

### Paleta de uso (consolidada)

Estos son los tokens que se usan a diario. Se aplican como `bg-primary`, `text-secondary`, `border-accent`, etc.

| Token | Hex | Nombre | Uso |
|---|---|---|---|
| `primary` | `#001E61` | Azul Marino | Base de marca, botones primarios, titulares |
| `primary-dark` | `#001545` | Azul Oscuro | Hover del accent, sombras |
| `secondary` | `#0091D1` | Azul Cielo | Acento cromático, hover del primary, links |
| `accent` | `#8EB952` | Verde Lima | Destaques, tags de sostenibilidad |
| `text-dark` | `#242424` | Carbon | Titulares y body enfatizado |
| `text-mid` | `#666666` | Gris Medio | Body normal |
| `text-soft` | `#8A98B6` | Gris Azul | Captions, kickers, metadatos |
| `bg-soft` | `#EBEFF8` | Azul Pálido | Fondos suaves de sección |
| `bg` | `#F9F9F9` | Background | Fondo default de secciones |
| `white` | `#FFFFFF` | Blanco | Cards, superficies |

### Escalas 50..900

Para hovers, elevaciones y variantes: `bg-primary-{50..900}` y `bg-secondary-{50..900}`.

### Sistema y sub-marca

- `bg-alert-surface`, `border-alert-outline`, `text-alert-text` (idem `warning`, `success`)
- `text-circulogic-green`, `bg-circulogic-blue-surface`, `text-circulogic-orange-dark`, etc.

---

### Tipografía — Plus Jakarta Sans

Utilidades directas que aplican tamaño, peso, tracking y color según el Figma:

| Clase | Tamaño | Peso | Uso |
|---|---|---|---|
| `type-display` | 80px | Light 300 | Hero, portada |
| `type-h2` | 64px | Light 300 | Titulares de sección |
| `type-h3` | 40px | SemiBold 600 | Sub-titulares |
| `type-kicker` | 12px | ExtraBold 800 uppercase, tracking `.2em` | Etiqueta encima de un titular |
| `type-nav` | 14px | SemiBold 600 uppercase, tracking `.1em` | Menú, CTAs textuales |
| `type-body-lg` | 16px | Medium 500 | Lead / intro |
| `type-body` | 16px | Regular 400 | Body copy |
| `type-caption` | 14px | Regular 400 | Metadatos, epígrafes |
| `type-tag` | 10px | ExtraBold 800 uppercase, tracking `.15em` | Chips, atributos técnicos |

**Highlight**: en `type-display` y `type-h2`, envuelve el tramo destacado en `<span class="hl">…</span>` para aplicar Bold + color primary. Es el patrón oficial del Figma.

```html
<h1 class="type-display">
  EL CAMBIO <span class="hl">COMIENZA AQUÍ</span>
</h1>
```

---

### Botones

Todos parten de `btn-base` + una variante:

```html
<a class="btn-base btn-primary">Explorar productos</a>
<a class="btn-base btn-secondary">Ver certificaciones</a>
<a class="btn-base btn-accent">Contactar ahora</a>
```

| Variante | Default | Hover |
|---|---|---|
| `btn-primary` | Fondo `primary`, texto blanco | Fondo `secondary` |
| `btn-secondary` | Fondo blanco, borde y texto `primary` | Se rellena de `primary`, texto blanco |
| `btn-accent` | Fondo `secondary`, texto blanco | Fondo `primary-dark` |

Altura 48px, radio 4px, texto 13px SemiBold uppercase tracking `.05em`.

El CTA del hero (`.hero-cta`) es un componente aparte: usa los SVG exportados
del Figma (`assets/icons/btn-primary-*.svg`) para el shape inclinado + chip de
flecha, con variantes hover.

---

## 🌐 Three.js

Cualquier elemento con `data-three-scene` monta una escena Three (carga perezosa):

```html
<div data-three-scene class="aspect-square"></div>
```

La escena base usa Primary de fondo y Secondary como mesh. Fácil de personalizar en `src/ts/modules/three-scene.ts`.
