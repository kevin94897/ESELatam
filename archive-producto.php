<?php
/**
 * Archivo del CPT "producto" (slug /catalogo, has_archive en
 * inc/cpt-productos.php).
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();
get_template_part('template-parts/catalogo-banner');
get_template_part('template-parts/catalogo-grid');
get_template_part('template-parts/contacto');
get_footer();
