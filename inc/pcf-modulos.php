<?php
/**
 * Campos de los módulos de contenido (inc/modulos.php) y las coordenadas de
 * la taxonomía "Países".
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

    $grupo = static fn (string $key, string $title, string $tipo, array $fields, string $desc = ''): array => [
        'key'                   => 'group_' . $key,
        'title'                 => $title,
        'location'              => [[['param' => 'post_type', 'operator' => '==', 'value' => $tipo]]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => $desc,
        'fields'                => $fields,
    ];

    /* -----------------------------------------------------------------
     * Certificación
     * -------------------------------------------------------------- */
    acf_add_local_field_group($grupo(
        'certificacion',
        __('Ficha del sello', 'ese-latam'),
        'certificacion',
        [
            $campo('true_false', 'destacado', __('Mostrar en la franja de sellos', 'ese-latam'), [
                'instructions'  => __('La franja corta que sale en la portada, en la ficha de producto y en las páginas de sector. La página de Certificaciones los muestra todos igualmente.', 'ese-latam'),
                'default_value' => 0,
                'ui'            => 1,
            ]),
            $campo('textarea', 'resumen', __('Resumen', 'ese-latam'), [
                'instructions' => __('Una línea. Es lo que se lee bajo el logo en la franja de sellos y en la ficha de producto.', 'ese-latam'),
                'rows'         => 2,
                'required'     => 1,
                'maxlength'    => 90,
            ]),
            $campo('text', 'subtitulo', __('Qué certifica', 'ese-latam'), [
                'instructions' => __('La línea bajo el nombre en la página de Certificaciones.', 'ese-latam'),
                'placeholder'  => __('Norma alemana de diseño y dimensiones (DIN EN 840)', 'ese-latam'),
            ]),
            $campo('wysiwyg', 'detalle', __('Descripción', 'ese-latam'), [
                'instructions' => __('El texto largo de la página de Certificaciones.', 'ese-latam'),
                'tabs'         => 'visual',
                'toolbar'      => 'basic',
                'media_upload' => 0,
            ]),
            $campo('repeater', 'criterios', __('Criterios que verifica', 'ese-latam'), [
                'instructions' => __('Los puntos con check de la página de Certificaciones.', 'ese-latam'),
                'layout'       => 'table',
                'button_label' => __('Añadir criterio', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'texto', __('Criterio', 'ese-latam'), ['key' => 'field_crit_texto']),
                ],
            ]),
            $campo('text', 'organismo', __('Organismo que lo emite', 'ese-latam'), [
                'placeholder' => 'TÜV SÜD',
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('text', 'norma', __('Norma o referencia', 'ese-latam'), [
                'placeholder' => 'EN 840',
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('file', 'documento', __('Certificado (PDF)', 'ese-latam'), [
                'instructions'  => __('Opcional: el documento descargable.', 'ese-latam'),
                'return_format' => 'url',
                'mime_types'    => 'pdf',
            ]),
        ],
        __('El nombre de la entrada y su logo (imagen destacada) se usan en la franja de la portada, en el hero de producto y en la página de Certificaciones.', 'ese-latam')
    ));

    /* -----------------------------------------------------------------
     * Distribuidor
     * -------------------------------------------------------------- */
    acf_add_local_field_group($grupo(
        'distribuidor',
        __('Ficha del distribuidor', 'ese-latam'),
        'distribuidor',
        [
            $tab('dst_datos', __('Datos', 'ese-latam')),
            $campo('message', '', __('País', 'ese-latam'), [
                'key'      => 'field_dstm_msg',
                'message'  => __('El <strong>país</strong> se elige en la caja de la derecha. Sus coordenadas —las que usa el globo 3D para apuntar— se editan una vez en <strong>Distribuidores → Países</strong>.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('text', 'ciudad', __('Ciudad', 'ese-latam'), [
                'wrapper' => ['width' => '50'],
            ]),
            $campo('text', 'representante', __('Representante', 'ese-latam'), [
                'wrapper' => ['width' => '50'],
            ]),
            $campo('textarea', 'direccion', __('Dirección', 'ese-latam'), [
                'instructions' => __('Se usa tal cual para centrar el mapa.', 'ese-latam'),
                'rows'         => 2,
            ]),
            $campo('textarea', 'descripcion', __('Descripción', 'ese-latam'), [
                'instructions' => __('Opcional: a qué tipo de cliente atiende.', 'ese-latam'),
                'rows'         => 2,
            ]),

            $tab('dst_contacto', __('Contacto', 'ese-latam')),
            $campo('text', 'telefono', __('Teléfono', 'ese-latam'), [
                'wrapper' => ['width' => '50'],
            ]),
            $campo('email', 'email', __('Email', 'ese-latam'), [
                'wrapper' => ['width' => '50'],
            ]),
            $campo('text', 'whatsapp', __('WhatsApp', 'ese-latam'), [
                'instructions' => __('Solo dígitos, con código de país: 51999888777.', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('url', 'web', __('Sitio web', 'ese-latam'), [
                'wrapper' => ['width' => '50'],
            ]),
        ],
        __('El nombre de la entrada es el de la empresa y el logo sale de la imagen destacada.', 'ese-latam')
    ));

    /* -----------------------------------------------------------------
     * Aliado
     * -------------------------------------------------------------- */
    acf_add_local_field_group($grupo(
        'aliado',
        __('Ficha del aliado', 'ese-latam'),
        'aliado',
        [
            $campo('url', 'web', __('Sitio web', 'ese-latam'), [
                'instructions' => __('Opcional: si está, el logo enlaza ahí.', 'ese-latam'),
            ]),
        ],
        __('El nombre de la entrada se usa como texto alternativo del logo (imagen destacada).', 'ese-latam')
    ));

    /* -----------------------------------------------------------------
     * Pregunta frecuente
     * -------------------------------------------------------------- */
    acf_add_local_field_group($grupo(
        'faq',
        __('Respuesta', 'ese-latam'),
        'faq',
        [
            $campo('wysiwyg', 'respuesta', __('Respuesta', 'ese-latam'), [
                'tabs'         => 'visual',
                'toolbar'      => 'basic',
                'media_upload' => 0,
                'required'     => 1,
            ]),
        ],
        __('El título de la entrada es la pregunta.', 'ese-latam')
    ));

    /* -----------------------------------------------------------------
     * Coordenadas del país (taxonomía)
     *
     * El globo 3D gira hasta el centro del país al elegirlo en el riel, así
     * que cada término guarda su latitud y longitud.
     * -------------------------------------------------------------- */
    acf_add_local_field_group([
        'key'      => 'group_pais',
        'title'    => __('Ubicación en el globo', 'ese-latam'),
        'location' => [[['param' => 'taxonomy', 'operator' => '==', 'value' => 'pais']]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'fields'                => [
            $campo('number', 'lat', __('Latitud', 'ese-latam'), [
                'instructions' => __('Centro del país, entre -90 y 90.', 'ese-latam'),
                'min'          => -90,
                'max'          => 90,
                'step'         => 'any',
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('number', 'lng', __('Longitud', 'ese-latam'), [
                'instructions' => __('Entre -180 y 180.', 'ese-latam'),
                'min'          => -180,
                'max'          => 180,
                'step'         => 'any',
                'wrapper'      => ['width' => '50'],
            ]),
        ],
    ]);
});
