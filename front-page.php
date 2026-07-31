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
           src="<?php echo esc_url(ESE_LATAM_URI . '/assets/video/hero-banner-video.mp4'); ?>"
           muted playsinline preload="auto" aria-hidden="true" tabindex="-1"></video>

    <div class="hero__shade" aria-hidden="true"></div>

    <div class="hero__island" data-hero-island aria-hidden="true">
        <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/island.png'); ?>"
             alt="" width="2400" height="1350" decoding="async">
    </div>

    <div class="hero__content">
        <h1 class="hero__title">
            <span class="hero__title-line"><span class="hero__title-inner" data-hero-line><?php esc_html_e('Contener', 'ese-latam'); ?></span></span>
            <span class="hero__title-line"><span class="hero__title-inner" data-hero-line><?php esc_html_e('para', 'ese-latam'); ?> <strong><?php esc_html_e('transformar', 'ese-latam'); ?></strong></span></span>
        </h1>

        <div class="hero__bottom">
            <div class="hero__intro">
                <p class="hero__lede" data-hero-reveal>
                    <?php esc_html_e('Diseñamos y distribuimos soluciones de contención de residuos que combinan ingeniería, certificaciones y acompañamiento en cada sector.', 'ese-latam'); ?>
                </p>

                <a href="#sectores" class="hero-cta" data-hero-reveal>
                    <span class="hero-cta__label"><?php esc_html_e('Explorar productos', 'ese-latam'); ?></span>
                    <span class="hero-cta__arrow" aria-hidden="true"></span>
                </a>
            </div>

            <aside class="hero-card" data-hero-reveal>
                <div class="hero-card__body">
                    <p class="hero-card__stat">100%</p>
                    <p class="hero-card__tag"><?php esc_html_e('HDPE de alta calidad', 'ese-latam'); ?></p>
                    <p class="hero-card__desc">
                        <?php esc_html_e('Contenedores de residuos sólidos para municipios y empresas en Latinoamérica', 'ese-latam'); ?>
                    </p>
                </div>
                <div class="hero-card__dots" aria-hidden="true">
                    <span class="is-active"></span><span></span><span></span>
                </div>
            </aside>
        </div>
    </div>

</section>

<?php
// Sección "Sectores" (Figma node 3266-2331) — slider Embla de áreas de impacto
$ese_sectores = [
    [
        'title' => __('Municipalidades y gobiernos locales', 'ese-latam'),
        'desc'  => __('Contenerización certificada para recolección urbana a gran escala.', 'ese-latam'),
        'img'   => 'municipalidades.jpg',
    ],
    [
        'title' => __('Empresas de recolección', 'ese-latam'),
        'desc'  => __('Flotas de contenedores compatibles con sistemas de carga mecanizada.', 'ese-latam'),
        'img'   => 'recoleccion.jpg',
    ],
    [
        'title' => __('Inmobiliarias', 'ese-latam'),
        'desc'  => __('Soluciones de contención para edificios y condominios.', 'ese-latam'),
        'img'   => 'municipalidades.jpg',
    ],
    [
        'title' => __('Hospitalarios', 'ese-latam'),
        'desc'  => __('Contenedores certificados para residuos biocontaminados.', 'ese-latam'),
        'img'   => 'recoleccion.jpg',
    ],
    [
        'title' => __('Uso doméstico', 'ese-latam'),
        'desc'  => __('Contenedores durables para la gestión de residuos en el hogar.', 'ese-latam'),
        'img'   => 'municipalidades.jpg',
    ],
    [
        'title' => __('Supermercados y aeropuertos', 'ese-latam'),
        'desc'  => __('Gestión de alto tránsito para espacios comerciales y terminales.', 'ese-latam'),
        'img'   => 'recoleccion.jpg',
    ],
    [
        'title' => __('Restaurantes y hostelería', 'ese-latam'),
        'desc'  => __('Contención higiénica para operaciones gastronómicas.', 'ese-latam'),
        'img'   => 'municipalidades.jpg',
    ],
    [
        'title' => __('Industria y manufactura', 'ese-latam'),
        'desc'  => __('Operaciones de largo plazo en entornos industriales exigentes.', 'ese-latam'),
        'img'   => 'recoleccion.jpg',
    ],
];
?>
<section id="sectores" class="hero-next bg-white relative z-10">
    <div class="hero-next__clouds" aria-hidden="true">
        <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/clouds.png'); ?>"
             alt="" decoding="async">
    </div>

    <div class="sectores" data-embla data-embla-contain="false">
        <header class="sectores__header" data-reveal="up">
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

        <div class="sectores__slider" data-reveal="up">
            <div class="sectores__legend">
                <div class="sectores__legend-text">
                    <p class="sectores__counter">
                        <span data-embla-current>01</span>
                        <span class="sectores__counter-total" data-embla-total>/08</span>
                    </p>
                    <p><?php esc_html_e('Contenedores adaptados a cada sector: desde infraestructura urbana hasta operaciones industriales de largo plazo.', 'ese-latam'); ?></p>
                </div>
                <div class="sectores__controls">
                    <button type="button" class="embla__arrow embla__arrow--solid is-mirrored" data-embla-prev
                            aria-label="<?php esc_attr_e('Slide anterior', 'ese-latam'); ?>">
                        <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/>
                        </svg>
                    </button>
                    <button type="button" class="embla__arrow embla__arrow--solid" data-embla-next
                            aria-label="<?php esc_attr_e('Slide siguiente', 'ese-latam'); ?>">
                        <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="embla__viewport" data-embla-viewport>
                <div class="embla__container">
                    <?php foreach ($ese_sectores as $sector) : ?>
                        <article class="embla__slide sector-card">
                            <img class="sector-card__img"
                                 src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/sectores/' . $sector['img']); ?>"
                                 alt="<?php echo esc_attr($sector['title']); ?>" loading="lazy" decoding="async">
                            <span class="sector-card__chip" aria-hidden="true">
                                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/>
                                </svg>
                            </span>
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

<?php get_footer(); ?>
