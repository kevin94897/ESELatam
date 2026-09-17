<?php
/**
 * Campos de la página "Impacto / Residuos inteligentes" (Figma 3824-3688).
 *
 * Solo lo que es de esta pantalla: el hero, el mosaico "decisión humana", la
 * franja de frases y los encabezados con los que presenta a las secciones
 * compartidas (alianzas y casos). Lo que se repite en otras páginas no está
 * acá: el selector de Residuos inteligentes se edita en ESE Latam → Residuos,
 * los logos de aliados son el módulo "Aliados" y el cierre de Contactemos
 * tiene su propio override en la pestaña "Secciones compartidas".
 *
 * El mosaico no es un repetidor: cada tarjeta ocupa una celda concreta de la
 * grilla bento (ver .imp-stats__mosaic en main.css) y tiene una forma
 * distinta, así que cada una lleva sus campos.
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

    $pagina = get_page_by_path('impacto');
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

    acf_add_local_field_group([
        'key'      => 'group_pagina_impacto',
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
            $tab('imp_hero', __('Hero', 'ese-latam')),
            $campo('text', 'impacto_hero_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('Impacto', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('textarea', 'impacto_hero_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => 'Residuos |inteligentes|',
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('textarea', 'impacto_hero_desc', __('Bajada', 'ese-latam'), ['rows' => 3]),
            $foto('impacto_hero_imagen', __('Foto de fondo', 'ese-latam')),
            $campo('link', 'impacto_hero_cta', __('Botón', 'ese-latam'), [
                'instructions' => __('Sin enlace, el hero se muestra sin botón.', 'ese-latam'),
            ]),

            /* ---------------- Mosaico ---------------- */
            $tab('imp_mosaico', __('Respaldo comprobado', 'ese-latam')),
            $campo('text', 'impacto_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('Respaldo comprobado', 'ese-latam'),
                'wrapper'     => ['width' => '40'],
            ]),
            $campo('textarea', 'impacto_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => "Gestionar residuos es una\n|decisión humana|",
                'wrapper'      => ['width' => '60'],
            ]),
            $foto('impacto_cielo', __('Cielo del fondo', 'ese-latam'), [
                'instructions' => __('La foto con parallax detrás de todo el bloque.', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),

            $campo('message', '', __('Las seis celdas', 'ese-latam'), [
                'key'      => 'field_impmos_msg',
                'message'  => __('El mosaico tiene una composición fija: la marca arriba a la izquierda, dos cifras apiladas, la tarjeta grande con foto, una foto con pie y el alcance. Cada celda se edita abajo; si dejas una vacía, se muestra sin ese elemento.', 'ese-latam'),
                'esc_html' => 0,
            ]),

            $foto('impacto_marca_fondo', __('Marca — fondo', 'ese-latam'), ['wrapper' => ['width' => '50']]),
            $foto('impacto_marca_logo', __('Marca — logo', 'ese-latam'), [
                'instructions' => __('El símbolo que flota sobre el fondo.', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),

            $campo('repeater', 'impacto_cifras', __('Cifras', 'ese-latam'), [
                'instructions' => __('Las dos tarjetas de número. El contador anima hasta la cifra al entrar en pantalla.', 'ese-latam'),
                'layout'       => 'table',
                'max'          => 2,
                'button_label' => __('Añadir cifra', 'ese-latam'),
                'sub_fields'   => [
                    $campo('number', 'numero', __('Número', 'ese-latam'), [
                        'key'      => 'field_impcifra_num',
                        'required' => 1,
                        'min'      => 0,
                    ]),
                    $campo('text', 'sufijo', __('Sufijo', 'ese-latam'), [
                        'key'         => 'field_impcifra_suf',
                        'placeholder' => '+',
                        'maxlength'   => 3,
                    ]),
                    $campo('text', 'etiqueta', __('Etiqueta', 'ese-latam'), [
                        'key'       => 'field_impcifra_lbl',
                        'required'  => 1,
                        'maxlength' => 40,
                    ]),
                ],
            ]),

            $campo('text', 'impacto_principal_titulo', __('Tarjeta grande — título', 'ese-latam'), [
                'wrapper' => ['width' => '50'],
            ]),
            $foto('impacto_principal_imagen', __('Tarjeta grande — foto', 'ese-latam'), ['wrapper' => ['width' => '50']]),
            $campo('textarea', 'impacto_principal_desc', __('Tarjeta grande — descripción', 'ese-latam'), ['rows' => 3]),

            $campo('text', 'impacto_foto_texto', __('Foto con pie — texto', 'ese-latam'), [
                'wrapper' => ['width' => '50'],
            ]),
            $foto('impacto_foto_imagen', __('Foto con pie — imagen', 'ese-latam'), ['wrapper' => ['width' => '50']]),

            $campo('text', 'impacto_alcance_kicker', __('Alcance — antetítulo', 'ese-latam'), [
                'placeholder' => __('Alcance Latam', 'ese-latam'),
                'wrapper'     => ['width' => '34'],
            ]),
            $campo('number', 'impacto_alcance_numero', __('Alcance — número', 'ese-latam'), [
                'instructions' => __('Vacío: se cuentan los países cargados en Distribuidores.', 'ese-latam'),
                'min'          => 0,
                'wrapper'      => ['width' => '33'],
            ]),
            $campo('text', 'impacto_alcance_etiqueta', __('Alcance — etiqueta', 'ese-latam'), [
                'placeholder' => __('Países', 'ese-latam'),
                'wrapper'     => ['width' => '33'],
            ]),

            /* ---------------- Franja ---------------- */
            $tab('imp_franja', __('Franja de frases', 'ese-latam')),
            $campo('repeater', 'impacto_frases', __('Frases', 'ese-latam'), [
                'instructions' => __('La cinta que se desplaza bajo el mosaico. Sin frases, la cinta no se muestra.', 'ese-latam'),
                'layout'       => 'table',
                'button_label' => __('Añadir frase', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'texto', __('Frase', 'ese-latam'), [
                        'key'       => 'field_impfrase_txt',
                        'required'  => 1,
                        'maxlength' => 60,
                    ]),
                ],
            ]),

            /* ---------------- Alianzas ---------------- */
            $tab('imp_alianzas', __('Alianzas', 'ese-latam')),
            $campo('message', '', __('Los logos', 'ese-latam'), [
                'key'      => 'field_impali_msg',
                'message'  => __('Los logos salen del módulo <strong>Aliados</strong>. Acá solo se define cómo se presenta la sección en esta página.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('text', 'impacto_aliados_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('Alianzas', 'ese-latam'),
                'wrapper'     => ['width' => '40'],
            ]),
            $campo('textarea', 'impacto_aliados_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => "Unidos por\n|las mejores alianzas|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('textarea', 'impacto_aliados_desc', __('Bajada', 'ese-latam'), ['rows' => 2]),
            $foto('impacto_aliados_isla', __('Isla flotante', 'ese-latam'), [
                'instructions' => __('La isla con inclinación al mover el mouse. Vacía: no se muestra.', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),

            /* ---------------- Casos ---------------- */
            $tab('imp_casos', __('Casos reales', 'ese-latam')),
            $campo('message', '', __('Las tarjetas', 'ese-latam'), [
                'key'      => 'field_impcasos_msg',
                'message'  => __('Las tarjetas son los cuatro casos más recientes del módulo <strong>Casos de éxito</strong>.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('text', 'impacto_casos_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('Casos reales', 'ese-latam'),
                'wrapper'     => ['width' => '40'],
            ]),
            $campo('textarea', 'impacto_casos_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => "Casos reales en\n|gestión urbana|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('textarea', 'impacto_casos_desc', __('Bajada', 'ese-latam'), ['rows' => 2]),
            $campo('link', 'impacto_casos_enlace', __('Enlace', 'ese-latam'), [
                'instructions' => __('Vacío: la sección se muestra sin enlace.', 'ese-latam'),
            ]),
        ],
    ]);
});
