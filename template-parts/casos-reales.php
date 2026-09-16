<?php
/**
 * "Casos reales en gestión urbana" (Figma componente Frame 427320457):
 * cuatro tarjetas de proyecto con foto, chip de categoría y enlace "Ver
 * artículo". Compartida por Solución por sector, Certificaciones e Impacto.
 *
 * Los casos salen del CPT `caso` (inc/cpt-casos.php): los 4 últimos
 * publicados; mientras no haya ninguno, las tarjetas de ejemplo. El enlace
 * "Ver todos" lleva al archivo /casos-de-exito/ (archive-caso.php).
 *
 * @param array{kicker?: string, title?: string, desc?: string, link_label?: string, link_href?: string} $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_cr = wp_parse_args($args ?? [], [
    'kicker'       => __('Casos reales', 'ese-latam'),
    'title'        => __('Casos reales en', 'ese-latam') . "\n" . __('|gestión urbana|', 'ese-latam'),
    'desc'         => '',
    'link_label'   => '',
    'link_href'    => ese_latam_casos_url(),
]);

$ese_casos = ese_latam_casos_cards(4);
?>

<section class="casos" id="casos-reales">
    <header class="casos__header" data-reveal-header>
        <div class="casos__heading">
            <p class="type-kicker text-secondary">/ <?php echo esc_html($ese_cr['kicker']); ?></p>
            <h2 class="type-h2 uppercase">
                <?php echo ese_latam_titulo($ese_cr['title'], 'span', 'hl'); ?>
            </h2>
            <?php if ('' !== $ese_cr['desc']) : ?>
                <p class="nos-desc" data-reveal-desc><?php echo esc_html($ese_cr['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php if ('' !== $ese_cr['link_label']) : ?>
            <a href="<?php echo esc_url($ese_cr['link_href']); ?>" class="link-arrow" data-reveal="up" data-reveal-delay="0.4">
                <span class="link-arrow__text"><?php echo esc_html($ese_cr['link_label']); ?></span>
                <span class="link-arrow__icon" aria-hidden="true">
                    <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/></svg>
                </span>
            </a>
        <?php endif; ?>
    </header>

    <ul class="casos__grid" data-reveal-stagger>
        <?php foreach ($ese_casos as $ese_caso) : ?>
            <?php ese_latam_caso_card($ese_caso); ?>
        <?php endforeach; ?>
    </ul>
</section>
