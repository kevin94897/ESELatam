<?php
/**
 * Template Name: Certificaciones
 *
 * Página "Certificaciones" (Figma node 3785-4089, "07 – Certificaciones").
 * WordPress la aplica sola a la página con slug `certificaciones` (que crea
 * inc/paginas.php) y, por el header de arriba, también se puede asignar a
 * mano desde el editor.
 *
 * Composición, en orden del Figma. Todo lo que ya existía se reutiliza:
 *   1. Hero oscuro (hero-interno.php)
 *   2. Nuestras certificaciones en detalle (certificaciones-detalle.php, propio)
 *   3. Marquee "marca la diferencia" (.nos-marquee)
 *   4. Marcando la diferencia → metodo-tabs.php (las tabs ESG de Nosotros, con 4 tabs)
 *   5. ¿Qué hace que sea excelente? → producto-pruebas.php (ficha de producto)
 *   6. Calidad superior desde el origen → objetivos-sticky.php (aside sticky de Nosotros)
 *   7. Estándar Blue Angel (blue-angel.php, propio)
 *   8. Valida certificados originales (valida-certificados.php, propio)
 *   9. Casos reales (casos-reales.php)
 *  10. Contactemos → contacto.php
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();

$ese_img = static fn (string $file): string => ESE_LATAM_URI . '/assets/imgs/' . $file;

// "Marcando la diferencia": 4 tabs. Copy propio (el Figma solo trae los
// títulos); la foto es una sola para las cuatro, como en el diseño.
$ese_diferencia = [
    ['title' => __('Reduce riesgos', 'ese-latam'),        'desc' => __('Contenedores certificados que evitan fallas en operación, accidentes y costos ocultos de reposición.', 'ese-latam'),        'img' => 'certificaciones/diferencia.webp'],
    ['title' => __('Compra responsable', 'ese-latam'),    'desc' => __('Cada sello respalda una decisión de compra pública o privada auditable y transparente.', 'ese-latam'),                    'img' => 'certificaciones/diferencia.webp'],
    ['title' => __('Evita falsificaciones', 'ese-latam'), 'desc' => __('El marcado en relieve y la documentación original te protegen de imitaciones sin ensayos.', 'ese-latam'),                'img' => 'certificaciones/diferencia.webp'],
    ['title' => __('Protege la inversión', 'ese-latam'),  'desc' => __('Vida útil comprobada en laboratorio: menos reemplazos y mejor retorno por cada contenedor.', 'ese-latam'),               'img' => 'certificaciones/diferencia.webp'],
];

// "Calidad superior desde el origen" (Figma 3797-993). Los paneles del
// Figma no traen foto: se reutilizan fotos del theme.
$ese_calidad = [
    [
        'id'    => 'diseno-con-proposito',
        'title' => __('Diseño con propósito', 'ese-latam'),
        'img'   => 'sectores/municipalidades.webp',
        'text'  => [
            __('Cada contenedor ESE es el resultado de un estudio de biomecánica y optimización urbana. Con nervaduras reforzadas para absorber la tensión de elevación y una tapa aerodinámica, cada detalle tiene el propósito explícito de', 'ese-latam') . ' ',
            __('durar y facilitar', 'ese-latam'),
            ' ' . __('la labor del operario.', 'ese-latam'),
        ],
    ],
    [
        'id'    => 'calidad-consistente',
        'title' => __('Calidad consistente', 'ese-latam'),
        'img'   => 'terreno/en-terreno.webp',
        'text'  => [
            __('Mediante un moldeo por inyección avanzado, logramos una', 'ese-latam') . ' ',
            __('homogeneidad molecular perfecta', 'ese-latam'),
            ' ' . __('en el polietileno de alta densidad (PEAD). Esto garantiza un espesor de pared uniforme, tenacidad al impacto constante y protección certificada contra degradación solar.', 'ese-latam'),
        ],
    ],
    [
        'id'    => 'trazabilidad',
        'title' => __('Trazabilidad', 'ese-latam'),
        'img'   => 'sectores/recoleccion.webp',
        'text'  => [
            __('Garantizamos la transparencia municipal con un sellado numérico de serie indeleble de por vida. Mediante chips RFID integrados de fábrica, es posible rastrear el', 'ese-latam') . ' ',
            __('origen exacto del plástico reciclado', 'ese-latam'),
            ' ' . __('PCR y verificar informes de conformidad en segundos.', 'ese-latam'),
        ],
    ],
    [
        'id'    => 'mejora-continua',
        'title' => __('Mejora continua', 'ese-latam'),
        'img'   => 'nosotros/parque.webp',
        'text'  => [
            __('Nuestras soluciones evolucionan con un ciclo de', 'ese-latam') . ' ',
            __('retroalimentación activa', 'ese-latam'),
            ' ' . __('de operarios de aseo urbanos a nivel global. Perfeccionamos continuamente la amortiguación acústica, aceleramos el acoplamiento y desarrollamos resinas ecológicas circulares sin perder rigidez.', 'ese-latam'),
        ],
    ],
];
?>

<div class="nosotros nosotros--certificaciones" data-nosotros>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>

    <?php
    get_template_part('template-parts/hero-interno', null, [
        'kicker'       => __('Respaldo comprobado', 'ese-latam'),
        'title'        => __('calidad y estándar certificado', 'ese-latam'),
        'title_strong' => __('que nos respaldan', 'ese-latam'),
        'desc'         => __('Garantizamos los ensayos de laboratorio, la trazabilidad del material y el cumplimiento de cada norma internacional que exige tu operación.', 'ese-latam'),
        'cta_label'    => __('Explorar productos', 'ese-latam'),
        'cta_href'     => (string) get_post_type_archive_link('producto'),
        'current'      => __('Certificaciones', 'ese-latam'),
        'bg'           => $ese_img('terreno/en-terreno.webp'),
    ]);

    get_template_part('template-parts/certificaciones-detalle');
    ?>

    <div class="nos-marquee nos-marquee--sm" aria-hidden="true">
        <p class="nos-marquee__track" data-marquee="right">
            <span><?php esc_html_e('marca la diferencia', 'ese-latam'); ?></span>
            <span><?php esc_html_e('marca la diferencia', 'ese-latam'); ?></span>
        </p>
    </div>

    <?php
    get_template_part('template-parts/metodo-tabs', null, [
        'id'           => 'diferencia',
        'kicker'       => __('Certificaciones', 'ese-latam'),
        'title'        => __('Marcando la', 'ese-latam'),
        'title_strong' => __('diferencia', 'ese-latam'),
        'desc'         => __('Un sello no es un logo en la ficha técnica: es la garantía de que el contenedor fue', 'ese-latam') . ' <span class="hl-accent">' . __('ensayado, auditado y trazado', 'ese-latam') . '</span> ' . __('antes de llegar a tu ciudad.', 'ese-latam'),
        'tabs'         => $ese_diferencia,
    ]);

    get_template_part('template-parts/producto-pruebas');

    get_template_part('template-parts/objetivos-sticky', null, [
        'id'           => 'calidad-superior',
        'kicker'       => __('Sobre nosotros', 'ese-latam'),
        'title'        => __('Calidad superior', 'ese-latam'),
        'title_strong' => __('desde el origen', 'ese-latam'),
        'desc'         => __('Metas claras que', 'ese-latam') . ' <span class="hl-accent">' . __('transforman', 'ese-latam') . '</span> ' . __('y', 'ese-latam') . ' <span class="hl-accent">' . __('mejoran', 'ese-latam') . '</span> ' . __('la gestión de residuos a nivel global.', 'ese-latam'),
        'items'        => $ese_calidad,
        'skip_label'   => __('Saltar sección', 'ese-latam'),
        'skip_href'    => '#blue-angel',
    ]);

    get_template_part('template-parts/blue-angel');
    get_template_part('template-parts/valida-certificados');

    get_template_part('template-parts/casos-reales', null, [
        'link_label' => __('Ver todos los casos', 'ese-latam'),
    ]);

    get_template_part('template-parts/contacto', null, [
        'class'          => 'contacto--upper',
        'heading'        => __('¿Necesitas validar un certificado o una', 'ese-latam'),
        'heading_strong' => __('ficha técnica', 'ese-latam'),
        'desc'           => __('Nuestro equipo técnico revisa sellos, informes de ensayo y documentación de origen sin costo para compras públicas.', 'ese-latam'),
    ]);
    ?>
</div>

<?php
get_footer();
