<?php
/**
 * Campos ACF PRO del CPT "producto" — la ficha de cada producto
 * (litraje, colores, características, ficha técnica, etc.), registrados
 * por PHP en vez de armados a mano en el admin de ACF: así viven en el
 * theme (versionados con el resto del código) y no dependen de que nadie
 * los exporte/importe a mano en cada instalación.
 *
 * Los nombres de grupo/pestaña calcan el copy real del Figma ("1. Litraje",
 * "2. Selección de color", "Características principales") para que el
 * cliente reconozca de inmediato qué controla cada campo en el frontend.
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

    acf_add_local_field_group([
        'key'    => 'group_producto_ficha',
        'title'  => __('Ficha del producto', 'ese-latam'),
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'producto',
                ],
            ],
        ],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'fields' => [
            // ---------- Pestaña: General ----------
            [
                'key'  => 'field_producto_tab_general',
                'type' => 'tab',
                'label' => __('General', 'ese-latam'),
                'placement' => 'top',
            ],
            [
                'key'         => 'field_producto_descripcion',
                'name'        => 'descripcion_corta',
                'type'        => 'textarea',
                'label'       => __('Descripción corta', 'ese-latam'),
                'instructions' => __('El párrafo que aparece debajo del título en la página del producto.', 'ese-latam'),
                'rows'        => 3,
                'required'    => 1,
            ],
            [
                'key'         => 'field_producto_enlace_compra',
                'name'        => 'enlace_compra',
                'type'        => 'url',
                'label'       => __('Enlace del botón "Comprar"', 'ese-latam'),
                'instructions' => __('A dónde lleva el botón de compra/contacto (WhatsApp, formulario, etc.). Déjalo vacío para ocultar el botón.', 'ese-latam'),
            ],
            [
                'key'          => 'field_producto_ficha_tecnica',
                'name'         => 'ficha_tecnica',
                'type'         => 'file',
                'label'        => __('Ficha técnica (PDF)', 'ese-latam'),
                'instructions' => __('El documento que se descarga desde "Ficha técnica PDF".', 'ese-latam'),
                'return_format' => 'array',
                'mime_types'   => 'pdf',
            ],

            // ---------- Pestaña: Litraje ----------
            [
                'key'  => 'field_producto_tab_litraje',
                'type' => 'tab',
                'label' => __('1. Litraje', 'ese-latam'),
                'placement' => 'top',
            ],
            [
                'key'          => 'field_producto_litrajes',
                'name'         => 'litrajes',
                'type'         => 'repeater',
                'label'        => __('Opciones de litraje', 'ese-latam'),
                'instructions' => __('Un renglón por cada tamaño disponible (80L, 120L, etc.). Marca cuál va seleccionado por defecto.', 'ese-latam'),
                'layout'       => 'table',
                'button_label' => __('Añadir litraje', 'ese-latam'),
                'sub_fields'   => [
                    [
                        'key'      => 'field_litraje_valor',
                        'name'     => 'valor',
                        'type'     => 'text',
                        'label'    => __('Litraje', 'ese-latam'),
                        'placeholder' => __('Ej. 80L', 'ese-latam'),
                        'required' => 1,
                        'wrapper'  => ['width' => '70'],
                    ],
                    [
                        'key'   => 'field_litraje_predeterminado',
                        'name'  => 'predeterminado',
                        'type'  => 'true_false',
                        'label' => __('Por defecto', 'ese-latam'),
                        'ui'    => 1,
                        'wrapper' => ['width' => '30'],
                    ],
                ],
            ],

            // ---------- Pestaña: Colores ----------
            [
                'key'  => 'field_producto_tab_colores',
                'type' => 'tab',
                'label' => __('2. Colores', 'ese-latam'),
                'placement' => 'top',
            ],
            [
                'key'          => 'field_producto_colores',
                'name'         => 'colores',
                'type'         => 'repeater',
                'label'        => __('Selección de color', 'ese-latam'),
                'instructions' => __('Un renglón por cada color disponible. La foto es opcional — si no se sube, se usa la imagen principal del producto.', 'ese-latam'),
                'layout'       => 'table',
                'button_label' => __('Añadir color', 'ese-latam'),
                'sub_fields'   => [
                    [
                        'key'      => 'field_color_nombre',
                        'name'     => 'nombre',
                        'type'     => 'text',
                        'label'    => __('Nombre', 'ese-latam'),
                        'placeholder' => __('Ej. Gris', 'ese-latam'),
                        'required' => 1,
                        'wrapper'  => ['width' => '30'],
                    ],
                    [
                        'key'   => 'field_color_hex',
                        'name'  => 'color',
                        'type'  => 'color_picker',
                        'label' => __('Color', 'ese-latam'),
                        'required' => 1,
                        'wrapper' => ['width' => '20'],
                    ],
                    [
                        'key'   => 'field_color_imagen',
                        'name'  => 'imagen',
                        'type'  => 'image',
                        'label' => __('Foto en este color (opcional)', 'ese-latam'),
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                        'wrapper' => ['width' => '50'],
                    ],
                ],
            ],

            // ---------- Pestaña: Características ----------
            [
                'key'  => 'field_producto_tab_caracteristicas',
                'type' => 'tab',
                'label' => __('Características', 'ese-latam'),
                'placement' => 'top',
            ],
            [
                'key'          => 'field_producto_caracteristicas',
                'name'         => 'caracteristicas',
                'type'         => 'repeater',
                'label'        => __('Características principales', 'ese-latam'),
                'instructions' => __('Los datos técnicos en pares etiqueta/valor (Volumen, Peso, Carga máxima, Material, Ruedas...).', 'ese-latam'),
                'layout'       => 'table',
                'button_label' => __('Añadir característica', 'ese-latam'),
                'sub_fields'   => [
                    [
                        'key'      => 'field_caracteristica_etiqueta',
                        'name'     => 'etiqueta',
                        'type'     => 'text',
                        'label'    => __('Etiqueta', 'ese-latam'),
                        'placeholder' => __('Ej. Volumen', 'ese-latam'),
                        'required' => 1,
                        'wrapper'  => ['width' => '50'],
                    ],
                    [
                        'key'      => 'field_caracteristica_valor',
                        'name'     => 'valor',
                        'type'     => 'text',
                        'label'    => __('Valor', 'ese-latam'),
                        'placeholder' => __('Ej. 80L', 'ese-latam'),
                        'required' => 1,
                        'wrapper'  => ['width' => '50'],
                    ],
                ],
            ],
        ],
    ]);
});
