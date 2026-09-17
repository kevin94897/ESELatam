<?php
/**
 * Campos de la página "Contacto" (Figma 3941-8369) y de los datos de
 * contacto del sitio.
 *
 * Acá va solo el COPY de la pantalla: titulares, rótulos del formulario y
 * textos de estado.
 *
 * Los DATOS —teléfono, correo, dirección, horario y redes— no son de esta
 * página sino de la empresa, así que viven en Apariencia → Personalizar →
 * ESE Latam (inc/customizer.php) y los lee ese_latam_contacto_datos().
 *
 * Las opciones de los tres desplegables no son campos: país sale de la
 * taxonomía "Países" de Distribuidores, sector del módulo "Sectores" y
 * producto del catálogo. Escribirlos otra vez acá sería tener la misma
 * lista en dos lados.
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

    $pagina = get_page_by_path('contacto');
    if (! $pagina instanceof WP_Post) {
        return;
    }

    // Rótulo + texto de ayuda de un campo del formulario. El `name` lo fija
    // inc/contacto.php (es lo que valida y manda el correo), así que acá solo
    // se escribe lo que se ve.
    $par = static function (string $name, string $label, string $ph = '') use ($campo): array {
        return [
            $campo('text', 'ctc_f_' . $name . '_label', $label, [
                'placeholder' => $label,
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('text', 'ctc_f_' . $name . '_ph', __('…texto de ayuda', 'ese-latam'), [
                'placeholder' => $ph,
                'wrapper'     => ['width' => '50'],
            ]),
        ];
    };

    acf_add_local_field_group([
        'key'      => 'group_pagina_contacto',
        'title'    => __('Contenido de la página', 'ese-latam'),
        'location' => [[['param' => 'page', 'operator' => '==', 'value' => (string) $pagina->ID]]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'fields'                => array_merge(

            /* ---------------- Encabezado ---------------- */
            [$tab('ctc_encabezado', __('Encabezado', 'ese-latam'))],
            [
                $campo('text', 'ctc_kicker', __('Antetítulo', 'ese-latam'), [
                    'placeholder' => __('Contacto', 'ese-latam'),
                    'wrapper'     => ['width' => '40'],
                ]),
                $campo('textarea', 'ctc_titulo', __('Titular', 'ese-latam'), [
                    'instructions' => ese_latam_ayuda_titulo(),
                    'rows'         => 2,
                    'placeholder'  => "Cuéntanos tu\n|operación|",
                    'wrapper'      => ['width' => '60'],
                ]),
                $campo('wysiwyg', 'ctc_lede', __('Bajada', 'ese-latam'), [
                    'instructions' => __('El tramo en <strong>negrita</strong> se muestra en verde.', 'ese-latam'),
                    'tabs'         => 'visual',
                    'toolbar'      => 'basic',
                    'media_upload' => 0,
                ]),
            ],

            /* ---------------- Formulario ---------------- */
            [$tab('ctc_form', __('Formulario', 'ese-latam'))],
            [
                $campo('message', '', __('De dónde salen los desplegables', 'ese-latam'), [
                    'key'      => 'field_ctcform_msg',
                    'message'  => __('Las opciones de <strong>País</strong> son los países de Distribuidores, las de <strong>Sector</strong> el módulo Sectores y las de <strong>Producto</strong> el catálogo. Acá solo se escriben los rótulos. Un rótulo vacío deja el campo sin etiqueta, no lo quita: los campos los fija el formulario.', 'ese-latam'),
                    'esc_html' => 0,
                ]),
            ],
            $par('nombre', __('Nombre y apellido', 'ese-latam'), __('Escribe tu nombre y apellido...', 'ese-latam')),
            $par('email', __('Correo electrónico', 'ese-latam'), 'ejemplo@entidad.com'),
            $par('telefono', __('Teléfono', 'ese-latam'), '+51 999 999 999'),
            $par('pais', __('País', 'ese-latam'), __('Selecciona tu país...', 'ese-latam')),
            $par('sector', __('Sector', 'ese-latam'), __('Selecciona tu sector...', 'ese-latam')),
            $par('producto', __('Producto de interés', 'ese-latam'), __('Selecciona un producto...', 'ese-latam')),
            $par('mensaje', __('Mensaje adicional', 'ese-latam'), __('Cuéntanos volúmenes, plazos y el detalle de tu operación...', 'ese-latam')),
            [
                $campo('text', 'ctc_f_otro', __('Última opción de los desplegables', 'ese-latam'), [
                    'instructions' => __('La salida para quien no se ve en la lista. Vacía: no se añade.', 'ese-latam'),
                    'placeholder'  => __('Otro', 'ese-latam'),
                    'wrapper'      => ['width' => '34'],
                ]),
                $campo('text', 'ctc_boton', __('Texto del botón', 'ese-latam'), [
                    'placeholder' => __('Solicitar asesoría', 'ese-latam'),
                    'wrapper'     => ['width' => '33'],
                ]),
                $campo('text', 'ctc_nota', __('Nota de campos obligatorios', 'ese-latam'), [
                    'instructions' => __('El asterisco se pinta solo donde va. Vacía: no se muestra.', 'ese-latam'),
                    'placeholder'  => __('Los campos con * son obligatorios.', 'ese-latam'),
                    'wrapper'      => ['width' => '33'],
                ]),
                $campo('textarea', 'ctc_msg_ok', __('Mensaje al enviar bien', 'ese-latam'), [
                    'rows'        => 2,
                    'placeholder' => __('¡Gracias! Recibimos tu mensaje y te responderemos en menos de 24 horas hábiles.', 'ese-latam'),
                    'wrapper'     => ['width' => '50'],
                ]),
                $campo('textarea', 'ctc_msg_error', __('Mensaje si falla', 'ese-latam'), [
                    'rows'        => 2,
                    'placeholder' => __('No pudimos enviar tu mensaje. Revisa los campos e inténtalo de nuevo.', 'ese-latam'),
                    'wrapper'     => ['width' => '50'],
                ]),
            ],

            /* ---------------- Panel lateral ---------------- */
            [$tab('ctc_aside', __('Panel lateral', 'ese-latam'))],
            [
                $campo('message', '', __('Los datos', 'ese-latam'), [
                    'key'      => 'field_ctcaside_msg',
                    'message'  => __('El teléfono, el correo, la dirección y el horario salen de <strong>Apariencia → Personalizar → ESE Latam</strong>. Acá solo el rótulo de cada uno.', 'ese-latam'),
                    'esc_html' => 0,
                ]),
                $campo('image', 'ctc_aside_imagen', __('Foto de la tarjeta', 'ese-latam'), [
                    'instructions'  => __('Vacía: no se muestra la tarjeta.', 'ese-latam'),
                    'return_format' => 'url',
                    'preview_size'  => 'medium',
                    'wrapper'       => ['width' => '40'],
                ]),
                $campo('text', 'ctc_aside_kicker', __('Tarjeta — antetítulo', 'ese-latam'), [
                    'placeholder' => __('Sede Miraflores', 'ese-latam'),
                    'wrapper'     => ['width' => '60'],
                ]),
                $campo('textarea', 'ctc_aside_texto', __('Tarjeta — texto', 'ese-latam'), ['rows' => 2]),
                $campo('text', 'ctc_aside_titulo', __('Título de la lista', 'ese-latam'), [
                    'instructions' => __('Vacío: la lista se muestra sin título.', 'ese-latam'),
                    'placeholder'  => __('Información de contacto', 'ese-latam'),
                ]),
                $campo('text', 'ctc_info_telefono', __('Rótulo del teléfono', 'ese-latam'), [
                    'placeholder' => __('Teléfono', 'ese-latam'),
                    'wrapper'     => ['width' => '25'],
                ]),
                $campo('text', 'ctc_info_email', __('Rótulo del correo', 'ese-latam'), [
                    'placeholder' => __('Correo electrónico', 'ese-latam'),
                    'wrapper'     => ['width' => '25'],
                ]),
                $campo('text', 'ctc_info_direccion', __('Rótulo de la dirección', 'ese-latam'), [
                    'placeholder' => __('Dirección', 'ese-latam'),
                    'wrapper'     => ['width' => '25'],
                ]),
                $campo('text', 'ctc_info_horario', __('Rótulo del horario', 'ese-latam'), [
                    'placeholder' => __('Horario de atención', 'ese-latam'),
                    'wrapper'     => ['width' => '25'],
                ]),
            ],

            /* ---------------- Sede ---------------- */
            [$tab('ctc_sede', __('Sede', 'ese-latam'))],
            [
                $campo('text', 'ctc_sede_kicker', __('Antetítulo', 'ese-latam'), [
                    'placeholder' => __('Dónde estamos', 'ese-latam'),
                    'wrapper'     => ['width' => '40'],
                ]),
                $campo('textarea', 'ctc_sede_titulo', __('Titular', 'ese-latam'), [
                    'instructions' => ese_latam_ayuda_titulo(),
                    'rows'         => 2,
                    'placeholder'  => "Sede central\n|ESE Latam|",
                    'wrapper'      => ['width' => '60'],
                ]),
                $campo('text', 'ctc_sede_enlace', __('Texto del enlace al mapa', 'ese-latam'), [
                    'instructions' => __('Vacío: no se muestra el enlace.', 'ese-latam'),
                    'placeholder'  => __('Llegar con Google Maps', 'ese-latam'),
                    'wrapper'      => ['width' => '50'],
                ]),
                $campo('true_false', 'ctc_sede_mapa', __('Mostrar el mapa', 'ese-latam'), [
                    'default_value' => 1,
                    'ui'            => 1,
                    'wrapper'       => ['width' => '50'],
                ]),
            ],

            /* ---------------- Preguntas ---------------- */
            [$tab('ctc_faq', __('Preguntas frecuentes', 'ese-latam'))],
            [
                $campo('message', '', __('Las preguntas', 'ese-latam'), [
                    'key'      => 'field_ctcfaq_msg',
                    'message'  => __('Salen del módulo <strong>Preguntas frecuentes</strong> del menú lateral. Acá solo se define cómo se presenta la sección.', 'ese-latam'),
                    'esc_html' => 0,
                ]),
                $campo('text', 'ctc_faq_kicker', __('Antetítulo', 'ese-latam'), [
                    'placeholder' => __('Respondemos dudas', 'ese-latam'),
                    'wrapper'     => ['width' => '40'],
                ]),
                $campo('textarea', 'ctc_faq_titulo', __('Titular', 'ese-latam'), [
                    'instructions' => ese_latam_ayuda_titulo(),
                    'rows'         => 2,
                    'placeholder'  => "Preguntas\n|frecuentes|",
                    'wrapper'      => ['width' => '60'],
                ]),
            ]
        ),
    ]);
});
