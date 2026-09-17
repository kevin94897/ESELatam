<?php
/**
 * Ficha de producto — hero "panel de configuración". Se llama desde
 * single-producto.php DENTRO del loop (the_post() ya corrió), así que lee el
 * post actual directamente — mismo criterio autocontenido que
 * template-parts/catalogo-banner.php.
 *
 * Composición (desktop):
 *
 *   breadcrumb ────────────────────────────────────────
 *   [ panel de configuración ]   [ escena isla+producto ]
 *   [ barra de atributos: capacidad · colores · specs ACF … ]
 *
 * Solo el producto y sus atributos, sin columnas laterales extra. En mobile
 * todo se apila: breadcrumb → escena → panel → atributos.
 *
 * Casi todo sale de los campos ACF de inc/acf-productos.php. Mientras el
 * cliente no los complete, cada bloque cae a un valor estático que calca el
 * diseño — así la ficha se ve entera desde el día uno y se va "poblando"
 * sola a medida que cargan datos reales, sin tocar esta plantilla.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_producto_id = get_the_ID();

// ---------- Datos ACF (con fallback estático al diseño) ----------

$ese_desc = get_field('descripcion_corta') ?: get_the_excerpt();

$ese_litrajes = get_field('litrajes');
if (! is_array($ese_litrajes) || empty($ese_litrajes)) {
    $ese_litrajes = array_map(
        static fn (string $v): array => ['valor' => $v, 'predeterminado' => '120L' === $v],
        ['80L', '120L', '140L', '180L', '240L', '360L']
    );
}

// Litraje activo al cargar: producto-specs.php recalcula el mismo valor de
// forma independiente para que su card "Volumen" (solo en el fallback
// estático) nunca pueda divergir de la píldora marcada acá.
$ese_litraje_default = $ese_litrajes[0]['valor'] ?? '';
foreach ($ese_litrajes as $ese_lit) {
    if (! empty($ese_lit['predeterminado'])) {
        $ese_litraje_default = $ese_lit['valor'];
        break;
    }
}

// Sin colores cargados el selector no se muestra: el theme no inventa una
// carta de colores que el producto no tiene.
$ese_colores = (array) ese_latam_campo('colores', $ese_producto_id, []);

$ese_compra = ese_latam_enlace(
    ese_latam_campo('enlace_compra', $ese_producto_id, null),
    __('Comprar', 'ese-latam'),
    ese_latam_contacto_url()
);
$ese_ficha      = get_field('ficha_tecnica');

// Sin foto propia no se pone una ajena: antes caía a un contenedor de 3
// ruedas del theme, así que la Papelera y el Soterrado se mostraban con una
// pieza que no era la suya.
$ese_img_default = (string) (get_the_post_thumbnail_url($ese_producto_id, 'large') ?: '');

// Cada color con su foto (si no tiene una propia, la principal del producto)
// y, cuando existen, las fotos por litraje: el panel deja elegir capacidad Y
// color, así que la foto depende de los dos (un 80L y un 360L del mismo
// color no son la misma pieza). `imgs` es un mapa litraje → URL; lo que no
// esté ahí cae a `img` (la "foto por defecto" del color). Este mismo array
// viaja a product-config.ts por `data-colors`.
// Fotos por color y capacidad (pestaña "3. Fotos" de la ficha): se agrupan
// por nombre de color para que cada uno lleve su mapa capacidad => foto. El
// agrupado vive en inc/template-tags.php porque las tarjetas del catálogo y
// de los sliders resuelven su foto con el mismo criterio.
$ese_fotos_por_color = ese_latam_producto_fotos($ese_producto_id);

$ese_colores_data = array_map(
    static function (array $c) use ($ese_img_default, $ese_fotos_por_color): array {
        $img  = ese_latam_img_url($c['imagen'] ?? '');
        $imgs = $ese_fotos_por_color[trim((string) ($c['nombre'] ?? ''))] ?? [];

        return [
            'nombre' => (string) ($c['nombre'] ?? ''),
            'color'  => (string) ($c['color'] ?? '#ffffff'),
            'img'    => $img ?: $ese_img_default,
            'imgs'   => (object) $imgs,
        ];
    },
    $ese_colores
);

// Foto inicial: la del primer color en el litraje por defecto — si no, la
// del color, y recién al final la destacada del producto. Sin esto el hero
// pintaba la destacada (un litraje cualquiera) y recién cambiaba al primer
// clic, con un salto visible.
$ese_img_inicial = $ese_img_default;
if (isset($ese_colores_data[0])) {
    $ese_primer_color = $ese_colores_data[0];
    $ese_variantes    = (array) $ese_primer_color['imgs'];
    $ese_img_inicial  = $ese_variantes[$ese_litraje_default] ?? $ese_primer_color['img'];
}

// Kicker: la categoría real del producto (taxonomía producto_categoria).
$ese_terms  = get_the_terms($ese_producto_id, 'producto_categoria');
$ese_kicker = is_array($ese_terms) && ! empty($ese_terms)
    ? $ese_terms[0]->name
    : __('Producto ESE Latam', 'ese-latam');

// El título va en dos renglones: la primera palabra en peso light y el
// resto en bold celeste (ver .producto-hero__title-accent).
$ese_titulo       = get_the_title();
$ese_titulo_parts = explode(' ', $ese_titulo, 2);
$ese_titulo_l1    = $ese_titulo_parts[0];
$ese_titulo_l2    = $ese_titulo_parts[1] ?? '';

// Barra de datos clave (pie del hero): capacidad activa (se sincroniza con
// las píldoras vía data-producto-volumen), cantidad de colores y hasta dos
// características ACF que no sean el volumen (ya está en "Capacidad").
// Sellos del producto: se eligen con el campo de relación "Certificaciones"
// de la ficha (inc/pcf-productos.php) y salen del módulo, así que el nombre y
// el logo son los mismos que en la franja y en la página de Certificaciones.
$ese_certs = [];

foreach ((array) ese_latam_campo('certificaciones', $ese_producto_id, []) as $ese_cert_id) {
    $ese_cert_id = (int) $ese_cert_id;
    if ($ese_cert_id <= 0 || 'publish' !== get_post_status($ese_cert_id)) {
        continue;
    }

    $ese_certs[] = [
        'name' => get_the_title($ese_cert_id),
        'img'  => (string) (get_the_post_thumbnail_url($ese_cert_id, 'medium') ?: ''),
    ];
}

$ese_certs_url = ese_latam_pagina_url('certificaciones', home_url('/#certificaciones'));

$ese_stats = [
    ['label' => __('Capacidad', 'ese-latam'), 'value' => $ese_litraje_default, 'sync' => true],
    ['label' => __('Colores', 'ese-latam'), 'value' => sprintf(
        /* translators: %d: cantidad de colores disponibles */
        _n('%d disponible', '%d disponibles', count($ese_colores_data), 'ese-latam'),
        count($ese_colores_data)
    ), 'sync' => false],
];
$ese_caracteristicas = get_field('caracteristicas');
if (is_array($ese_caracteristicas)) {
    foreach ($ese_caracteristicas as $ese_caract) {
        $ese_etiqueta = (string) ($ese_caract['etiqueta'] ?? '');
        if ('' === $ese_etiqueta || false !== mb_strpos(mb_strtolower($ese_etiqueta), 'volumen')) {
            continue;
        }
        $ese_stats[] = ['label' => $ese_etiqueta, 'value' => (string) ($ese_caract['valor'] ?? ''), 'sync' => false];
        if (count($ese_stats) >= 4) {
            break;
        }
    }
}
if (count($ese_stats) < 4) {
    $ese_stats[] = ['label' => __('Material', 'ese-latam'), 'value' => __('HDPE de alta densidad', 'ese-latam'), 'sync' => false];
}
if (count($ese_stats) < 4) {
    $ese_stats[] = ['label' => __('Origen', 'ese-latam'), 'value' => __('Ingeniería europea', 'ese-latam'), 'sync' => false];
}
?>

