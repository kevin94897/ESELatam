<?php
/**
 * Sección "Residuos Inteligentes" (Figma node 3328-3006) — selector de
 * servicios (Educar/Segregar/Transformar) con crossfade GSAP. Compartida
 * entre la home (front-page.php) y la página Impacto (page-impacto.php):
 * antes vivía inline en la home, se extrajo tal cual para no duplicarla.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

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
        'img' => 'sectores/recoleccion.webp',
    ],
    [
        'slug' => 'transformar',
        'title' => __('Transformar', 'ese-latam'),
        'desc' => __('Convertimos los residuos correctamente segregados en materia prima para nuevos productos, cerrando el ciclo de la economía circular.', 'ese-latam'),
        'icon' => 'icon-transformar.svg',
        'img' => 'sectores/municipalidades.webp',
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
