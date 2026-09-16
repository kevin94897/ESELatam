<?php
/**
 * Sección "Certificaciones" (Figma node 3328-7259) — franja de logos de
 * estándares/certificaciones. Compartida entre front-page.php (variante
 * clara, la del diseño original) y single-producto.php (variante oscura,
 * arriba de template-parts/contacto.php).
 *
 * El copy sale de ESE Latam → Certificaciones y los sellos de
 * ese_latam_certificaciones() (inc/contenido.php). Cada página puede pisar
 * el encabezado desde su propio editor ("Secciones compartidas"), y las
 * plantillas siguen pudiendo hacerlo por `$args` — ver la precedencia
 * documentada en inc/pcf.php.
 *
 * @param array{dark?: bool, centered?: bool, kicker?: string, title?: string, desc?: string} $args
 *   'dark' => true pinta la variante oscura (fondo navy, texto blanco, logos
 *   sobre chip blanco); 'title' cambia el titular ("calidad |certificada|" en
 *   la single de sector); 'centered' centra el header.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_cert_args = wp_parse_args($args ?? [], [
    'dark'     => false,
    'centered' => false,
]);
$ese_dark = ! empty($ese_cert_args['dark']);

$ese_cert = ese_latam_seccion_args(
    'certificaciones',
    $ese_cert_args,
    [
        'kicker'       => '',
        'title'        => '',
        'desc'         => '',
        'link'         => null,
    ],
    [
        'kicker'       => 'kicker',
        'title'        => 'titulo',
        'desc'         => 'desc',
        'link'         => 'enlace',
    ]
);

$ese_cert_link       = ese_latam_enlace($ese_cert['link']);
$ese_certificaciones = ese_latam_certificaciones_destacadas();
$ese_cert_desc       = ese_latam_texto_rico((string) $ese_cert['desc']);

// Sin sellos y sin encabezado no hay franja que mostrar.
if ([] === $ese_certificaciones && '' === trim((string) $ese_cert['title']) && '' === $ese_cert_desc) {
    return;
}
?>
<section id="certificaciones" class="certificaciones<?php echo $ese_dark ? ' certificaciones--dark' : ''; ?><?php echo $ese_cert_args['centered'] ? ' certificaciones--centered' : ''; ?> relative z-10">
    <header class="certificaciones__header" data-reveal-header>
        <div class="certificaciones__heading-group">
            <?php if ('' !== trim((string) $ese_cert['kicker'])) : ?>
                <p class="type-kicker text-secondary">/ <?php echo esc_html($ese_cert['kicker']); ?></p>
            <?php endif; ?>
            <?php // Clases propias en vez de .type-h2/.hl: esos dos son @utility de
            // Tailwind, que compilan a la capa "utilities" — con más prioridad que
            // cualquier @layer components sin importar la especificidad del
            // selector, así que .certificaciones--dark nunca podría pisarlos.
            // Mismo criterio que .producto-hero__title/.contacto__heading, que por
            // la misma razón tampoco reutilizan .type-h2. ?>
            <?php if ('' !== trim((string) $ese_cert['title'])) : ?>
                <h2 class="certificaciones__title">
                    <?php echo ese_latam_titulo((string) $ese_cert['title'], 'span', 'certificaciones__title-accent'); ?>
                </h2>
            <?php endif; ?>
            <?php if ('' !== $ese_cert_desc) : ?>
                <p class="certificaciones__desc"><?php echo $ese_cert_desc; ?></p>
            <?php endif; ?>
        </div>

        <?php // Cierra la cascada del header (título → bajada → kicker) ?>
        <?php if ('' !== $ese_cert_link['label']) : ?>
        <a href="<?php echo esc_url($ese_cert_link['href']); ?>" class="link-arrow<?php echo $ese_dark ? ' link-arrow--light' : ''; ?>"<?php echo ese_latam_target_attr($ese_cert_link['target']); ?> data-reveal="up"
            data-reveal-delay="0.5">
            <span class="link-arrow__text"><?php echo esc_html($ese_cert_link['label']); ?></span>
            <span class="link-arrow__icon" aria-hidden="true">
                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                        fill="currentColor" />
                </svg>
            </span>
        </a>
        <?php endif; ?>
    </header>

    <?php if ([] !== $ese_certificaciones) : ?>
        <div class="certificaciones__row" data-reveal-stagger data-reveal-delay="0.15">
            <?php foreach ($ese_certificaciones as $cert) : ?>
                <article class="certificaciones__item">
                    <?php if ('' !== $cert['img']) : ?>
                        <div class="certificaciones__logo">
                            <img src="<?php echo esc_url($cert['img']); ?>"
                                alt="<?php echo esc_attr($cert['name']); ?>" loading="lazy" decoding="async">
                        </div>
                    <?php endif; ?>
                    <span class="certificaciones__divider" aria-hidden="true"></span>
                    <p class="certificaciones__name"><?php echo esc_html($cert['name']); ?></p>
                    <?php if ('' !== $cert['desc']) : ?>
                        <p class="certificaciones__item-desc"><?php echo esc_html($cert['desc']); ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
