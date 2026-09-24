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

/**
 * Gancho del slide: la capacidad máxima cuando es numérica ("Hasta 360L"),
 * y si no la categoría del producto, que siempre dice algo útil.
 */
function ese_latam_catalogo_eyebrow(string $max_litraje, int $post_id): string {
    if ('' !== $max_litraje && preg_match('/^\d/', $max_litraje)) {
        /* translators: %s: capacidad máxima del producto */
        return sprintf(__('Hasta %s', 'ese-latam'), $max_litraje);
    }

    $terms = get_the_terms($post_id, 'producto_categoria');
    if (is_array($terms) && ! empty($terms)) {
        return $terms[0]->name;
    }

    return __('Producto ESE Latam', 'ese-latam');
}

$ese_catalogo_slides = [];

if ($ese_catalogo_query->have_posts()) {
    while ($ese_catalogo_query->have_posts()) {
        $ese_catalogo_query->the_post();

        $litrajes    = get_field('litrajes');
        $max_litraje = is_array($litrajes) && ! empty($litrajes) ? end($litrajes)['valor'] : '';

        // La foto del producto en su capacidad por defecto, igual que la
        // ficha y las tarjetas (ver ese_latam_producto_foto). Antes caía a un
        // contenedor de 3 ruedas del theme, así que un producto sin foto se
        // anunciaba con una pieza ajena.
        $ese_slide_img = ese_latam_producto_foto(get_the_ID());

        $ese_catalogo_slides[] = [
            'title'   => get_the_title(),
            'desc'    => get_field('descripcion_corta') ?: get_the_excerpt(),
            // "Hasta 360L" solo tiene sentido con una capacidad numérica. Hay
            // productos cuya ficha técnica no declara litraje (una papelera
            // sobre poste, por ejemplo) y ahí el litraje es una etiqueta como
            // "Estándar": anunciarlos como "Hasta Estándar" no se lee. En ese
            // caso el gancho pasa a ser la categoría del producto.
            'eyebrow' => ese_latam_catalogo_eyebrow($max_litraje, get_the_ID()),
            'img'     => $ese_slide_img,
            'thumb'   => $ese_slide_img,
            'href'    => get_permalink(),
        ];
    }
    wp_reset_postdata();
}

// Sin productos publicados no hay banner: el theme no anuncia un contenedor
// de ejemplo (mismo criterio que el resto de los módulos).
if ([] === $ese_catalogo_slides) {
    return;
}

$ese_catalogo_total   = count($ese_catalogo_slides);
$ese_catalogo_current = $ese_catalogo_slides[0];
?>

<section class="catalogo-banner" data-product-island
    data-slides="<?php echo esc_attr(wp_json_encode($ese_catalogo_slides)); ?>">
    <?php
    // Foto de fondo: es el LCP de la página, así que va en WebP (el PNG de
    // 2.3 MB queda solo de fallback), con una versión de 900px para móvil
    // y fetchpriority alto para que no compita con el resto de assets.
    ?>
    <div class="catalogo-banner__bg" aria-hidden="true">
        <picture>
            <source type="image/webp" media="(max-width: 63.9375rem)"
                srcset="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/catalogo/banner-bg-mountain-900.webp'); ?>">
            <source type="image/webp"
                srcset="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/catalogo/banner-bg-mountain.webp'); ?>">
            <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/catalogo/banner-bg-mountain.png'); ?>" alt=""
                width="1536" height="796" decoding="async" fetchpriority="high">
        </picture>
    </div>
    <div class="catalogo-banner__shade" aria-hidden="true"></div>
    <div class="catalogo-banner__noise" aria-hidden="true"></div>
    <div class="catalogo-banner__grid" aria-hidden="true"></div>
    <p class="catalogo-banner__watermark" aria-hidden="true">
        <?php echo esc_html($ese_catalogo_current['title']); ?>
    </p>

    <div class="catalogo-banner__content">
        <?php
        get_template_part('template-parts/breadcrumbs', null, [
            'current' => __('Productos', 'ese-latam'),
            'class'   => 'catalogo-banner__crumb crumbs--light',
        ]);
        ?>

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
                                    <?php if ('' !== $ese_slide['thumb']) : ?>
                                        <img class="catalogo-banner__shop-card-img"
                                            src="<?php echo esc_url($ese_slide['thumb']); ?>" alt="" loading="lazy"
                                            decoding="async">
                                    <?php endif; ?>
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
                    'class' => '',
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
            <?php if ('' !== $ese_catalogo_current['img']) : ?>
                <div class="catalogo-banner__product catalogo-banner__product--main" data-product data-depth="1">
                    <span class="product-card__shadow" aria-hidden="true" data-float-shadow></span>
                    <img data-product-main-img src="<?php echo esc_url($ese_catalogo_current['img']); ?>"
                        alt="<?php echo esc_attr($ese_catalogo_current['title']); ?>" decoding="async" data-float
                        data-float-distance="14" data-float-duration="3.2">
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="catalogo-banner__bottom-bar">
        <div class="catalogo-banner__bottom-bar-header">
            <p class="catalogo-banner__counter">
                <span class="catalogo-banner__counter-current" data-catalogo-counter>01</span>
                <span class="catalogo-banner__counter-total">/<?php echo esc_html(str_pad((string) $ese_catalogo_total, 2, '0', STR_PAD_LEFT)); ?></span>
            </p>

            <?php if ($ese_catalogo_total > 1): ?>
                <?php // Mismas flechas cuadradas que .sectores__controls (home): dos
                // botones prev/next en vez de la píldora con dots. El JS
                // (product-island.ts) sigue leyendo data-catalogo-slider-prev/next. ?>
                <div class="catalogo-banner__arrows">
                    <button type="button" class="embla__arrow embla__arrow--solid is-mirrored" data-catalogo-slider-prev
                        aria-label="<?php esc_attr_e('Producto anterior', 'ese-latam'); ?>">
                        <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/></svg>
                    </button>
                    <button type="button" class="embla__arrow embla__arrow--solid" data-catalogo-slider-next
                        aria-label="<?php esc_attr_e('Producto siguiente', 'ese-latam'); ?>">
                        <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/></svg>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <div class="catalogo-banner__progress" aria-hidden="true">
            <span class="catalogo-banner__progress-bar" data-catalogo-progress-bar style="width: <?php echo esc_attr(number_format((float)(100 / max(1, $ese_catalogo_total)), 2, '.', '')); ?>%;"></span>
        </div>
    </div>
</section>
