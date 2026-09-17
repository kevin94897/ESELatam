<?php
/**
 * Campos de la página "Nosotros" (Figma 3441-370).
 *
 * Solo lo que es de esta pantalla: el hero, misión/visión, los objetivos con
 * su aside pegajoso, la presentación de los aliados, el marquee, los pilares
 * ESG del método y el bloque HDPE. Lo que se repite en otras páginas no está
 * acá: los logos de aliados son el módulo "Aliados" y el cierre de
 * Contactemos tiene su override en la pestaña "Secciones compartidas".
 *
 * Los íconos de las tarjetas HDPE no son campos: son tres dibujos del Figma
 * que viven en ese_latam_iconos_hdpe() (inc/template-tags.php). El editor
 * elige cuál usa cada tarjeta, no lo dibuja.
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

    $pagina = get_page_by_path('nosotros');
    if (! $pagina instanceof WP_Post) {
        return;
    }

    $campo = 'ese_latam_campo_def';
    $tab   = 'ese_latam_campo_tab';

    $foto = static fn (string $name, string $label, array $args = []): array =>
        ese_latam_campo_def('image', $name, $label, array_merge([
            'return_format' => 'url',
            'preview_size'  => 'medium',
        ], $args));

    // Antetítulo + titular: el par que abre casi todos los bloques.
    $encabezado = static function (string $prefijo, string $kicker, string $titulo) use ($campo): array {
        return [
            $campo('text', $prefijo . '_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => $kicker,
                'wrapper'     => ['width' => '40'],
            ]),
            $campo('textarea', $prefijo . '_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => $titulo,
                'wrapper'      => ['width' => '60'],
            ]),
        ];
    };

    // Las bajadas admiten negrita: en esta página la negrita se pinta en
    // verde (ver .nos-desc strong / .nos-objetivo__text strong en main.css).
    $bajada = static fn (string $name, string $label = ''): array =>
        ese_latam_campo_def('wysiwyg', $name, '' !== $label ? $label : __('Bajada', 'ese-latam'), [
            'instructions' => __('El tramo en <strong>negrita</strong> se muestra en verde.', 'ese-latam'),
            'tabs'         => 'visual',
            'toolbar'      => 'basic',
            'media_upload' => 0,
        ]);

    acf_add_local_field_group([
        'key'      => 'group_pagina_nosotros',
        'title'    => __('Contenido de la página', 'ese-latam'),
        'location' => [[['param' => 'page', 'operator' => '==', 'value' => (string) $pagina->ID]]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'fields'                => array_merge(

            /* ---------------- Hero ---------------- */
            [$tab('nos_hero', __('Hero', 'ese-latam'))],
            [
                $campo('textarea', 'nos_hero_titulo', __('Titular', 'ese-latam'), [
                    'instructions' => ese_latam_ayuda_titulo(),
                    'rows'         => 2,
                    'placeholder'  => 'reinventando el |entorno en latam|',
                ]),
                $campo('textarea', 'nos_hero_desc_ini', __('Bajada — antes de la cifra', 'ese-latam'), [
                    'instructions' => __('La frase se parte en tres para que la cifra se anime al entrar en pantalla.', 'ese-latam'),
                    'rows'         => 2,
                ]),
                $campo('number', 'nos_hero_cifra', __('Cifra', 'ese-latam'), [
                    'instructions' => __('Vacía: la bajada se muestra de corrido, sin contador.', 'ese-latam'),
                    'min'          => 0,
                    'wrapper'      => ['width' => '25'],
                ]),
                $campo('textarea', 'nos_hero_desc_fin', __('Bajada — después de la cifra', 'ese-latam'), [
                    'rows'    => 2,
                    'wrapper' => ['width' => '75'],
                ]),
                $foto('nos_hero_imagen', __('Foto de fondo', 'ese-latam'), ['wrapper' => ['width' => '60']]),
                $campo('text', 'nos_hero_scroll', __('Palabra del indicador de scroll', 'ese-latam'), [
                    'instructions' => __('Vacía: no se muestra el indicador.', 'ese-latam'),
                    'placeholder'  => __('Scroll', 'ese-latam'),
                    'maxlength'    => 20,
                    'wrapper'      => ['width' => '40'],
                ]),
            ],

            /* ---------------- Construimos para el futuro ---------------- */
            [$tab('nos_const', __('Construimos para el futuro', 'ese-latam'))],
            $encabezado('nos_const', __('Sobre nosotros', 'ese-latam'), "Construimos\n|para el futuro|"),
            [
                $campo('repeater', 'nos_const_bloques', __('Bloques de texto', 'ese-latam'), [
                    'instructions' => __('Misión y visión. Sin bloques, la sección no se muestra.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir bloque', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'title', __('Título', 'ese-latam'), [
                            'key'      => 'field_nosconst_titulo',
                            'required' => 1,
                        ]),
                        $campo('textarea', 'texto', __('Texto', 'ese-latam'), [
                            'key'  => 'field_nosconst_texto',
                            'rows' => 4,
                        ]),
                    ],
                ]),
                $foto('nos_const_foto', __('Foto de la izquierda', 'ese-latam'), ['wrapper' => ['width' => '60']]),
                $campo('text', 'nos_const_foto_alt', __('Texto alternativo de la foto', 'ese-latam'), [
                    'instructions' => __('Lo que lee un lector de pantalla.', 'ese-latam'),
                    'wrapper'      => ['width' => '40'],
                ]),
                $foto('nos_const_cielo', __('Cielo del fondo', 'ese-latam'), [
                    'instructions' => __('Vacío: no se muestra el recuadro del cielo ni la isla.', 'ese-latam'),
                    'wrapper'      => ['width' => '34'],
                ]),
                $foto('nos_const_isla', __('Isla flotante', 'ese-latam'), ['wrapper' => ['width' => '33']]),
                $campo('text', 'nos_const_isla_alt', __('Texto alternativo de la isla', 'ese-latam'), [
                    'wrapper' => ['width' => '33'],
                ]),
            ],

            /* ---------------- Objetivos con propósito ---------------- */
            [$tab('nos_obj', __('Objetivos con propósito', 'ese-latam'))],
            $encabezado('nos_obj', __('Sobre nosotros', 'ese-latam'), "Objetivos con\n|propósito|"),
            [
                $bajada('nos_obj_desc'),
                $campo('repeater', 'nos_obj_items', __('Objetivos', 'ese-latam'), [
                    'instructions' => __('Cada uno es un panel con foto y una entrada del menú que sigue al scroll. Sin objetivos, la sección no se muestra.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir objetivo', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'title', __('Título', 'ese-latam'), [
                            'key'      => 'field_nosobj_titulo',
                            'required' => 1,
                            'wrapper'  => ['width' => '60'],
                        ]),
                        ese_latam_campo_def('image', 'img', __('Foto', 'ese-latam'), [
                            'key'           => 'field_nosobj_img',
                            'return_format' => 'url',
                            'preview_size'  => 'medium',
                            'wrapper'       => ['width' => '40'],
                        ]),
                        $campo('wysiwyg', 'texto', __('Texto', 'ese-latam'), [
                            'key'          => 'field_nosobj_texto',
                            'instructions' => __('El tramo en <strong>negrita</strong> se muestra en verde.', 'ese-latam'),
                            'tabs'         => 'visual',
                            'toolbar'      => 'basic',
                            'media_upload' => 0,
                        ]),
                    ],
                ]),
                $campo('link', 'nos_obj_salto', __('Enlace "saltar sección"', 'ese-latam'), [
                    'instructions' => __('El atajo al final del menú lateral. Vacío: no se muestra.', 'ese-latam'),
                ]),
                $campo('text', 'nos_obj_metas_titulo', __('Título de las metas', 'ese-latam'), [
                    'placeholder' => __('Algunas de nuestras metas y objetivos del programa:', 'ese-latam'),
                ]),
                $campo('repeater', 'nos_obj_metas', __('Metas', 'ese-latam'), [
                    'instructions' => __('La lista con vistos del menú lateral. Sin metas, la lista no se muestra.', 'ese-latam'),
                    'layout'       => 'table',
                    'button_label' => __('Añadir meta', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('textarea', 'texto', __('Meta', 'ese-latam'), [
                            'key'      => 'field_nosmeta_texto',
                            'required' => 1,
                            'rows'     => 2,
                        ]),
                    ],
                ]),
            ],

            /* ---------------- Aliados ---------------- */
            [$tab('nos_aliados', __('Nuestros aliados', 'ese-latam'))],
            [
                $campo('message', '', __('Los logos', 'ese-latam'), [
                    'key'      => 'field_nosali_msg',
                    'message'  => __('Los logos salen del módulo <strong>Aliados</strong> del menú lateral. Acá solo se define cómo se presenta la sección en esta página.', 'ese-latam'),
                    'esc_html' => 0,
                ]),
            ],
            $encabezado('nos_aliados', __('Nuestros aliados', 'ese-latam'), "Trabajamos con\n|los mejores aliados|"),
            [
                $campo('textarea', 'nos_aliados_desc', __('Bajada', 'ese-latam'), ['rows' => 2]),
                $foto('nos_aliados_isla', __('Isla flotante', 'ese-latam'), [
                    'instructions' => __('La isla con inclinación al mover el mouse. Vacía: no se muestra.', 'ese-latam'),
                    'wrapper'      => ['width' => '50'],
                ]),
            ],

            /* ---------------- Marquee ---------------- */
            [$tab('nos_marquee', __('Marquee', 'ese-latam'))],
            [
                $campo('text', 'nos_marquee', __('Frase', 'ese-latam'), [
                    'instructions' => __('La cinta que separa a los aliados del método. Vacía: no se muestra.', 'ese-latam'),
                    'placeholder'  => 'circulogic',
                    'maxlength'    => 60,
                ]),
            ],

            /* ---------------- Método Circulogic ---------------- */
            [$tab('nos_metodo', __('Método Circulogic', 'ese-latam'))],
            $encabezado('nos_metodo', __('Sobre nosotros', 'ese-latam'), "Método\n|Circulogic|"),
            [
                $bajada('nos_metodo_desc'),
                $campo('repeater', 'nos_metodo_tabs', __('Pilares', 'ese-latam'), [
                    'instructions' => __('Cada pilar es una pestaña con su foto; se turnan solas. Sin pilares, la sección no se muestra.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir pilar', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'title', __('Título', 'ese-latam'), [
                            'key'      => 'field_nosmet_titulo',
                            'required' => 1,
                            'wrapper'  => ['width' => '60'],
                        ]),
                        ese_latam_campo_def('image', 'img', __('Foto', 'ese-latam'), [
                            'key'           => 'field_nosmet_img',
                            'return_format' => 'url',
                            'preview_size'  => 'medium',
                            'wrapper'       => ['width' => '40'],
                        ]),
                        $campo('textarea', 'desc', __('Descripción', 'ese-latam'), [
                            'key'  => 'field_nosmet_desc',
                            'rows' => 2,
                        ]),
                    ],
                ]),
            ],

            /* ---------------- HDPE ---------------- */
            [$tab('nos_hdpe', __('HDPE', 'ese-latam'))],
            $encabezado('nos_hdpe', __('Tecnología y sostenibilidad', 'ese-latam'), "hdpe: el futuro es\n|circular|"),
            [
                $bajada('nos_hdpe_desc'),
                $campo('repeater', 'nos_hdpe_chips', __('Etiquetas', 'ese-latam'), [
                    'instructions' => __('Los vistos bajo la bajada. Sin etiquetas, la fila no se muestra.', 'ese-latam'),
                    'layout'       => 'table',
                    'button_label' => __('Añadir etiqueta', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'texto', __('Etiqueta', 'ese-latam'), [
                            'key'       => 'field_noschip_texto',
                            'required'  => 1,
                            'maxlength' => 40,
                        ]),
                    ],
                ]),
                $campo('file', 'nos_hdpe_video', __('Video de la escena', 'ese-latam'), [
                    'instructions'  => __('Se reproduce solo, en silencio y en bucle. Vacío: no se muestra la escena.', 'ese-latam'),
                    'return_format' => 'url',
                    'mime_types'    => 'webm,mp4',
                    'wrapper'       => ['width' => '50'],
                ]),
                $campo('text', 'nos_hdpe_escena_kicker', __('Escena — antetítulo', 'ese-latam'), [
                    'placeholder' => __('Impacto positivo', 'ese-latam'),
                    'wrapper'     => ['width' => '25'],
                ]),
                $campo('text', 'nos_hdpe_escena_sub', __('Escena — pie', 'ese-latam'), [
                    'placeholder' => __('Estándar Global', 'ese-latam'),
                    'wrapper'     => ['width' => '25'],
                ]),
                $campo('repeater', 'nos_hdpe_cards', __('Tarjetas', 'ese-latam'), [
                    'instructions' => __('Las tres tarjetas del pie. Sin tarjetas, la fila no se muestra.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir tarjeta', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'title', __('Título', 'ese-latam'), [
                            'key'      => 'field_noshdpe_titulo',
                            'required' => 1,
                            'wrapper'  => ['width' => '40'],
                        ]),
                        $campo('text', 'desc', __('Bajada', 'ese-latam'), [
                            'key'     => 'field_noshdpe_desc',
                            'wrapper' => ['width' => '30'],
                        ]),
                        $campo('select', 'icono', __('Ícono', 'ese-latam'), [
                            'key'           => 'field_noshdpe_icono',
                            'choices'       => ese_latam_iconos_hdpe_opciones(),
                            'allow_null'    => 1,
                            'return_format' => 'value',
                            'wrapper'       => ['width' => '30'],
                        ]),
                        $campo('true_false', 'gira', __('El ícono gira', 'ese-latam'), [
                            'key'   => 'field_noshdpe_gira',
                            'ui'    => 1,
                        ]),
                    ],
                ]),
            ]
        ),
    ]);
});
