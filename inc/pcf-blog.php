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
            $campo('text', 'blog_hero_kicker', __('Antetítulo sobre el titular del hero', 'ese-latam'), [
                'instructions' => __('Palabra corta en mayúsculas, centrada encima del titular. Hasta 30 caracteres. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Blog', 'ese-latam'),
                'wrapper'      => ['width' => '40'],
            ]),
            $campo('textarea', 'blog_hero_titulo', __('Titular principal del hero', 'ese-latam'), [
                'instructions' => __('Frase grande en mayúsculas, centrada en el hero oscuro sobre el listado de artículos.', 'ese-latam')
                    . ' ' . ese_latam_ayuda_titulo()
                    . ' ' . __('Vacío: no se muestra.', 'ese-latam'),
                'rows'         => 2,
                'placeholder'  => "Impacto y\n|sostenibilidad|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('textarea', 'blog_hero_desc', __('Bajada bajo el titular del hero', 'ese-latam'), [
                'instructions' => __('Párrafo corto centrado bajo el titular. Una o dos frases, hasta 200 caracteres. Vacía: no se muestra.', 'ese-latam'),
                'rows'         => 2,
            ]),
            $campo('image', 'blog_hero_imagen', __('Foto de fondo del hero', 'ese-latam'), [
                'instructions'  => __('Opcional. Ocupa todo el ancho detrás del titular. WebP o JPG · 1920×720 px (8:3) · máx. 300 KB. Se muestra oscurecida y se recorta al centro. Vacía: el hero queda en negro liso, como en el diseño.', 'ese-latam'),
                'return_format' => 'url',
                'preview_size'  => 'medium',
                'wrapper'       => ['width' => '50'],
            ]),

            /* ---------------- Filtros ---------------- */
            $tab('blog_filtros', __('Filtros', 'ese-latam')),
            $campo('message', '', __('De dónde salen las opciones de los filtros', 'ese-latam'), [
                'key'      => 'field_blogfil_msg',
                'message'  => __('Las opciones de los dos desplegables son las <strong>categorías</strong> y las <strong>etiquetas</strong> de las entradas. Acá solo se escriben los rótulos.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('text', 'blog_buscador_label', __('Buscador — rótulo sobre la caja', 'ese-latam'), [
                'instructions' => __('Palabra sobre la caja de búsqueda, primera de la fila de filtros bajo el hero. Hasta 30 caracteres. Vacío: la caja va sin rótulo.', 'ese-latam'),
                'placeholder'  => __('Buscador', 'ese-latam'),
                'wrapper'      => ['width' => '34'],
            ]),
            $campo('text', 'blog_buscador_placeholder', __('Buscador — texto de ejemplo en la caja', 'ese-latam'), [
                'instructions' => __('Texto gris que se ve dentro de la caja de búsqueda antes de escribir, como “Busca artículos, tags o palabras clave”. Hasta 50 caracteres. Vacío: la caja se ve en blanco.', 'ese-latam'),
                'placeholder'  => __('Busca artículos, tags o palabras clave', 'ese-latam'),
                'wrapper'      => ['width' => '66'],
            ]),
            $campo('text', 'blog_categorias_label', __('Filtro de categorías — rótulo', 'ese-latam'), [
                'instructions' => __('Palabra sobre el desplegable de categorías, en medio de la fila de filtros. Hasta 30 caracteres. Vacío: el desplegable va sin rótulo.', 'ese-latam'),
                'placeholder'  => __('Categorías', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('text', 'blog_categorias_todas', __('Filtro de categorías — opción «todas»', 'ese-latam'), [
                'instructions' => __('Primera opción del desplegable, la que muestra todos los artículos, como “Todas las categorías”. Hasta 30 caracteres. Vacío: esa opción queda en blanco.', 'ese-latam'),
                'placeholder'  => __('Todas las categorías', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),

            /* ---------------- Filtros — etiquetas ---------------- */
            $tab('blog_filtros_etiquetas', __('Filtros — etiquetas', 'ese-latam')),
            $campo('text', 'blog_etiquetas_label', __('Filtro de etiquetas — rótulo', 'ese-latam'), [
                'instructions' => __('Frase sobre el desplegable de etiquetas, el último de la fila de filtros. Hasta 30 caracteres. Vacío: el desplegable va sin rótulo.', 'ese-latam'),
                'placeholder'  => __('Filtrar por etiqueta', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('text', 'blog_etiquetas_todas', __('Filtro de etiquetas — opción «todas»', 'ese-latam'), [
                'instructions' => __('Primera opción del desplegable de etiquetas, la que muestra todos los artículos, como “Todos”. Hasta 30 caracteres. Vacío: esa opción queda en blanco.', 'ese-latam'),
                'placeholder'  => __('Todos', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),

            /* ---------------- Listado ---------------- */
            $tab('blog_listado', __('Listado', 'ese-latam')),
            $campo('number', 'blog_por_pagina', __('Artículos por página del listado', 'ese-latam'), [
                'instructions' => __('Cuántas tarjetas se ven antes de pasar a la página siguiente. El diseño son dos filas de cuatro. Entre 1 y 48. Vacío: 8.', 'ese-latam'),
                'min'          => 1,
                'max'          => 48,
                'wrapper'      => ['width' => '25'],
            ]),
            $campo('text', 'blog_conteo_ini', __('Contador de resultados — texto antes de la cifra', 'ese-latam'), [
                'instructions' => __('Palabra antes del número de artículos encontrados, bajo los filtros, como “Mostrando”. La frase se parte en dos para resaltar el número. Vacío: el contador empieza por el número. Sin textos del contador ni enlace de restablecer, la fila no se muestra.', 'ese-latam'),
                'placeholder'  => __('Mostrando', 'ese-latam'),
                'wrapper'      => ['width' => '37'],
            ]),
            $campo('text', 'blog_conteo_fin', __('Contador de resultados — texto después de la cifra', 'ese-latam'), [
                'instructions' => __('Palabra después del número de artículos, como “artículos”. Vacío: el contador termina en el número.', 'ese-latam'),
                'placeholder'  => __('artículos', 'ese-latam'),
                'wrapper'      => ['width' => '38'],
            ]),
            $campo('text', 'blog_reset', __('Enlace «restablecer filtros»', 'ese-latam'), [
                'instructions' => __('Texto del enlace a la derecha del contador, que borra la búsqueda y los filtros. Hasta 30 caracteres. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Restablecer filtros', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('text', 'blog_card_cta', __('Texto del enlace de cada tarjeta', 'ese-latam'), [
                'instructions' => __('Texto con flecha al pie de cada tarjeta de artículo, como “Ver artículo”. Hasta 20 caracteres. Vacío: la tarjeta muestra solo la flecha.', 'ese-latam'),
                'placeholder'  => __('Ver artículo', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),

            /* ---------------- Listado — sin resultados y paginación ---------------- */
            $tab('blog_listado_paginacion', __('Listado — sin resultados y paginación', 'ese-latam')),
            $campo('textarea', 'blog_vacio', __('Aviso cuando no hay resultados', 'ese-latam'), [
                'instructions' => __('Mensaje centrado en lugar de las tarjetas cuando la búsqueda o los filtros no encuentran artículos. Una o dos frases. Vacío: el espacio queda en blanco.', 'ese-latam'),
                'rows'        => 2,
                'placeholder' => __('No encontramos artículos con estos filtros. Prueba con otra búsqueda o restablece los filtros.', 'ese-latam'),
            ]),
            $campo('text', 'blog_pag_anterior', __('Paginación — botón «anterior»', 'ese-latam'), [
                'instructions' => __('Botón a la izquierda de los números de página, bajo las tarjetas. Solo aparece si hay más de una página. Hasta 15 caracteres. Vacío: no se muestra y quedan solo los números.', 'ese-latam'),
                'placeholder'  => __('Anterior', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('text', 'blog_pag_siguiente', __('Paginación — botón «siguiente»', 'ese-latam'), [
                'instructions' => __('Botón a la derecha de los números de página. Solo aparece si hay más de una página. Hasta 15 caracteres. Vacío: no se muestra y quedan solo los números.', 'ese-latam'),
                'placeholder'  => __('Siguiente', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),

            /* ---------------- Artículo ---------------- */
            $tab('blog_articulo', __('Artículo', 'ese-latam')),
            $campo('message', '', __('Dónde se editan los datos de cada artículo', 'ese-latam'), [
                'key'      => 'field_blogart_msg',
                'message'  => __('Se repiten en <strong>todos</strong> los artículos. Lo que cambia de uno a otro —la entradilla, el lugar y el año— se edita en cada entrada.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('text', 'blog_art_categoria_label', __('Artículo — rótulo de la categoría', 'ese-latam'), [
                'instructions' => __('Rótulo sobre la categoría, en la columna lateral de cada artículo, como “Categoría del artículo:”. Hasta 30 caracteres. Vacío: no se muestra el bloque de categoría.', 'ese-latam'),
                'placeholder'  => __('Categoría del artículo:', 'ese-latam'),
                'wrapper'      => ['width' => '34'],
            ]),
            $campo('text', 'blog_art_etiquetas_label', __('Artículo — rótulo de las etiquetas', 'ese-latam'), [
                'instructions' => __('Rótulo sobre las etiquetas, en la columna lateral de cada artículo. Hasta 30 caracteres. Vacío: no se muestra el bloque de etiquetas.', 'ese-latam'),
                'placeholder'  => __('Etiquetas:', 'ese-latam'),
                'wrapper'      => ['width' => '33'],
            ]),
            $campo('text', 'blog_art_compartir_label', __('Artículo — rótulo de compartir', 'ese-latam'), [
                'instructions' => __('Rótulo sobre los botones para compartir en LinkedIn, Facebook, WhatsApp y copiar el enlace, en la columna lateral. Hasta 30 caracteres. Vacío: no se muestran los botones de compartir. Sin ninguno de los tres rótulos, la columna lateral no se muestra.', 'ese-latam'),
                'placeholder'  => __('Comparte este artículo:', 'ese-latam'),
                'wrapper'      => ['width' => '33'],
            ]),
            $campo('text', 'blog_art_copiado', __('Artículo — aviso al copiar el enlace', 'ese-latam'), [
                'instructions' => __('Mensaje breve bajo los botones de compartir, cuando alguien copia el enlace del artículo. Hasta 40 caracteres. Vacío: no se muestra ningún aviso.', 'ese-latam'),
                'placeholder'  => __('Enlace copiado al portapapeles', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('text', 'blog_art_volver', __('Artículo — enlace de vuelta al blog', 'ese-latam'), [
                'instructions' => __('Texto del enlace con flecha al final de cada artículo, que lleva al listado del blog. Hasta 30 caracteres. Vacío: no se muestra.', 'ese-latam'),
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
            $campo('textarea', 'articulo_entradilla', __('Entradilla al inicio del artículo', 'ese-latam'), [
                'instructions' => __('El párrafo en cursiva que abre el cuerpo del artículo, antes del texto principal. Dos o tres frases. Vacío: no se muestra.', 'ese-latam'),
                'rows'         => 3,
            ]),
            $campo('text', 'articulo_lugar', __('Lugar del proyecto', 'ese-latam'), [
                'instructions' => __('Ciudad y país, en la línea al pie del hero junto a la categoría y el año. Hasta 40 caracteres. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Quito, Ecuador', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('number', 'articulo_anio', __('Año del proyecto', 'ese-latam'), [
                'instructions' => __('Se muestra en la línea al pie del hero, junto al lugar. Llénalo solo si el año del proyecto no es el de publicación. Vacío: el año en que se publicó.', 'ese-latam'),
                'min'          => 1900,
                'max'          => 2200,
                'wrapper'      => ['width' => '50'],
            ]),
        ],
    ]);
});