<section class="producto-hero" data-product-config data-producto-hero
    data-colors="<?php echo esc_attr(wp_json_encode($ese_colores_data)); ?>">
    <?php // Fondo propio de la ficha: degradado radial de marca, no la foto de
    // montaña del banner de catálogo. Al no cargar imagen, el hero pinta en
    // el primer frame y el LCP pasa a ser el título. ?>
    <?php // Marca "hay JS": solo entonces el CSS esconde las partes del hero
    // hasta que producto-hero-intro.ts las anime (sin JS quedan visibles). ?>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>
    <div class="producto-hero__bg" aria-hidden="true"></div>
    <div class="producto-hero__dots" aria-hidden="true"></div>
    <div class="catalogo-banner__grid" aria-hidden="true"></div>

    <div class="producto-hero__inner">
        <?php
        get_template_part('template-parts/breadcrumbs', null, [
            'items'   => [[
                'label' => __('Productos', 'ese-latam'),
                'url'   => get_post_type_archive_link('producto'),
            ]],
            'current' => $ese_titulo,
            'class'   => 'producto-hero__crumb crumbs--light',
        ]);
        ?>

        <?php // La entrada de TODO el hero (panel, escena, atributos) la coreografía
        // producto-hero-intro.ts en un solo timeline — por eso acá no hay
        // data-reveal como en el resto del sitio. ?>
        <div class="producto-hero__panel">
            <div class="producto-hero__kicker">
                <span><?php echo esc_html($ese_kicker); ?></span>
            </div>

            <h1 class="producto-hero__title">
                <span class="producto-hero__title-light"><?php echo esc_html($ese_titulo_l1); ?></span>
                <?php if ('' !== $ese_titulo_l2) : ?>
                    <span class="producto-hero__title-accent"><?php echo esc_html($ese_titulo_l2); ?></span>
                <?php endif; ?>
            </h1>

            <?php if ($ese_desc) : ?>
                <p class="producto-hero__desc"><?php echo esc_html($ese_desc); ?></p>
            <?php endif; ?>

            <div class="producto-hero__group">
                <p class="producto-hero__group-label">
                    <span class="producto-hero__group-num"></span>
                    <span class="producto-hero__group-text"><?php esc_html_e('Elige la capacidad', 'ese-latam'); ?></span>
                </p>
                <div class="producto-hero__pills">
                    <?php
                    $ese_default_seen = false;
                    foreach ($ese_litrajes as $ese_litraje) :
                        $ese_is_default = ! $ese_default_seen && $ese_litraje['valor'] === $ese_litraje_default;
                        if ($ese_is_default) {
                            $ese_default_seen = true;
                        }
                        ?>
                        <button type="button"
                            class="producto-hero__pill<?php echo $ese_is_default ? ' is-active' : ''; ?>"
                            data-producto-litraje="<?php echo esc_attr($ese_litraje['valor']); ?>">
                            <?php echo esc_html($ese_litraje['valor']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="producto-hero__group">
                <p class="producto-hero__group-label">
                    <span class="producto-hero__group-num"></span>
                    <span class="producto-hero__group-text"><?php esc_html_e('Selecciona el color', 'ese-latam'); ?></span>
                    <span class="producto-hero__group-value" data-producto-color-name><?php echo esc_html($ese_colores_data[0]['nombre'] ?? ''); ?></span>
                </p>
                <div class="producto-hero__swatches">
                    <?php foreach ($ese_colores_data as $ese_i => $ese_color) : ?>
                        <button type="button"
                            class="producto-hero__swatch<?php echo 0 === $ese_i ? ' is-active' : ''; ?>"
                            style="--swatch: <?php echo esc_attr($ese_color['color']); ?>;"
                            data-producto-color="<?php echo (int) $ese_i; ?>"
                            title="<?php echo esc_attr($ese_color['nombre']); ?>"
                            aria-label="<?php echo esc_attr($ese_color['nombre']); ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="producto-hero__actions">
                <?php
                ese_latam_cta_button([
                    'href'   => $ese_compra['href'],
                    'label'  => $ese_compra['label'],
                    'target' => $ese_compra['target'],
                    'class'  => 'hero-cta--light',
                ]);
                ?>

                <a class="producto-hero__ficha"
                    href="<?php echo esc_url(is_array($ese_ficha) ? ($ese_ficha['url'] ?? '#') : '#'); ?>"
                    <?php echo is_array($ese_ficha) ? 'download' : 'aria-disabled="true"'; ?>>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <?php echo ese_latam_icon_svg('download'); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </svg>
                    <?php esc_html_e('Ficha técnica', 'ese-latam'); ?>
                </a>
            </div>
        </div>

        <?php // Escena: mismo patrón que template-parts/catalogo-banner.php — la sombra
        // debe ser HERMANA del <img data-float> dentro del mismo wrapper (float.ts la
        // busca en el parentElement del elemento que flota). El foco de luz y los
        // anillos son solo CSS, dan profundidad detrás del producto. ?>
        <div class="producto-hero__scene">
            <div class="producto-hero__spot" aria-hidden="true"></div>
            <div class="producto-hero__ring" aria-hidden="true"></div>

            <div class="producto-hero__pedestal" aria-hidden="true">
                <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/island/island-catalogo.webp'); ?>"
                    alt="" loading="lazy" decoding="async">
            </div>

            <?php if ('' !== $ese_img_inicial) : ?>
                <div class="producto-hero__product" data-product>
                    <span class="product-card__shadow" aria-hidden="true" data-float-shadow></span>
                    <img data-producto-img src="<?php echo esc_url($ese_img_inicial); ?>"
                        alt="<?php echo esc_attr($ese_titulo); ?>" decoding="async" fetchpriority="high" data-float
                        data-float-distance="12" data-float-duration="3.4">
                </div>
            <?php endif; ?>
        </div>


        <?php // Fila inferior: barra de atributos a la izquierda y sellos de
        // certificación a la derecha (Figma 3682-5745). Sin data-reveal: en
        // pantallas de 1080px queda por debajo del umbral (top 85%) de
        // scroll-reveals.ts y no llegaría a mostrarse sin scrollear; la anima
        // la intro (producto-hero-intro.ts) junto con el resto del hero. ?>
        <div class="producto-hero__bottom">
            <dl class="producto-hero__stats">
                <?php foreach ($ese_stats as $ese_stat) : ?>
                    <div class="producto-hero__stat">
                        <dt class="producto-hero__stat-label"><?php echo esc_html($ese_stat['label']); ?></dt>
                        <dd class="producto-hero__stat-value" <?php echo $ese_stat['sync'] ? 'data-producto-volumen' : ''; ?>><?php echo esc_html($ese_stat['value']); ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>

            <div class="producto-hero__certs">
                <p class="producto-hero__certs-label"><?php esc_html_e('Certificaciones', 'ese-latam'); ?></p>
                <ul class="producto-hero__certs-list">
                    <?php foreach ($ese_certs as $ese_cert) : ?>
                        <li>
                            <a class="producto-hero__cert" href="<?php echo esc_url($ese_certs_url); ?>"
                                title="<?php echo esc_attr(sprintf(__('Certificación %s', 'ese-latam'), $ese_cert['name'])); ?>">
                                <span class="producto-hero__cert-tile" aria-hidden="true">
                                    <?php if ('' !== $ese_cert['img']) : ?>
                                        <img src="<?php echo esc_url($ese_cert['img']); ?>" alt="" loading="lazy" decoding="async">
                                    <?php else : ?>
                                        <span class="producto-hero__cert-initials"><?php echo esc_html(mb_strtoupper(mb_substr($ese_cert['name'], 0, 3))); ?></span>
                                    <?php endif; ?>
                                </span>
                                <span class="producto-hero__cert-name"><?php echo esc_html($ese_cert['name']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <?php // Mismo cue que el hero de Nosotros (.nos-hero__scroll): línea vertical
    // con degradado que "cae" en bucle + la palabra Scroll, abajo a la derecha. ?>
    <a href="#especificaciones" class="producto-hero__scroll-hint" aria-label="<?php esc_attr_e('Ir a las especificaciones', 'ese-latam'); ?>">
        <span class="producto-hero__scroll-line" aria-hidden="true"></span>
        <span class="producto-hero__scroll-text"><?php esc_html_e('Scroll', 'ese-latam'); ?></span>
    </a>
</section>
