<?php
/**
 * Página de un sector (Figma node 3551-5049, "04 – Solución: Municipalidades").
 *
 * Es la single del módulo "Sectores" (CPT `sector`, inc/cpt-sectores.php) y
 * vive en `/sectores/{slug}/`. Todo su contenido sale de la ficha del sector
 * (inc/pcf-sectores.php), así que añadir un sector nuevo es publicar una
 * entrada: no hay nada escrito en esta plantilla.
 *
 * Composición, en orden del Figma. Todo lo que ya existía se reutiliza:
 *   1. Hero oscuro (hero-interno.php: el .nos-hero de Nosotros + kicker + CTA)
 *   2. Desafíos en la gestión urbana (sector-desafios.php, propio)
 *   3. Nuestro criterio de adaptabilidad (sector-criterio.php, propio)
 *   4. Marquee "soluciones recomendadas" (.nos-marquee de Nosotros)
 *   5. Soluciones recomendadas → producto-recomendados.php (ficha de producto)
 *   6. Certificaciones → certificaciones.php (centrada por $args)
 *   7. ESE Latam en la economía circular (economia-circular.php, propio)
 *   8. Casos reales (casos-reales.php, compartida con Certificaciones e Impacto)
 *   9. Contactemos → contacto.php
 *
 * Va dentro del wrapper `.nosotros[data-nosotros]`: así reutiliza los
 * gutters, el hero y el módulo nosotros.ts que lo anima.
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();

$ese_id    = get_the_ID();
$ese_madre = get_page_by_path(ESE_LATAM_SECTORES_BASE);

// Titular en dos pesos: "municipalidades y" + "gobiernos locales". El corte
// sale del nombre del sector, así que un sector nuevo lo hereda sin tocar nada.
$ese_palabras = explode(' ', get_the_title($ese_id));
$ese_strong   = count($ese_palabras) > 2 ? implode(' ', array_splice($ese_palabras, -2)) : '';
$ese_light    = implode(' ', $ese_palabras);
// Convención de titulares del theme: el tramo en color va entre barras
// (ver ese_latam_titulo() en inc/pcf.php).
$ese_titular  = '' !== $ese_strong ? $ese_light . ' |' . $ese_strong . '|' : $ese_light;

$ese_foto    = (string) (get_the_post_thumbnail_url($ese_id, 'full') ?: '');
$ese_resumen = trim((string) ese_latam_campo('resumen', $ese_id, ''));

$ese_sec = [
    'kicker'   => trim((string) ese_latam_campo('kicker', $ese_id, '')),
    'desc'     => trim((string) ese_latam_campo('hero_desc', $ese_id, '')) ?: $ese_resumen,
    'bg'       => ese_latam_img_url(ese_latam_campo('hero_imagen', $ese_id, ''), $ese_foto),
    'desafios' => ese_latam_texto_rico((string) ese_latam_campo('desafios_desc', $ese_id, '')),
    'dolores'  => ese_latam_sector_tarjetas(ese_latam_campo('dolores', $ese_id, [])),
    'alivios'  => ese_latam_sector_tarjetas(ese_latam_campo('alivios', $ese_id, [])),
    'cita'     => ese_latam_texto_rico((string) ese_latam_campo('cita', $ese_id, '')),
    'criterio' => trim((string) ese_latam_campo('criterio', $ese_id, '')),
];

// Las tres llamadas que rodean al contenedor del bloque "criterio".
$ese_callouts = [];
foreach ((array) ese_latam_campo('criterio_callouts', $ese_id, []) as $ese_fila) {
    $ese_label = trim((string) ($ese_fila['label'] ?? ''));
    if ('' === $ese_label) {
        continue;
    }
    $ese_callouts[] = [
        'label' => $ese_label,
        'pos'   => (string) ($ese_fila['pos'] ?? 'left-top'),
    ];
}

$ese_sec_cta = ese_latam_enlace(ese_latam_campo('hero_cta', $ese_id, null));
$ese_marquee = trim((string) ese_latam_campo('marquee', $ese_id, ''));
?>

<div class="nosotros nosotros--sector" data-nosotros>
    <?php // Marca "hay JS": solo entonces el CSS esconde el hero hasta que
    // nosotros.ts lo anime (sin JS queda visible desde el HTML). ?>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>

    <?php
    get_template_part('template-parts/hero-interno', null, [
        'kicker'     => $ese_sec['kicker'],
        'title'      => $ese_titular,
        'desc'       => $ese_sec['desc'],
        'cta_label'  => $ese_sec_cta['label'],
        'cta_href'   => $ese_sec_cta['href'],
        'cta_target' => $ese_sec_cta['target'],
        // La miga de pan nombra a la página madre con su propio título.
        'crumb'      => [['label' => ese_latam_titulo_plano(get_the_title($ese_madre)), 'url' => ese_latam_pagina_url(ESE_LATAM_SECTORES_BASE, home_url('/#sectores'))]],
        'current'    => get_the_title($ese_id),
        'bg'         => $ese_sec['bg'],
    ]);

    // Sin dolores ni alivios cargados el comparador no tiene nada que comparar.
    if ([] !== $ese_sec['dolores'] || [] !== $ese_sec['alivios']) {
        get_template_part('template-parts/sector-desafios', null, [
            'kicker'        => (string) ese_latam_campo('desafios_kicker', $ese_id, ''),
            'title'         => (string) ese_latam_campo('desafios_titulo', $ese_id, ''),
            'label_dolores' => (string) ese_latam_campo('desafios_label_dolores', $ese_id, ''),
            'label_alivios' => (string) ese_latam_campo('desafios_label_alivios', $ese_id, ''),
            'desc'    => $ese_sec['desafios'],
            'dolores' => $ese_sec['dolores'],
            'alivios' => $ese_sec['alivios'],
        ]);
    }

    if ('' !== $ese_sec['cita'] || '' !== $ese_sec['criterio']) {
        get_template_part('template-parts/sector-criterio', null, [
            'kicker'    => (string) ese_latam_campo('criterio_kicker', $ese_id, ''),
            'title'     => (string) ese_latam_campo('criterio_titulo', $ese_id, ''),
            'quote'     => $ese_sec['cita'],
            'desc'      => $ese_sec['criterio'],
            'firma'     => (string) ese_latam_campo('criterio_firma', $ese_id, ''),
            'firma_sub' => (string) ese_latam_campo('criterio_firma_sub', $ese_id, ''),
            'product'   => ese_latam_img_url(ese_latam_campo('criterio_producto', $ese_id, '')),
            'callouts'  => $ese_callouts,
            'alt'       => get_the_title($ese_id),
        ]);
    }
    ?>

    <?php if ('' !== $ese_marquee) : ?>
        <div class="nos-marquee nos-marquee--sm" aria-hidden="true">
            <p class="nos-marquee__track" data-marquee="right">
                <span><?php echo esc_html($ese_marquee); ?></span>
                <span><?php echo esc_html($ese_marquee); ?></span>
            </p>
        </div>
    <?php endif; ?>

    <?php
    // Mismo bloque que la ficha de producto y el caso de éxito: desde que lo
    // comparten, el copy viaja por args. Acá sigue escrito en la plantilla:
    // el sector todavía no tiene campos para esta sección.
    get_template_part('template-parts/producto-recomendados', null, [
        'kicker' => __('Productos', 'ese-latam'),
        'title'  => __('Soluciones', 'ese-latam') . ' ' . __('|recomendadas|', 'ese-latam'),
        'desc'   => __('Otras configuraciones de la misma familia, pensadas para distintos volúmenes y necesidades de recolección', 'ese-latam'),
    ]);

    get_template_part('template-parts/certificaciones', null, [
        'centered' => true,
    ]);

    get_template_part('template-parts/economia-circular');

    get_template_part('template-parts/casos-reales', null, ese_latam_args_casos($ese_id, 'casos'));

    get_template_part('template-parts/contacto', null, [
        'class' => 'contacto--upper',
    ]);
    ?>
</div>

<?php
get_footer();
