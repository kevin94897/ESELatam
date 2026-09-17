<?php
/**
 * CPT "Caso de éxito" — el blog de casos (Figma 3891-3677, "11 – Blog:
 * Casos de Éxito"). Cada caso es una entrada con foto destacada, sector
 * (taxonomía caso_sector, la misma lista de ese_latam_sectores()) y ciudad
 * (taxonomía caso_ciudad). El archivo vive en /casos-de-exito/ y lo pinta
 * archive-caso.php; las singles cuelgan de /casos-de-exito/{slug}/.
 *
 * Acá también viven los filtros del archivo (?q=, ?sector=, ?ciudad=) y los
 * helpers de tarjeta que comparten archive-caso.php y casos-reales.php.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

add_action('init', static function (): void {
    register_post_type('caso', [
        'labels' => [
            'name'               => __('Casos de éxito', 'ese-latam'),
            'singular_name'      => __('Caso de éxito', 'ese-latam'),
            'add_new'            => __('Añadir caso', 'ese-latam'),
            'add_new_item'       => __('Añadir nuevo caso', 'ese-latam'),
            'edit_item'          => __('Editar caso', 'ese-latam'),
            'new_item'           => __('Nuevo caso', 'ese-latam'),
            'view_item'          => __('Ver caso', 'ese-latam'),
            'view_items'         => __('Ver casos', 'ese-latam'),
            'search_items'       => __('Buscar casos', 'ese-latam'),
            'not_found'          => __('No se encontraron casos', 'ese-latam'),
            'not_found_in_trash' => __('No hay casos en la papelera', 'ese-latam'),
            'all_items'          => __('Todos los casos', 'ese-latam'),
            'menu_name'          => __('Casos de éxito', 'ese-latam'),
            'featured_image'     => __('Foto del caso', 'ese-latam'),
            'set_featured_image' => __('Elegir foto del caso', 'ese-latam'),
        ],
        'public'        => true,
        'has_archive'   => 'casos-de-exito',
        'rewrite'       => ['slug' => 'casos-de-exito', 'with_front' => false],
        'menu_icon'     => 'dashicons-megaphone',
        'menu_position' => 6,
        'supports'      => ['title', 'editor', 'excerpt', 'thumbnail'],
        'show_in_rest'  => true,
    ]);

    // Sector del caso: checkbox (jerárquica) para que quede una lista
    // controlada — es el filtro "Sector" del archivo y el chip de la tarjeta.
    register_taxonomy('caso_sector', 'caso', [
        'labels' => [
            'name'          => __('Sectores', 'ese-latam'),
            'singular_name' => __('Sector', 'ese-latam'),
            'menu_name'     => __('Sectores', 'ese-latam'),
        ],
        'hierarchical'      => true,
        'public'            => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'sector-caso'],
    ]);

    // Ciudad: mismo patrón — filtro "Filtrar por ciudad" y chip con el pin.
    register_taxonomy('caso_ciudad', 'caso', [
        'labels' => [
            'name'          => __('Ciudades', 'ese-latam'),
            'singular_name' => __('Ciudad', 'ese-latam'),
            'menu_name'     => __('Ciudades', 'ese-latam'),
        ],
        'hierarchical'      => true,
        'public'            => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'ciudad'],
    ]);
});

/**
 * Primera carga después de registrar el CPT: siembra los 8 sectores como
 * términos (misma lista del menú y de la página Sectores, así el filtro no
 * arranca vacío) y refresca las reglas de rewrite para que /casos-de-exito/
 * responda sin pasar por Ajustes → Enlaces permanentes. Idempotente vía
 * opción, igual que ese_latam_asegurar_paginas().
 */
add_action('init', static function (): void {
    if ('1' === get_option('ese_latam_casos_setup')) {
        return;
    }

    foreach (ese_latam_sectores() as $sector) {
        if (! term_exists($sector['slug'], 'caso_sector')) {
            wp_insert_term($sector['title'], 'caso_sector', ['slug' => $sector['slug']]);
        }
    }

    flush_rewrite_rules(false);
    update_option('ese_latam_casos_setup', '1');
}, 99);

/**
 * URL del archivo de casos (/casos-de-exito/).
 */
function ese_latam_casos_url(): string {
    $url = get_post_type_archive_link('caso');
    return is_string($url) && '' !== $url ? $url : home_url('/casos-de-exito/');
}

/**
 * Filtros activos del archivo, ya saneados.
 *
 * Ninguno de los tres se llama como su concepto, y es a propósito: `s` haría
 * que WordPress tratara la petición como búsqueda global y dejara de aplicar
 * archive-caso.php, y `sector` es la query var del CPT `sector`
 * (inc/cpt-sectores.php), así que `?sector=hospitalarios` servía la página
 * de ese sector en vez de filtrar los casos. De ahí `q` y `rubro`.
 *
 * @return array{q: string, sector: string, ciudad: string}
 */
