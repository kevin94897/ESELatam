<?php
/**
 * Campos del CPT "producto": la ficha completa (litrajes, colores,
 * características, certificados) registrada por PHP, como el resto del theme.
 *
 * Sustituye a inc/acf-productos.php. Tres diferencias con aquella versión:
 *
 *   1. **Validaciones.** Lo que la ficha necesita para verse bien va marcado
 *      como obligatorio, los repetidores tienen mínimos y máximos, y los
 *      archivos limitan su tipo. Antes se podía guardar un producto sin
 *      litrajes y la tarjeta salía con un guion.
 *   2. **Relaciones en vez de texto suelto.** Los sellos y los sectores ya no
 *      se escriben a mano: se eligen de sus módulos, así que el nombre y el
 *      logo salen siempre iguales y se pueden recorrer en los dos sentidos.
 *   3. **Agrupación por pestañas** con los nombres del Figma ("1. Litraje",
 *      "2. Selección de color"), para que el cliente reconozca qué controla
 *      cada campo en el frontend.
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

    $campo = 'ese_latam_campo_def';
    $tab   = 'ese_latam_campo_tab';

    acf_add_local_field_group([
        'key'      => 'group_producto_ficha',
        'title'    => __('Ficha del producto', 'ese-latam'),
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'producto']]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => __('El nombre del producto y su foto principal salen del título y de la imagen destacada.', 'ese-latam'),
        'fields'                => [

            /* ---------------- General ---------------- */
            $tab('prod_general', __('General', 'ese-latam')),
            $campo('textarea', 'descripcion_corta', __('Descripción corta', 'ese-latam'), [
                'instructions' => __('El párrafo bajo el título en la ficha. Entre 80 y 320 caracteres.', 'ese-latam'),
                'rows'         => 3,
                'required'     => 1,
                'maxlength'    => 320,
            ]),
            $campo('link', 'enlace_compra', __('Botón “Comprar”', 'ese-latam'), [
                'instructions' => __('Texto y destino del botón principal de la ficha. Vacío: “Comprar” hacia la página de Contacto.', 'ese-latam'),
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('file', 'ficha_tecnica', __('Ficha técnica (PDF)', 'ese-latam'), [
                'instructions'  => __('Solo PDF. Sin archivo, el botón de descarga no se muestra.', 'ese-latam'),
                'return_format' => 'url',
                'mime_types'    => 'pdf',
                'wrapper'       => ['width' => '40'],
            ]),

            /* ---------------- 1. Modelos ---------------- */
            $tab('prod_modelos', __('1. Modelos', 'ese-latam')),
            $campo('message', '', __('Cómo se usan', 'ese-latam'), [
                'key'      => 'field_prodmodelos_msg',
                'message'  => __('Cada producto del catálogo agrupa a los <strong>modelos</strong> de su familia: “Papeleras” contiene Open Dinova, Campus Goool, Venta… En la ficha aparecen como una primera lista de opciones, y al elegir uno se muestran solo sus capacidades, sus colores y sus fotos. Con un único modelo la lista no se dibuja y la ficha se ve como antes.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('repeater', 'modelos', __('Modelos de la familia', 'ese-latam'), [
                'instructions' => __('Un renglón por modelo. El orden es el de la lista en la ficha; el primero es el que se muestra al abrir.', 'ese-latam'),
                'layout'       => 'table',
                'min'          => 0,
                'max'          => 24,
                'button_label' => __('Añadir modelo', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'nombre', __('Nombre', 'ese-latam'), [
                        'key'       => 'field_modelo_nombre',
                        'required'  => 1,
                        'maxlength' => 60,
                        'wrapper'   => ['width' => '35'],
                    ]),
                    $campo('text', 'descripcion', __('Descripción corta', 'ese-latam'), [
                        'key'          => 'field_modelo_desc',
                        'instructions' => __('Opcional: una línea que reemplaza al texto del producto cuando se elige este modelo.', 'ese-latam'),
                        'maxlength'    => 320,
                        'wrapper'      => ['width' => '65'],
                    ]),
                ],
            ]),

            /* ---------------- 2. Litraje ---------------- */
            $tab('prod_litraje', __('2. Litraje', 'ese-latam')),
            $campo('repeater', 'litrajes', __('Opciones de litraje', 'ese-latam'), [
                'instructions' => __('Las pastillas de capacidad del hero. Marca una como predeterminada: es la que se muestra al abrir la ficha y la que sale en la tarjeta del catálogo.', 'ese-latam'),
                'layout'       => 'table',
                'min'          => 1,
                'max'          => 12,
                'required'     => 1,
                'button_label' => __('Añadir litraje', 'ese-latam'),
                'sub_fields'   => [
                    $campo('select', 'modelo', __('Modelo', 'ese-latam'), [
                        'key'          => 'field_litraje_modelo',
                        'instructions' => __('A qué modelo pertenece. Vacío: vale para todos.', 'ese-latam'),
                        'choices'      => [],
                        'allow_null'   => 1,
                        'wrapper'      => ['width' => '30'],
                    ]),
                    $campo('text', 'valor', __('Litraje', 'ese-latam'), [
                        'key'         => 'field_litraje_valor',
                        'instructions' => __('Como se lee en la pastilla: 120L.', 'ese-latam'),
                        'required'    => 1,
                        'maxlength'   => 12,
                    ]),
                    $campo('true_false', 'predeterminado', __('Por defecto', 'ese-latam'), [
                        'key' => 'field_litraje_default',
                        'ui'  => 1,
                    ]),
                ],
            ]),

            /* ---------------- 3. Colores ---------------- */
            $tab('prod_colores', __('3. Selección de color', 'ese-latam')),
            $campo('repeater', 'colores', __('Selección de color', 'ese-latam'), [
                'instructions' => __('Cada color es un círculo bajo las pastillas de litraje. La foto cambia al elegirlo.', 'ese-latam'),
                'layout'       => 'block',
                'min'          => 0,
                'max'          => 12,
                'button_label' => __('Añadir color', 'ese-latam'),
                'sub_fields'   => [
                    $campo('select', 'modelo', __('Modelo', 'ese-latam'), [
                        'key'          => 'field_color_modelo',
                        'instructions' => __('A qué modelo pertenece. Vacío: vale para todos.', 'ese-latam'),
                        'choices'      => [],
                        'allow_null'   => 1,
                        'wrapper'      => ['width' => '30'],
                    ]),
                    $campo('text', 'nombre', __('Nombre', 'ese-latam'), [
                        'key'          => 'field_color_nombre',
                        'instructions' => __('Como se lee en la ficha al elegir el color: Verde, Gris antracita…', 'ese-latam'),
                        'required'     => 1,
                        'maxlength'    => 40,
                        'wrapper'      => ['width' => '50'],
                    ]),
                    $campo('color_picker', 'color', __('Color', 'ese-latam'), [
                        'key'      => 'field_color_hex',
                        'required' => 1,
                        'wrapper'  => ['width' => '50'],
                    ]),
                    $campo('image', 'imagen', __('Foto por defecto', 'ese-latam'), [
                        'key'           => 'field_color_img',
                        'instructions'  => __('La que se usa cuando una capacidad no tiene foto propia en la pestaña “3. Fotos”. PNG con fondo transparente.', 'ese-latam'),
                        'return_format' => 'url',
                        'preview_size'  => 'medium',
                        'mime_types'    => 'png,webp',
                    ]),
                ],
            ]),

            /* ---------------- 4. Fotos ---------------- */
            $tab('prod_fotos', __('4. Fotos', 'ese-latam')),
            $campo('message', '', __('Cómo se usan', 'ese-latam'), [
                'key'      => 'field_prodfotos_msg',
                'message'  => __('Una fila por foto. Al elegir un color y una capacidad en la ficha se busca aquí la combinación exacta; si no existe, se usa la <strong>foto por defecto</strong> de ese color. Los dos desplegables se llenan solos con lo que hayas cargado en las pestañas anteriores: <strong>guarda el producto</strong> después de añadir un color o un litraje para verlos aquí.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('repeater', 'fotos', __('Fotos por color y capacidad', 'ese-latam'), [
                'layout'       => 'table',
                'button_label' => __('Añadir foto', 'ese-latam'),
                'sub_fields'   => [
                    $campo('select', 'modelo', __('Modelo', 'ese-latam'), [
                        'key'          => 'field_foto_modelo',
                        'instructions' => __('A qué modelo pertenece. Vacío: vale para todos.', 'ese-latam'),
                        'choices'      => [],
                        'allow_null'   => 1,
                        'wrapper'      => ['width' => '25'],
                    ]),
                    $campo('select', 'color', __('Color', 'ese-latam'), [
                        'key'      => 'field_foto_color',
                        'required' => 1,
                        'choices'  => [],
                    ]),
                    $campo('select', 'litraje', __('Capacidad', 'ese-latam'), [
                        'key'      => 'field_foto_litraje',
                        'required' => 1,
                        'choices'  => [],
                    ]),
                    $campo('image', 'imagen', __('Foto', 'ese-latam'), [
                        'key'           => 'field_foto_img',
                        'required'      => 1,
                        'return_format' => 'url',
                        'preview_size'  => 'thumbnail',
                        'mime_types'    => 'png,webp',
                    ]),
                ],
            ]),

            /* ---------------- Características ---------------- */
            $tab('prod_caracteristicas', __('Características', 'ese-latam')),
            $campo('repeater', 'caracteristicas', __('Características principales', 'ese-latam'), [
                'instructions' => __('La barra de atributos bajo el producto. Una fila llamada “Material” alimenta además la tarjeta del catálogo.', 'ese-latam'),
                'layout'       => 'table',
                'min'          => 0,
                'max'          => 8,
                'button_label' => __('Añadir característica', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'etiqueta', __('Etiqueta', 'ese-latam'), [
                        'key'       => 'field_caract_etiqueta',
                        'required'  => 1,
                        'maxlength' => 30,
                    ]),
                    $campo('text', 'valor', __('Valor', 'ese-latam'), [
                        'key'       => 'field_caract_valor',
                        'required'  => 1,
                        'maxlength' => 60,
                    ]),
                ],
            ]),

            /* ---------------- Relaciones ---------------- */
            $tab('prod_relaciones', __('Relaciones', 'ese-latam')),
            $campo('message', '', __('Cómo funcionan', 'ese-latam'), [
                'key'      => 'field_prodrel_msg',
                'message'  => __('Los sellos y los sectores no se escriben acá: se eligen de sus módulos. Así el logo y el texto de cada sello salen siempre iguales, y cambiar el nombre de un sector lo actualiza en todas las fichas a la vez.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('relationship', 'certificaciones', __('Certificaciones', 'ese-latam'), [
                'instructions'  => __('Los sellos que aparecen en el hero de la ficha, en el orden en que se elijan.', 'ese-latam'),
                'post_type'     => ['certificacion'],
                'filters'       => ['search'],
                'return_format' => 'id',
                'max'           => 8,
            ]),
            $campo('relationship', 'sectores', __('Sectores donde se usa', 'ese-latam'), [
                'instructions'  => __('Opcional: para qué sectores está pensado este producto.', 'ese-latam'),
                'post_type'     => ['sector'],
                'filters'       => ['search'],
                'return_format' => 'id',
            ]),
        ],
    ]);
});

