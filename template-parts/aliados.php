<?php
/**
 * Sección "Aliados" (Figma 3454-430 en Nosotros, 3836-8443 en Impacto):
 * grilla de logos en vidrio sobre degradado radial con anillos, y la isla
 * flotante con tilt al mouse (animación en nosotros.ts, initAliados).
 * Antes vivía inline en page-nosotros.php; se extrajo para reutilizarla.
 *
 * @param array{kicker?: string, title?: string, title_strong?: string, desc?: string} $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_aliados = wp_parse_args($args ?? [], [
    'kicker'       => __('Nuestros aliados', 'ese-latam'),
    'title'        => __('Trabajamos con', 'ese-latam'),
    'title_strong' => __('los mejores aliados', 'ese-latam'),
    'desc'         => '',
]);

$ese_img = static fn (string $file): string => ESE_LATAM_URI . '/assets/imgs/' . $file;
?>

<section id="aliados" class="nos-aliados" data-nos-aliados>
        <div class="nos-aliados__rings" aria-hidden="true">
            <span class="nos-aliados__ring" data-nos-ring></span>
            <span class="nos-aliados__ring" data-nos-ring></span>
            <span class="nos-aliados__ring" data-nos-ring></span>
        </div>

        <header class="nos-aliados__header" data-reveal-header>
            <p class="type-kicker text-white">/ <?php echo esc_html($ese_aliados['kicker']); ?></p>
            <h2 class="nos-aliados__title">
                <?php echo esc_html($ese_aliados['title']); ?><br>
                <strong><?php echo esc_html($ese_aliados['title_strong']); ?></strong>
            </h2>
            <?php if ('' !== $ese_aliados['desc']) : ?>
                <p class="nos-aliados__desc" data-reveal-desc><?php echo esc_html($ese_aliados['desc']); ?></p>
            <?php endif; ?>
        </header>

        <ul class="nos-aliados__grid" data-nos-logos>
            <?php for ($ese_l = 0; $ese_l < 10; $ese_l++): ?>
                <li class="nos-aliados__card" data-nos-logo>
                    <img src="<?php echo esc_url($ese_img('nosotros/aliado-logo.svg')); ?>" alt="Logoipsum" width="149" height="30" loading="lazy" decoding="async">
                </li>
            <?php endfor; ?>
        </ul>

        <div class="nos-aliados__island" data-nos-aliados-island>
            <div class="nos-aliados__island-tilt" data-nos-aliados-tilt>
                <span class="nos-aliados__island-shadow" aria-hidden="true" data-float-shadow></span>
                <img src="<?php echo esc_url($ese_img('nosotros/isla.webp')); ?>" alt="" width="1370" height="955"
                     loading="lazy" decoding="async" data-float data-float-distance="16" data-float-duration="4">
            </div>
        </div>
    </section>

