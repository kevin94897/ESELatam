<?php
/**
 * Ficha de producto — "Soluciones recomendadas" (Figma 5105-4693): carrusel
 * coverflow con los otros productos del catálogo, sobre fondo navy. Se
 * llama desde single-producto.php DENTRO del loop para poder dejar fuera el
 * producto que se está viendo.
 *
 * Las cards son el mismo `.product-card` del slider de la home y de la
 * grilla del catálogo (variante `--dark`), alimentadas por el mismo helper
 * (ese_latam_producto_card_data); el carrusel es el mismo módulo
 * (product-carousel.ts) con el coverflow inclinado vía `data-carousel-*`.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_actual_id = get_the_ID();

$ese_reco_query = new WP_Query([
    'post_type'      => 'producto',
    'post_status'    => 'publish',
    'posts_per_page' => 8,
    'post__not_in'   => [$ese_actual_id],
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
]);

$ese_recomendados = array_map(
    static fn (WP_Post $p): array => ese_latam_producto_card_data($p->ID),
    $ese_reco_query->posts
);

// Con menos de 3 "otros" productos vuelve a entrar el actual: así el
// coverflow tiene al menos una vecina distinta a cada lado del activo
// (product-carousel.ts cierra el anillo duplicando el set cuando hay pocos
// slides, pero no inventa productos). Si ni así llega a 2, cae al set de
// ejemplo — mismo criterio que el slider de la home.
if (count($ese_recomendados) < 3) {
    $ese_recomendados[] = ese_latam_producto_card_data($ese_actual_id);
}
if (count($ese_recomendados) < 2) {
    $ese_recomendados = ese_latam_productos_placeholder();
}
?>

<section id="soluciones-recomendadas" class="recomendados" data-carousel-root>
    <?php // Misma trama de puntos del hero de la ficha (fondo navy también). ?>
    <div class="producto-hero__dots" aria-hidden="true"></div>

    <header class="recomendados__header" data-reveal-header>
        <p class="type-kicker text-white">/ <?php esc_html_e('Productos', 'ese-latam'); ?></p>
        <?php // Clases propias en vez de .type-h2/.hl: son @utility de Tailwind y
        // su color no se puede pisar desde @layer components (ver
        // .certificaciones__title). ?>
        <h2 class="recomendados__title">
            <?php esc_html_e('Soluciones', 'ese-latam'); ?>
            <span class="recomendados__title-accent"><?php esc_html_e('recomendadas', 'ese-latam'); ?></span>
        </h2>
        <p class="recomendados__desc">
            <?php esc_html_e('Otras configuraciones de la misma familia, pensadas para distintos volúmenes y necesidades de recolección', 'ese-latam'); ?>
        </p>
    </header>

    <div class="recomendados__slider" data-reveal="up">
        <button type="button" class="embla__arrow embla__arrow--glass is-mirrored" data-carousel-prev
            aria-label="<?php esc_attr_e('Producto anterior', 'ese-latam'); ?>">
            <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true">
                <path
                    d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                    fill="currentColor" />
            </svg>
        </button>

        <?php // rotate 44 / depth 200 / modifier 1: cada vecina gira 44° y retrocede
        // 200px (≈ 0.6 del ancho de la card, como el componente de referencia);
        // la home mantiene sus valores por defecto (cards planas). ?>
        <div class="swiper" data-product-carousel data-carousel-visible="5" data-carousel-rotate="44"
            data-carousel-depth="200" data-carousel-modifier="1">
            <div class="swiper-wrapper">
                <?php foreach ($ese_recomendados as $ese_producto) : ?>
                    <article class="swiper-slide product-card product-card--glass">
                        <div class="product-card__media">
                            <span class="product-card__shadow" aria-hidden="true" data-float-shadow></span>
                            <img class="product-card__img" src="<?php echo esc_url($ese_producto['img']); ?>"
                                alt="<?php echo esc_attr($ese_producto['name']); ?>" loading="lazy" decoding="async"
                                data-float data-float-distance="14" data-float-duration="3.2">
                        </div>
                        <div class="product-card__body">
                            <p class="product-card__cat"><?php echo esc_html($ese_producto['cat']); ?></p>
                            <h3 class="product-card__name"><?php echo esc_html($ese_producto['name']); ?></h3>
                            <dl class="product-card__specs">
                                <div>
                                    <dt><?php esc_html_e('Litraje', 'ese-latam'); ?></dt>
                                    <dd><?php echo esc_html($ese_producto['litraje']); ?></dd>
                                </div>
                                <div>
                                    <dt><?php esc_html_e('Material', 'ese-latam'); ?></dt>
                                    <dd><?php echo esc_html($ese_producto['material']); ?></dd>
                                </div>
                            </dl>
                        </div>
                        <?php // El chip ES el enlace (no la card entera), igual que en la home y
                        // en la grilla del catálogo: el drag del slider no compite con él. ?>
                        <a class="product-card__chip" href="<?php echo esc_url($ese_producto['href']); ?>"
                            aria-label="<?php echo esc_attr(sprintf(__('Ver %s', 'ese-latam'), $ese_producto['name'])); ?>">
                            <svg width="16" height="13" viewBox="0 0 16 13" fill="none"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path
                                    d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                                    fill="currentColor" />
                            </svg>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="button" class="embla__arrow embla__arrow--glass" data-carousel-next
            aria-label="<?php esc_attr_e('Producto siguiente', 'ese-latam'); ?>">
            <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true">
                <path
                    d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                    fill="currentColor" />
            </svg>
        </button>
    </div>
</section>
