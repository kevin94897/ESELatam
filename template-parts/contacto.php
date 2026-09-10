<?php
/**
 * Sección "Contactemos" (Figma node 535-782) — CTA de cierre, al pie de todo el contenido.
 *
 * Compartida por la home, el catálogo, la ficha de producto y Nosotros.
 * Todo el copy se puede pisar vía `$args` de get_template_part(); sin
 * argumentos imprime exactamente lo de siempre (el copy de la home).
 *
 * @param array{
 *     class?: string,           Clase extra para <section> (p. ej. 'contacto--upper')
 *     kicker?: string,
 *     heading?: string,         Tramo del titular en peso normal
 *     heading_strong?: string,  Tramo en bold (opcional); el "?" final va aparte
 *     desc?: string,
 *     bg?: string,              URL de la foto de fondo
 *     cta_label?: string,
 *     cta_href?: string,
 * } $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_contacto = wp_parse_args($args ?? [], [
    'class'          => '',
    'kicker'         => __('Contactemos', 'ese-latam'),
    'heading'        => __('¿Listo para llevar tu gestión de residuos al siguiente nivel', 'ese-latam'),
    'heading_strong' => '',
    'desc'           => __('Fomentamos la cultura del reciclaje a través de contenedores con señalética clara y pedagogía urbana, facilitando la identificación correcta de cada tipo de residuo.', 'ese-latam'),
    'bg'             => ESE_LATAM_URI . '/assets/imgs/contacto-bg.jpg',
    'cta_label'      => __('Contactar', 'ese-latam'),
    // Antes era un mailto: ahora que existe la página de Contacto con
    // formulario, el CTA de cierre lleva ahí.
    'cta_href'       => ese_latam_contacto_url(),
]);
?>

<section id="contacto" class="<?php echo esc_attr(trim('contacto ' . $ese_contacto['class'])); ?>">
    <div class="contacto__bg" aria-hidden="true">
        <img src="<?php echo esc_url($ese_contacto['bg']); ?>" alt="" loading="lazy"
            decoding="async">
    </div>

    <div class="contacto__card" data-reveal="fade">
        <p class="type-kicker text-white">/ <?php echo esc_html($ese_contacto['kicker']); ?></p>

        <?php // Todo en una línea a propósito: cualquier salto entre </strong> y el
        // "?" se renderiza como un espacio ("RESPONSABLE ?"). ?>
        <h2 class="contacto__heading" data-contacto-heading><?php echo esc_html($ese_contacto['heading']); ?><?php if ('' !== $ese_contacto['heading_strong']): ?> <strong><?php echo esc_html($ese_contacto['heading_strong']); ?></strong><?php endif; ?>?</h2>

        <hr class="contacto__divider">

        <div class="contacto__row">
            <p class="contacto__desc">
                <?php echo esc_html($ese_contacto['desc']); ?>
            </p>
            <?php
            ese_latam_cta_button([
                'href' => $ese_contacto['cta_href'],
                'label' => $ese_contacto['cta_label'],
            ]);
            ?>
        </div>
    </div>
</section>