function ese_latam_casos_filtros(): array {
    return [
        'q'      => isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '',
        'sector' => isset($_GET['rubro']) ? sanitize_title(wp_unslash($_GET['rubro'])) : '',
        'ciudad' => isset($_GET['ciudad']) ? sanitize_title(wp_unslash($_GET['ciudad'])) : '',
    ];
}

/**
 * Aplica los filtros a la query principal del archivo: 8 por página (dos
 * filas de cuatro, como el Figma), búsqueda por texto y taxonomías.
 */
function ese_latam_casos_pre_get_posts(WP_Query $query): void {
    if (is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive('caso')) {
        return;
    }

    $filtros = ese_latam_casos_filtros();

    $query->set('posts_per_page', 8);

    if ('' !== $filtros['q']) {
        $query->set('s', $filtros['q']);
    }

    $tax_query = [];
    if ('' !== $filtros['sector']) {
        $tax_query[] = ['taxonomy' => 'caso_sector', 'field' => 'slug', 'terms' => $filtros['sector']];
    }
    if ('' !== $filtros['ciudad']) {
        $tax_query[] = ['taxonomy' => 'caso_ciudad', 'field' => 'slug', 'terms' => $filtros['ciudad']];
    }
    if (! empty($tax_query)) {
        $tax_query['relation'] = 'AND';
        $query->set('tax_query', $tax_query);
    }
}
add_action('pre_get_posts', 'ese_latam_casos_pre_get_posts');

/**
 * Datos de una tarjeta de caso (`.caso`) a partir de un post del CPT.
 *
 * @return array{title: string, tag: string, ciudad: string, img: string, href: string}
 */
function ese_latam_caso_card_data(int $post_id): array {
    $sectores = get_the_terms($post_id, 'caso_sector');
    $ciudades = get_the_terms($post_id, 'caso_ciudad');

    return [
        'title'  => get_the_title($post_id),
        'tag'    => is_array($sectores) && ! empty($sectores) ? $sectores[0]->name : '',
        'ciudad' => is_array($ciudades) && ! empty($ciudades) ? $ciudades[0]->name : '',
        'img'    => (string) (get_the_post_thumbnail_url($post_id, 'large') ?: ''),
        'href'   => (string) get_permalink($post_id),
    ];
}

/**
 * Últimos N casos publicados como tarjetas. Lista vacía si todavía no hay
 * ninguno: la sección que la pide decide no pintarse, igual que el resto de
 * los módulos. Lo usa "Casos reales" (template-parts/casos-reales.php).
 *
 * `$excluir` es para el single de caso, donde la franja es "Casos de éxito
 * relacionados" y no tiene sentido que el caso se recomiende a sí mismo.
 *
 * @param list<int> $excluir
 * @return list<array{title: string, tag: string, ciudad: string, img: string, href: string}>
 */
function ese_latam_casos_cards(int $cantidad = 4, array $excluir = []): array {
    $args = [
        'post_type'      => 'caso',
        'post_status'    => 'publish',
        'posts_per_page' => $cantidad,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ];

    $excluir = array_values(array_filter(array_map('intval', $excluir)));
    if ([] !== $excluir) {
        $args['post__not_in'] = $excluir;
    }

    $ids = get_posts($args);

    return array_map(static fn (int $id): array => ese_latam_caso_card_data($id), $ids);
}

/**
 * Marca una tarjeta de caso (`<li class="caso">`). Un solo marcado para el
 * archivo (variante `caso--blog`, con chip de ciudad) y "Casos reales".
 *
 * @param array{title: string, tag: string, ciudad?: string, img: string, href: string} $caso
 * @param array{class?: string, cta?: string, ciudad?: bool} $opts
 */
