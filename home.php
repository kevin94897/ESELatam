<?php
/**
 * Índice del blog (Figma 3848-9017, "09 – Blog: Listado").
 *
 * WordPress aplica home.php a la página fijada como "página de entradas"
 * en Ajustes → Lectura, que crea y fija inc/paginas.php. Las entradas son
 * las nativas, con sus categorías y etiquetas (ver inc/blog.php).
 *
 * Composición, en orden del Figma:
 *   1. Hero oscuro centrado (casos-hero.php) — el mismo de Casos de éxito,
 *      acá sin foto de fondo.
 *   2. Filtros, grilla de tarjetas y paginación (blog-archivo.php)
 *   3. Contactemos → template-parts/contacto.php
 *
 * Todo el copy se edita en la página "Blog" (inc/pcf-blog.php).
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();

$ese_id  = (int) get_option('page_for_posts');
$ese_cmp = static fn (string $name, $def = '') => ese_latam_campo($name, $ese_id, $def);
?>

<div class="nosotros nosotros--casos" data-nosotros>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>

    <?php
    get_template_part('template-parts/casos-hero', null, [
        'kicker'  => (string) $ese_cmp('blog_hero_kicker'),
        'title'   => (string) $ese_cmp('blog_hero_titulo'),
        'desc'    => (string) $ese_cmp('blog_hero_desc'),
        'current' => $ese_id > 0 ? get_the_title($ese_id) : '',
        'bg'      => ese_latam_img_url($ese_cmp('blog_hero_imagen')),
    ]);

    get_template_part('template-parts/blog-archivo');

    get_template_part('template-parts/contacto', null, [
        'class' => 'contacto--upper',
    ]);
    ?>
</div>

<?php
get_footer();
