<?php
/**
 * Campos del módulo "Sectores" y de su página madre.
 *
 * Dos grupos con responsabilidades separadas:
 *
 *   - "Ficha del sector" (post_type == sector): todo lo que describe a UN
 *     sector. Parte se muestra fuera de su página —el resumen y la foto van
 *     al slider de la portada, al megamenú y a la grilla— y el resto arma su
 *     single en `/sectores/{slug}/` (single-sector.php).
 *
 *   - "Página Soluciones por sector" (solo la página madre): lo que es de esa
 *     pantalla y de ninguna otra, el hero y los pasos del proceso. La grilla
 *     de sectores no está acá: sale del módulo.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Subcampos de "Dolores" y "Alivios": los dos bloques del comparador de
 * desafíos tienen exactamente la misma forma.
 *
 * @return list<array<string, mixed>>
 */
function ese_latam_campos_desafio(string $prefijo): array {
    $campo = static fn (string $type, string $name, string $label, array $args = []): array =>
        ese_latam_campo_def($type, $name, $label, array_merge(['key' => 'field_' . $prefijo . '_' . $name], $args));

    return [
        $campo('text', 'titulo', __('Título', 'ese-latam'), ['wrapper' => ['width' => '50']]),
        $campo('text', 'sub', __('Línea secundaria', 'ese-latam'), ['wrapper' => ['width' => '50']]),
        $campo('textarea', 'descripcion', __('Descripción', 'ese-latam'), ['rows' => 2]),
        $campo('image', 'imagen', __('Foto', 'ese-latam'), [
            'instructions'  => __('Opcional: solo algunas tarjetas del comparador llevan foto.', 'ese-latam'),
            'return_format' => 'url',
            'preview_size'  => 'thumbnail',
        ]),
    ];
}

