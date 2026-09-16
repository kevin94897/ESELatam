<?php
/**
 * Módulo "Sectores": un CPT por sector, en vez de la lista cerrada que antes
 * vivía en un repetidor.
 *
 * Cada sector es una entrada con su propia ficha (inc/pcf-sectores.php) y su
 * página en `/sectores/{slug}/` (single-sector.php). La misma lista alimenta
 * el slider de la portada, el submenú "Sectores" del nav, la grilla de
 * "Soluciones por sector" y las etiquetas de los casos de éxito: todo sale de
 * ese_latam_sectores() (inc/template-tags.php).
 *
 * La página madre `/sectores/` (id 7) sigue siendo una página normal con sus
 * propios campos; el CPT cuelga de esa misma base sin pisarla, porque las
 * reglas del post type se evalúan antes que el comodín de páginas.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Slug de la página madre y base de las URLs del módulo.
 */
const ESE_LATAM_SECTORES_BASE = 'sectores';

add_action('init', static function (): void {
    register_post_type('sector', [
        'labels' => [
            'name'               => __('Sectores', 'ese-latam'),
            'singular_name'      => __('Sector', 'ese-latam'),
            'add_new'            => __('Añadir sector', 'ese-latam'),
            'add_new_item'       => __('Añadir nuevo sector', 'ese-latam'),
            'edit_item'          => __('Editar sector', 'ese-latam'),
            'new_item'           => __('Nuevo sector', 'ese-latam'),
            'view_item'          => __('Ver sector', 'ese-latam'),
            'view_items'         => __('Ver sectores', 'ese-latam'),
            'search_items'       => __('Buscar sectores', 'ese-latam'),
            'not_found'          => __('No se encontraron sectores', 'ese-latam'),
            'not_found_in_trash' => __('No hay sectores en la papelera', 'ese-latam'),
            'all_items'          => __('Todos los sectores', 'ese-latam'),
            'menu_name'          => __('Sectores', 'ese-latam'),
            'featured_image'     => __('Foto del sector', 'ese-latam'),
            'set_featured_image' => __('Elegir foto del sector', 'ese-latam'),
        ],
        // Sin archivo propio: el listado es la página "Soluciones por sector".
        'public'        => true,
        'has_archive'   => false,
        'rewrite'       => ['slug' => ESE_LATAM_SECTORES_BASE, 'with_front' => false],
        'menu_icon'     => 'dashicons-category',
        'menu_position' => 5,
        // `page-attributes` da el campo "Orden": es el orden del slider, del
        // megamenú y de la grilla.
        'supports'      => ['title', 'thumbnail', 'page-attributes'],
        'show_in_rest'  => true,
    ]);
});

/**
 * Las entradas del módulo se ordenan por el campo "Orden" del editor y, a
 * igualdad, por antigüedad: así el orden del admin es el del frontend.
 */
add_action('pre_get_posts', static function (WP_Query $q): void {
    if (! is_admin() || ! $q->is_main_query()) {
        return;
    }
    if ('sector' === $q->get('post_type') && '' === $q->get('orderby')) {
        $q->set('orderby', 'menu_order title');
        $q->set('order', 'ASC');
    }
});

/**
 * Los sectores vivían como páginas en la raíz (`/municipalidades/`). Al pasar
 * al módulo cuelgan de `/sectores/`, así que la URL vieja redirige a la nueva
 * en vez de devolver un 404.
 */
add_action('template_redirect', static function (): void {
    if (! is_404()) {
        return;
    }

    $pedido = trim((string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH), '/');
    if ('' === $pedido || false !== strpos($pedido, '/')) {
        return;
    }

    $sector = get_page_by_path($pedido, OBJECT, 'sector');
    if ($sector instanceof WP_Post && 'publish' === $sector->post_status) {
        wp_safe_redirect((string) get_permalink($sector), 301);
        exit;
    }
});

/**
 * Refresca las reglas de URL una sola vez, cuando el módulo se registra por
 * primera vez. Sin esto `/sectores/{slug}/` daría 404 hasta que alguien
 * guarde los enlaces permanentes a mano.
 */
add_action('init', static function (): void {
    if ('2' === get_option('ese_latam_sectores_setup')) {
        return;
    }

    flush_rewrite_rules(false);
    update_option('ese_latam_sectores_setup', '2');
}, 99);
