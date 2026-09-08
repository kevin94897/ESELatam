<?php
/**
 * CPT "Producto" — el catálogo. Cada producto es una entrada de este tipo;
 * sus datos de ficha (litraje, colores, características, etc.) se editan
 * en los meta boxes de producto-meta-boxes.php.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

add_action('init', static function (): void {
    register_post_type('producto', [
        'labels' => [
            'name'               => __('Productos', 'ese-latam'),
            'singular_name'      => __('Producto', 'ese-latam'),
            'add_new'            => __('Añadir producto', 'ese-latam'),
            'add_new_item'       => __('Añadir nuevo producto', 'ese-latam'),
            'edit_item'          => __('Editar producto', 'ese-latam'),
            'new_item'           => __('Nuevo producto', 'ese-latam'),
            'view_item'          => __('Ver producto', 'ese-latam'),
            'view_items'         => __('Ver productos', 'ese-latam'),
            'search_items'       => __('Buscar productos', 'ese-latam'),
            'not_found'          => __('No se encontraron productos', 'ese-latam'),
            'not_found_in_trash' => __('No hay productos en la papelera', 'ese-latam'),
            'all_items'          => __('Todos los productos', 'ese-latam'),
            'menu_name'          => __('Productos', 'ese-latam'),
            'featured_image'     => __('Imagen principal', 'ese-latam'),
            'set_featured_image' => __('Elegir imagen principal', 'ese-latam'),
        ],
        'public'       => true,
        'has_archive'  => 'catalogo',
        'rewrite'      => ['slug' => 'productos'],
        'menu_icon'    => 'dashicons-archive',
        'menu_position' => 5,
        'supports'     => ['title', 'thumbnail'],
        'show_in_rest' => true,
    ]);

    // Checkbox tipo categorías (no jerárquico como tags): el cliente marca
    // qué categorías aplican a cada producto — mismo filtro que en la
    // sección "Productos" del home (Contenedores, Papeleras, Soterrados...).
    register_taxonomy('producto_categoria', 'producto', [
        'labels' => [
            'name'          => __('Categorías de producto', 'ese-latam'),
            'singular_name' => __('Categoría de producto', 'ese-latam'),
            'menu_name'     => __('Categorías', 'ese-latam'),
        ],
        'hierarchical'      => true,
        'public'            => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'categoria-producto'],
    ]);

    // Igual de simple: checkbox de certificaciones (Blue Angel, PKN, DIN,
    // TÜV SÜD, Seconda Vita...) — reutiliza el mismo patrón de taxonomía en
    // vez de un campo de texto libre, así queda una lista controlada y
    // consistente con la sección "Certificaciones" del home.
    register_taxonomy('producto_certificacion', 'producto', [
        'labels' => [
            'name'          => __('Certificaciones', 'ese-latam'),
            'singular_name' => __('Certificación', 'ese-latam'),
            'menu_name'     => __('Certificaciones', 'ese-latam'),
        ],
        'hierarchical'      => true,
        'public'            => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'certificacion'],
    ]);
});
