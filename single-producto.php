<?php
/**
 * Ficha de producto (CPT `producto`, URLs /productos/{slug}/).
 *
 * Composición de template-parts, mismo criterio que archive-producto.php /
 * page-catalogo-de-productos.php: cada sección resuelve sus propios datos
 * (ACF con fallback estático) de forma independiente, así que se pueden
 * reordenar o quitar sin arrastrar variables de otro archivo.
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();

while (have_posts()) :
    the_post();

    get_template_part('template-parts/producto-hero');
    // get_template_part('template-parts/producto-specs');
    get_template_part('template-parts/producto-pruebas');
    get_template_part('template-parts/producto-terreno');
    get_template_part('template-parts/producto-recomendados');

endwhile;

get_template_part('template-parts/certificaciones', null, ['dark' => true]);
get_template_part('template-parts/contacto');

get_footer();
