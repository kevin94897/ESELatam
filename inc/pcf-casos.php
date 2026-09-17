<?php
/**
 * Campos del single de "Caso de éxito" (Figma 3891-4495).
 *
 * Lo nativo del CPT no se repite acá: el titular, la bajada (extracto), el
 * relato, la foto, el sector y la ciudad son los campos de siempre de la
 * entrada (inc/cpt-casos.php). Estos son los bloques que el relato no cubre:
 * la arquitectura del proyecto con sus pestañas, la franja de resultados,
 * los productos que se usaron y cómo se presentan las secciones.
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

    acf_add_local_field_group([
        'key'      => 'group_caso',
        'title'    => __('Ficha del caso', 'ese-latam'),
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'caso']]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => __('El titular, la bajada (extracto), el relato, la foto, el sector y la ciudad son los campos de siempre de la entrada.', 'ese-latam'),
        'fields'                => array_merge(

            /* ---------------- Hero ---------------- */
            [$tab('caso_hero', __('Hero', 'ese-latam'))],
            [
                $campo('number', 'caso_anio', __('Año', 'ese-latam'), [
                    'instructions' => __('Se muestra bajo el titular, junto a la ciudad. Vacío: el año en que se publicó.', 'ese-latam'),
                    'min'          => 1900,
                    'max'          => 2200,
                    'wrapper'      => ['width' => '50'],
                ]),
            ],

            /* ---------------- Arquitectura ---------------- */
            [$tab('caso_arq', __('Reto, problema y solución', 'ese-latam'))],
            $encabezado('caso_arq', __('Arquitectura del proyecto', 'ese-latam'), "el reto, problema\n|y solución|"),
            [
                $campo('wysiwyg', 'caso_arq_desc', __('Bajada', 'ese-latam'), [
                    'instructions' => __('El tramo en <strong>negrita</strong> se muestra en verde.', 'ese-latam'),
                    'tabs'         => 'visual',
                    'toolbar'      => 'basic',
                    'media_upload' => 0,
                ]),
                $campo('repeater', 'caso_arq_items', __('Pestañas', 'ese-latam'), [
                    'instructions' => __('Una por cada mirada del proyecto. Con una sola no se muestran las pastillas; sin ninguna, el bloque no se muestra.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir pestaña', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'label', __('Texto de la pastilla', 'ese-latam'), [
                            'key'       => 'field_casoarq_label',
                            'required'  => 1,
                            'maxlength' => 24,
                            'wrapper'   => ['width' => '30'],
                        ]),
                        $campo('text', 'kicker', __('Etiqueta', 'ese-latam'), [
                            'key'         => 'field_casoarq_kicker',
                            'placeholder' => __('Desafío técnico', 'ese-latam'),
                            'wrapper'     => ['width' => '30'],
                        ]),
                        $campo('text', 'title', __('Título', 'ese-latam'), [
                            'key'      => 'field_casoarq_titulo',
                            'required' => 1,
                            'wrapper'  => ['width' => '40'],
                        ]),
                        $campo('textarea', 'texto', __('Texto', 'ese-latam'), [
                            'key'  => 'field_casoarq_texto',
                            'rows' => 4,
                        ]),
                        $campo('text', 'nota', __('Nota al pie', 'ese-latam'), [
                            'key'          => 'field_casoarq_nota',
                            'instructions' => __('La línea con el ícono de información. Vacía: no se muestra.', 'ese-latam'),
                            'wrapper'      => ['width' => '60'],
                        ]),
                        ese_latam_campo_def('image', 'img', __('Foto', 'ese-latam'), [
                            'key'           => 'field_casoarq_img',
                            'return_format' => 'url',
                            'preview_size'  => 'medium',
                            'wrapper'       => ['width' => '40'],
                        ]),
                    ],
                ]),
            ],

            /* ---------------- Resultados ---------------- */
            [$tab('caso_result', __('Resultados', 'ese-latam'))],
            [
                $campo('text', 'caso_marquee', __('Frase de la cinta', 'ese-latam'), [
                    'instructions' => __('La cinta enorme que anuncia los resultados. Vacía: no se muestra.', 'ese-latam'),
                    'placeholder'  => __('Rendimiento real', 'ese-latam'),
                    'maxlength'    => 40,
                ]),
                $campo('repeater', 'caso_cifras', __('Cifras', 'ese-latam'), [
                    'instructions' => __('El valor se escribe tal cual sale: «-45%», «1,200m²», «50 seg». Sin cifras, la franja no se muestra.', 'ese-latam'),
                    'layout'       => 'table',
                    'button_label' => __('Añadir cifra', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'valor', __('Valor', 'ese-latam'), [
                            'key'       => 'field_casocifra_valor',
                            'required'  => 1,
                            'maxlength' => 12,
                        ]),
                        $campo('text', 'etiqueta', __('Etiqueta', 'ese-latam'), [
                            'key'       => 'field_casocifra_label',
                            'maxlength' => 40,
                        ]),
                    ],
                ]),
            ],

            /* ---------------- Relato ---------------- */
            [$tab('caso_relato', __('Relato', 'ese-latam'))],
            [
                $campo('message', '', __('Dónde se escribe', 'ese-latam'), [
                    'key'      => 'field_casorelato_msg',
                    'message'  => __('El relato es el <strong>editor</strong> de esta entrada: titulares, párrafos, citas e imágenes con pie. Acá solo el antetítulo con el que se presenta.', 'ese-latam'),
                    'esc_html' => 0,
                ]),
                $campo('text', 'caso_relato_kicker', __('Antetítulo', 'ese-latam'), [
                    'placeholder' => __('El proyecto', 'ese-latam'),
                    'wrapper'     => ['width' => '50'],
                ]),
            ],

            /* ---------------- Soluciones ---------------- */
            [$tab('caso_soluciones', __('Soluciones utilizadas', 'ese-latam'))],
            $encabezado('caso_sol', __('Productos', 'ese-latam'), 'Soluciones |utilizadas|'),
            [
                $campo('textarea', 'caso_sol_desc', __('Bajada', 'ese-latam'), ['rows' => 2]),
                $campo('relationship', 'caso_productos', __('Productos del caso', 'ese-latam'), [
                    'instructions'  => __('Los que se usaron de verdad, en el orden en que se muestran. Vacío: se muestran los del catálogo.', 'ese-latam'),
                    'post_type'     => ['producto'],
                    'filters'       => ['search'],
                    'return_format' => 'id',
                ]),
            ],

            /* ---------------- Relacionados ---------------- */
            [$tab('caso_relacionados', __('Casos relacionados', 'ese-latam'))],
            $encabezado('caso_rel', __('Casos de éxito', 'ese-latam'), "Casos de éxito\n|relacionados|"),
            [
                $campo('textarea', 'caso_rel_desc', __('Bajada', 'ese-latam'), ['rows' => 2]),
                $campo('link', 'caso_rel_enlace', __('Enlace', 'ese-latam'), [
                    'instructions' => __('Vacío: la sección se muestra sin enlace.', 'ese-latam'),
                ]),
            ]
        ),
    ]);
});
