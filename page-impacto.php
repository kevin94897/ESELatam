<?php
/**
 * Template Name: Residuos inteligentes (Impacto)
 *
 * Página "Impacto / Residuos inteligentes" (Figma node 3824-3688, "08 –
 * Impacto"). WordPress la aplica sola a la página con slug `impacto` (que
 * crea inc/paginas.php) y, por el header de arriba, también se puede
 * asignar a mano desde el editor.
 *
 * Composición, en orden del Figma. Todo lo que ya existía se reutiliza:
 *   1. Hero oscuro (hero-interno.php)
 *   2. Gestionar residuos es una decisión humana (impacto-stats.php, propio)
 *   3. Franja de frases (impacto-ticker.php, propio)
 *   4. Ingeniería de alto desempeño → residuos.php (la misma sección de la home)
 *   5. Unidos por las mejores alianzas → aliados.php (la grilla de logos de Nosotros)
 *   6. Casos reales (casos-reales.php)
 *   7. Contactemos → contacto.php
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();

$ese_img = static fn (string $file): string => ESE_LATAM_URI . '/assets/imgs/' . $file;
?>

<div class="nosotros nosotros--impacto" data-nosotros>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>

    <?php
    get_template_part('template-parts/hero-interno', null, [
        'kicker'       => __('Impacto', 'ese-latam'),
        'title'        => __('Residuos |inteligentes|', 'ese-latam'),
        'desc'         => __('Trabajamos con gobiernos y comunidades para convertir la gestión de residuos en una decisión humana: medible, circular y con impacto real en cada ciudad.', 'ese-latam'),
        'cta_label'    => __('Conoce los casos', 'ese-latam'),
        'cta_href'     => '#casos-reales',
        'current'      => __('Impacto', 'ese-latam'),
        'bg'           => $ese_img('nosotros/parque.webp'),
    ]);

    get_template_part('template-parts/impacto-stats');
    get_template_part('template-parts/impacto-ticker');
    get_template_part('template-parts/residuos');

    get_template_part('template-parts/aliados', null, [
        'kicker'       => __('Alianzas', 'ese-latam'),
        'title'        => __('Unidos por', 'ese-latam') . "\n" . __('|las mejores alianzas|', 'ese-latam'),
        'desc'         => __('Distribuidores, municipios y operadores que ya trabajan con contenedores ESE en toda Latinoamérica.', 'ese-latam'),
    ]);

    get_template_part('template-parts/casos-reales', null, [
        'link_label' => __('Ver todos los casos', 'ese-latam'),
    ]);

    get_template_part('template-parts/contacto', null, [
        'class' => 'contacto--upper',
    ]);
    ?>
</div>

<?php
get_footer();
