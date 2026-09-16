<?php
/**
 * "ESE Latam en la economía circular" (Figma 5195-5458): diagrama circular
 * con 4 etapas (nodos sobre el anillo, cada uno un tab que cambia la
 * leyenda de arriba) + la salida "vertedero / incineración" fija abajo, y a
 * la derecha el texto con flechas prev/next. Cambio de leyenda por
 * tab-panels.ts (data-tabs + data-tabs-prev/next).
 *
 * El anillo (arcos + iconos) es el SVG exportado del Figma; los nodos
 * clicables son botones transparentes superpuestos en la posición de cada
 * círculo del SVG (porcentajes sobre el viewBox de 860px).
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_img = static fn (string $file): string => ESE_LATAM_URI . '/assets/imgs/' . $file;

// Etapas en el orden del ciclo (sentido horario desde arriba). Solo
// "Distribución" trae copy del Figma; el resto sigue la lógica del diagrama
// (reacondicionar → recuperar componentes → reciclar) a la espera de texto
// del cliente.
$ese_etapas = [
    ['key' => 'dist',    'title' => __('Distribución', 'ese-latam'),               'desc' => __('Realizamos mantenimiento, reparaciones y reemplazo de piezas para mantener cada contenedor operativo durante el mayor tiempo posible.', 'ese-latam')],
    ['key' => 'reacond', 'title' => __('Reacondicionamiento', 'ese-latam'),        'desc' => __('Los contenedores que vuelven de servicio se limpian, reparan y recertifican para reingresar al circuito con la misma garantía de un equipo nuevo.', 'ese-latam')],
    ['key' => 'recup',   'title' => __('Recuperación de componentes', 'ese-latam'), 'desc' => __('Tapas, ruedas y ejes en buen estado se recuperan como repuestos, reduciendo la necesidad de fabricar piezas nuevas.', 'ese-latam')],
    ['key' => 'recicla', 'title' => __('Reciclaje', 'ese-latam'),                  'desc' => __('Cuando un contenedor termina su vida útil, el HDPE se muele y se reincorpora como materia prima PCR en nuevos contenedores.', 'ese-latam')],
];
?>

<section class="eco" id="economia-circular" data-tabs>
    <div class="eco__bg" aria-hidden="true">
        <img src="<?php echo esc_url($ese_img('economia/bg.webp')); ?>" alt="" loading="lazy" decoding="async"
            data-parallax data-parallax-from="-6" data-parallax-to="6">
    </div>

    <div class="eco__diagram" data-reveal="fade">
        <div class="eco__legend-top" aria-live="polite">
            <?php foreach ($ese_etapas as $ese_i => $ese_etapa) : ?>
                <div class="eco__legend eco__legend--<?php echo esc_attr($ese_etapa['key']); ?><?php echo 0 === $ese_i ? ' is-active' : ''; ?>" data-tab-panel>
                    <p class="eco__legend-title"><?php echo esc_html($ese_etapa['title']); ?></p>
                    <p class="eco__legend-desc"><?php echo esc_html($ese_etapa['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="eco__ring">
            <img class="eco__ring-svg" src="<?php echo esc_url($ese_img('economia/ciclo.svg')); ?>" alt="" width="860" height="860" loading="lazy" decoding="async">
            <div class="eco__center" data-float data-float-distance="6" data-float-duration="5">
                <img src="<?php echo esc_url($ese_img('economia/centro.webp')); ?>" alt="" loading="lazy" decoding="async">
            </div>
            <?php foreach ($ese_etapas as $ese_i => $ese_etapa) : ?>
                <button type="button" role="tab"
                    class="eco__node eco__node--<?php echo esc_attr($ese_etapa['key']); ?><?php echo 0 === $ese_i ? ' is-active' : ''; ?>"
                    aria-selected="<?php echo 0 === $ese_i ? 'true' : 'false'; ?>" data-tab>
                    <span class="sr-only"><?php echo esc_html($ese_etapa['title']); ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="eco__vertedero">
            <span class="eco__vertedero-link" aria-hidden="true"></span>
            <img class="eco__vertedero-icon" src="<?php echo esc_url($ese_img('economia/vertedero.svg')); ?>" alt="" width="105" height="105" loading="lazy" decoding="async">
            <p class="eco__legend-title eco__legend-title--gray"><?php esc_html_e('Vertedero / incineración', 'ese-latam'); ?></p>
            <p class="eco__legend-desc"><?php esc_html_e('Solo cuando ya no es posible reparar, reacondicionar, reutilizar componentes ni reciclar el material, el contenedor pasa a incineración o disposición final.', 'ese-latam'); ?></p>
        </div>
    </div>

    <div class="eco__text">
        <header data-reveal-header>
            <p class="type-kicker text-secondary">/ <?php esc_html_e('Circulogic', 'ese-latam'); ?></p>
            <h2 class="type-h2 uppercase">
                <?php echo ese_latam_titulo(__('ESE Latam en la', 'ese-latam') . "\n" . __('|economía circular|', 'ese-latam'), 'span', 'hl'); ?>
            </h2>
        </header>
        <div class="eco__desc" data-reveal="up" data-reveal-delay="0.15">
            <p>
                <?php esc_html_e('Cada contenedor ESE está diseñado para mantenerse en uso el mayor tiempo posible. Antes de convertirse en un nuevo producto, pasa por un proceso que', 'ese-latam'); ?>
                <strong><?php esc_html_e('prioriza el servicio, el reacondicionamiento', 'ese-latam'); ?></strong>
                <?php esc_html_e('y la recuperación de componentes para extender su vida útil.', 'ese-latam'); ?>
            </p>
            <p><?php esc_html_e('Solo cuando estas alternativas ya no son posibles, el material se recicla para fabricar nuevos contenedores, cerrando el ciclo de la economía circular y reduciendo el consumo de recursos y el impacto ambiental.', 'ese-latam'); ?></p>
        </div>
        <div class="eco__controls" data-reveal="up" data-reveal-delay="0.3">
            <button type="button" class="eco__btn eco__btn--prev" data-tabs-prev aria-label="<?php esc_attr_e('Etapa anterior', 'ese-latam'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M21.75 12C21.75 12.2984 21.6315 12.5845 21.4205 12.7955C21.2095 13.0065 20.9234 13.125 20.625 13.125H6.09094L11.7966 18.7969C11.9016 18.9016 11.9849 19.026 12.0418 19.1629C12.0987 19.2999 12.1279 19.4467 12.1279 19.595C12.1279 19.7433 12.0987 19.8901 12.0418 20.0271C11.9849 20.164 11.9016 20.2884 11.7966 20.3931C11.6919 20.4981 11.5675 20.5814 11.4306 20.6383C11.2936 20.6952 11.1468 20.7244 10.9985 20.7244C10.8502 20.7244 10.7034 20.6952 10.5664 20.6383C10.4295 20.5814 10.3051 20.4981 10.2004 20.3931L2.57044 12.7631C2.36064 12.5533 2.24219 12.2685 2.24219 11.9719C2.24219 11.6753 2.36064 11.3905 2.57044 11.1806L10.2004 3.55063C10.4123 3.33875 10.6997 3.21967 10.9994 3.21967C11.2991 3.21967 11.5865 3.33875 11.7984 3.55063C12.0103 3.7625 12.1294 4.04991 12.1294 4.34957C12.1294 4.64922 12.0103 4.93663 11.7984 5.1485L6.09094 10.875H20.625C20.9234 10.875 21.2095 10.9935 21.4205 11.2045C21.6315 11.4155 21.75 11.7016 21.75 12Z" fill="currentColor"/></svg>
            </button>
            <button type="button" class="eco__btn eco__btn--next" data-tabs-next aria-label="<?php esc_attr_e('Etapa siguiente', 'ese-latam'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M21.4296 12.7631L13.7996 20.3931C13.6949 20.4981 13.5705 20.5814 13.4336 20.6383C13.2966 20.6952 13.1498 20.7244 13.0015 20.7244C12.8532 20.7244 12.7064 20.6952 12.5694 20.6383C12.4325 20.5814 12.3081 20.4981 12.2034 20.3931C12.0984 20.2884 12.0151 20.164 11.9582 20.0271C11.9013 19.8901 11.8721 19.7433 11.8721 19.595C11.8721 19.4467 11.9013 19.2999 11.9582 19.1629C12.0151 19.026 12.0984 18.9016 12.2034 18.7969L17.9091 13.125H3.375C3.07663 13.125 2.79048 13.0065 2.5795 12.7955C2.36853 12.5845 2.25 12.2984 2.25 12C2.25 11.7016 2.36853 11.4155 2.5795 11.2045C2.79048 10.9935 3.07663 10.875 3.375 10.875H17.9091L12.2034 5.1485C11.9915 4.93663 11.8724 4.64922 11.8724 4.34957C11.8724 4.04991 11.9915 3.7625 12.2034 3.55063C12.4153 3.33875 12.7027 3.21967 13.0024 3.21967C13.302 3.21967 13.5894 3.33875 13.8013 3.55063L21.4313 11.1806C21.5361 11.2852 21.6191 11.4095 21.6757 11.5463C21.7322 11.6831 21.7612 11.8297 21.761 11.9777C21.7608 12.1257 21.7315 12.2722 21.6746 12.4089C21.6177 12.5455 21.5344 12.6696 21.4296 12.7741V12.7631Z" fill="currentColor"/></svg>
            </button>
        </div>
    </div>
</section>