/**
 * Llena los dos desplegables de la pestaña "3. Fotos" con los colores y los
 * litrajes que tenga cargados ESE producto.
 *
 * Los subcampos de un repetidor pasan por el mismo filtro que el resto, así
 * que basta con reconocerlos por su key. Si el producto todavía no tiene
 * colores o litrajes guardados, el desplegable queda vacío y el aviso de la
 * pestaña explica que hay que guardar primero.
 */
add_filter('pcf/prepare_field', static function ($field) {
    if (! is_array($field) || ! isset($field['key'])) {
        return $field;
    }
    $modelos = ['field_litraje_modelo', 'field_color_modelo', 'field_foto_modelo'];
    $otros   = ['field_foto_color', 'field_foto_litraje'];

    if (! in_array($field['key'], $modelos, true) && ! in_array($field['key'], $otros, true)) {
        return $field;
    }

    $post_id = (int) get_the_ID();
    if ($post_id <= 0 || 'producto' !== get_post_type($post_id)) {
        return $field;
    }

    $opciones = [];

    if (in_array($field['key'], $modelos, true)) {
        foreach ((array) ese_latam_campo('modelos', $post_id, []) as $modelo) {
            $nombre = trim((string) ($modelo['nombre'] ?? ''));
            if ('' !== $nombre) {
                $opciones[$nombre] = $nombre;
            }
        }
    } elseif ('field_foto_color' === $field['key']) {
        foreach ((array) ese_latam_campo('colores', $post_id, []) as $color) {
            $nombre = trim((string) ($color['nombre'] ?? ''));
            if ('' !== $nombre) {
                $opciones[$nombre] = $nombre;
            }
        }
    } else {
        foreach ((array) ese_latam_campo('litrajes', $post_id, []) as $litraje) {
            $valor = trim((string) ($litraje['valor'] ?? ''));
            if ('' !== $valor) {
                $opciones[$valor] = $valor;
            }
        }
    }

    $field['choices'] = $opciones;

    return $field;
});
