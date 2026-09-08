<?php
/**
 * Sección "Certificaciones" (Figma node 3328-7259) — franja de logos de
 * estándares/certificaciones. Compartida entre front-page.php (variante
 * clara, la del diseño original) y single-producto.php (variante oscura,
 * arriba de template-parts/contacto.php).
 *
 * @param array{dark?: bool} $args 'dark' => true pinta la variante oscura
 *   (fondo navy, texto blanco, logos sobre chip blanco). Por defecto false.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_dark = ! empty($args['dark']);

$ese_certificaciones = [
    ['name' => 'Blue Angel', 'desc' => __('Bajo impacto ambiental', 'ese-latam'), 'img' => 'blue-angel.png'],
    ['name' => 'PKN', 'desc' => __('Cumplimiento de normas europeas', 'ese-latam'), 'img' => 'pkn.png'],
    ['name' => 'Seconda Vita', 'desc' => __('Certificado de material reciclado', 'ese-latam'), 'img' => 'seconda-vita.png'],
    ['name' => 'DIN', 'desc' => __('Estándares de ingeniería alemana', 'ese-latam'), 'img' => 'din.png'],
    ['name' => 'TÜV SÜD', 'desc' => __('Inspección técnica y resistencia', 'ese-latam'), 'img' => 'tuv-sud.png'],
];
?>
<section id="certificaciones" class="certificaciones<?php echo $ese_dark ? ' certificaciones--dark' : ''; ?> relative z-10">
    <header class="certificaciones__header" data-reveal-header>
        <div class="certificaciones__heading-group">
            <p class="type-kicker text-secondary">/ <?php esc_html_e('Estándar global', 'ese-latam'); ?></p>
            <?php // Clases propias en vez de .type-h2/.hl: esos dos son @utility de
            // Tailwind, que compilan a la capa "utilities" — con más prioridad que
            // cualquier @layer components sin importar la especificidad del
            // selector, así que .certificaciones--dark nunca podría pisarlos.
            // Mismo criterio que .producto-hero__title/.contacto__heading, que por
            // la misma razón tampoco reutilizan .type-h2. ?>
            <h2 class="certificaciones__title">
                <?php esc_html_e('nuestras', 'ese-latam'); ?>
                <span class="certificaciones__title-accent"><?php esc_html_e('certificaciones', 'ese-latam'); ?></span>
            </h2>
            <p class="certificaciones__desc">
                <?php esc_html_e('Dependiendo de la exigencia del entorno y las líneas de producto, nuestras certificaciones respaldan nuestra', 'ese-latam'); ?>
                <span class="hl-accent"><?php esc_html_e('durabilidad', 'ese-latam'); ?></span>
                <?php esc_html_e('y', 'ese-latam'); ?>
                <span class="hl-accent"><?php esc_html_e('eficiencia.', 'ese-latam'); ?></span>
            </p>
        </div>

        <?php // Cierra la cascada del header (título → bajada → kicker) ?>
        <a href="#" class="link-arrow<?php echo $ese_dark ? ' link-arrow--light' : ''; ?>" data-reveal="up"
            data-reveal-delay="0.5">
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
        <?php foreach ($ese_certificaciones as $cert) : ?>
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
