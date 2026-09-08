<?php
/**
 * Banner de la página Catálogo (Figma node 3639-16225). Compartido entre
 * page-catalogo-de-productos.php (la página real, creada desde wp-admin)
 * y archive-producto.php (el archivo del CPT en /catalogo, has_archive en
 * inc/cpt-productos.php) — mismo contenido hasta que se defina cuál de las
 * dos URLs es la definitiva. Por ahora solo el banner; la grilla con todos
 * los productos es el siguiente paso.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

// Slides reales desde el CPT; si el cliente todavía no cargó productos,
// cae a un único slide de ejemplo con el copy real del Figma.
$ese_catalogo_query = new WP_Query([
    'post_type'      => 'producto',
    'post_status'    => 'publish',
    'posts_per_page' => 8,
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
]);

$ese_catalogo_slides = [];

if ($ese_catalogo_query->have_posts()) {
    while ($ese_catalogo_query->have_posts()) {
        $ese_catalogo_query->the_post();

        $litrajes    = get_field('litrajes');
        $max_litraje = is_array($litrajes) && ! empty($litrajes) ? end($litrajes)['valor'] : '';

        $ese_thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: get_the_post_thumbnail_url(get_the_ID(), 'large');
        $ese_catalogo_slides[] = [
            'title'   => get_the_title(),
            'desc'    => get_field('descripcion_corta') ?: get_the_excerpt(),
            'eyebrow' => $max_litraje ? sprintf(__('Hasta %s', 'ese-latam'), $max_litraje) : __('Producto ESE Latam', 'ese-latam'),
            'img'     => get_the_post_thumbnail_url(get_the_ID(), 'large') ?: (ESE_LATAM_URI . '/assets/imgs/catalogo/contenedor-3-ruedas.png'),
            // 'medium' (≤ 300px) para las cards del slider — 10-20× más liviano que 'large'.
            'thumb'   => $ese_thumb_url ?: (ESE_LATAM_URI . '/assets/imgs/catalogo/contenedor-3-ruedas.png'),
            'href'    => get_permalink(),
        ];
    }
    wp_reset_postdata();
} else {
    $ese_catalogo_slides[] = [
        'title'   => __('Contenedor 3 ruedas', 'ese-latam'),
        'desc'    => __('Diseño certificado en verde ecológico de alta intensidad. Excelente para acopio y selección de residuos compostables u orgánicos domésticos.', 'ese-latam'),
        'eyebrow' => __('Hasta 150 litros', 'ese-latam'),
        'img'     => ESE_LATAM_URI . '/assets/imgs/catalogo/contenedor-3-ruedas.png',
        'thumb'   => ESE_LATAM_URI . '/assets/imgs/catalogo/contenedor-3-ruedas.png',
        'href'    => '#',
    ];
}

$ese_catalogo_total   = count($ese_catalogo_slides);
$ese_catalogo_current = $ese_catalogo_slides[0];
?>

<section class="catalogo-banner" data-product-island
    data-slides="<?php echo esc_attr(wp_json_encode($ese_catalogo_slides)); ?>">
    <div class="catalogo-banner__bg" aria-hidden="true">
        <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/catalogo/banner-bg-mountain.png'); ?>" alt=""
            decoding="async">
    </div>
    <div class="catalogo-banner__shade" aria-hidden="true"></div>
    <div class="catalogo-banner__noise" aria-hidden="true"></div>
    <div class="catalogo-banner__grid" aria-hidden="true"></div>
    <p class="catalogo-banner__watermark" aria-hidden="true">
        <?php echo esc_html($ese_catalogo_current['title']); ?>
    </p>

    <div class="catalogo-banner__content">
        <p class="catalogo-banner__crumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M10 5.27979V10.56C10 10.6767 9.9561 10.7886 9.87796 10.8711C9.79982 10.9536 9.69384 11 9.58333 11H6.66667C6.55616 11 6.45018 10.9536 6.37204 10.8711C6.2939 10.7886 6.25 10.6767 6.25 10.56V7.69988C6.25 7.64153 6.22805 7.58557 6.18898 7.54431C6.14991 7.50305 6.09692 7.47987 6.04167 7.47987H3.95833C3.90308 7.47987 3.85009 7.50305 3.81102 7.54431C3.77195 7.58557 3.75 7.64153 3.75 7.69988V10.56C3.75 10.6767 3.7061 10.7886 3.62796 10.8711C3.54982 10.9536 3.44384 11 3.33333 11H0.416667C0.30616 11 0.200179 10.9536 0.122039 10.8711C0.0438988 10.7886 0 10.6767 0 10.56V5.27979C0.000102442 5.04643 0.0879669 4.82267 0.244271 4.65772L4.41094 0.257552C4.5672 0.0926383 4.77908 0 5 0C5.22092 0 5.4328 0.0926383 5.58906 0.257552L9.75573 4.65772C9.91203 4.82267 9.9999 5.04643 10 5.27979Z" fill="currentColor" />
                </svg>
                <?php esc_html_e('Inicio', 'ese-latam'); ?>
            </a>
            <span class="catalogo-banner__crumb-sep" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="4" height="7" viewBox="0 0 4 7" fill="none">
                    <path d="M3.86395 3.8233L0.788805 6.86609C0.70215 6.95183 0.58462 7 0.462071 7C0.339522 7 0.221993 6.95183 0.135337 6.86609C0.0486823 6.78034 9.1306e-10 6.66405 0 6.54279C-9.1306e-10 6.42153 0.0486823 6.30524 0.135337 6.21949L2.88413 3.50038L0.136106 0.780507C0.0931991 0.738051 0.059163 0.687648 0.0359418 0.632177C0.0127205 0.576706 0.000768656 0.517252 0.000768656 0.45721C0.000768656 0.397169 0.0127205 0.337715 0.0359418 0.282243C0.059163 0.226772 0.0931991 0.17637 0.136106 0.133914C0.179014 0.0914579 0.229952 0.0577801 0.286013 0.0348031C0.342074 0.0118261 0.40216 -4.47345e-10 0.46284 0C0.52352 4.47346e-10 0.583606 0.0118261 0.639667 0.0348031C0.695728 0.0577801 0.746666 0.0914579 0.789574 0.133914L3.86471 3.1767C3.90767 3.21916 3.94173 3.26958 3.96494 3.32509C3.98816 3.38059 4.00007 3.44009 4 3.50016C3.99993 3.56023 3.98787 3.61969 3.96453 3.67515C3.94118 3.7306 3.907 3.78094 3.86395 3.8233Z" fill="currentColor"/>
                </svg>
            </span>
            <?php esc_html_e('Productos', 'ese-latam'); ?>
        </p>

        <?php
        // Slider de productos (debajo del crumb): cada card es un producto
        // real del catálogo — deslizar o tocar una salta a ese producto
        // (mismo goTo() de product-island.ts, que además crea y controla su
        // propio Embla acá, sin pasar por el genérico initSliders() de
        // slider.ts — por eso NO lleva data-embla). Con un solo producto
        // (fallback sin CPT) no hay nada que deslizar.
        ?>
        <?php if ($ese_catalogo_total > 1): ?>
            <div class="catalogo-banner__shop-cards" data-catalogo-slider>
                <div class="embla__viewport" data-embla-viewport>
                    <div class="embla__container">
                        <?php foreach ($ese_catalogo_slides as $ese_i => $ese_slide): ?>
                            <div class="embla__slide catalogo-banner__shop-card<?php echo 0 === $ese_i ? ' is-active' : ''; ?>"
                                data-catalogo-goto="<?php echo (int) $ese_i; ?>">
                                <div class="catalogo-banner__shop-card-frame">
                                    <span class="catalogo-banner__shop-card-kicker"><?php echo esc_html($ese_slide['eyebrow']); ?></span>
                                    <span class="catalogo-banner__shop-card-text">
                                        <span class="catalogo-banner__shop-card-name"><?php echo esc_html($ese_slide['title']); ?></span>
                                        <!-- <span class="catalogo-banner__shop-card-meta"><?php echo esc_html($ese_slide['desc']); ?></span> -->
                                    </span>
                                    <img class="catalogo-banner__shop-card-img"
                                        src="<?php echo esc_url($ese_slide['thumb']); ?>" alt="" loading="lazy"
                                        decoding="async">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="catalogo-banner__body">
            <p class="catalogo-banner__eyebrow"><?php echo esc_html($ese_catalogo_current['eyebrow']); ?></p>
            <h1 class="catalogo-banner__title"><?php echo esc_html($ese_catalogo_current['title']); ?></h1>
            <p class="catalogo-banner__desc"><?php echo esc_html($ese_catalogo_current['desc']); ?></p>

            <div class="catalogo-banner__bottom">
                <?php
                ese_latam_cta_button([
                    'href'  => $ese_catalogo_current['href'],
                    'label' => __('Ver producto', 'ese-latam'),
                    'class' => 'hero-cta--light',
                ]);
                ?>
            </div>
        </div>
    </div>

    <?php // Escena de la isla: solo el producto activo, parado sobre el pedestal. ?>
    <div class="catalogo-banner__scene">
        <div class="catalogo-banner__pedestal" aria-hidden="true">
            <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/island/island-catalogo.webp'); ?>" alt=""
                loading="lazy" decoding="async">
        </div>

        <div class="catalogo-banner__products">
            <div class="catalogo-banner__product catalogo-banner__product--main" data-product data-depth="1">
                <span class="product-card__shadow" aria-hidden="true" data-float-shadow></span>
                <img data-product-main-img src="<?php echo esc_url($ese_catalogo_current['img']); ?>"
                    alt="<?php echo esc_attr($ese_catalogo_current['title']); ?>" decoding="async" data-float
                    data-float-distance="14" data-float-duration="3.2">
            </div>
        </div>
    </div>

    <div class="catalogo-banner__bottom-bar">
        <div class="catalogo-banner__bottom-bar-header">
            <p class="catalogo-banner__counter">
                <span class="catalogo-banner__counter-current" data-catalogo-counter>01</span>
                <span class="catalogo-banner__counter-total">/<?php echo esc_html(str_pad((string) $ese_catalogo_total, 2, '0', STR_PAD_LEFT)); ?></span>
            </p>

            <?php if ($ese_catalogo_total > 1): ?>
                <div class="catalogo-banner__shop-controls">
                    <button type="button" class="catalogo-banner__control-btn" data-catalogo-slider-prev
                        aria-label="<?php esc_attr_e('Producto anterior', 'ese-latam'); ?>">
                        <svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M5 1L1 5L5 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="catalogo-banner__dots" data-catalogo-dots>
                        <?php for ($ese_d = 0; $ese_d < $ese_catalogo_total; $ese_d++): ?>
                            <button type="button" class="catalogo-banner__dot<?php echo 0 === $ese_d ? ' is-active' : ''; ?>"
                                data-catalogo-dot="<?php echo (int) $ese_d; ?>"
                                aria-label="<?php echo esc_attr(sprintf(__('Ir al producto %d', 'ese-latam'), $ese_d + 1)); ?>"></button>
                        <?php endfor; ?>
                    </div>
                    <button type="button" class="catalogo-banner__control-btn" data-catalogo-slider-next
                        aria-label="<?php esc_attr_e('Producto siguiente', 'ese-latam'); ?>">
                        <svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M1 1L5 5L1 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <div class="catalogo-banner__progress" aria-hidden="true">
            <span class="catalogo-banner__progress-bar" data-catalogo-progress-bar style="width: <?php echo esc_attr(number_format((float)(100 / max(1, $ese_catalogo_total)), 2, '.', '')); ?>%;"></span>
        </div>
    </div>
</section>
