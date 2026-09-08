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

get_header();
get_template_part('template-parts/catalogo-banner');
get_template_part('template-parts/catalogo-grid');
get_template_part('template-parts/contacto');
get_footer();
