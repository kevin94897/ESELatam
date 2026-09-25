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
            $campo('text', 'legal_kicker', __('Antetítulo sobre el titular del hero', 'ese-latam'), [
                'instructions' => __('Palabra corta en mayúsculas, centrada encima del titular. Hasta 30 caracteres. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Legal', 'ese-latam'),
                'wrapper'      => ['width' => '40'],
            ]),
            $campo('textarea', 'legal_titulo', __('Titular principal del hero', 'ese-latam'), [
                'instructions' => __('Frase grande en mayúsculas, centrada en el hero oscuro.', 'ese-latam')
                    . ' ' . ese_latam_ayuda_titulo()
                    . ' ' . __('Vacío: no se muestra.', 'ese-latam'),
                'rows'         => 2,
                'placeholder'  => "Términos y\n|condiciones|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('textarea', 'legal_desc', __('Bajada bajo el titular del hero', 'ese-latam'), [
                'instructions' => __('Párrafo corto centrado bajo el titular. Una o dos frases, hasta 200 caracteres. Vacía: no se muestra.', 'ese-latam'),
                'rows'         => 2,
            ]),
            $campo('image', 'legal_imagen', __('Foto de fondo del hero', 'ese-latam'), [
                'instructions'  => __('Opcional. Ocupa todo el ancho detrás del titular. WebP o JPG · 1920×720 px (8:3) · máx. 300 KB. Se muestra oscurecida y se recorta al centro. Vacía: el hero queda en negro liso.', 'ese-latam'),
                'return_format' => 'url',
                'preview_size'  => 'medium',
                'wrapper'       => ['width' => '50'],
            ]),

            /* ---------------- Contenido ---------------- */
            $tab('legal_contenido', __('Contenido', 'ese-latam')),
            $campo('text', 'legal_indice_titulo', __('Título del índice lateral', 'ese-latam'), [
                'instructions' => __('Rótulo corto en mayúsculas sobre el índice de la izquierda, que sigue al scroll. Hasta 30 caracteres. Vacío: el índice se muestra sin título.', 'ese-latam'),
                'placeholder'  => __('Contenido', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('text', 'legal_actualizado', __('Fecha de última actualización', 'ese-latam'), [
                'instructions' => __('Texto al pie del índice, bajo una línea, como “Actualizado en septiembre de 2026”. Escríbelo completo. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Actualizado en septiembre de 2026', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('repeater', 'legal_secciones', __('Secciones del texto legal', 'ese-latam'), [
                'instructions' => __('Cada sección es un punto del índice y un bloque del texto, numerados solos en este orden. Sin secciones, la página muestra solo el hero y el cierre de Contactemos.', 'ese-latam'),
                'layout'       => 'block',
                'button_label' => __('Añadir sección', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'titulo', __('Título de la sección', 'ese-latam'), [
                        'key'          => 'field_legalsec_titulo',
                        'instructions' => __('Encabeza el bloque y es lo que se lee en el índice; también arma el enlace de la sección. Hasta 60 caracteres. Obligatorio: la fila sin título no se muestra.', 'ese-latam'),
                        'required'     => 1,
                    ]),
                    $campo('wysiwyg', 'texto', __('Texto de la sección', 'ese-latam'), [
                        'key'          => 'field_legalsec_texto',
                        'instructions' => __('Cuerpo del bloque bajo su título. Admite párrafos, listas, negritas y enlaces. Vacío: se muestra solo el título.', 'ese-latam'),
                        'tabs'         => 'all',
                        'toolbar'      => 'full',
                        'media_upload' => 0,
                    ]),
                ],
            ]),
        ],
    ]);
});
