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

// Todo el copy de esta pantalla se edita en la propia página (ver
// inc/pcf-impacto.php); las secciones compartidas traen el suyo.
$ese_id      = (int) get_queried_object_id();
$ese_hero_cta = ese_latam_enlace(ese_latam_campo('impacto_hero_cta', $ese_id, null));
?>

<div class="nosotros nosotros--impacto" data-nosotros>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>

    <?php
    get_template_part('template-parts/hero-interno', null, [
        'kicker'     => (string) ese_latam_campo('impacto_hero_kicker', $ese_id, ''),
        'title'      => (string) ese_latam_campo('impacto_hero_titulo', $ese_id, ''),
        'desc'       => (string) ese_latam_campo('impacto_hero_desc', $ese_id, ''),
        'cta_label'  => $ese_hero_cta['label'],
        'cta_href'   => $ese_hero_cta['href'],
        'cta_target' => $ese_hero_cta['target'],
        // La miga de pan usa el antetítulo ("Impacto"), que es como el menú
        // nombra a esta página; sin él cae al título real.
        'current'    => (string) ese_latam_campo('impacto_hero_kicker', $ese_id, '') ?: get_the_title($ese_id),
        'bg'         => ese_latam_img_url(ese_latam_campo('impacto_hero_imagen', $ese_id, '')),
    ]);

    get_template_part('template-parts/impacto-stats');
    get_template_part('template-parts/impacto-ticker');
    get_template_part('template-parts/residuos');

    get_template_part('template-parts/aliados', null, [
        'kicker' => (string) ese_latam_campo('impacto_aliados_kicker', $ese_id, ''),
        'title'  => (string) ese_latam_campo('impacto_aliados_titulo', $ese_id, ''),
        'desc'   => (string) ese_latam_campo('impacto_aliados_desc', $ese_id, ''),
        'isla'   => ese_latam_img_url(ese_latam_campo('impacto_aliados_isla', $ese_id, '')),
    ]);

    get_template_part('template-parts/casos-reales', null, ese_latam_args_casos($ese_id, 'impacto_casos'));

    get_template_part('template-parts/contacto', null, [
        'class' => 'contacto--upper',
    ]);
    ?>
</div>

<?php
get_footer();
