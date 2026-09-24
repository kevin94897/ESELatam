<?php
/**
 * Ficha de producto — "Datos clave + sellos" (antes la fila inferior del
 * hero, Figma 3682-5745). Ahora es una sección propia, clara, justo debajo
 * del banner: el hero queda para la escena y el configurador, y los
 * atributos ganan aire y se leen sobre blanco.
 *
 * Se llama DENTRO del loop (usa get_the_ID()). Resuelve sus datos por su
 * cuenta —mismo criterio que el resto de partes de la ficha— así que se
 * puede mover o quitar sin tocar el hero:
 *   · Capacidad: la marcada por defecto en el primer modelo; product-config.ts
 *     la reescribe al cambiar de píldora vía [data-producto-volumen]
 *     (busca en todo el documento, no solo en el hero).
 *   · Colores: cuenta del modelo activo ([data-producto-colores], ídem).
 *   · Hasta dos características ACF que no sean el volumen, con fallbacks.
 *   · Sellos: los elegidos en el campo de relación "Certificaciones".
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_producto_id = get_the_ID();
$ese_modelos     = ese_latam_producto_modelos($ese_producto_id);

// Mismo fallback estático que el hero, para que ambos digan lo mismo.
if ([] === $ese_modelos) {
    $ese_modelos = [[
        'nombre'      => '',
        'descripcion' => '',
        'litrajes'    => array_map(
            static fn (string $v): array => ['valor' => $v, 'predeterminado' => '120L' === $v],
            ['80L', '120L', '180L', '240L', '360L']
        ),
        'colores'     => [],
    ]];
}

$ese_modelo_activo   = $ese_modelos[0];
$ese_litraje_default = (string) ($ese_modelo_activo['litrajes'][0]['valor'] ?? '');
foreach ($ese_modelo_activo['litrajes'] as $ese_item) {
    if (! empty($ese_item['predeterminado'])) {
        $ese_litraje_default = (string) $ese_item['valor'];
        break;
    }
}

$ese_stats = [
    ['label' => __('Capacidad', 'ese-latam'), 'value' => $ese_litraje_default, 'attr' => 'data-producto-volumen'],
    ['label' => __('Colores', 'ese-latam'), 'value' => sprintf(
        /* translators: %d: cantidad de colores disponibles */
        _n('%d disponible', '%d disponibles', count($ese_modelo_activo['colores']), 'ese-latam'),
        count($ese_modelo_activo['colores'])
    ), 'attr' => 'data-producto-colores'],
];

$ese_caracteristicas = get_field('caracteristicas', $ese_producto_id);
if (is_array($ese_caracteristicas)) {
    foreach ($ese_caracteristicas as $ese_caract) {
        $ese_etiqueta = (string) ($ese_caract['etiqueta'] ?? '');
        if ('' === $ese_etiqueta || false !== mb_strpos(mb_strtolower($ese_etiqueta), 'volumen')) {
            continue;
        }
        $ese_stats[] = ['label' => $ese_etiqueta, 'value' => (string) ($ese_caract['valor'] ?? ''), 'attr' => ''];
        if (count($ese_stats) >= 4) {
            break;
        }
    }
}
if (count($ese_stats) < 4) {
    $ese_stats[] = ['label' => __('Material', 'ese-latam'), 'value' => __('HDPE de alta densidad', 'ese-latam'), 'attr' => ''];
}
if (count($ese_stats) < 4) {
    $ese_stats[] = ['label' => __('Origen', 'ese-latam'), 'value' => __('Ingeniería europea', 'ese-latam'), 'attr' => ''];
}

// Sellos del producto: nombre y logo salen del módulo de certificaciones,
// los mismos que en la franja y en la página de Certificaciones.
$ese_certs = [];
foreach ((array) ese_latam_campo('certificaciones', $ese_producto_id, []) as $ese_cert_id) {
    $ese_cert_id = (int) $ese_cert_id;
    if ($ese_cert_id <= 0 || 'publish' !== get_post_status($ese_cert_id)) {
        continue;
    }
    $ese_certs[] = [
        'name' => get_the_title($ese_cert_id),
        'img'  => (string) (get_the_post_thumbnail_url($ese_cert_id, 'medium') ?: ''),
    ];
}
$ese_certs_url = ese_latam_pagina_url('certificaciones', home_url('/#certificaciones'));
?>

<section class="producto-atributos" id="atributos">
    <div class="producto-atributos__inner">
        <div class="producto-atributos__card producto-atributos__card--stats" data-reveal="up">
            <p class="producto-atributos__label"><?php esc_html_e('Datos clave', 'ese-latam'); ?></p>
            <dl class="producto-atributos__list">
                <?php foreach ($ese_stats as $ese_stat) : ?>
                    <div class="producto-atributos__stat">
                        <dt class="producto-atributos__stat-label"><?php echo esc_html($ese_stat['label']); ?></dt>
                        <dd class="producto-atributos__stat-value" <?php echo esc_attr($ese_stat['attr']); ?>><?php echo esc_html($ese_stat['value']); ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </div>

        <?php if ([] !== $ese_certs) : ?>
            <div class="producto-atributos__card producto-atributos__card--certs" data-reveal="up" data-reveal-delay="0.15">
                <p class="producto-atributos__label"><?php esc_html_e('Certificaciones', 'ese-latam'); ?></p>
                <ul class="producto-atributos__certs">
                    <?php foreach ($ese_certs as $ese_cert) : ?>
                        <li>
                            <a class="producto-atributos__cert" href="<?php echo esc_url($ese_certs_url); ?>"
                                title="<?php echo esc_attr(sprintf(__('Certificación %s', 'ese-latam'), $ese_cert['name'])); ?>">
                                <span class="producto-atributos__cert-tile" aria-hidden="true">
                                    <?php if ('' !== $ese_cert['img']) : ?>
                                        <img src="<?php echo esc_url($ese_cert['img']); ?>" alt="" loading="lazy" decoding="async">
                                    <?php else : ?>
                                        <span class="producto-atributos__cert-initials"><?php echo esc_html(mb_strtoupper(mb_substr($ese_cert['name'], 0, 3))); ?></span>
                                    <?php endif; ?>
                                </span>
                                <span class="producto-atributos__cert-name"><?php echo esc_html($ese_cert['name']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>