add_action('acf/init', static function (): void {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    $campo = 'ese_latam_campo_def';
    $tab   = 'ese_latam_campo_tab';

    /* ---------------------------------------------------------------------
     * Ficha de cada sector
     * ------------------------------------------------------------------ */

    acf_add_local_field_group([
        'key'      => 'group_sector',
        'title'    => __('Ficha del sector', 'ese-latam'),
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'sector']]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => __('El nombre y la foto destacada del sector se usan además en el slider de la portada, en el menú y en la grilla de “Soluciones por sector”.', 'ese-latam'),
        'fields'                => [

            // ---------- Cómo se presenta fuera de su página ----------
            $tab('sector_resumen', __('Resumen', 'ese-latam')),
            $campo('message', '', __('Dónde se usa', 'ese-latam'), [
                'key'      => 'field_sec_msg',
                'message'  => __('Este bloque es la cara del sector en el resto de la web: el slider de la portada, el submenú <strong>Sectores</strong> y la grilla de “Soluciones por sector”. La foto sale de la <strong>imagen destacada</strong> de la derecha.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('textarea', 'resumen', __('Resumen', 'ese-latam'), [
                'instructions' => __('Una o dos líneas. Es lo que se lee en la tarjeta del slider y en el menú.', 'ese-latam'),
                'rows'         => 2,
            ]),
            $campo('textarea', 'grilla_desc', __('Bajada en la grilla', 'ese-latam'), [
                'instructions' => __('La grilla de “Soluciones por sector” tiene tarjetas más grandes y admite un texto más largo. Vacío: se usa el resumen.', 'ese-latam'),
                'rows'         => 2,
            ]),
            $campo('image', 'grilla_imagen', __('Foto en la grilla', 'ese-latam'), [
                'instructions'  => __('Vacío: se usa la imagen destacada.', 'ese-latam'),
                'return_format' => 'url',
                'preview_size'  => 'medium',
            ]),

            // ---------- Su propia página ----------
            $tab('sector_hero', __('Hero de su página', 'ese-latam')),
            $campo('text', 'kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('Solución para', 'ese-latam'),
            ]),
            $campo('textarea', 'hero_desc', __('Bajada', 'ese-latam'), [
                'instructions' => __('Vacío: se usa el resumen.', 'ese-latam'),
                'rows'         => 2,
            ]),
            $campo('image', 'hero_imagen', __('Foto de fondo', 'ese-latam'), [
                'instructions'  => __('Vacío: se usa la imagen destacada.', 'ese-latam'),
                'return_format' => 'url',
                'preview_size'  => 'medium',
            ]),
            $campo('link', 'hero_cta', __('Botón', 'ese-latam'), [
                'instructions' => __('Sin enlace, el hero se muestra sin botón.', 'ese-latam'),
            ]),

            // ---------- Desafíos ----------
            $tab('sector_desafios', __('Desafíos', 'ese-latam')),
            $campo('text', 'desafios_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('Desafíos', 'ese-latam'),
                'wrapper'     => ['width' => '40'],
            ]),
            $campo('textarea', 'desafios_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => "Desafíos en la\n|gestión urbana|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('wysiwyg', 'desafios_desc', __('Bajada de la sección', 'ese-latam'), [
                'tabs'         => 'visual',
                'toolbar'      => 'basic',
                'media_upload' => 0,
            ]),
            $campo('text', 'desafios_label_dolores', __('Etiqueta del primer panel', 'ese-latam'), [
                'placeholder' => __('Dolores y brechas', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('text', 'desafios_label_alivios', __('Etiqueta del segundo panel', 'ese-latam'), [
                'placeholder' => __('Alivio ESE Latam', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('repeater', 'dolores', __('Dolores y brechas', 'ese-latam'), [
                'instructions' => __('El estado “problema” del comparador.', 'ese-latam'),
                'layout'       => 'block',
                'button_label' => __('Añadir dolor', 'ese-latam'),
                'sub_fields'   => ese_latam_campos_desafio('dolor'),
            ]),
            $campo('repeater', 'alivios', __('Cómo lo resolvemos', 'ese-latam'), [
                'instructions' => __('El estado “solución”: conviene que responda punto por punto a los dolores.', 'ese-latam'),
                'layout'       => 'block',
                'button_label' => __('Añadir solución', 'ese-latam'),
                'sub_fields'   => ese_latam_campos_desafio('alivio'),
            ]),

            // ---------- Criterio ----------
            $tab('sector_criterio', __('Criterio', 'ese-latam')),
            $campo('text', 'criterio_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('ESE Latam', 'ese-latam'),
                'wrapper'     => ['width' => '40'],
            ]),
            $campo('textarea', 'criterio_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => "Nuestro criterio\n|de adaptabilidad|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('wysiwyg', 'cita', __('Frase destacada', 'ese-latam'), [
                'instructions' => __('Se imprime entre comillas. Se puede resaltar en negrita el tramo en color.', 'ese-latam'),
                'tabs'         => 'visual',
                'toolbar'      => 'basic',
                'media_upload' => 0,
            ]),
            $campo('textarea', 'criterio', __('Texto del criterio', 'ese-latam'), ['rows' => 4]),
            $campo('text', 'criterio_firma', __('Firma', 'ese-latam'), [
                'instructions' => __('Quién respalda la frase, junto al icono de marca.', 'ese-latam'),
                'placeholder'  => __('ESE Latam', 'ese-latam'),
                'wrapper'      => ['width' => '40'],
            ]),
            $campo('text', 'criterio_firma_sub', __('Firma — segunda línea', 'ese-latam'), [
                'placeholder' => __('Equipo ejecutivo de desarrollo sostenible', 'ese-latam'),
                'wrapper'     => ['width' => '60'],
            ]),
            $campo('image', 'criterio_producto', __('Foto del contenedor', 'ese-latam'), [
                'instructions'  => __('El producto que flota a la derecha, con sus etiquetas alrededor. PNG con fondo transparente.', 'ese-latam'),
                'return_format' => 'url',
                'preview_size'  => 'medium',
                'mime_types'    => 'png,webp',
            ]),
            $campo('repeater', 'criterio_callouts', __('Etiquetas del contenedor', 'ese-latam'), [
                'instructions' => __('Las tres llamadas con línea y punto. La posición decide de qué lado sale cada una.', 'ese-latam'),
                'layout'       => 'table',
                'max'          => 3,
                'button_label' => __('Añadir etiqueta', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'label', __('Texto', 'ese-latam'), [
                        'key'       => 'field_callout_label',
                        'required'  => 1,
                        'maxlength' => 24,
                    ]),
                    $campo('select', 'pos', __('Posición', 'ese-latam'), [
                        'key'           => 'field_callout_pos',
                        'required'      => 1,
                        'choices'       => [
                            'left-top'    => __('Izquierda arriba', 'ese-latam'),
                            'left-bottom' => __('Izquierda abajo', 'ese-latam'),
                            'right'       => __('Derecha', 'ese-latam'),
                        ],
                        'default_value' => 'left-top',
                    ]),
                ],
            ]),

            // ---------- Cierre ----------
            $tab('sector_cierre', __('Cierre', 'ese-latam')),
            $campo('text', 'marquee', __('Frase del marquee', 'ese-latam'), [
                'instructions' => __('La cinta que separa el criterio de las soluciones recomendadas. Vacía: no se muestra.', 'ese-latam'),
                'placeholder'  => __('soluciones recomendadas', 'ese-latam'),
                'maxlength'    => 60,
            ]),
            $campo('message', '', __('Casos reales', 'ese-latam'), [
                'key'      => 'field_seccasos_msg',
                'message'  => __('Las tarjetas son los cuatro casos más recientes del módulo <strong>Casos de éxito</strong>. Acá solo se define cómo se presenta la sección.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('text', 'casos_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('Casos reales', 'ese-latam'),
                'wrapper'     => ['width' => '40'],
            ]),
            $campo('textarea', 'casos_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => "Casos reales en\n|gestión urbana|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('textarea', 'casos_desc', __('Bajada', 'ese-latam'), ['rows' => 2]),
            $campo('link', 'casos_enlace', __('Enlace', 'ese-latam'), [
                'instructions' => __('Vacío: la sección se muestra sin enlace.', 'ese-latam'),
            ]),
        ],
    ]);

    /* ---------------------------------------------------------------------
     * Página madre "Soluciones por sector"
     * ------------------------------------------------------------------ */

    // Por ID y no por plantilla: WordPress aplica page-sectores.php por el
    // slug de la página, así que no hay ninguna plantilla asignada que mirar.
    $pagina = get_page_by_path(ESE_LATAM_SECTORES_BASE);
    if (! $pagina instanceof WP_Post) {
        return;
    }

    acf_add_local_field_group([
        'key'      => 'group_pagina_sectores',
        'title'    => __('Contenido de la página', 'ese-latam'),
        'location' => [[['param' => 'page', 'operator' => '==', 'value' => (string) $pagina->ID]]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => __('Solo lo de esta pantalla. La grilla de sectores sale del módulo Sectores.', 'ese-latam'),
        'fields'                => [

            $tab('pag_sec_hero', __('Hero', 'ese-latam')),
            $campo('textarea', 'sectores_hero_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => "Soluciones por\n|sector|",
            ]),
            $campo('wysiwyg', 'sectores_hero_desc', __('Bajada', 'ese-latam'), [
                'tabs'         => 'visual',
                'toolbar'      => 'basic',
                'media_upload' => 0,
            ]),

            $tab('pag_sec_proceso', __('Proceso', 'ese-latam')),
            $campo('text', 'sectores_proceso_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('Cómo trabajamos', 'ese-latam'),
            ]),
            $campo('repeater', 'sectores_proceso', __('Pasos', 'ese-latam'), [
                'instructions' => __('Cada paso es una pestaña del bloque “Entendemos tu operación”. El número lo pone la plantilla según el orden.', 'ese-latam'),
                'layout'       => 'block',
                'button_label' => __('Añadir paso', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'tab', __('Texto de la pestaña', 'ese-latam'), [
                        'key' => 'field_paso_tab',
                    ]),
                    // Dos campos y no la convención |así|: proceso-steps.ts
                    // reescribe cada mitad del titular al cambiar de paso, así
                    // que necesitan seguir siendo dos valores separados.
                    $campo('text', 'titulo', __('Titular', 'ese-latam'), [
                        'key'     => 'field_paso_titulo',
                        'wrapper' => ['width' => '50'],
                    ]),
                    $campo('text', 'titulo_destacado', __('Titular — tramo destacado', 'ese-latam'), [
                        'key'     => 'field_paso_destacado',
                        'wrapper' => ['width' => '50'],
                    ]),
                    $campo('textarea', 'descripcion', __('Descripción', 'ese-latam'), [
                        'key'  => 'field_paso_desc',
                        'rows' => 3,
                    ]),
                    $campo('image', 'imagen', __('Foto', 'ese-latam'), [
                        'key'           => 'field_paso_img',
                        'return_format' => 'url',
                        'preview_size'  => 'medium',
                    ]),
                ],
            ]),
        ],
    ]);
});
