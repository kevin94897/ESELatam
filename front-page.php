<?php
/**
 * Front page — Hero "Estado A" (Figma node 4271-1457) con animación de scroll:
 * video scrubbed, isla flotante que aparece y nubes que cubren la transición.
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();
?>

<section class="hero" data-hero>
    <video class="hero__video" data-hero-video
        src="<?php echo esc_url(ESE_LATAM_URI . '/assets/video/hero-banner-video.mp4'); ?>" muted playsinline
        preload="auto" aria-hidden="true" tabindex="-1"></video>

    <div class="hero__shade" aria-hidden="true"></div>

    <div class="hero__island" data-hero-island aria-hidden="true">
        <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/island.png'); ?>" alt="" width="2400" height="1350"
            decoding="async">
    </div>

    <div class="hero__content">
        <div class="hero__title-wrap" data-hero-title-wrap>
            <h1 class="hero__title">
                <span class="hero__title-line"><span class="hero__title-inner"
                        data-hero-line><?php esc_html_e('Contener', 'ese-latam'); ?></span></span>
                <span class="hero__title-line"><span class="hero__title-inner"
                        data-hero-line><?php esc_html_e('para', 'ese-latam'); ?>
                        <strong><?php esc_html_e('transformar', 'ese-latam'); ?></strong></span></span>
            </h1>
        </div>

        <div class="hero__bottom" data-hero-bottom>
            <div class="hero__intro">
                <p class="hero__lede" data-hero-reveal>
                    <?php esc_html_e('Diseñamos y distribuimos soluciones de contención de residuos que combinan ingeniería, certificaciones y acompañamiento en cada sector.', 'ese-latam'); ?>
                </p>

                <?php
                ese_latam_cta_button([
                    'href' => '#sectores',
                    'label' => __('Explorar productos', 'ese-latam'),
                    'reveal' => true,
                ]);
                ?>

            </div>

            <aside class="hero-card">
                <div class="hero-card__body">
                    <p class="hero-card__stat">100%</p>
                    <p class="hero-card__tag"><?php esc_html_e('HDPE de alta calidad', 'ese-latam'); ?></p>
                    <p class="hero-card__desc">
                        <?php esc_html_e('Contenedores de residuos sólidos para municipios y empresas en Latinoamérica', 'ese-latam'); ?>
                    </p>
                </div>
                <!-- <div class="hero-card__dots" aria-hidden="true">
                    <span class="is-active"></span><span></span><span></span>
                </div> -->
            </aside>
        </div>
    </div>

</section>

<?php
// Sección "Sectores" (Figma node 3266-2331) — slider Embla de áreas de impacto
$ese_sectores = [
    [
        'title' => __('Municipalidades y gobiernos locales', 'ese-latam'),
        'desc' => __('Contenerización certificada para recolección urbana a gran escala.', 'ese-latam'),
        'img' => 'municipalidades.jpg',
    ],
    [
        'title' => __('Empresas de recolección', 'ese-latam'),
        'desc' => __('Flotas de contenedores compatibles con sistemas de carga mecanizada.', 'ese-latam'),
        'img' => 'recoleccion.jpg',
    ],
    [
        'title' => __('Inmobiliarias', 'ese-latam'),
        'desc' => __('Soluciones de contención para edificios y condominios.', 'ese-latam'),
        'img' => 'municipalidades.jpg',
    ],
    [
        'title' => __('Hospitalarios', 'ese-latam'),
        'desc' => __('Contenedores certificados para residuos biocontaminados.', 'ese-latam'),
        'img' => 'recoleccion.jpg',
    ],
    [
        'title' => __('Uso doméstico', 'ese-latam'),
        'desc' => __('Contenedores durables para la gestión de residuos en el hogar.', 'ese-latam'),
        'img' => 'municipalidades.jpg',
    ],
    [
        'title' => __('Supermercados y aeropuertos', 'ese-latam'),
        'desc' => __('Gestión de alto tránsito para espacios comerciales y terminales.', 'ese-latam'),
        'img' => 'recoleccion.jpg',
    ],
    [
        'title' => __('Restaurantes y hostelería', 'ese-latam'),
        'desc' => __('Contención higiénica para operaciones gastronómicas.', 'ese-latam'),
        'img' => 'municipalidades.jpg',
    ],
    [
        'title' => __('Industria y manufactura', 'ese-latam'),
        'desc' => __('Operaciones de largo plazo en entornos industriales exigentes.', 'ese-latam'),
        'img' => 'recoleccion.jpg',
    ],
];
?>
<section id="sectores" class="hero-next bg-white relative z-10">
    <div class="hero-next__clouds" aria-hidden="true" data-reveal="fade">
        <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/clouds.png'); ?>" alt="" decoding="async">
    </div>

    <div class="sectores" data-embla data-embla-contain="false" data-embla-loop="true" data-embla-autoplay="6000">
        <header class="sectores__header" data-reveal-header>
            <div>
                <p class="type-kicker text-secondary">/ <?php esc_html_e('Áreas de impacto', 'ese-latam'); ?></p>
                <h2 class="type-h2 mt-6 uppercase">
                    <?php esc_html_e('sectores que', 'ese-latam'); ?><br>
                    <span class="hl"><?php esc_html_e('transformamos', 'ese-latam'); ?></span>
                </h2>
            </div>
            <p class="sectores__desc">
                <?php esc_html_e('Brindamos un', 'ese-latam'); ?>
                <strong><?php esc_html_e('enfoque estructurado', 'ese-latam'); ?></strong>
                <?php esc_html_e('para la gestión de residuos en diversos entornos, desde la implementación de infraestructura pesada hasta operaciones de', 'ese-latam'); ?>
                <strong><?php esc_html_e('largo plazo', 'ese-latam'); ?></strong>
                <?php esc_html_e('en zonas urbanas.', 'ese-latam'); ?>
            </p>
        </header>

        <div class="sectores__slider">
            <div class="sectores__legend" data-reveal="right">
                <div class="sectores__legend-text">
                    <p class="sectores__counter">
                        <span data-embla-current>01</span>
                        <span class="sectores__counter-total" data-embla-total>/08</span>
                    </p>
                    <p><?php esc_html_e('Contenedores adaptados a cada sector: desde infraestructura urbana hasta operaciones industriales de largo plazo.', 'ese-latam'); ?>
                    </p>
                    <div class="sectores__progress" aria-hidden="true">
                        <span class="sectores__progress-bar" data-embla-progress></span>
                    </div>
                </div>
                <div class="sectores__controls">
                    <button type="button" class="embla__arrow embla__arrow--solid is-mirrored" data-embla-prev
                        aria-label="<?php esc_attr_e('Slide anterior', 'ese-latam'); ?>">
                        <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path
                                d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                    <button type="button" class="embla__arrow embla__arrow--solid" data-embla-next
                        aria-label="<?php esc_attr_e('Slide siguiente', 'ese-latam'); ?>">
                        <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path
                                d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="embla__viewport" data-embla-viewport>
                <div class="embla__container" data-reveal-stagger data-reveal-delay="0.2">
                    <?php foreach ($ese_sectores as $sector): ?>
                        <article class="embla__slide sector-card">
                            <img class="sector-card__img"
                                src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/sectores/' . $sector['img']); ?>"
                                alt="<?php echo esc_attr($sector['title']); ?>" loading="lazy" decoding="async">
                            <a href="#" class="sector-card__chip"
                                aria-label="<?php echo esc_attr(sprintf(__('Ver más sobre %s', 'ese-latam'), $sector['title'])); ?>">
                                <svg width="16" height="13" viewBox="0 0 16 13" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path
                                        d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                                        fill="currentColor" />
                                </svg>
                            </a>
                            <div class="sector-card__body">
                                <h3 class="sector-card__title"><?php echo esc_html($sector['title']); ?></h3>
                                <p class="sector-card__desc"><?php echo esc_html($sector['desc']); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php // Marquee "Transformamos" (Figma node 3287-283) — texto gigante ligado al
      // scroll. Las dos pasadas van en UNA sola sección: marquee.ts anima por
      // track ([data-marquee]), no por sección, así que la segunda conserva su
      // sentido invertido y además ambas comparten el mismo rango de scroll
      // (el trigger es el padre), con lo que se espejan exactamente. ?>
<section class="marquee bg-white relative z-10" aria-hidden="true">
    <div class="marquee__track" data-marquee>
        <span><?php esc_html_e('Transformamos', 'ese-latam'); ?></span>
        <span><?php esc_html_e('Transformamos', 'ese-latam'); ?></span>
        <span><?php esc_html_e('Transformamos', 'ese-latam'); ?></span>
    </div>

    <?php // data-marquee="right" invierte el recorrido del scrub (ver marquee.ts) ?>
    <div class="marquee__track" data-marquee="right">
        <span><?php esc_html_e('Transformamos', 'ese-latam'); ?></span>
        <span><?php esc_html_e('Transformamos', 'ese-latam'); ?></span>
        <span><?php esc_html_e('Transformamos', 'ese-latam'); ?></span>
    </div>
</section>

<?php
// Sección "Productos" (Figma node 3287-243) — slider de foco central con
// parallax de fondo. Los productos alternan las 3 fotos disponibles.
$ese_productos = [
    ['cat' => __('Soterrados', 'ese-latam'), 'name' => __('Contenedor de 2 ruedas', 'ese-latam'), 'litraje' => '5000 Litros', 'material' => 'HDPE Virgen', 'img' => 'bin-3.png'],
    ['cat' => __('Contenedores', 'ese-latam'), 'name' => __('Contenedor de 4 ruedas', 'ese-latam'), 'litraje' => '1100 Litros', 'material' => 'HDPE Reciclado', 'img' => 'bin-1.png'],
    ['cat' => __('Contenedores', 'ese-latam'), 'name' => __('Contenedor de 2 ruedas', 'ese-latam'), 'litraje' => '240 Litros', 'material' => 'HDPE Virgen', 'img' => 'bin-2.png'],
    ['cat' => __('Papeleras', 'ese-latam'), 'name' => __('Papelera urbana', 'ese-latam'), 'litraje' => '120 Litros', 'material' => 'HDPE Reciclado', 'img' => 'bin-1.png'],
    ['cat' => __('Biológicos', 'ese-latam'), 'name' => __('Contenedor sanitario', 'ese-latam'), 'litraje' => '360 Litros', 'material' => 'HDPE Virgen', 'img' => 'bin-2.png'],
    ['cat' => __('Domésticos', 'ese-latam'), 'name' => __('Contenedor doméstico', 'ese-latam'), 'litraje' => '120 Litros', 'material' => 'HDPE Reciclado', 'img' => 'bin-3.png'],
    ['cat' => __('Soterrados', 'ese-latam'), 'name' => __('Contenedor soterrado', 'ese-latam'), 'litraje' => '3000 Litros', 'material' => 'HDPE Virgen', 'img' => 'bin-1.png'],
    ['cat' => __('Contenedores', 'ese-latam'), 'name' => __('Contenedor de 3 ruedas', 'ese-latam'), 'litraje' => '770 Litros', 'material' => 'HDPE Reciclado', 'img' => 'bin-2.png'],
];
$ese_producto_filtros = [
    __('Ver todo', 'ese-latam'),
    __('Contenedores', 'ese-latam'),
    __('Papeleras', 'ese-latam'),
    __('Soterrados', 'ese-latam'),
    __('Biológicos', 'ese-latam'),
    __('Domésticos', 'ese-latam'),
];
?>
<section id="productos" class="productos-wrap bg-white relative z-10">
    <div class="productos">
        <div class="productos__bg" aria-hidden="true">
            <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/productos/bg-skyline.png'); ?>" alt=""
                loading="lazy" decoding="async">
        </div>
        <?php // Las hojas entran con un fade al llegar la sección. `fade` (y no
        // una variante direccional) porque --right va espejada con
        // transform: scaleX(-1) y esa variante no toca transforms. ?>
        <img class="productos__leaves productos__leaves--left"
            src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/productos/leaves.png'); ?>" alt="" loading="lazy"
            decoding="async" aria-hidden="true" data-reveal="corner-tl">
        <img class="productos__leaves productos__leaves--right"
            src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/productos/leaves.png'); ?>" alt="" loading="lazy"
            decoding="async" aria-hidden="true" data-reveal="corner-tr" data-reveal-delay="0.18">

        <header class="productos__header" data-reveal-header>
            <p class="type-kicker text-white/90">/ <?php esc_html_e('Nuestra gama de productos', 'ese-latam'); ?></p>
            <h2 class="productos__title">
                <?php esc_html_e('soluciones para', 'ese-latam'); ?>
                <strong><?php esc_html_e('cada necesidad', 'ese-latam'); ?></strong>
            </h2>
            <p class="productos__desc">
                <?php esc_html_e('Desde 120 hasta 5,000 litros, en HDPE reciclado certificado, con opciones de 2, 3 y 4 ruedas para distintos volúmenes y frecuencias de recolección.', 'ese-latam'); ?>
            </p>
        </header>

        <div class="productos__filters" data-reveal="up" role="tablist"
            aria-label="<?php esc_attr_e('Filtrar productos', 'ese-latam'); ?>">
            <?php foreach ($ese_producto_filtros as $i => $filtro): ?>
                <button type="button" class="productos__filter<?php echo $i === 0 ? ' is-active' : ''; ?>">
                    <?php echo esc_html($filtro); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="productos__slider" data-reveal="up">
            <button type="button" class="embla__arrow embla__arrow--light is-mirrored" data-carousel-prev
                aria-label="<?php esc_attr_e('Producto anterior', 'ese-latam'); ?>">
                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path
                        d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                        fill="currentColor" />
                </svg>
            </button>

            <div class="swiper" data-product-carousel data-carousel-visible="5">
                <div class="swiper-wrapper">
                    <?php foreach ($ese_productos as $producto): ?>
                        <article class="swiper-slide product-card">
                            <div class="product-card__media">
                                <span class="product-card__shadow" aria-hidden="true" data-float-shadow></span>
                                <img class="product-card__img"
                                    src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/productos/' . $producto['img']); ?>"
                                    alt="<?php echo esc_attr($producto['name']); ?>" loading="lazy" decoding="async"
                                    data-float data-float-distance="14" data-float-duration="3.2">
                            </div>
                            <div class="product-card__body">
                                <p class="product-card__cat"><?php echo esc_html($producto['cat']); ?></p>
                                <h3 class="product-card__name"><?php echo esc_html($producto['name']); ?></h3>
                                <dl class="product-card__specs">
                                    <div>
                                        <dt><?php esc_html_e('Litraje', 'ese-latam'); ?></dt>
                                        <dd><?php echo esc_html($producto['litraje']); ?></dd>
                                    </div>
                                    <div>
                                        <dt><?php esc_html_e('Material', 'ese-latam'); ?></dt>
                                        <dd><?php echo esc_html($producto['material']); ?></dd>
                                    </div>
                                </dl>
                            </div>
                            <span class="product-card__chip" aria-hidden="true">
                                <svg width="16" height="13" viewBox="0 0 16 13" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="button" class="embla__arrow embla__arrow--light" data-carousel-next
                aria-label="<?php esc_attr_e('Producto siguiente', 'ese-latam'); ?>">
                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path
                        d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                        fill="currentColor" />
                </svg>
            </button>
        </div>

        <div class="productos__pagination" aria-hidden="true"></div>

        <a href="#" class="productos__cta" data-reveal="up">
            <?php esc_html_e('Explora todo el catálogo', 'ese-latam'); ?>
        </a>
    </div>
</section>

<?php
// Sección "Distribuidores" (Figma node 3323-55) — globo 3D interactivo
// (Three.js + GSAP, ver globe-scene.ts) con lista de países a la derecha:
// al hacer clic en un país, el globo gira y hace zoom hasta señalarlo.
//
// Datos de contacto de distribuidores: solo Perú trae contenido real del
// diseño (Figma); el resto de países son placeholder a la espera de la
// data real del cliente — reemplazar antes de publicar.
$ese_distribuidores = [
    [
        'slug' => 'peru',
        'name' => __('Perú', 'ese-latam'),
        'lat' => -9.19,
        'lng' => -75.02,
        'items' => [
            ['name' => 'Plásticos Roca', 'address' => 'Av. López Paso 943, Sol Carmen de la Legua, Callao'],
            ['name' => 'SA', 'address' => 'Av. Argentina 1044, Mariano Melgar, Arequipa'],
        ],
    ],
    [
        'slug' => 'chile',
        'name' => __('Chile', 'ese-latam'),
        'lat' => -35.68,
        'lng' => -71.54,
        'items' => [
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
        ],
    ],
    [
        'slug' => 'colombia',
        'name' => __('Colombia', 'ese-latam'),
        'lat' => 4.57,
        'lng' => -74.30,
        'items' => [
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
        ],
    ],
    [
        'slug' => 'argentina',
        'name' => __('Argentina', 'ese-latam'),
        'lat' => -38.42,
        'lng' => -63.62,
        'items' => [
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
        ],
    ],
    [
        'slug' => 'mexico',
        'name' => __('México', 'ese-latam'),
        'lat' => 23.63,
        'lng' => -102.55,
        'items' => [
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
        ],
    ],
    [
        'slug' => 'ecuador',
        'name' => __('Ecuador', 'ese-latam'),
        'lat' => -1.83,
        'lng' => -78.18,
        'items' => [
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
        ],
    ],
    [
        'slug' => 'panama',
        'name' => __('Panamá', 'ese-latam'),
        'lat' => 8.54,
        'lng' => -80.78,
        'items' => [
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
        ],
    ],
    [
        'slug' => 'costa-rica',
        'name' => __('Costa Rica', 'ese-latam'),
        'lat' => 9.75,
        'lng' => -83.75,
        'items' => [
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
        ],
    ],
    [
        'slug' => 'republica-dominicana',
        'name' => __('Rep. Dominicana', 'ese-latam'),
        'lat' => 18.74,
        'lng' => -70.16,
        'items' => [
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
        ],
    ],
    [
        'slug' => 'uruguay',
        'name' => __('Uruguay', 'ese-latam'),
        'lat' => -32.52,
        'lng' => -55.77,
        'items' => [
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
        ],
    ],
    [
        'slug' => 'paraguay',
        'name' => __('Paraguay', 'ese-latam'),
        'lat' => -23.44,
        'lng' => -58.44,
        'items' => [
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
        ],
    ],
    [
        'slug' => 'honduras',
        'name' => __('Honduras', 'ese-latam'),
        'lat' => 15.20,
        'lng' => -86.24,
        'items' => [
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
        ],
    ],
    [
        'slug' => 'el-salvador',
        'name' => __('El Salvador', 'ese-latam'),
        'lat' => 13.79,
        'lng' => -88.90,
        'items' => [
            ['name' => __('Distribuidor autorizado', 'ese-latam'), 'address' => __('Datos de contacto próximamente', 'ese-latam')],
        ],
    ],
];
?>
<section id="distribuidores" class="distribuidores relative z-10" data-globe>
    <div class="distribuidores__rings" aria-hidden="true">
        <span></span><span></span>
    </div>

    <?php // El globo es el fondo de la sección: ocupa todo y el resto flota encima. ?>
    <div class="distribuidores__globe" data-reveal="fade" data-lenis-prevent aria-hidden="true">
        <div data-globe-canvas
            data-earth-map="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/distribuidores/earth-daymap.jpg'); ?>"
            data-earth-specular="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/distribuidores/earth-specular.jpg'); ?>"
            data-earth-normal="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/distribuidores/earth-normal.jpg'); ?>">
        </div>
    </div>

    <div class="distribuidores__intro" data-reveal-header>
        <p class="type-kicker text-white/90">/ <?php esc_html_e('Distribuidores', 'ese-latam'); ?></p>
        <h2 class="distribuidores__title">
            <?php esc_html_e('Presencia', 'ese-latam'); ?> <strong><?php esc_html_e('sin', 'ese-latam'); ?></strong><br>
            <strong><?php esc_html_e('fronteras', 'ese-latam'); ?></strong>
        </h2>
        <p class="distribuidores__desc">
            <?php esc_html_e('Nuestra red de distribuidores autorizados que garantizan soporte técnico y repuestos originales.', 'ese-latam'); ?>
        </p>
    </div>

    <?php // Riel de países: los 10 visibles a la vez, sin scroll anidado. ?>
    <div class="distribuidores__rail" data-reveal="left" data-reveal-delay="0.15">
        <p class="distribuidores__rail-head">
            <span class="distribuidores__rail-count"><?php echo count($ese_distribuidores); ?></span>
            <?php esc_html_e('países conectados', 'ese-latam'); ?>
        </p>

        <div class="distribuidores__rail-items" data-country-list role="tablist"
            aria-label="<?php esc_attr_e('Países con distribuidor', 'ese-latam'); ?>">
            <?php foreach ($ese_distribuidores as $i => $pais): ?>
                <button type="button" class="country-pill<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab"
                    aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
                    aria-controls="distribuidor-<?php echo esc_attr($pais['slug']); ?>" data-country
                    data-country-slug="<?php echo esc_attr($pais['slug']); ?>"
                    data-lat="<?php echo esc_attr($pais['lat']); ?>" data-lng="<?php echo esc_attr($pais['lng']); ?>">
                    <span class="country-pill__dot" aria-hidden="true"></span>
                    <span class="country-pill__name"><?php echo esc_html($pais['name']); ?></span>
                    <span class="country-pill__count"><?php echo count($pais['items']); ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <?php // Tarjeta flotante: los paneles se apilan en la misma celda de grilla,
    // así la tarjeta toma el alto del más largo y no salta al cambiar de país. ?>
    <div class="distribuidores__panel" data-reveal="up" data-reveal-delay="0.25">
        <div class="distribuidores__panel-stack" data-country-panels>
            <?php foreach ($ese_distribuidores as $i => $pais): ?>
                <article class="country-panel<?php echo 0 === $i ? ' is-active' : ''; ?>"
                    id="distribuidor-<?php echo esc_attr($pais['slug']); ?>"
                    data-country-panel="<?php echo esc_attr($pais['slug']); ?>" role="tabpanel">
                    <p class="country-panel__kicker"><?php esc_html_e('Distribuidores en', 'ese-latam'); ?></p>
                    <h3 class="country-panel__name"><?php echo esc_html($pais['name']); ?></h3>

                    <ul class="country-panel__list">
                        <?php foreach ($pais['items'] as $item): ?>
                            <li class="country-panel__item">
                                <span class="country-panel__avatar"
                                    aria-hidden="true"><?php echo esc_html(mb_substr($item['name'], 0, 1)); ?></span>
                                <span class="country-panel__text">
                                    <strong><?php echo esc_html($item['name']); ?></strong>
                                    <span><?php echo esc_html($item['address']); ?></span>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </div>

        <a href="#contacto" class="distribuidores__link">
            <span class="distribuidores__link-text"><?php esc_html_e('Contactar distribuidor', 'ese-latam'); ?></span>
            <span class="distribuidores__link-icon" aria-hidden="true">
                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                        fill="currentColor" />
                </svg>
            </span>
        </a>
    </div>
</section>

<?php
// Sección "Residuos Inteligentes" (Figma node 3328-3006) — selector de
// servicios (Educar/Segregar/Transformar): clic en una tarjeta cambia el
// panel de la derecha (foto + título + descripción) con un crossfade GSAP.
// Solo "Educar" trae copy real del Figma; Segregar/Transformar son
// placeholder (mismo tono de marca) a la espera de contenido del cliente,
// y reutilizan fotos ya existentes en el theme en vez de traer nuevas.
$ese_servicios = [
    [
        'slug' => 'educar',
        'title' => __('Educar', 'ese-latam'),
        'desc' => __('Fomentamos la cultura del reciclaje a través de contenedores con señalética clara y pedagogía urbana, facilitando la identificación correcta de cada tipo de residuo.', 'ese-latam'),
        'icon' => 'icon-educar.svg',
        'img' => 'residuos/educar-thumb.jpg',
    ],
    [
        'slug' => 'segregar',
        'title' => __('Segregar', 'ese-latam'),
        'desc' => __('Clasificamos los residuos en origen con contenedores diferenciados por color y tipo, optimizando cada etapa de la recolección.', 'ese-latam'),
        'icon' => 'icon-segregar.svg',
        'img' => 'sectores/recoleccion.jpg',
    ],
    [
        'slug' => 'transformar',
        'title' => __('Transformar', 'ese-latam'),
        'desc' => __('Convertimos los residuos correctamente segregados en materia prima para nuevos productos, cerrando el ciclo de la economía circular.', 'ese-latam'),
        'icon' => 'icon-transformar.svg',
        'img' => 'sectores/municipalidades.jpg',
    ],
];
?>
<section id="residuos-inteligentes" class="residuos bg-white relative z-10">
    <header class="residuos__header" data-reveal-header>
        <p class="type-kicker text-secondary">/ <?php esc_html_e('Residuos inteligentes', 'ese-latam'); ?></p>
        <h2 class="type-h2 uppercase text-center">
            <?php esc_html_e('ingeniería de', 'ese-latam'); ?><br>
            <span class="hl"><?php esc_html_e('alto desempeño', 'ese-latam'); ?></span>
        </h2>
        <p class="residuos__desc">
            <?php esc_html_e('Explora nuestra gama de productos diseñados para la', 'ese-latam'); ?>
            <span class="text-accent font-extrabold"><?php esc_html_e('eficiencia operativa', 'ese-latam'); ?></span>
            <?php esc_html_e('y la', 'ese-latam'); ?>
            <span class="text-accent font-extrabold"><?php esc_html_e('sostenibilidad', 'ese-latam'); ?></span>
            <?php esc_html_e('urbana en toda Latinoamérica.', 'ese-latam'); ?>
        </p>
    </header>

    <div class="residuos__selector" data-service-selector data-service-autoplay="5000" data-reveal="up">
        <div class="residuos__tabs">
            <?php foreach ($ese_servicios as $i => $servicio): ?>
                <button type="button" class="residuos__tab<?php echo 0 === $i ? ' is-active' : ''; ?>" data-service-tab
                    data-title="<?php echo esc_attr($servicio['title']); ?>"
                    data-desc="<?php echo esc_attr($servicio['desc']); ?>"
                    data-img="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/' . $servicio['img']); ?>">
                    <span class="residuos__tab-icon" aria-hidden="true">
                        <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/icons/' . $servicio['icon']); ?>" alt=""
                            loading="lazy">
                    </span>
                    <span class="residuos__tab-text">
                        <span class="residuos__tab-title"><?php echo esc_html($servicio['title']); ?></span>
                        <span
                            class="residuos__tab-sub"><?php esc_html_e('Información clara para tomar decisiones', 'ese-latam'); ?></span>
                    </span>
                    <span class="residuos__tab-arrow" aria-hidden="true">
                        <svg width="9" height="15" viewBox="0 0 9 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1.15 14L0 12.9L3.85 7.5L0 2.1L1.15 1L6 7.5L1.15 14Z" fill="currentColor" />
                        </svg>
                    </span>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="residuos__panel">
            <div class="residuos__media" data-service-media>
                <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/' . $ese_servicios[0]['img']); ?>" alt=""
                    data-service-img loading="lazy" decoding="async">
                <span class="residuos__play" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M8 6.82v10.36c0 .8.87 1.29 1.55.86l8.14-5.18a1 1 0 0 0 0-1.72L9.55 5.96A1 1 0 0 0 8 6.82Z"
                            fill="currentColor" />
                    </svg>
                </span>
            </div>
            <h3 class="residuos__panel-title" data-service-title><?php echo esc_html($ese_servicios[0]['title']); ?>
            </h3>
            <p class="residuos__panel-desc" data-service-desc><?php echo esc_html($ese_servicios[0]['desc']); ?></p>
        </div>
    </div>

    <a href="#impacto" class="link-arrow">
        <span class="link-arrow__text"><?php esc_html_e('Conoce nuestro impacto', 'ese-latam'); ?></span>
        <span class="link-arrow__icon" aria-hidden="true">
            <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                    fill="currentColor" />
            </svg>
        </span>
    </a>
</section>

<?php
// Sección "Certificaciones" (Figma node 3328-7259) — logos de estándares/certificaciones
$ese_certificaciones = [
    ['name' => 'Blue Angel', 'desc' => __('Bajo impacto ambiental', 'ese-latam'), 'img' => 'blue-angel.png'],
    ['name' => 'PKN', 'desc' => __('Cumplimiento de normas europeas', 'ese-latam'), 'img' => 'pkn.png'],
    ['name' => 'Seconda Vita', 'desc' => __('Certificado de material reciclado', 'ese-latam'), 'img' => 'seconda-vita.png'],
    ['name' => 'DIN', 'desc' => __('Estándares de ingeniería alemana', 'ese-latam'), 'img' => 'din.png'],
    ['name' => 'TÜV SÜD', 'desc' => __('Inspección técnica y resistencia', 'ese-latam'), 'img' => 'tuv-sud.png'],
];
?>
<section id="certificaciones" class="certificaciones bg-white relative z-10">
    <header class="certificaciones__header" data-reveal-header>
        <div class="certificaciones__heading-group">
            <p class="type-kicker text-secondary">/ <?php esc_html_e('Estándar global', 'ese-latam'); ?></p>
            <h2 class="type-h2 uppercase">
                <?php esc_html_e('nuestras', 'ese-latam'); ?>
                <span class="hl"><?php esc_html_e('certificaciones', 'ese-latam'); ?></span>
            </h2>
            <p class="certificaciones__desc">
                <?php esc_html_e('Dependiendo de la exigencia del entorno y las líneas de producto, nuestras certificaciones respaldan nuestra', 'ese-latam'); ?>
                <span class="hl-accent"><?php esc_html_e('durabilidad', 'ese-latam'); ?></span>
                <?php esc_html_e('y', 'ese-latam'); ?>
                <span class="hl-accent"><?php esc_html_e('eficiencia.', 'ese-latam'); ?></span>
            </p>
        </div>

        <?php // Cierra la cascada del header (título → bajada → kicker) ?>
        <a href="#" class="link-arrow" data-reveal="up" data-reveal-delay="0.5">
            <span
                class="link-arrow__text"><?php esc_html_e('Explora todas nuestras certificaciones', 'ese-latam'); ?></span>
            <span class="link-arrow__icon" aria-hidden="true">
                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                        fill="currentColor" />
                </svg>
            </span>
        </a>
    </header>

    <div class="certificaciones__row" data-reveal-stagger data-reveal-delay="0.15">
        <?php foreach ($ese_certificaciones as $cert): ?>
            <article class="certificaciones__item">
                <div class="certificaciones__logo">
                    <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/certificaciones/' . $cert['img']); ?>"
                        alt="<?php echo esc_attr($cert['name']); ?>" loading="lazy" decoding="async">
                </div>
                <span class="certificaciones__divider" aria-hidden="true"></span>
                <p class="certificaciones__name"><?php echo esc_html($cert['name']); ?></p>
                <p class="certificaciones__item-desc"><?php echo esc_html($cert['desc']); ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php // Sección "Contactemos" (Figma node 535-782) — CTA de cierre, al pie de todo el contenido ?>
<section id="contacto" class="contacto">
    <div class="contacto__bg" aria-hidden="true">
        <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/contacto-bg.jpg'); ?>" alt="" loading="lazy"
            decoding="async">
    </div>

    <div class="contacto__card" data-reveal="fade">
        <p class="type-kicker text-white">/ <?php esc_html_e('Contactemos', 'ese-latam'); ?></p>

        <h2 class="contacto__heading" data-contacto-heading>
            <?php esc_html_e('¿Listo para llevar tu gestión de residuos al ', 'ese-latam'); ?><?php esc_html_e('siguiente nivel', 'ese-latam'); ?></span>?
        </h2>

        <hr class="contacto__divider">

        <div class="contacto__row">
            <p class="contacto__desc">
                <?php esc_html_e('Fomentamos la cultura del reciclaje a través de contenedores con señalética clara y pedagogía urbana, facilitando la identificación correcta de cada tipo de residuo.', 'ese-latam'); ?>
            </p>
            <?php
            ese_latam_cta_button([
                'href' => 'mailto:hola@eselatam.com',
                'label' => __('Contactar', 'ese-latam'),
            ]);
            ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>