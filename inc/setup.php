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

    // Logo desde Apariencia → Personalizar → Identidad del sitio. El theme no
    // usa the_custom_logo(): su markup no serviría para las DOS capas que se
    // funden con el scroll en la cabecera (ver .site-logo en main.css), así
    // que la imagen se lee con ese_latam_logo() y se pinta en el markup
    // propio. Sin logo cargado sigue el PNG del theme.
    add_theme_support('custom-logo', [
        'height'      => 43,
        'width'       => 142,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    // El footer son TRES columnas, cada una con su propio título y su propio
    // orden, así que cada una lleva su menú: con uno solo no habría forma de
    // mover un link de columna desde el admin. Los crea y asigna
    // ese_latam_asegurar_menus() (inc/menus.php).
    register_nav_menus([
        'primary'  => __('Menú principal', 'ese-latam'),
        'footer_1' => __('Menú Footer — columna 1', 'ese-latam'),
        'footer_2' => __('Menú Footer — columna 2', 'ese-latam'),
        'footer_3' => __('Menú Footer — columna 3', 'ese-latam'),
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
        ['label' => __('Productos', 'ese-latam'),        'url' => get_post_type_archive_link('producto'), 'mega' => false],
        ['label' => __('Sectores', 'ese-latam'),         'url' => ese_latam_pagina_url('sectores', home_url('/#sectores')), 'mega' => true],
        ['label' => __('Certificaciones', 'ese-latam'),  'url' => ese_latam_pagina_url('certificaciones', home_url('/#certificaciones')), 'mega' => false],
        ['label' => __('Impacto', 'ese-latam'),          'url' => ese_latam_pagina_url('impacto', home_url('/#impacto')), 'mega' => false],
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
        // El panel lo arma ese_latam_nav_mega() (inc/menus.php), el mismo que
        // usa el menú real: si no hay sectores publicados, devuelve '' y el
        // item queda sin desplegable.
        $mega         = $with_children && $item['mega'] ? ese_latam_nav_mega((string) $item['url']) : '';
        $has_children = '' !== $mega;
        $li_class     = 'menu-item' . ($has_children ? ' menu-item-has-children' : '');

        echo '<li class="' . esc_attr($li_class) . '">';
        echo '<a href="' . esc_url((string) $item['url']) . '"'
            . ($has_children ? ' aria-haspopup="true" aria-expanded="false"' : '')
            . '>' . esc_html($item['label']) . '</a>';

        echo $mega;
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
