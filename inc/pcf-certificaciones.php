<?php
/**
 * Campos de la página "Certificaciones" (Figma 3785-4089).
 *
 * Los sellos NO se escriben acá: son el módulo "Certificaciones"
 * (inc/modulos.php). Esta página solo elige cuáles muestra —con un campo de
 * relación— y aporta el copy que los envuelve: el hero, los cuatro
 * argumentos de "Marcando la diferencia", los paneles de "Calidad superior",
 * el ciclo Blue Angel y la guía para validar certificados.
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

    $pagina = get_page_by_path('certificaciones');
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

    // Encabezado de sección: el trío que se repite en casi todos los bloques.
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
            $campo('wysiwyg', $prefijo . '_desc', __('Bajada', 'ese-latam'), [
                'tabs'         => 'visual',
                'toolbar'      => 'basic',
                'media_upload' => 0,
            ]),
        ];
    };

    acf_add_local_field_group([
        'key'      => 'group_pagina_certificaciones',
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
            [$tab('cert_hero', __('Hero', 'ese-latam'))],
            [
                $campo('text', 'certpag_hero_kicker', __('Antetítulo', 'ese-latam'), [
                    'placeholder' => __('Respaldo comprobado', 'ese-latam'),
                    'wrapper'     => ['width' => '40'],
                ]),
                $campo('textarea', 'certpag_hero_titulo', __('Titular', 'ese-latam'), [
                    'instructions' => ese_latam_ayuda_titulo(),
                    'rows'         => 2,
                    'placeholder'  => 'calidad y estándar certificado |que nos respaldan|',
                    'wrapper'      => ['width' => '60'],
                ]),
                $campo('textarea', 'certpag_hero_desc', __('Bajada', 'ese-latam'), ['rows' => 3]),
                $foto('certpag_hero_imagen', __('Foto de fondo', 'ese-latam')),
                $campo('link', 'certpag_hero_cta', __('Botón', 'ese-latam'), [
                    'instructions' => __('Sin enlace, el hero se muestra sin botón.', 'ese-latam'),
                ]),
            ],

            /* ---------------- Sellos ---------------- */
            [$tab('cert_sellos', __('Sellos', 'ese-latam'))],
            [
                $campo('message', '', __('De dónde salen', 'ese-latam'), [
                    'key'      => 'field_certpag_msg',
                    'message'  => __('Cada sello, con su logo, su descripción y sus criterios, se edita en el módulo <strong>Certificaciones</strong> del menú lateral. Acá solo eliges cuáles aparecen en esta página y en qué orden.', 'ese-latam'),
                    'esc_html' => 0,
                ]),
            ],
            $encabezado('certpag_sellos', __('Respaldo comprobado', 'ese-latam'), "Nuestras\n|certificaciones|"),
            [
                $campo('relationship', 'certpag_sellos_lista', __('Sellos que se muestran', 'ese-latam'), [
                    'instructions'  => __('Vacío: se muestran todos los del módulo, en su propio orden.', 'ese-latam'),
                    'post_type'     => ['certificacion'],
                    'filters'       => ['search'],
                    'return_format' => 'id',
                ]),
            ],

            /* ---------------- Marquee ---------------- */
            [$tab('cert_marquee', __('Marquee', 'ese-latam'))],
            [
                $campo('text', 'certpag_marquee', __('Frase', 'ese-latam'), [
                    'instructions' => __('La cinta que separa los sellos del siguiente bloque. Vacía: no se muestra.', 'ese-latam'),
                    'placeholder'  => __('marca la diferencia', 'ese-latam'),
                    'maxlength'    => 60,
                ]),
            ],

            /* ---------------- Marcando la diferencia ---------------- */
            [$tab('cert_diferencia', __('Marcando la diferencia', 'ese-latam'))],
            $encabezado('certpag_dif', __('Certificaciones', 'ese-latam'), "Marcando la\n|diferencia|"),
            [
                $campo('repeater', 'certpag_dif_tabs', __('Argumentos', 'ese-latam'), [
                    'instructions' => __('Cada uno es una pestaña del bloque. Sin argumentos, el bloque no se muestra.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir argumento', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'title', __('Título', 'ese-latam'), [
                            'key'      => 'field_certdif_titulo',
                            'required' => 1,
                        ]),
                        $campo('textarea', 'desc', __('Descripción', 'ese-latam'), [
                            'key'  => 'field_certdif_desc',
                            'rows' => 2,
                        ]),
                        $foto('img', __('Foto', 'ese-latam'), ['key' => 'field_certdif_img']),
                    ],
                ]),
            ],

            /* ---------------- Calidad superior ---------------- */
            [$tab('cert_calidad', __('Calidad superior', 'ese-latam'))],
            $encabezado('certpag_cal', __('Sobre nosotros', 'ese-latam'), "Calidad superior\n|desde el origen|"),
            [
                $campo('repeater', 'certpag_cal_items', __('Paneles', 'ese-latam'), [
                    'instructions' => __('El aside pegajoso: al hacer scroll, cada panel se fija mientras se lee. Sin paneles, el bloque no se muestra.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir panel', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'title', __('Título', 'ese-latam'), [
                            'key'      => 'field_certcal_titulo',
                            'required' => 1,
                            'wrapper'  => ['width' => '60'],
                        ]),
                        $foto('img', __('Foto', 'ese-latam'), [
                            'key'     => 'field_certcal_img',
                            'wrapper' => ['width' => '40'],
                        ]),
                        $campo('wysiwyg', 'text', __('Texto', 'ese-latam'), [
                            'key'          => 'field_certcal_text',
                            'instructions' => __('El tramo en <strong>negrita</strong> se muestra en verde.', 'ese-latam'),
                            'tabs'         => 'visual',
                            'toolbar'      => 'basic',
                            'media_upload' => 0,
                        ]),
                    ],
                ]),
                $campo('link', 'certpag_cal_salto', __('Enlace "saltar sección"', 'ese-latam'), [
                    'instructions' => __('El atajo al final del menú lateral. Vacío: no se muestra.', 'ese-latam'),
                ]),
            ],

            /* ---------------- Blue Angel ---------------- */
            [$tab('cert_bangel', __('Blue Angel', 'ese-latam'))],
            $encabezado('certpag_ba', __('Productos', 'ese-latam'), 'Estándar |Blue Angel|'),
            [
                $campo('repeater', 'certpag_ba_pasos', __('Etapas del ciclo', 'ese-latam'), [
                    'instructions' => __('El carrusel en arco. El orden es el del ciclo; sin etapas, el bloque no se muestra.', 'ese-latam'),
                    'layout'       => 'table',
                    'button_label' => __('Añadir etapa', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'label', __('Etapa', 'ese-latam'), [
                            'key'      => 'field_certba_label',
                            'required' => 1,
                        ]),
                        $foto('img', __('Foto', 'ese-latam'), [
                            'key'          => 'field_certba_img',
                            'preview_size' => 'thumbnail',
                        ]),
                    ],
                ]),
                $campo('number', 'certpag_ba_inicial', __('Etapa centrada al abrir', 'ese-latam'), [
                    'instructions' => __('Posición en la lista, empezando por 1. Vacío: la primera.', 'ese-latam'),
                    'min'          => 1,
                    'wrapper'      => ['width' => '40'],
                ]),
            ],

            /* ---------------- Validación ---------------- */
            [$tab('cert_valida', __('Validar certificados', 'ese-latam'))],
            $encabezado('certpag_val', __('Sobre nosotros', 'ese-latam'), "Valida certificados\n|originales|"),
            [
                $campo('text', 'certpag_val_pasos_kicker', __('Antetítulo del carrusel', 'ese-latam'), [
                    'placeholder' => __('Pasos a seguir', 'ese-latam'),
                    'wrapper'     => ['width' => '50'],
                ]),
                $campo('link', 'certpag_val_enlace', __('Enlace del carrusel', 'ese-latam'), [
                    'instructions' => __('Vacío: no se muestra.', 'ese-latam'),
                    'wrapper'      => ['width' => '50'],
                ]),
                $campo('repeater', 'certpag_val_pasos', __('Pasos', 'ese-latam'), [
                    'instructions' => __('Las tarjetas del carrusel. Sin pasos, el bloque no se muestra.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir paso', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'tab', __('Texto de la pestaña', 'ese-latam'), [
                            'key'       => 'field_certval_tab',
                            'required'  => 1,
                            'maxlength' => 20,
                            'wrapper'   => ['width' => '30'],
                        ]),
                        $campo('text', 'title', __('Título', 'ese-latam'), [
                            'key'      => 'field_certval_titulo',
                            'required' => 1,
                            'wrapper'  => ['width' => '70'],
                        ]),
                        $campo('textarea', 'text', __('Explicación', 'ese-latam'), [
                            'key'  => 'field_certval_text',
                            'rows' => 3,
                        ]),
                        $campo('textarea', 'tip', __('Consejo', 'ese-latam'), [
                            'key'          => 'field_certval_tip',
                            'instructions' => __('La línea destacada al pie de la tarjeta.', 'ese-latam'),
                            'rows'         => 2,
                        ]),
                    ],
                ]),
            ],

            /* ---------------- Casos ---------------- */
            [$tab('cert_casos', __('Casos reales', 'ese-latam'))],
            $encabezado('certpag_casos', __('Casos reales', 'ese-latam'), "Casos reales en\n|gestión urbana|"),
            [
                $campo('link', 'certpag_casos_enlace', __('Enlace', 'ese-latam'), [
                    'instructions' => __('Vacío: la sección se muestra sin enlace.', 'ese-latam'),
                ]),
            ]
        ),
    ]);
});
