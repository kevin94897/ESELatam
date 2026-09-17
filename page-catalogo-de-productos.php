<?php
/**
 * Página "Catálogo de productos" (creada desde wp-admin, slug
 * catalogo-de-productos) — WordPress aplica este archivo automáticamente
 * por convención de nombre (page-{slug}.php), sin necesidad de asignar
 * una plantilla a mano en el editor.
 *
 * @package EseLatam
 */
declare(strict_types=1);

// Toda la pantalla va sobre blanco: el gris de fondo del theme (--color-bg)
// se ve en los costados cuando el viewport supera el ancho máximo del bloque
// y bajo el pie. El filtro se marca ya con su borde, así que tampoco
// necesita el gris. Va como clase en el body para no tener que envolver la
// página en un div solo por el color.
add_filter('body_class', static function (array $clases): array {
    $clases[] = 'catalogo-blanco';

    return $clases;
});

get_header();
get_template_part('template-parts/catalogo-banner');
get_template_part('template-parts/catalogo-grid');
get_template_part('template-parts/contacto');
get_footer();
