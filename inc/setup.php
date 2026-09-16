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
                echo '<img class="nav-mega__thumb" src="' . esc_url($child['img']) . '"'
                    . ' alt="" width="48" height="48" loading="lazy" decoding="async">';
                echo '<span class="nav-mega__text">';
                echo '<span class="nav-mega__title">' . esc_html($child['title']) . '</span>';
                echo '<span class="nav-mega__desc">' . esc_html($child['desc']) . '</span>';
                echo '</span></a></li>';
            }
            // Pie del panel: a la página de sectores (a lo ancho de las dos columnas).
            echo '<li class="menu-item nav-mega__all">';
            echo '<a href="' . esc_url((string) $item['url']) . '">' . esc_html__('Ver todos los sectores', 'ese-latam');
            echo '<span class="nav-mega__all-icon" aria-hidden="true">'
                . '<svg width="14" height="12" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/></svg>'
                . '</span></a></li>';
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
