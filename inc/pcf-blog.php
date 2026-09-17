<?php
/**
 * Campos de la página "Blog" (Figma 3848-9017, "09 – Blog: Listado").
 *
 * Los artículos NO se escriben acá: son las entradas nativas de WordPress,
 * con sus categorías y etiquetas. Esta página solo aporta el copy que las
 * envuelve —el hero, los rótulos de los filtros, el contador, la paginación
 * y los rótulos que se repiten en TODOS los artículos— y cuántas se muestran
 * por página.
 *
 * Lo propio de cada artículo (entradilla, lugar, año) vive en la entrada:
 * es el grupo `group_articulo` del final de este archivo.
 *
 * Se cuelgan de la página fijada como página de entradas (Ajustes →
 * Lectura), que crea inc/paginas.php.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

add_action('acf/init', static function (): void {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    $pagina = get_page_by_path('blog');
    if (! $pagina instanceof WP_Post) {
        return;
    }

    $campo = 'ese_latam_campo_def';
    $tab   = 'ese_latam_campo_tab';

    acf_add_local_field_group([
        'key'      => 'group_pagina_blog',
        'title'    => __('Contenido de la página', 'ese-latam'),
        'location' => [[['param' => 'page', 'operator' => '==', 'value' => (string) $pagina->ID]]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'fields'                => [

            /* ---------------- Hero ---------------- */
            $tab('blog_hero', __('Hero', 'ese-latam')),
            $campo('text', 'blog_hero_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('Blog', 'ese-latam'),
                'wrapper'     => ['width' => '40'],
            ]),
            $campo('textarea', 'blog_hero_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => "Impacto y\n|sostenibilidad|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('textarea', 'blog_hero_desc', __('Bajada', 'ese-latam'), ['rows' => 2]),
            $campo('image', 'blog_hero_imagen', __('Foto de fondo', 'ese-latam'), [
                'instructions'  => __('Opcional. Vacía: el hero queda en negro liso, como en el diseño.', 'ese-latam'),
                'return_format' => 'url',
                'preview_size'  => 'medium',
                'wrapper'       => ['width' => '50'],
            ]),

            /* ---------------- Filtros ---------------- */
            $tab('blog_filtros', __('Filtros', 'ese-latam')),
            $campo('message', '', __('De dónde salen las opciones', 'ese-latam'), [
                'key'      => 'field_blogfil_msg',
                'message'  => __('Las opciones de los dos desplegables son las <strong>categorías</strong> y las <strong>etiquetas</strong> de las entradas. Acá solo se escriben los rótulos.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('text', 'blog_buscador_label', __('Buscador — rótulo', 'ese-latam'), [
                'placeholder' => __('Buscador', 'ese-latam'),
                'wrapper'     => ['width' => '34'],
            ]),
            $campo('text', 'blog_buscador_placeholder', __('Buscador — texto de ayuda', 'ese-latam'), [
                'placeholder' => __('Busca artículos, tags o palabras clave', 'ese-latam'),
                'wrapper'     => ['width' => '66'],
            ]),
            $campo('text', 'blog_categorias_label', __('Categorías — rótulo', 'ese-latam'), [
                'placeholder' => __('Categorías', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('text', 'blog_categorias_todas', __('Categorías — opción "todas"', 'ese-latam'), [
                'placeholder' => __('Todas las categorías', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('text', 'blog_etiquetas_label', __('Etiquetas — rótulo', 'ese-latam'), [
                'placeholder' => __('Filtrar por etiqueta', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('text', 'blog_etiquetas_todas', __('Etiquetas — opción "todas"', 'ese-latam'), [
                'placeholder' => __('Todos', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),

            /* ---------------- Listado ---------------- */
            $tab('blog_listado', __('Listado', 'ese-latam')),
            $campo('number', 'blog_por_pagina', __('Artículos por página', 'ese-latam'), [
                'instructions' => __('El diseño son dos filas de cuatro. Vacío: 8.', 'ese-latam'),
                'min'          => 1,
                'max'          => 48,
                'wrapper'      => ['width' => '25'],
            ]),
            $campo('text', 'blog_conteo_ini', __('Contador — antes de la cifra', 'ese-latam'), [
                'instructions' => __('La frase se parte en dos para poder resaltar el número.', 'ese-latam'),
                'placeholder'  => __('Mostrando', 'ese-latam'),
                'wrapper'      => ['width' => '37'],
            ]),
            $campo('text', 'blog_conteo_fin', __('Contador — después de la cifra', 'ese-latam'), [
                'placeholder' => __('artículos', 'ese-latam'),
                'wrapper'     => ['width' => '38'],
            ]),
            $campo('text', 'blog_reset', __('Enlace "restablecer filtros"', 'ese-latam'), [
                'instructions' => __('Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Restablecer filtros', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('text', 'blog_card_cta', __('Texto del enlace de cada tarjeta', 'ese-latam'), [
                'placeholder' => __('Ver artículo', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('textarea', 'blog_vacio', __('Aviso cuando no hay resultados', 'ese-latam'), [
                'rows'        => 2,
                'placeholder' => __('No encontramos artículos con estos filtros. Prueba con otra búsqueda o restablece los filtros.', 'ese-latam'),
            ]),
            $campo('text', 'blog_pag_anterior', __('Paginación — anterior', 'ese-latam'), [
                'placeholder' => __('Anterior', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('text', 'blog_pag_siguiente', __('Paginación — siguiente', 'ese-latam'), [
                'placeholder' => __('Siguiente', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),

            /* ---------------- Artículo ---------------- */
            $tab('blog_articulo', __('Artículo', 'ese-latam')),
            $campo('message', '', __('Los rótulos de la ficha', 'ese-latam'), [
                'key'      => 'field_blogart_msg',
                'message'  => __('Se repiten en <strong>todos</strong> los artículos. Lo que cambia de uno a otro —la entradilla, el lugar y el año— se edita en cada entrada.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('text', 'blog_art_categoria_label', __('Rótulo de la categoría', 'ese-latam'), [
                'instructions' => __('Vacío: no se muestra el bloque de categoría.', 'ese-latam'),
                'placeholder'  => __('Categoría del artículo:', 'ese-latam'),
                'wrapper'      => ['width' => '34'],
            ]),
            $campo('text', 'blog_art_etiquetas_label', __('Rótulo de las etiquetas', 'ese-latam'), [
                'instructions' => __('Vacío: no se muestra el bloque de etiquetas.', 'ese-latam'),
                'placeholder'  => __('Etiquetas:', 'ese-latam'),
                'wrapper'      => ['width' => '33'],
            ]),
            $campo('text', 'blog_art_compartir_label', __('Rótulo de compartir', 'ese-latam'), [
                'instructions' => __('Vacío: no se muestran los botones de compartir.', 'ese-latam'),
                'placeholder'  => __('Comparte este artículo:', 'ese-latam'),
                'wrapper'      => ['width' => '33'],
            ]),
            $campo('text', 'blog_art_copiado', __('Aviso al copiar el enlace', 'ese-latam'), [
                'placeholder' => __('Enlace copiado al portapapeles', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('text', 'blog_art_volver', __('Enlace de vuelta al blog', 'ese-latam'), [
                'instructions' => __('Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Volver al blog', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
        ],
    ]);

    /* -----------------------------------------------------------------
     * Ficha de cada artículo
     *
     * Solo lo que el marcado necesita y WordPress no da: el resto —titular,
     * bajada, cuerpo, foto, categorías y etiquetas— son los campos nativos
     * de la entrada.
     * -------------------------------------------------------------- */
    acf_add_local_field_group([
        'key'      => 'group_articulo',
        'title'    => __('Ficha del artículo', 'ese-latam'),
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'post']]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => __('El titular, la bajada (extracto), el cuerpo, la foto, la categoría y las etiquetas son los campos de siempre de la entrada.', 'ese-latam'),
        'fields'                => [
            $campo('textarea', 'articulo_entradilla', __('Entradilla', 'ese-latam'), [
                'instructions' => __('El párrafo en cursiva que abre el artículo. Vacío: no se muestra.', 'ese-latam'),
                'rows'         => 3,
            ]),
            $campo('text', 'articulo_lugar', __('Lugar', 'ese-latam'), [
                'instructions' => __('Se muestra bajo el titular, junto al año. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Quito, Ecuador', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('number', 'articulo_anio', __('Año', 'ese-latam'), [
                'instructions' => __('Solo si el año del proyecto no es el de publicación. Vacío: el año en que se publicó.', 'ese-latam'),
                'min'          => 1900,
                'max'          => 2200,
                'wrapper'      => ['width' => '50'],
            ]),
        ],
    ]);
});
