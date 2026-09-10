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

$ese_colores = get_field('colores');
if (! is_array($ese_colores) || empty($ese_colores)) {
    $ese_colores = [
        ['nombre' => __('Negro', 'ese-latam'),   'color' => '#1c1c1c', 'imagen' => null],
        ['nombre' => __('Verde', 'ese-latam'),   'color' => '#4b8b3b', 'imagen' => null],
        ['nombre' => __('Azul', 'ese-latam'),    'color' => '#1e5bd6', 'imagen' => null],
        ['nombre' => __('Amarillo', 'ese-latam'), 'color' => '#efc03f', 'imagen' => null],
        ['nombre' => __('Rojo', 'ese-latam'),    'color' => '#e0453c', 'imagen' => null],
        ['nombre' => __('Gris', 'ese-latam'),    'color' => '#a8adb5', 'imagen' => null],
    ];
}

$ese_compra_url = get_field('enlace_compra');
$ese_ficha      = get_field('ficha_tecnica');

$ese_img_default = get_the_post_thumbnail_url($ese_producto_id, 'large')
    ?: (ESE_LATAM_URI . '/assets/imgs/catalogo/contenedor-3-ruedas.png');

// Cada color con su foto (si no tiene una propia, la principal del producto).
// Este mismo array viaja a product-config.ts por `data-colors`.
$ese_colores_data = array_map(
    static function (array $c) use ($ese_img_default): array {
        $img = is_array($c['imagen'] ?? null) ? ($c['imagen']['url'] ?? '') : '';
        return [
            'nombre' => (string) ($c['nombre'] ?? ''),
            'color'  => (string) ($c['color'] ?? '#ffffff'),
            'img'    => $img ?: $ese_img_default,
        ];
    },
    $ese_colores
);

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

<section class="producto-hero" data-product-config
    data-colors="<?php echo esc_attr(wp_json_encode($ese_colores_data)); ?>">
    <?php // Fondo propio de la ficha: degradado radial de marca, no la foto de
    // montaña del banner de catálogo. Al no cargar imagen, el hero pinta en
    // el primer frame y el LCP pasa a ser el título. ?>
    <div class="producto-hero__bg" aria-hidden="true"></div>
    <div class="producto-hero__dots" aria-hidden="true"></div>
    <div class="catalogo-banner__grid" aria-hidden="true"></div>

    <div class="producto-hero__inner">
        <p class="producto-hero__crumb catalogo-banner__crumb" data-reveal="fade">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path
                        d="M10 5.27979V10.56C10 10.6767 9.9561 10.7886 9.87796 10.8711C9.79982 10.9536 9.69384 11 9.58333 11H6.66667C6.55616 11 6.45018 10.9536 6.37204 10.8711C6.2939 10.7886 6.25 10.6767 6.25 10.56V7.69988C6.25 7.64153 6.22805 7.58557 6.18898 7.54431C6.14991 7.50305 6.09692 7.47987 6.04167 7.47987H3.95833C3.90308 7.47987 3.85009 7.50305 3.81102 7.54431C3.77195 7.58557 3.75 7.64153 3.75 7.69988V10.56C3.75 10.6767 3.7061 10.7886 3.62796 10.8711C3.54982 10.9536 3.44384 11 3.33333 11H0.416667C0.30616 11 0.200179 10.9536 0.122039 10.8711C0.0438988 10.7886 0 10.6767 0 10.56V5.27979C0.000102442 5.04643 0.0879669 4.82267 0.244271 4.65772L4.41094 0.257552C4.5672 0.0926383 4.77908 0 5 0C5.22092 0 5.4328 0.0926383 5.58906 0.257552L9.75573 4.65772C9.91203 4.82267 9.9999 5.04643 10 5.27979Z"
                        fill="currentColor" />
                </svg>
                <?php esc_html_e('Inicio', 'ese-latam'); ?>
            </a>
            <span class="catalogo-banner__crumb-sep" aria-hidden="true">/</span>
            <a href="<?php echo esc_url(get_post_type_archive_link('producto')); ?>">
                <?php esc_html_e('Productos', 'ese-latam'); ?>
            </a>
            <span class="catalogo-banner__crumb-sep" aria-hidden="true">/</span>
            <span class="producto-hero__crumb-current"><?php echo esc_html($ese_titulo); ?></span>
        </p>

        <?php // data-reveal-stagger: cada bloque del panel entra en cascada al cargar
        // (scroll-reveals.ts dispara enseguida lo que ya está en viewport). ?>
        <div class="producto-hero__panel" data-reveal-stagger>
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
                    <span class="producto-hero__group-num">01</span>
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
                    <span class="producto-hero__group-num">02</span>
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
                    'href'  => $ese_compra_url ?: '#',
                    'label' => __('Comprar', 'ese-latam'),
                    'class' => 'hero-cta--light',
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
        <div class="producto-hero__scene" data-reveal="fade" data-reveal-delay="0.15">
            <div class="producto-hero__spot" aria-hidden="true"></div>
            <div class="producto-hero__ring" aria-hidden="true"></div>

            <div class="producto-hero__pedestal" aria-hidden="true">
                <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/island/island-catalogo.webp'); ?>"
                    alt="" loading="lazy" decoding="async">
            </div>

            <div class="producto-hero__product" data-product>
                <span class="product-card__shadow" aria-hidden="true" data-float-shadow></span>
                <img data-producto-img src="<?php echo esc_url($ese_img_default); ?>"
                    alt="<?php echo esc_attr($ese_titulo); ?>" decoding="async" fetchpriority="high" data-float
                    data-float-distance="12" data-float-duration="3.4">
            </div>
        </div>


        <?php // Sin data-reveal: en pantallas de 1080px queda por debajo del umbral
        // (top 85%) de scroll-reveals.ts y no llegaría a mostrarse sin scrollear. ?>
        <dl class="producto-hero__stats">
            <?php foreach ($ese_stats as $ese_stat) : ?>
                <div class="producto-hero__stat">
                    <dt class="producto-hero__stat-label"><?php echo esc_html($ese_stat['label']); ?></dt>
                    <dd class="producto-hero__stat-value" <?php echo $ese_stat['sync'] ? 'data-producto-volumen' : ''; ?>><?php echo esc_html($ese_stat['value']); ?></dd>
                </div>
            <?php endforeach; ?>
        </dl>
    </div>

    <?php // Mismo cue que el hero de Nosotros (.nos-hero__scroll): línea vertical
    // con degradado que "cae" en bucle + la palabra Scroll, abajo a la derecha. ?>
    <a href="#especificaciones" class="producto-hero__scroll-hint" aria-label="<?php esc_attr_e('Ir a las especificaciones', 'ese-latam'); ?>">
        <span class="producto-hero__scroll-line" aria-hidden="true"></span>
        <span class="producto-hero__scroll-text"><?php esc_html_e('Scroll', 'ese-latam'); ?></span>
    </a>
</section>
