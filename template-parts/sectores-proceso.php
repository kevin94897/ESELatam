<?php
/**
 * Página Sectores — "Entendemos tu operación" (Figma componente 3551-437):
 * foto de fondo + tarjeta de vidrio con kicker "/ PASO 0N", titular en dos
 * renglones, bajada, divisor y tres pasos seleccionables. Elegir un paso
 * cambia el contenido de la tarjeta con crossfade y, si el paso trae foto
 * propia, también el fondo; con `data-proceso-autoplay` los pasos rotan
 * solos (ver proceso-steps.ts, mismo patrón que residuos-selector.ts).
 *
 * Mismo vidrio que .contacto__card (foto + doble gradiente + blur), pero
 * con clases propias: el card de Contactemos lleva su reveal por letra y
 * su CTA, que acá no van.
 *
 * Solo el paso 01 trae copy en el Figma; 02 y 03 son borrador redactado en
 * el mismo tono. Revisar con el cliente antes de publicar.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_pasos = [
    [
        'num'    => '01',
        'tab'    => __('Entendemos tu operación', 'ese-latam'),
        'light'  => __('Entendemos tu', 'ese-latam'),
        'bold'   => __('operación', 'ese-latam'),
        'desc'   => __('Analizamos tus flujos operativos, normativas y puntos críticos para diseñar una estrategia de contención alineada con tus objetivos.', 'ese-latam'),
        'img'    => 'sectores/proceso.webp',
    ],
    [
        'num'    => '02',
        'tab'    => __('Diseñamos la solución', 'ese-latam'),
        'light'  => __('Diseñamos la', 'ese-latam'),
        'bold'   => __('solución', 'ese-latam'),
        'desc'   => __('Dimensionamos capacidades, elegimos la línea de contenedores certificada para tu sector y definimos puntos de acopio, señalética y frecuencias de recolección.', 'ese-latam'),
        'img'    => 'terreno/en-terreno.webp',
    ],
    [
        'num'    => '03',
        'tab'    => __('Acompañamos la implementación', 'ese-latam'),
        'light'  => __('Acompañamos la', 'ese-latam'),
        'bold'   => __('implementación', 'ese-latam'),
        'desc'   => __('Coordinamos la entrega, capacitamos a tu equipo operativo y damos soporte técnico continuo con repuestos originales desde Alemania.', 'ese-latam'),
        'img'    => 'sectores/recoleccion.webp',
    ],
];
?>

<section class="proceso" data-proceso data-proceso-autoplay="6000">
    <div class="proceso__bg" aria-hidden="true" data-proceso-bg>
        <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/' . $ese_pasos[0]['img']); ?>" alt=""
            loading="lazy" decoding="async" data-proceso-img>
    </div>

    <div class="proceso__card" data-reveal="up">
        <div class="proceso__head" data-proceso-panel>
            <p class="type-kicker proceso__kicker">
                / <?php esc_html_e('Paso', 'ese-latam'); ?> <span data-proceso-num><?php echo esc_html($ese_pasos[0]['num']); ?></span>
            </p>
            <h2 class="proceso__title">
                <span data-proceso-light><?php echo esc_html($ese_pasos[0]['light']); ?></span><br>
                <strong data-proceso-bold><?php echo esc_html($ese_pasos[0]['bold']); ?></strong>
            </h2>
            <p class="proceso__desc" data-proceso-desc><?php echo esc_html($ese_pasos[0]['desc']); ?></p>
        </div>

        <hr class="proceso__divider">

        <div class="proceso__steps" role="tablist" aria-label="<?php esc_attr_e('Pasos del proceso', 'ese-latam'); ?>">
            <?php foreach ($ese_pasos as $ese_i => $ese_paso) : ?>
                <button type="button" role="tab"
                    class="proceso__step<?php echo 0 === $ese_i ? ' is-active' : ''; ?>"
                    aria-selected="<?php echo 0 === $ese_i ? 'true' : 'false'; ?>"
                    data-proceso-step
                    data-num="<?php echo esc_attr($ese_paso['num']); ?>"
                    data-light="<?php echo esc_attr($ese_paso['light']); ?>"
                    data-bold="<?php echo esc_attr($ese_paso['bold']); ?>"
                    data-desc="<?php echo esc_attr($ese_paso['desc']); ?>"
                    data-img="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/' . $ese_paso['img']); ?>">
                    <span class="proceso__step-num" aria-hidden="true"><?php echo esc_html($ese_paso['num']); ?></span>
                    <span class="proceso__step-text"><?php echo esc_html($ese_paso['tab']); ?></span>
                    <span class="proceso__step-bar" aria-hidden="true"></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
