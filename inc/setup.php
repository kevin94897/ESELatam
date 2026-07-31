<?php
/**
 * Configuración base del theme.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', static function (): void {
    load_theme_textdomain('ese-latam', ESE_LATAM_DIR . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', [
        'search-form',
        'gallery',
        'caption',
        'style',
        'script',
        'comment-list',
        'comment-form',
    ]);
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');

    register_nav_menus([
        'primary' => __('Menú principal', 'ese-latam'),
        'footer'  => __('Menú del footer', 'ese-latam'),
    ]);
});

/**
 * Fallback del menú principal cuando aún no hay un menú asignado en WP.
 * Replica los items del diseño (Figma "Hero – Estado A").
 *
 * @param array<string, mixed> $args Argumentos de wp_nav_menu.
 */
function ese_latam_nav_fallback(array $args): void {
    $items = [
        ['label' => __('Productos', 'ese-latam'),        'url' => '#productos',       'children' => false],
        ['label' => __('Sectores', 'ese-latam'),         'url' => '#sectores',        'children' => true],
        ['label' => __('Certificaciones', 'ese-latam'),  'url' => '#certificaciones', 'children' => false],
        ['label' => __('Impacto', 'ese-latam'),          'url' => '#impacto',         'children' => false],
    ];

    $class = isset($args['menu_class']) && is_string($args['menu_class']) ? $args['menu_class'] : '';

    echo '<ul class="' . esc_attr($class) . '">';
    foreach ($items as $item) {
        $li_class = 'menu-item' . ($item['children'] ? ' menu-item-has-children' : '');
        echo '<li class="' . esc_attr($li_class) . '">';
        echo '<a href="' . esc_url($item['url']) . '">' . esc_html($item['label']) . '</a>';
        echo '</li>';
    }
    echo '</ul>';
}

/**
 * Sidebars.
 */
add_action('widgets_init', static function (): void {
    register_sidebar([
        'name'          => __('Footer', 'ese-latam'),
        'id'            => 'footer-1',
        'description'   => __('Widgets del footer.', 'ese-latam'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title text-lg font-medium text-secondary-500">',
        'after_title'   => '</h3>',
    ]);
});
