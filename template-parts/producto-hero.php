<?php
/**
 * Ficha de producto — hero "panel de configuración" (litraje + color +
 * compra, con el producto flotando sobre la isla). Se llama desde
 * single-producto.php DENTRO del loop (the_post() ya corrió), así que lee el
 * post actual directamente — mismo criterio autocontenido que
 * template-parts/catalogo-banner.php.
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

// El título va en dos renglones: la primera palabra en blanco y el resto
// en celeste (ver .producto-hero__title-accent), como en el diseño.
$ese_titulo       = get_the_title();
$ese_titulo_parts = explode(' ', $ese_titulo, 2);
$ese_titulo_l1    = $ese_titulo_parts[0];
$ese_titulo_l2    = $ese_titulo_parts[1] ?? '';

// Features del panel derecho: todavía no hay campo ACF para esto.
$ese_features = [
    ['icon' => 'shield', 'title' => __('Alta resistencia', 'ese-latam'), 'desc' => __('HDPE virgen de alta densidad.', 'ese-latam')],
    ['icon' => 'wheel',  'title' => __('Movilidad total', 'ese-latam'),  'desc' => __('3 ruedas para mayor estabilidad.', 'ese-latam')],
    ['icon' => 'check',  'title' => __('Larga vida útil', 'ese-latam'),  'desc' => __('Resistente a impactos, UV y químicos.', 'ese-latam')],
    ['icon' => 'leaf',   'title' => __('Ecológico', 'ese-latam'),        'desc' => __('Material 100% reciclable.', 'ese-latam')],
];
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
        <?php // Fuera del panel a propósito: así en mobile el orden puede ser
        // breadcrumb → escena → configuración sin reordenar hijos anidados. ?>
        <p class="producto-hero__crumb catalogo-banner__crumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path
                        d="M10 5.27979V10.56C10 10.6767 9.9561 10.7886 9.87796 10.8711C9.79982 10.9536 9.69384 11 9.58333 11H6.66667C6.55616 11 6.45018 10.9536 6.37204 10.8711C6.2939 10.7886 6.25 10.6767 6.25 10.56V7.69988C6.25 7.64153 6.22805 7.58557 6.18898 7.54431C6.14991 7.50305 6.09692 7.47987 6.04167 7.47987H3.95833C3.90308 7.47987 3.85009 7.50305 3.81102 7.54431C3.77195 7.58557 3.75 7.64153 3.75 7.69988V10.56C3.75 10.6767 3.7061 10.7886 3.62796 10.8711C3.54982 10.9536 3.44384 11 3.33333 11H0.416667C0.30616 11 0.200179 10.9536 0.122039 10.8711C0.0438988 10.7886 0 10.6767 0 10.56V5.27979C0.000102442 5.04643 0.0879669 4.82267 0.244271 4.65772L4.41094 0.257552C4.5672 0.0926383 4.77908 0 5 0C5.22092 0 5.4328 0.0926383 5.58906 0.257552L9.75573 4.65772C9.91203 4.82267 9.9999 5.04643 10 5.27979Z"
                        fill="currentColor" />
                </svg>
            </a>
            <span class="catalogo-banner__crumb-sep" aria-hidden="true">/</span>
            <a href="<?php echo esc_url(get_post_type_archive_link('producto')); ?>">
                <?php esc_html_e('Productos', 'ese-latam'); ?>
            </a>
            <span class="catalogo-banner__crumb-sep" aria-hidden="true">/</span>
            <?php echo esc_html($ese_titulo); ?>
        </p>

        <div class="producto-hero__panel">
            <div class="producto-hero__kicker">
                <span><?php esc_html_e('Panel de configuración', 'ese-latam'); ?></span>
            </div>

            <h1 class="producto-hero__title">
                <?php echo esc_html($ese_titulo_l1); ?>
                <?php if ('' !== $ese_titulo_l2) : ?>
                    <span class="producto-hero__title-accent"><?php echo esc_html($ese_titulo_l2); ?></span>
                <?php endif; ?>
            </h1>

            <p class="producto-hero__desc"><?php echo esc_html($ese_desc); ?></p>

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
                </p>
                <div class="producto-hero__swatches">
                    <?php foreach ($ese_colores_data as $ese_i => $ese_color) : ?>
                        <button type="button"
                            class="producto-hero__swatch<?php echo 0 === $ese_i ? ' is-active' : ''; ?>"
                            style="--swatch: <?php echo esc_attr($ese_color['color']); ?>;"
                            data-producto-color="<?php echo (int) $ese_i; ?>"
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
                    <?php esc_html_e('Descargar ficha técnica', 'ese-latam'); ?>
                </a>
            </div>
        </div>

        <?php // Escena: mismo patrón que template-parts/catalogo-banner.php — la sombra
        // debe ser HERMANA del <img data-float> dentro del mismo wrapper (float.ts la
        // busca en el parentElement del elemento que flota). ?>
        <div class="producto-hero__scene">
            <div class="producto-hero__pedestal" aria-hidden="true">
                <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/island/island-catalogo.webp'); ?>"
                    alt="" loading="lazy" decoding="async">
            </div>

            <div class="producto-hero__product" data-product>
                <span class="product-card__shadow" aria-hidden="true" data-float-shadow></span>
                <img data-producto-img src="<?php echo esc_url($ese_img_default); ?>"
                    alt="<?php echo esc_attr($ese_titulo); ?>" decoding="async" data-float
                    data-float-distance="14" data-float-duration="3.2">
            </div>
        </div>

        <aside class="producto-hero__aside">
            <div class="producto-hero__features">
                <?php foreach ($ese_features as $ese_feature) : ?>
                    <div class="producto-hero__feature">
                        <span class="producto-hero__feature-icon" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <?php echo ese_latam_icon_svg($ese_feature['icon']); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                            </svg>
                        </span>
                        <span class="producto-hero__feature-text">
                            <span class="producto-hero__feature-title"><?php echo esc_html($ese_feature['title']); ?></span>
                            <span class="producto-hero__feature-desc"><?php echo esc_html($ese_feature['desc']); ?></span>
                        </span>
                    </div>
                <?php endforeach; ?>

                <div class="producto-hero__features-foot">
                    <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/logo-ese.png'); ?>" alt="ESE Latam"
                        width="48" height="20" loading="lazy" decoding="async">
                    <span><?php esc_html_e('Calidad y respaldo garantizado.', 'ese-latam'); ?></span>
                </div>
            </div>
        </aside>
    </div>

    <div class="producto-hero__scroll-hint" aria-hidden="true">
        <span class="producto-hero__scroll-icon">
            <span class="producto-hero__scroll-dot"></span>
        </span>
        <span class="producto-hero__scroll-text"><?php esc_html_e('Desliza para ver más', 'ese-latam'); ?></span>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round" class="producto-hero__scroll-arrow">
            <path d="M12 5v14M19 12l-7 7-7-7" />
        </svg>
    </div>
</section>
