<?php
/**
 * Template Name: Soluciones por sector
 *
 * Página "Soluciones por sector" (Figma node 3510-6982). WordPress la
 * aplica sola a la página con slug `sectores` — que el theme crea una vez
 * desde inc/paginas.php — y, por el header de arriba, también se puede
 * asignar a mano desde el editor.
 *
 * Composición, en orden del Figma. Todo lo que ya existía en el sitio se
 * reutiliza tal cual (con `$args` donde hace falta) en vez de duplicarlo:
 *   1. Hero claro con breadcrumb (propio, template-parts/sectores-hero.php)
 *   2. Grilla bento de los 8 sectores (propio, sectores-grid.php; los datos
 *      salen de ese_latam_sectores(), la misma lista de la home y del menú)
 *   3. "Entendemos tu operación": pasos con autoplay (propio, sectores-proceso.php)
 *   4. Certificaciones → template-parts/certificaciones.php (idéntica a la home)
 *   5. Contactemos → template-parts/contacto.php (copy propio vía $args)
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();
?>

<div class="sectores-page" data-sectores-page>
    <?php
    get_template_part('template-parts/sectores-hero');
    get_template_part('template-parts/sectores-grid');
    get_template_part('template-parts/sectores-proceso');
    get_template_part('template-parts/certificaciones');
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
