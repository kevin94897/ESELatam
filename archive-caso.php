<?php
/**
 * Archivo del CPT "caso" — el blog de Casos de éxito (Figma node 3891-3677,
 * "11 – Blog: Casos de Éxito"). Responde en /casos-de-exito/ (has_archive
 * en inc/cpt-casos.php); los filtros viajan por query string (?q=, ?sector=,
 * ?ciudad=) y los aplica ese_latam_casos_pre_get_posts() a la query
 * principal, así la paginación nativa (/page/2/) sigue funcionando.
 *
 * Composición, en orden del Figma:
 *   1. Hero oscuro centrado (casos-hero.php) — mismo .nos-hero de las
 *      internas, animado por initHero de nosotros.ts.
 *   2. Buscador + filtros, grilla de 8 tarjetas y paginación (casos-archivo.php)
 *   3. Contactemos → template-parts/contacto.php con copy propio
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();

$ese_img = static fn (string $file): string => ESE_LATAM_URI . '/assets/imgs/' . $file;
?>

<div class="nosotros nosotros--casos" data-nosotros>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>

    <?php
    get_template_part('template-parts/casos-hero');
    get_template_part('template-parts/casos-archivo');

    get_template_part('template-parts/contacto', null, [
        'class'          => 'contacto--upper',
        'heading'        => __('¿Necesitas asesoría? Te ayudamos a elegir |con criterio|', 'ese-latam'),
        'question'       => false,
        'desc'           => __('Desarrollamos configuraciones a la medida de cualquier exigencia operativa. Contáctanos para recibir asesoría especializada y diseñar tu sistema óptimo.', 'ese-latam'),
        'bg'             => $ese_img('nosotros/cta-bg.webp'),
    ]);
    ?>
</div>

<?php
get_footer();
