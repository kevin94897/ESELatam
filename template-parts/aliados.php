<?php
/**
 * Sección "Aliados" (Figma 3454-430 en Nosotros, 3836-8443 en Impacto):
 * grilla de logos en vidrio sobre degradado radial con anillos, y la isla
 * flotante con tilt al mouse (animación en nosotros.ts, initAliados).
 * Antes vivía inline en page-nosotros.php; se extrajo para reutilizarla.
 *
 * @param array{kicker?: string, title?: string, desc?: string, isla?: string} $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_aliados = wp_parse_args($args ?? [], [
    'kicker' => '',
    'title'  => '',
    'desc'   => '',
    'isla'   => '',
]);

$ese_lista = ese_latam_modulo_entradas('aliado');

// Sin aliados cargados no se pinta la franja: el theme no inventa logos.
if ([] === $ese_lista) {
    return;
}
?>

<section id="aliados" class="nos-aliados" data-nos-aliados>
        <div class="nos-aliados__rings" aria-hidden="true">
            <span class="nos-aliados__ring" data-nos-ring></span>
            <span class="nos-aliados__ring" data-nos-ring></span>
            <span class="nos-aliados__ring" data-nos-ring></span>
        </div>

        <header class="nos-aliados__header" data-reveal-header>
            <?php if ('' !== $ese_aliados['kicker']) : ?>
                <p class="type-kicker text-white">/ <?php echo esc_html($ese_aliados['kicker']); ?></p>
            <?php endif; ?>
            <?php if ('' !== $ese_aliados['title']) : ?>
                <h2 class="nos-aliados__title">
                    <?php echo ese_latam_titulo($ese_aliados['title']); ?>
                </h2>
            <?php endif; ?>
            <?php if ('' !== $ese_aliados['desc']) : ?>
                <p class="nos-aliados__desc" data-reveal-desc><?php echo esc_html($ese_aliados['desc']); ?></p>
            <?php endif; ?>
        </header>

        <ul class="nos-aliados__grid" data-nos-logos>
            <?php foreach ($ese_lista as $ese_aliado) :
                $ese_logo = (string) (get_the_post_thumbnail_url($ese_aliado->ID, 'medium') ?: '');
                $ese_web  = trim((string) ese_latam_campo('web', $ese_aliado->ID, ''));
                if ('' === $ese_logo) {
                    continue;
                }
                ?>
                <li class="nos-aliados__card" data-nos-logo>
                    <?php if ('' !== $ese_web) : ?><a href="<?php echo esc_url($ese_web); ?>" target="_blank" rel="noopener"><?php endif; ?>
                    <img src="<?php echo esc_url($ese_logo); ?>" alt="<?php echo esc_attr(get_the_title($ese_aliado->ID)); ?>" loading="lazy" decoding="async">
                    <?php if ('' !== $ese_web) : ?></a><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ('' !== $ese_aliados['isla']) : ?>
            <div class="nos-aliados__island" data-nos-aliados-island>
                <div class="nos-aliados__island-tilt" data-nos-aliados-tilt>
                    <span class="nos-aliados__island-shadow" aria-hidden="true" data-float-shadow></span>
                    <img src="<?php echo esc_url($ese_aliados['isla']); ?>" alt="" width="1370" height="955"
                         loading="lazy" decoding="async" data-float data-float-distance="16" data-float-duration="4">
                </div>
            </div>
        <?php endif; ?>
    </section>

