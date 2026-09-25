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

    // Antetítulo + titular de una sección. `$seccion` completa las etiquetas
    // ("Antetítulo de la sección …") y `$donde` abre la ayuda del titular.
    $encabezado = static function (string $prefijo, string $kicker, string $titulo, string $seccion, string $donde) use ($campo): array {
        return [
            $campo('text', $prefijo . '_kicker', sprintf(__('Antetítulo de la sección %s', 'ese-latam'), $seccion), [
                'instructions' => __('Palabra corta tras la barra, sobre el titular. Hasta 30 caracteres. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => $kicker,
                'wrapper'      => ['width' => '40'],
            ]),
            $campo('textarea', $prefijo . '_titulo', sprintf(__('Titular de la sección %s', 'ese-latam'), $seccion), [
                'instructions' => $donde . ' ' . ese_latam_ayuda_titulo() . ' ' . __('Vacío: no se muestra.', 'ese-latam'),
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
                $campo('number', 'caso_anio', __('Año del proyecto', 'ese-latam'), [
                    'instructions' => __('Se muestra en el hero, bajo la línea del titular, junto al sector y la ciudad. Cuatro cifras: 2024. Vacío: el año en que se publicó el caso.', 'ese-latam'),
                    'min'          => 1900,
                    'max'          => 2200,
                    'wrapper'      => ['width' => '50'],
                ]),
            ],

            /* ---------------- Arquitectura ---------------- */
            [$tab('caso_arq', __('Reto, problema y solución', 'ese-latam'))],
            $encabezado(
                'caso_arq',
                __('Arquitectura del proyecto', 'ese-latam'),
                "el reto, problema\n|y solución|",
                __('Reto y solución', 'ese-latam'),
                __('Frase grande centrada, sobre las pastillas de las pestañas.', 'ese-latam')
            ),
            [
                $campo('wysiwyg', 'caso_arq_desc', __('Bajada de la sección Reto y solución', 'ese-latam'), [
                    'instructions' => __('Párrafo centrado bajo el titular. Una o dos frases. El tramo en <strong>negrita</strong> se muestra en verde. Vacía: no se muestra.', 'ese-latam'),
                    'tabs'         => 'visual',
                    'toolbar'      => 'basic',
                    'media_upload' => 0,
                ]),
                $campo('repeater', 'caso_arq_items', __('Pestañas del reto, problema y solución', 'ese-latam'), [
                    'instructions' => __('Una por cada mirada del proyecto (reto, problema, solución): foto a la izquierda y texto a la derecha. Con una sola no se muestran las pastillas; sin ninguna, la sección entera no se muestra.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir pestaña', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'label', __('Texto de la pastilla de la pestaña', 'ese-latam'), [
                            'key'          => 'field_casoarq_label',
                            'instructions' => __('El botón que abre esta pestaña. Hasta 24 caracteres.', 'ese-latam'),
                            'required'     => 1,
                            'maxlength'    => 24,
                            'wrapper'      => ['width' => '30'],
                        ]),
                        $campo('text', 'kicker', __('Etiqueta con punto de la pestaña', 'ese-latam'), [
                            'key'          => 'field_casoarq_kicker',
                            'instructions' => __('Cápsula sobre el título, por ejemplo “Desafío técnico”. Vacía: no se muestra.', 'ese-latam'),
                            'placeholder'  => __('Desafío técnico', 'ese-latam'),
                            'wrapper'      => ['width' => '30'],
                        ]),
                        $campo('text', 'title', __('Título de la pestaña', 'ese-latam'), [
                            'key'          => 'field_casoarq_titulo',
                            'instructions' => __('Frase en grande a la derecha de la foto. Una línea.', 'ese-latam'),
                            'required'     => 1,
                            'wrapper'      => ['width' => '40'],
                        ]),
                        $campo('textarea', 'texto', __('Texto de la pestaña', 'ese-latam'), [
                            'key'          => 'field_casoarq_texto',
                            'instructions' => __('Párrafo bajo el título. Tres a cinco líneas. Vacío: no se muestra.', 'ese-latam'),
                            'rows'         => 4,
                        ]),
                        $campo('text', 'nota', __('Nota al pie de la pestaña', 'ese-latam'), [
                            'key'          => 'field_casoarq_nota',
                            'instructions' => __('La línea con el ícono de información, al final del texto. Vacía: no se muestra.', 'ese-latam'),
                            'wrapper'      => ['width' => '60'],
                        ]),
                        ese_latam_campo_def('image', 'img', __('Foto de la pestaña', 'ese-latam'), [
                            'key'           => 'field_casoarq_img',
                            'instructions'  => __('A la izquierda del texto, con esquinas redondeadas. WebP o JPG · 1400×955 px (casi 3:2) · máx. 300 KB. Se recorta al centro. Vacía: la pestaña se muestra solo con texto.', 'ese-latam'),
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
                $campo('text', 'caso_marquee', __('Frase de la cinta de resultados', 'ese-latam'), [
                    'instructions' => __('La cinta enorme en movimiento que anuncia las cifras. Hasta 40 caracteres. Vacía: no se muestra.', 'ese-latam'),
                    'placeholder'  => __('Rendimiento real', 'ese-latam'),
                    'maxlength'    => 40,
                ]),
                $campo('repeater', 'caso_cifras', __('Cifras de resultados del caso', 'ese-latam'), [
                    'instructions' => __('Franja de cifras grandes en azul, repartidas a lo ancho bajo la cinta. Lo ideal son tres. El valor se escribe tal cual sale: “-45%”, “1,200m²”, “50 seg”. Sin cifras, la franja no se muestra.', 'ese-latam'),
                    'layout'       => 'table',
                    'button_label' => __('Añadir cifra', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'valor', __('Valor de la cifra', 'ese-latam'), [
                            'key'          => 'field_casocifra_valor',
                            'instructions' => __('El número grande, con su unidad. Hasta 12 caracteres.', 'ese-latam'),
                            'required'     => 1,
                            'maxlength'    => 12,
                        ]),
                        $campo('text', 'etiqueta', __('Texto bajo la cifra', 'ese-latam'), [
                            'key'          => 'field_casocifra_label',
                            'instructions' => __('Qué mide la cifra. Hasta 40 caracteres. Vacío: solo se ve el valor.', 'ese-latam'),
                            'maxlength'    => 40,
                        ]),
                    ],
                ]),
            ],

            /* ---------------- Relato ---------------- */
            [$tab('caso_relato', __('Relato', 'ese-latam'))],
            [
                $campo('message', '', __('Dónde se escribe el relato', 'ese-latam'), [
                    'key'      => 'field_casorelato_msg',
                    'message'  => __('El relato es el <strong>editor</strong> de esta entrada: titulares, párrafos, citas e imágenes con pie. Acá solo el antetítulo con el que se presenta.', 'ese-latam'),
                    'esc_html' => 0,
                ]),
                $campo('text', 'caso_relato_kicker', __('Antetítulo sobre el relato', 'ese-latam'), [
                    'instructions' => __('Palabra corta tras la barra, sobre el texto del relato, por ejemplo “El proyecto”. Hasta 30 caracteres. Vacío: no se muestra. Si el editor está vacío, tampoco.', 'ese-latam'),
                    'placeholder'  => __('El proyecto', 'ese-latam'),
                    'wrapper'      => ['width' => '50'],
                ]),
            ],

            /* ---------------- Soluciones ---------------- */
            [$tab('caso_soluciones', __('Soluciones utilizadas', 'ese-latam'))],
            $encabezado(
                'caso_sol',
                __('Productos', 'ese-latam'),
                'Soluciones |utilizadas|',
                __('Soluciones utilizadas', 'ese-latam'),
                __('Frase grande sobre el carrusel de productos, en el fondo azul.', 'ese-latam')
            ),
            [
                $campo('textarea', 'caso_sol_desc', __('Bajada de la sección Soluciones utilizadas', 'ese-latam'), [
                    'instructions' => __('Párrafo corto bajo el titular. Una o dos frases. Vacía: no se muestra.', 'ese-latam'),
                    'rows'         => 2,
                ]),
                $campo('relationship', 'caso_productos', __('Productos usados en el caso', 'ese-latam'), [
                    'instructions'  => __('Las tarjetas del carrusel: los productos que se usaron de verdad, en el orden en que se muestran. Vacío: se muestran hasta 8 productos del catálogo, en su orden.', 'ese-latam'),
                    'post_type'     => ['producto'],
                    'filters'       => ['search'],
                    'return_format' => 'id',
                ]),
            ],

            /* ---------------- Relacionados ---------------- */
            [$tab('caso_relacionados', __('Casos relacionados', 'ese-latam'))],
            $encabezado(
                'caso_rel',
                __('Casos de éxito', 'ese-latam'),
                "Casos de éxito\n|relacionados|",
                __('Casos relacionados', 'ese-latam'),
                __('Frase grande a la izquierda, sobre las tarjetas de otros casos.', 'ese-latam')
            ),
            [
                $campo('textarea', 'caso_rel_desc', __('Bajada de la sección Casos relacionados', 'ese-latam'), [
                    'instructions' => __('Párrafo corto bajo el titular. Una o dos frases. Vacía: no se muestra. Las tarjetas son los cuatro casos más recientes, sin este; si no hay otros, la sección no se muestra.', 'ese-latam'),
                    'rows'         => 2,
                ]),
                $campo('link', 'caso_rel_enlace', __('Enlace a todos los casos', 'ese-latam'), [
                    'instructions' => __('Enlace con flecha a la derecha del titular. Escribe el texto; si no eliges página, lleva al listado de casos de éxito. Vacío o sin texto: la sección se muestra sin enlace.', 'ese-latam'),
                ]),
            ]
        ),
    ]);
});
