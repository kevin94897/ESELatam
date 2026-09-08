<?php
/**
 * Sección "Contactemos" (Figma node 535-782) — CTA de cierre, al pie de todo el contenido.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}
?>

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
