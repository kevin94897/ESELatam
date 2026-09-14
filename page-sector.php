<?php
/**
 * Template Name: Solución por sector
 *
 * Single de sector (Figma node 3551-5049, "04 – Solución: Municipalidades").
 * Una sola plantilla para todos los sectores: el sector se elige por el
 * slug de la página (mismo slug que en ese_latam_sectores()). Hoy solo
 * existe la página `municipalidades`, que crea inc/paginas.php; para sumar
 * otro sector basta agregar su slug allí y su copy en $ese_contenido acá
 * (mientras no lo tenga, hereda el de municipalidades).
 *
 * Composición, en orden del Figma. Todo lo que ya existía se reutiliza:
 *   1. Hero oscuro (hero-interno.php: el .nos-hero de Nosotros + kicker + CTA)
 *   2. Desafíos en la gestión urbana (sector-desafios.php, propio)
 *   3. Nuestro criterio de adaptabilidad (sector-criterio.php, propio)
 *   4. Marquee "soluciones recomendadas" (.nos-marquee de Nosotros)
 *   5. Soluciones recomendadas → producto-recomendados.php (ficha de producto)
 *   6. Calidad certificada → certificaciones.php (titular y centrado por $args)
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

$ese_img  = static fn (string $file): string => ESE_LATAM_URI . '/assets/imgs/' . $file;
$ese_slug = (string) get_post_field('post_name', get_queried_object_id());

$ese_sector = null;
foreach (ese_latam_sectores() as $ese_candidato) {
    if ($ese_candidato['slug'] === $ese_slug) {
        $ese_sector = $ese_candidato;
        break;
    }
}
$ese_sector ??= ese_latam_sectores()[0];

// Titular en dos pesos: "municipalidades y" + "gobiernos locales".
$ese_palabras = explode(' ', $ese_sector['title']);
$ese_strong   = count($ese_palabras) > 2 ? implode(' ', array_splice($ese_palabras, -2)) : '';
$ese_light    = implode(' ', $ese_palabras);

// ---------- Copy por sector ----------
$ese_contenido = [
    'municipalidades' => [
        'kicker'  => __('Solución para', 'ese-latam'),
        'desc'    => __('Sistemas de contención para ciudades más limpias y ordenadas.', 'ese-latam'),
        'desafios_desc' => __('Abordamos y resolvemos de raíz los problemas más complejos de contaminación, higiene pública y logística con tecnología de', 'ese-latam') . ' <span class="hl-accent">' . __('nivel europeo', 'ese-latam') . '</span>.',
        'dolores' => [
            ['title' => __('Estética y orden urbano', 'ese-latam'),     'sub' => __('(Falta de limpieza y desorden visual)', 'ese-latam'), 'desc' => __('Pérdida continua de la limpieza y el orden urbano en zonas comunes.', 'ese-latam')],
            ['title' => __('Deterioro comunitario', 'ese-latam'),       'sub' => __('Falta de limpieza y desorden visual', 'ese-latam'),   'desc' => __('Contenedores tradicionales frágiles o mal diseñados que colapsan ante el uso urbano masivo y continuo.', 'ese-latam'), 'img' => 'sectores/desafio-deterioro.webp'],
            ['title' => __('Residuos en vías', 'ese-latam'),            'sub' => __('Falta de limpieza y desorden visual', 'ese-latam'),   'desc' => __('Acumulación descontrolada de residuos y bolsas en esquinas, calles, veredas…', 'ese-latam')],
            ['title' => __('Sanidad urbana', 'ese-latam'),              'sub' => __('Control de plagas y sellado', 'ese-latam'),           'desc' => __('Pérdida continua de la limpieza y el orden urbano en zonas comunes.', 'ese-latam'), 'img' => 'sectores/desafio-sanidad.webp'],
            ['title' => __('Incompatibilidad mecánica', 'ese-latam'),   'sub' => __('Gestión ineficiente', 'ese-latam'),                  'desc' => __('Pérdida continua de la limpieza y el orden urbano en zonas comunes.', 'ese-latam')],
            ['title' => __('Riesgos sanitarios y plagas', 'ese-latam'), 'sub' => __('Falta de limpieza y desorden visual', 'ese-latam'),   'desc' => __('Proliferación masiva de plagas (roedores, moscas e insectos) e incremento de riesgos sanitarios críticos debido a sistemas de contención abiertos.', 'ese-latam')],
        ],
        // El Figma solo muestra el estado "Dolores y brechas"; el alivio
        // responde punto por punto a cada dolor, a validar con el cliente.
        'alivios' => [
            ['title' => __('Ciudades ordenadas', 'ese-latam'),    'sub' => __('Diseño urbano integrado', 'ese-latam'), 'desc' => __('Contenedores con volumetría y color uniformes que devuelven orden visual a plazas, calles y zonas comunes.', 'ese-latam')],
            ['title' => __('Durabilidad comprobada', 'ese-latam'), 'sub' => __('Ingeniería europea', 'ese-latam'),      'desc' => __('HDPE de alta densidad con nervaduras reforzadas y protección UV: resisten años de uso intensivo sin deformarse.', 'ese-latam'), 'img' => 'sectores/municipalidades.webp'],
            ['title' => __('Vías despejadas', 'ese-latam'),       'sub' => __('Capacidad calibrada', 'ese-latam'),      'desc' => __('Litrajes definidos según densidad y frecuencia de recolección, para que ningún residuo quede fuera del contenedor.', 'ese-latam')],
            ['title' => __('Sanidad garantizada', 'ese-latam'),   'sub' => __('Cierre hermético', 'ese-latam'),         'desc' => __('Tapas con sellado que aíslan olores y bloquean el acceso de plagas y vectores.', 'ese-latam'), 'img' => 'sectores/recoleccion.webp'],
            ['title' => __('Compatibilidad total', 'ese-latam'),  'sub' => __('Norma EN 840', 'ese-latam'),             'desc' => __('Peine DIN y ejes reforzados compatibles con cualquier sistema de volteo mecanizado.', 'ese-latam')],
            ['title' => __('Control sanitario', 'ese-latam'),     'sub' => __('Higiene y trazabilidad', 'ese-latam'),   'desc' => __('Superficies lisas de fácil lavado y chips RFID opcionales para auditar rutas y frecuencias de limpieza.', 'ese-latam')],
        ],
        'quote'   => __('Porque entendemos que el mismo contenedor', 'ese-latam') . ' <span class="hl-accent">' . __('no funciona igual', 'ese-latam') . '</span> ' . __('en todos los contextos.', 'ese-latam'),
        'criterio' => __('Personalizamos el equipamiento y su disposición según la densidad habitacional, clima, características logísticas de recolección y arquitectura particular de cada comuna para asegurar una solución definitiva.', 'ese-latam'),
    ],
];
$ese_copy = $ese_contenido[$ese_sector['slug']] ?? $ese_contenido['municipalidades'];
?>

<div class="nosotros nosotros--sector" data-nosotros>
    <?php // Marca "hay JS": solo entonces el CSS esconde el hero hasta que
    // nosotros.ts lo anime (sin JS queda visible desde el HTML). ?>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>

    <?php
    get_template_part('template-parts/hero-interno', null, [
        'kicker'       => $ese_copy['kicker'],
        'title'        => $ese_light,
        'title_strong' => $ese_strong,
        'desc'         => $ese_copy['desc'],
        'cta_label'    => __('Explorar productos', 'ese-latam'),
        'cta_href'     => (string) get_post_type_archive_link('producto'),
        'crumb'        => [['label' => __('Sectores', 'ese-latam'), 'url' => ese_latam_pagina_url('sectores', home_url('/#sectores'))]],
        'current'      => $ese_sector['title'],
        'bg'           => $ese_img('sectores/' . $ese_sector['img']),
    ]);

    get_template_part('template-parts/sector-desafios', null, [
        'desc'    => $ese_copy['desafios_desc'],
        'dolores' => $ese_copy['dolores'],
        'alivios' => $ese_copy['alivios'],
    ]);

    get_template_part('template-parts/sector-criterio', null, [
        'quote' => $ese_copy['quote'],
        'desc'  => $ese_copy['criterio'],
    ]);
    ?>

    <div class="nos-marquee nos-marquee--sm" aria-hidden="true">
        <p class="nos-marquee__track" data-marquee="right">
            <span><?php esc_html_e('soluciones recomendadas', 'ese-latam'); ?></span>
            <span><?php esc_html_e('soluciones recomendadas', 'ese-latam'); ?></span>
        </p>
    </div>

    <?php
    get_template_part('template-parts/producto-recomendados');

    get_template_part('template-parts/certificaciones', null, [
        'title'        => __('calidad', 'ese-latam'),
        'title_accent' => __('certificada', 'ese-latam'),
        'centered'     => true,
    ]);

    get_template_part('template-parts/economia-circular');

    get_template_part('template-parts/casos-reales', null, [
        'kicker'     => __('Casos reales', 'ese-latam'),
        'link_label' => __('Ver todos los casos', 'ese-latam'),
    ]);

    get_template_part('template-parts/contacto', null, [
        'class'          => 'contacto--upper',
        'heading'        => __('¿Listo para llevar tu gestión de residuos al', 'ese-latam'),
        'heading_strong' => __('siguiente nivel', 'ese-latam'),
        'desc'           => __('Trabajamos directamente con tu equipo para entender la operación, diseñar la solución correcta e implementarla. Con soporte técnico desde Alemania.', 'ese-latam'),
    ]);
    ?>
</div>

<?php
get_footer();
