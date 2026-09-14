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
    // URLs absolutas (home_url) y no anclas sueltas: el header se comparte en
    // todas las páginas y un `#sectores` desde una ficha de producto no lleva
    // a ningún lado. "Productos" ya tiene página propia: el catálogo.
    $items = [
        ['label' => __('Productos', 'ese-latam'),        'url' => get_post_type_archive_link('producto'), 'children' => []],
        ['label' => __('Sectores', 'ese-latam'),         'url' => ese_latam_pagina_url('sectores', home_url('/#sectores')), 'children' => ese_latam_sectores()],
        ['label' => __('Certificaciones', 'ese-latam'),  'url' => ese_latam_pagina_url('certificaciones', home_url('/#certificaciones')), 'children' => []],
        ['label' => __('Impacto', 'ese-latam'),          'url' => ese_latam_pagina_url('impacto', home_url('/#impacto')), 'children' => []],
    ];

    $class = isset($args['menu_class']) && is_string($args['menu_class']) ? $args['menu_class'] : '';

    // Mismo contrato que wp_nav_menu: con depth 1 (menú móvil) no hay
    // submenús; con 0 o >= 2 (nav-pill) sí. El submenú usa las mismas clases
    // que emite el walker de WP (.sub-menu, .menu-item-has-children), así el
    // CSS/JS del desplegable sirve igual cuando se asigne un menú real.
    $depth         = isset($args['depth']) ? (int) $args['depth'] : 0;
    $with_children = 0 === $depth || $depth >= 2;

    echo '<ul class="' . esc_attr($class) . '">';
    foreach ($items as $item) {
        $has_children = $with_children && ! empty($item['children']);
        $li_class     = 'menu-item' . ($has_children ? ' menu-item-has-children' : '');

        echo '<li class="' . esc_attr($li_class) . '">';
        echo '<a href="' . esc_url((string) $item['url']) . '"'
            . ($has_children ? ' aria-haspopup="true" aria-expanded="false"' : '')
            . '>' . esc_html($item['label']) . '</a>';

        if ($has_children) {
            echo '<ul class="sub-menu nav-mega">';
            foreach ($item['children'] as $child) {
                echo '<li class="menu-item">';
                echo '<a class="nav-mega__item" href="' . esc_url(ese_latam_sector_url($child)) . '">';
                echo '<img class="nav-mega__thumb" src="' . esc_url(ESE_LATAM_URI . '/assets/imgs/sectores/' . $child['img']) . '"'
                    . ' alt="" width="48" height="48" loading="lazy" decoding="async">';
                echo '<span class="nav-mega__text">';
                echo '<span class="nav-mega__title">' . esc_html($child['title']) . '</span>';
                echo '<span class="nav-mega__desc">' . esc_html($child['desc']) . '</span>';
                echo '</span></a></li>';
            }
            echo '</ul>';
        }

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
