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
    // Datos clave + sellos del producto (antes iban al pie del hero) y,
    // a continuación, la franja de certificaciones (antes cerraba la
    // página en su variante oscura).
    get_template_part('template-parts/producto-atributos');
    get_template_part('template-parts/certificaciones');
    // get_template_part('template-parts/producto-specs');
    get_template_part('template-parts/producto-pruebas');
    get_template_part('template-parts/producto-terreno');
    // El bloque ahora lo comparten la ficha y el caso de éxito, así que el
    // copy viaja por args. Acá sigue escrito en la plantilla: la ficha de
    // producto todavía no tiene campos para esta sección.
    get_template_part('template-parts/producto-recomendados', null, [
        'kicker' => __('Productos', 'ese-latam'),
        'title'  => __('Soluciones', 'ese-latam') . ' ' . __('|recomendadas|', 'ese-latam'),
        'desc'   => __('Otras configuraciones de la misma familia, pensadas para distintos volúmenes y necesidades de recolección', 'ese-latam'),
    ]);

endwhile;

get_template_part('template-parts/contacto');

get_footer();
