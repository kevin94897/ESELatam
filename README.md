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
├── header.php / footer.php
├── inc/
│   ├── setup.php          # Supports, menús (+ fallback del menú), sidebars
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

`front-page.php` + `src/ts/modules/hero-scroll.ts`. El hero queda pineado
durante `PIN_VIEWPORTS` (2.5) viewports de scroll:

1. **Carga**: solo el titular centrado (reveal con máscara por línea) sobre el
   video; el video asienta desde un ligero zoom.
2. **0 → 16%**: el titular viaja a su posición de layout (la distancia se mide
   del DOM, no hay valores mágicos).
3. **14 → 30%**: entran el menú, el lede, el CTA y la card de stats.
4. **20 → 38%**: la isla emerge (con flotación continua en loop).
5. **60 → 100%**: la sección siguiente — coronada por las nubes
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
