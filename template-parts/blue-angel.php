<?php
/**
 * Página Certificaciones — "Estándar Blue Angel" (Figma 3807-5473): franja
 * celeste degradada con titular blanco y la ilustración del ciclo de
 * fabricación (exportada del Figma como una sola imagen).
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}
?>

<section class="bangel" id="blue-angel">
    <header class="bangel__header" data-reveal-header>
        <p class="type-kicker">/ <?php esc_html_e('Productos', 'ese-latam'); ?></p>
        <h2 class="bangel__title">
            <?php esc_html_e('Estándar', 'ese-latam'); ?>
            <strong><?php esc_html_e('Blue Angel', 'ese-latam'); ?></strong>
        </h2>
        <p class="bangel__desc" data-reveal-desc>
            <?php esc_html_e('Cada etapa de nuestro proceso de ingeniería está diseñada para cumplir con los estándares de sostenibilidad', 'ese-latam'); ?>
            <strong><?php esc_html_e('más rigurosos', 'ese-latam'); ?></strong>
            <?php esc_html_e('del mundo.', 'ese-latam'); ?>
        </p>
    </header>

    <figure class="bangel__figure" data-reveal="up" data-reveal-delay="0.2">
        <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/certificaciones/blue-angel-ciclo.webp'); ?>"
            alt="<?php esc_attr_e('Ciclo de fabricación de contenedores bajo el estándar Blue Angel', 'ese-latam'); ?>"
            width="1459" height="777" loading="lazy" decoding="async"
            data-parallax data-parallax-from="6" data-parallax-to="-6">
    </figure>
</section>