function ese_latam_caso_card(array $caso, array $opts = []): void {
    $opts = wp_parse_args($opts, [
        'class'  => '',
        'cta'    => __('Ver artículo', 'ese-latam'),
        'ciudad' => false,
    ]);
    $ciudad = isset($caso['ciudad']) ? (string) $caso['ciudad'] : '';
    ?>
    <li class="<?php echo esc_attr(trim('caso ' . $opts['class'])); ?>">
        <a class="caso__link" href="<?php echo esc_url($caso['href']); ?>">
            <?php if ('' !== $caso['img']) : ?>
                <img class="caso__img" src="<?php echo esc_url($caso['img']); ?>" alt="" loading="lazy" decoding="async">
            <?php endif; ?>
            <span class="caso__shade" aria-hidden="true"></span>
            <?php if ('' !== $caso['tag']) : ?>
                <span class="caso__tag"><?php echo esc_html($caso['tag']); ?></span>
            <?php endif; ?>
            <?php if ($opts['ciudad'] && '' !== $ciudad) : ?>
                <span class="caso__loc">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M6 3C5.62916 3 5.26665 3.10997 4.95831 3.31599C4.64996 3.52202 4.40964 3.81486 4.26773 4.15747C4.12581 4.50008 4.08868 4.87708 4.16103 5.24079C4.23337 5.60451 4.41195 5.9386 4.67417 6.20083C4.9364 6.46305 5.27049 6.64163 5.63421 6.71397C5.99792 6.78632 6.37492 6.74919 6.71753 6.60727C7.06014 6.46536 7.35298 6.22504 7.55901 5.91669C7.76503 5.60835 7.875 5.24584 7.875 4.875C7.875 4.37772 7.67746 3.90081 7.32583 3.54917C6.97419 3.19754 6.49728 3 6 3ZM6 6C5.7775 6 5.55999 5.93402 5.37498 5.8104C5.18998 5.68679 5.04578 5.51109 4.96064 5.30552C4.87549 5.09995 4.85321 4.87375 4.89662 4.65552C4.94002 4.43729 5.04717 4.23684 5.2045 4.0795C5.36184 3.92217 5.56229 3.81502 5.78052 3.77162C5.99875 3.72821 6.22495 3.75049 6.43052 3.83564C6.63609 3.92078 6.81179 4.06498 6.9354 4.24998C7.05902 4.43499 7.125 4.6525 7.125 4.875C7.125 5.17337 7.00647 5.45952 6.7955 5.6705C6.58452 5.88147 6.29837 6 6 6ZM6 0.75C4.90636 0.751241 3.85787 1.18624 3.08455 1.95955C2.31124 2.73287 1.87624 3.78136 1.875 4.875C1.875 6.34687 2.55516 7.90688 3.84375 9.38672C4.42276 10.0554 5.07443 10.6576 5.78672 11.182C5.84977 11.2262 5.92489 11.2499 6.00187 11.2499C6.07886 11.2499 6.15398 11.2262 6.21703 11.182C6.92801 10.6574 7.57841 10.0552 8.15625 9.38672C9.44297 7.90688 10.125 6.34687 10.125 4.875C10.1238 3.78136 9.68876 2.73287 8.91545 1.95955C8.14213 1.18624 7.09364 0.751241 6 0.75ZM6 10.4062C5.22516 9.79688 2.625 7.55859 2.625 4.875C2.625 3.97989 2.98058 3.12145 3.61351 2.48851C4.24645 1.85558 5.10489 1.5 6 1.5C6.89511 1.5 7.75355 1.85558 8.38649 2.48851C9.01942 3.12145 9.375 3.97989 9.375 4.875C9.375 7.55766 6.77484 9.79688 6 10.4062Z" fill="currentColor"/>
                    </svg>
                    <?php echo esc_html($ciudad); ?>
                </span>
            <?php endif; ?>
            <span class="caso__body">
                <span class="caso__title"><?php echo esc_html($caso['title']); ?></span>
                <span class="caso__cta">
                    <span class="caso__cta-text"><?php echo esc_html($opts['cta']); ?></span>
                    <span class="caso__cta-icon" aria-hidden="true">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.9216 7.00884L17.7878 15.0195C17.7836 15.2703 17.6799 15.5125 17.4996 15.6928C17.3193 15.8731 17.0771 15.9768 16.8263 15.981C16.5754 15.9851 16.3366 15.8895 16.1622 15.7151C15.9878 15.5408 15.8922 15.3019 15.8964 15.0511L15.9932 9.32123L7.67907 17.6354C7.49906 17.8154 7.25728 17.9188 7.0069 17.923C6.75652 17.9272 6.51805 17.8318 6.34397 17.6577C6.16988 17.4836 6.07443 17.2451 6.07861 16.9947C6.08279 16.7444 6.18627 16.5026 6.36627 16.3226L14.6804 8.00843L8.95007 8.10251C8.69926 8.1067 8.46038 8.01108 8.28599 7.83669C8.1116 7.66231 8.01598 7.42343 8.02017 7.17261C8.02436 6.9218 8.12802 6.67959 8.30834 6.49928C8.48865 6.31896 8.73086 6.21531 8.98167 6.21111L16.9923 6.07727C17.1166 6.07505 17.2394 6.09741 17.3535 6.14308C17.4675 6.18874 17.5707 6.25681 17.6571 6.34337C17.7434 6.42994 17.8113 6.53328 17.8566 6.64749C17.902 6.76169 17.9241 6.88449 17.9216 7.00884Z" fill="currentColor"/></svg>
                    </span>
                </span>
            </span>
        </a>
    </li>
    <?php
}
