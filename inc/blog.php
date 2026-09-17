<?php
/**
 * Blog — el índice editorial del sitio (Figma 3848-9017, "09 – Blog:
 * Listado"). Son las ENTRADAS nativas de WordPress, no un módulo nuevo: el
 * Figma filtra por "Categorías" y "Etiqueta", que es exactamente lo que
 * `category` y `post_tag` ya hacen, y así el cliente publica desde el sitio
 * de siempre sin aprender otra pantalla.
 *
 * El listado vive en la página "Blog" fijada en Ajustes → Lectura
 * (ese_latam_asegurar_blog(), inc/paginas.php), así que lo pinta home.php.
 *
 * Acá van los filtros del listado (?q=, ?categoria=, ?etiqueta=) y el
 * armado de cada tarjeta. La tarjeta en sí es la misma `.caso` del blog de
 * Casos de éxito (ese_latam_caso_card(), inc/cpt-casos.php): en el Figma es
 * literalmente el mismo componente.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * URL del índice del blog. Cae a la home si todavía no hay página fijada.
 */
function ese_latam_blog_url(): string {
    $id = (int) get_option('page_for_posts');
    if ($id > 0) {
        $url = get_permalink($id);
        if (is_string($url) && '' !== $url) {
            return $url;
        }
    }

    return home_url('/');
}

/**
 * Filtros activos del listado, ya saneados.
 *
 * Ninguno se llama como su concepto, y es a propósito: `s` haría que
 * WordPress tratara la petición como búsqueda global, y `category_name` y
 * `tag` la convertirían en un archivo de término —otra plantilla, sin los
 * filtros—. Con nombres propios el índice sigue siendo el índice.
 *
 * @return array{q: string, categoria: string, etiqueta: string}
 */
function ese_latam_blog_filtros(): array {
    return [
        'q'         => isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '',
        'categoria' => isset($_GET['categoria']) ? sanitize_title(wp_unslash($_GET['categoria'])) : '',
        'etiqueta'  => isset($_GET['etiqueta']) ? sanitize_title(wp_unslash($_GET['etiqueta'])) : '',
    ];
}

/**
 * Aplica los filtros a la query principal del índice. Por página salen las
 * que diga el campo del listado (8 por defecto: las dos filas de cuatro del
 * Figma), así la paginación nativa (/blog/page/2/) sigue funcionando.
 */
function ese_latam_blog_pre_get_posts(WP_Query $query): void {
    if (is_admin() || ! $query->is_main_query() || ! $query->is_home()) {
        return;
    }

    $filtros = ese_latam_blog_filtros();

    $por_pagina = (int) ese_latam_campo('blog_por_pagina', (int) get_option('page_for_posts'), 0);
    $query->set('posts_per_page', $por_pagina > 0 ? $por_pagina : 8);

    if ('' !== $filtros['q']) {
        $query->set('s', $filtros['q']);
    }

    $tax_query = [];
    if ('' !== $filtros['categoria']) {
        $tax_query[] = ['taxonomy' => 'category', 'field' => 'slug', 'terms' => $filtros['categoria']];
    }
    if ('' !== $filtros['etiqueta']) {
        $tax_query[] = ['taxonomy' => 'post_tag', 'field' => 'slug', 'terms' => $filtros['etiqueta']];
    }
    if (! empty($tax_query)) {
        $tax_query['relation'] = 'AND';
        $query->set('tax_query', $tax_query);
    }
}
add_action('pre_get_posts', 'ese_latam_blog_pre_get_posts');

/**
 * Datos de una tarjeta del listado a partir de una entrada. Mismo formato
 * que ese_latam_caso_card_data(), porque la tarjeta es la misma: el chip
 * lleva la primera categoría y no hay pin de ciudad.
 *
 * @return array{title: string, tag: string, ciudad: string, img: string, href: string}
 */
function ese_latam_blog_card_data(int $post_id): array {
    $categorias = get_the_category($post_id);

    return [
        'title'  => get_the_title($post_id),
        'tag'    => is_array($categorias) && ! empty($categorias) ? $categorias[0]->name : '',
        'ciudad' => '',
        'img'    => (string) (get_the_post_thumbnail_url($post_id, 'large') ?: ''),
        'href'   => (string) get_permalink($post_id),
    ];
}
