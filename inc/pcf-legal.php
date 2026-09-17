<?php
/**
 * Campos de las páginas legales (Términos y condiciones, Políticas de
 * privacidad y cualquier otra que use la plantilla "Legal").
 *
 * La ubicación es la PLANTILLA y no el ID de página: así una legal nueva
 * —o una copia hecha con "Duplicar"— trae sus campos sin tocar código, que
 * es justo lo que no pasa con los grupos atados a un ID.
 *
 * El índice no es un campo: se arma solo con los títulos de las secciones,
 * para que no puedan quedar desincronizados.
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
        'key'      => 'group_pagina_legal',
        'title'    => __('Contenido de la página', 'ese-latam'),
        'location' => [[['param' => 'page_template', 'operator' => '==', 'value' => 'page-legal.php']]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'fields'                => [

            /* ---------------- Hero ---------------- */
            $tab('legal_hero', __('Hero', 'ese-latam')),
            $campo('text', 'legal_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('Legal', 'ese-latam'),
                'wrapper'     => ['width' => '40'],
            ]),
            $campo('textarea', 'legal_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => "Términos y\n|condiciones|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('textarea', 'legal_desc', __('Bajada', 'ese-latam'), ['rows' => 2]),
            $campo('image', 'legal_imagen', __('Foto de fondo', 'ese-latam'), [
                'instructions'  => __('Opcional. Vacía: el hero queda en negro liso.', 'ese-latam'),
                'return_format' => 'url',
                'preview_size'  => 'medium',
                'wrapper'       => ['width' => '50'],
            ]),

            /* ---------------- Contenido ---------------- */
            $tab('legal_contenido', __('Contenido', 'ese-latam')),
            $campo('text', 'legal_indice_titulo', __('Título del índice', 'ese-latam'), [
                'instructions' => __('Vacío: el índice se muestra sin título.', 'ese-latam'),
                'placeholder'  => __('Contenido', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('text', 'legal_actualizado', __('Última actualización', 'ese-latam'), [
                'instructions' => __('Se muestra al pie del índice. Vacía: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Actualizado en septiembre de 2026', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('repeater', 'legal_secciones', __('Secciones', 'ese-latam'), [
                'instructions' => __('Cada sección es un punto del índice y un bloque del texto, numerados en este orden. Sin secciones, la página se muestra solo con el hero.', 'ese-latam'),
                'layout'       => 'block',
                'button_label' => __('Añadir sección', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'titulo', __('Título', 'ese-latam'), [
                        'key'          => 'field_legalsec_titulo',
                        'instructions' => __('Es lo que se lee en el índice y lo que arma el enlace de la sección.', 'ese-latam'),
                        'required'     => 1,
                    ]),
                    $campo('wysiwyg', 'texto', __('Texto', 'ese-latam'), [
                        'key'          => 'field_legalsec_texto',
                        'instructions' => __('Admite párrafos, listas, negritas y enlaces.', 'ese-latam'),
                        'tabs'         => 'all',
                        'toolbar'      => 'full',
                        'media_upload' => 0,
                    ]),
                ],
            ]),
        ],
    ]);
});
