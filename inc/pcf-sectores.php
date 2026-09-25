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
        $campo('text', 'titulo', __('Título de la tarjeta', 'ese-latam'), [
            'instructions' => __('Texto en negrita al pie de la tarjeta. Pocas palabras. Vacío: la tarjeta no se muestra.', 'ese-latam'),
            'wrapper'      => ['width' => '50'],
        ]),
        $campo('text', 'sub', __('Línea bajo el título de la tarjeta', 'ese-latam'), [
            'instructions' => __('Frase corta en negrita bajo el título. Vacía: la tarjeta queda sin esa línea.', 'ese-latam'),
            'wrapper'      => ['width' => '50'],
        ]),
        $campo('textarea', 'descripcion', __('Descripción de la tarjeta', 'ese-latam'), [
            'instructions' => __('Una o dos frases al pie de la tarjeta. Vacía: la tarjeta queda sin descripción.', 'ese-latam'),
            'rows'         => 2,
        ]),
        $campo('image', 'imagen', __('Foto de fondo de la tarjeta', 'ese-latam'), [
            'instructions'  => __('Opcional: solo algunas tarjetas del comparador llevan foto, bajo una sombra oscura. WebP o JPG · 1200×1200 px (1:1) · máx. 250 KB. Se recorta al centro: la 2.ª tarjeta es alta y la 6.ª ancha. Vacía: la tarjeta queda gris clara.', 'ese-latam'),
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
        'description'           => __('El nombre del sector es el titular del hero de su página (las dos últimas palabras van en color) y, con la foto destacada, se usa además en el slider de la portada, en el menú y en la grilla de “Soluciones por sector”.', 'ese-latam'),
        'fields'                => [

            // ---------- Cómo se presenta fuera de su página ----------
            $tab('sector_resumen', __('Resumen', 'ese-latam')),
            $campo('message', '', __('Dónde se usa este resumen', 'ese-latam'), [
                'key'      => 'field_sec_msg',
                'message'  => __('Este bloque es la cara del sector en el resto de la web: el slider de la portada, el submenú <strong>Sectores</strong> y la grilla de “Soluciones por sector”. La foto sale de la <strong>imagen destacada</strong> de la derecha.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('textarea', 'resumen', __('Resumen del sector en tarjetas', 'ese-latam'), [
                'instructions' => __('Una o dos líneas. Se lee en la tarjeta del slider de la portada y en el submenú Sectores; también sirve de respaldo para la bajada de la grilla y del hero. Vacío: esas tarjetas quedan solo con el nombre.', 'ese-latam'),
                'rows'         => 2,
            ]),
            $campo('textarea', 'grilla_desc', __('Bajada de la tarjeta en la grilla', 'ese-latam'), [
                'instructions' => __('Texto bajo el nombre en la tarjeta de la grilla de “Soluciones por sector”. Las tarjetas son más grandes y admiten dos o tres líneas. Vacío: se usa el resumen.', 'ese-latam'),
                'rows'         => 2,
            ]),
            $campo('image', 'grilla_imagen', __('Foto de la tarjeta en la grilla', 'ese-latam'), [
                'instructions'  => __('Fondo de la tarjeta del sector en la grilla de “Soluciones por sector”, bajo una sombra oscura. WebP o JPG · 1600×1200 px (4:3) · máx. 300 KB. Se recorta al centro: según su lugar, la tarjeta es ancha, alta o cuadrada. Vacía: se usa la imagen destacada.', 'ese-latam'),
                'return_format' => 'url',
                'preview_size'  => 'medium',
            ]),

            // ---------- Su propia página ----------
            $tab('sector_hero', __('Hero de su página', 'ese-latam')),
            $campo('text', 'kicker', __('Antetítulo sobre el titular del hero', 'ese-latam'), [
                'instructions' => __('Frase corta sobre el nombre del sector, por ejemplo “Solución para”. Hasta 30 caracteres. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Solución para', 'ese-latam'),
            ]),
            $campo('textarea', 'hero_desc', __('Bajada del hero del sector', 'ese-latam'), [
                'instructions' => __('Párrafo corto a la derecha del titular, abajo en el hero. Una o dos frases. Vacía: se usa el resumen.', 'ese-latam'),
                'rows'         => 2,
            ]),
            $campo('image', 'hero_imagen', __('Foto de fondo del hero del sector', 'ese-latam'), [
                'instructions'  => __('Ocupa toda la pantalla detrás del titular y se muestra muy oscurecida. WebP o JPG · 1920×1080 px (16:9) · máx. 400 KB. Se recorta al centro. Vacía: se usa la imagen destacada del sector.', 'ese-latam'),
                'return_format' => 'url',
                'preview_size'  => 'medium',
            ]),
            $campo('link', 'hero_cta', __('Botón del hero (texto y enlace)', 'ese-latam'), [
                'instructions' => __('Botón bajo la bajada del hero. Escribe el texto del botón y la página a la que lleva. Vacío o sin texto: el hero se muestra sin botón.', 'ese-latam'),
            ]),

            // ---------- Desafíos ----------
            $tab('sector_desafios', __('Desafíos', 'ese-latam')),
            $campo('text', 'desafios_kicker', __('Antetítulo de la sección Desafíos', 'ese-latam'), [
                'instructions' => __('Palabra corta tras la barra, sobre el titular de la sección. Hasta 30 caracteres. Vacío: queda solo la barra “/”.', 'ese-latam'),
                'placeholder'  => __('Desafíos', 'ese-latam'),
                'wrapper'      => ['width' => '40'],
            ]),
            $campo('textarea', 'desafios_titulo', __('Titular de la sección Desafíos', 'ese-latam'), [
                'instructions' => __('Frase grande a la izquierda, sobre el comparador de tarjetas.', 'ese-latam') . ' ' . ese_latam_ayuda_titulo() . ' ' . __('Vacío: la sección queda sin titular.', 'ese-latam'),
                'rows'         => 2,
                'placeholder'  => "Desafíos en la\n|gestión urbana|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('wysiwyg', 'desafios_desc', __('Bajada de la sección Desafíos', 'ese-latam'), [
                'instructions' => __('Párrafo a la derecha del titular. Una o dos frases. El tramo en negrita se destaca. Vacía: no se muestra.', 'ese-latam'),
                'tabs'         => 'visual',
                'toolbar'      => 'basic',
                'media_upload' => 0,
            ]),

            $tab('sector_desafios_tarjetas', __('Desafíos — tarjetas', 'ese-latam')),
            $campo('text', 'desafios_label_dolores', __('Texto del botón del primer panel', 'ese-latam'), [
                'instructions' => __('Botón que muestra las tarjetas de dolores, por ejemplo “Dolores y brechas”. Los botones solo aparecen si hay dolores y soluciones. Vacío: el botón queda sin texto.', 'ese-latam'),
                'placeholder'  => __('Dolores y brechas', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('text', 'desafios_label_alivios', __('Texto del botón del segundo panel', 'ese-latam'), [
                'instructions' => __('Botón que muestra las tarjetas de soluciones, por ejemplo “Alivio ESE Latam”. Vacío: el botón queda sin texto.', 'ese-latam'),
                'placeholder'  => __('Alivio ESE Latam', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('repeater', 'dolores', __('Tarjetas de dolores y brechas', 'ese-latam'), [
                'instructions' => __('El estado “problema” del comparador, en una grilla de hasta 6 tarjetas: el orden decide su lugar. Vacío: la sección Desafíos no se muestra.', 'ese-latam'),
                'layout'       => 'block',
                'button_label' => __('Añadir dolor', 'ese-latam'),
                'sub_fields'   => ese_latam_campos_desafio('dolor'),
            ]),
            $campo('repeater', 'alivios', __('Tarjetas de cómo lo resolvemos', 'ese-latam'), [
                'instructions' => __('El estado “solución”, hasta 6 tarjetas: conviene que responda punto por punto a los dolores. Vacío: solo se ven los dolores, sin botones.', 'ese-latam'),
                'layout'       => 'block',
                'button_label' => __('Añadir solución', 'ese-latam'),
                'sub_fields'   => ese_latam_campos_desafio('alivio'),
            ]),

            // ---------- Criterio ----------
            $tab('sector_criterio', __('Criterio', 'ese-latam')),
            $campo('text', 'criterio_kicker', __('Antetítulo de la sección Criterio', 'ese-latam'), [
                'instructions' => __('Palabra corta tras la barra, sobre el titular. Hasta 30 caracteres. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('ESE Latam', 'ese-latam'),
                'wrapper'      => ['width' => '40'],
            ]),
            $campo('textarea', 'criterio_titulo', __('Titular de la sección Criterio', 'ese-latam'), [
                'instructions' => __('Frase grande a la izquierda, sobre la frase destacada.', 'ese-latam') . ' ' . ese_latam_ayuda_titulo() . ' ' . __('Vacío: no se muestra.', 'ese-latam'),
                'rows'         => 2,
                'placeholder'  => "Nuestro criterio\n|de adaptabilidad|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('wysiwyg', 'cita', __('Frase destacada entre comillas', 'ese-latam'), [
                'instructions' => __('Se imprime entre comillas bajo el titular. Se puede resaltar en negrita el tramo en color. Una o dos frases. Si esta frase y el texto del criterio quedan vacíos, la sección Criterio no se muestra.', 'ese-latam'),
                'tabs'         => 'visual',
                'toolbar'      => 'basic',
                'media_upload' => 0,
            ]),
            $campo('textarea', 'criterio', __('Texto del criterio bajo la frase', 'ese-latam'), [
                'instructions' => __('Párrafo bajo la frase destacada. Hasta 3 o 4 líneas. Vacío: no se muestra.', 'ese-latam'),
                'rows'         => 4,
            ]),

            $tab('sector_criterio_firma', __('Criterio — firma y contenedor', 'ese-latam')),
            $campo('text', 'criterio_firma', __('Firma de la frase — nombre', 'ese-latam'), [
                'instructions' => __('Quién respalda la frase, junto al ícono de marca y bajo una línea divisoria. Vacía: no se muestra.', 'ese-latam'),
                'placeholder'  => __('ESE Latam', 'ese-latam'),
                'wrapper'      => ['width' => '40'],
            ]),
            $campo('text', 'criterio_firma_sub', __('Firma de la frase — segunda línea', 'ese-latam'), [
                'instructions' => __('Cargo o equipo bajo el nombre de la firma. Vacía: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Equipo ejecutivo de desarrollo sostenible', 'ese-latam'),
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('image', 'criterio_producto', __('Foto del contenedor flotante', 'ese-latam'), [
                'instructions'  => __('El producto que flota a la derecha, con sus etiquetas alrededor. PNG o WebP con fondo transparente · 600 px de ancho, alto libre · máx. 200 KB. Se muestra entero, sin recortar. Vacía: no se muestran ni la foto ni las etiquetas.', 'ese-latam'),
                'return_format' => 'url',
                'preview_size'  => 'medium',
                'mime_types'    => 'png,webp',
            ]),
            $campo('repeater', 'criterio_callouts', __('Etiquetas alrededor del contenedor', 'ese-latam'), [
                'instructions' => __('Las llamadas con línea y punto verde junto a la foto del contenedor. Hasta 3. La posición decide de qué lado sale cada una. Vacío: la foto se ve sin etiquetas.', 'ese-latam'),
                'layout'       => 'table',
                'max'          => 3,
                'button_label' => __('Añadir etiqueta', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'label', __('Texto de la etiqueta', 'ese-latam'), [
                        'key'          => 'field_callout_label',
                        'instructions' => __('Una o dos palabras en mayúsculas: Hormigón, HDPE Premium… Hasta 24 caracteres.', 'ese-latam'),
                        'required'     => 1,
                        'maxlength'    => 24,
                    ]),
                    $campo('select', 'pos', __('Posición de la etiqueta', 'ese-latam'), [
                        'key'           => 'field_callout_pos',
                        'instructions'  => __('Usa una posición distinta para cada etiqueta.', 'ese-latam'),
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
            $campo('text', 'marquee', __('Frase de la cinta en movimiento', 'ese-latam'), [
                'instructions' => __('La cinta que separa el criterio de las soluciones recomendadas. Hasta 60 caracteres. Vacía: no se muestra.', 'ese-latam'),
                'placeholder'  => __('soluciones recomendadas', 'ese-latam'),
                'maxlength'    => 60,
            ]),
            $campo('message', '', __('Dónde se editan los casos reales', 'ese-latam'), [
                'key'      => 'field_seccasos_msg',
                'message'  => __('Las tarjetas son los cuatro casos más recientes del módulo <strong>Casos de éxito</strong>. Acá solo se define cómo se presenta la sección. Sin casos publicados, la sección no se muestra.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('text', 'casos_kicker', __('Antetítulo de la sección Casos reales', 'ese-latam'), [
                'instructions' => __('Palabra corta tras la barra, sobre el titular. Hasta 30 caracteres. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Casos reales', 'ese-latam'),
                'wrapper'      => ['width' => '40'],
            ]),
            $campo('textarea', 'casos_titulo', __('Titular de la sección Casos reales', 'ese-latam'), [
                'instructions' => __('Frase grande a la izquierda, sobre las tarjetas de casos.', 'ese-latam') . ' ' . ese_latam_ayuda_titulo() . ' ' . __('Vacío: no se muestra.', 'ese-latam'),
                'rows'         => 2,
                'placeholder'  => "Casos reales en\n|gestión urbana|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('textarea', 'casos_desc', __('Bajada de la sección Casos reales', 'ese-latam'), [
                'instructions' => __('Párrafo corto bajo el titular. Una o dos frases. Vacía: no se muestra.', 'ese-latam'),
                'rows'         => 2,
            ]),
            $campo('link', 'casos_enlace', __('Enlace a todos los casos', 'ese-latam'), [
                'instructions' => __('Enlace con flecha a la derecha del titular, normalmente al listado de casos de éxito. Escribe el texto y la página. Vacío o sin texto: la sección se muestra sin enlace.', 'ese-latam'),
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
        'description'           => __('Solo lo de esta pantalla. La grilla de sectores sale del módulo Sectores; Certificaciones y Contactemos se editan en sus propios módulos.', 'ese-latam'),
        'fields'                => [

            $tab('pag_sec_hero', __('Hero', 'ese-latam')),
            $campo('textarea', 'sectores_hero_titulo', __('Titular principal del hero', 'ese-latam'), [
                'instructions' => __('Frase grande centrada, bajo la miga de pan.', 'ese-latam') . ' ' . ese_latam_ayuda_titulo() . ' ' . __('Vacío: no se muestra.', 'ese-latam'),
                'rows'         => 2,
                'placeholder'  => "Soluciones por\n|sector|",
            ]),
            $campo('wysiwyg', 'sectores_hero_desc', __('Bajada bajo el titular', 'ese-latam'), [
                'instructions' => __('Párrafo corto centrado bajo el titular. Una o dos frases. Vacía: no se muestra.', 'ese-latam'),
                'tabs'         => 'visual',
                'toolbar'      => 'basic',
                'media_upload' => 0,
            ]),

            $tab('pag_sec_proceso', __('Proceso', 'ese-latam')),
            $campo('repeater', 'sectores_proceso', __('Pasos del proceso', 'ese-latam'), [
                'instructions' => __('Cada paso es una pestaña de la tarjeta “Entendemos tu operación”, sobre una foto de fondo; los pasos rotan solos. El número lo pone la plantilla según el orden. Lo ideal son tres. Vacío: la sección no se muestra.', 'ese-latam'),
                'layout'       => 'block',
                'button_label' => __('Añadir paso', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'tab', __('Texto de la pestaña del paso', 'ese-latam'), [
                        'key'          => 'field_paso_tab',
                        'instructions' => __('Nombre corto del paso, al pie de la tarjeta junto a su número. Vacío: el paso no se muestra.', 'ese-latam'),
                    ]),
                    // Dos campos y no la convención |así|: proceso-steps.ts
                    // reescribe cada mitad del titular al cambiar de paso, así
                    // que necesitan seguir siendo dos valores separados.
                    $campo('text', 'titulo', __('Titular del paso — primer renglón', 'ese-latam'), [
                        'key'          => 'field_paso_titulo',
                        'instructions' => __('Primer renglón del titular de la tarjeta, en letra fina.', 'ese-latam'),
                        'wrapper'      => ['width' => '50'],
                    ]),
                    $campo('text', 'titulo_destacado', __('Titular del paso — tramo destacado', 'ese-latam'), [
                        'key'          => 'field_paso_destacado',
                        'instructions' => __('Segundo renglón del titular, en negrita.', 'ese-latam'),
                        'wrapper'      => ['width' => '50'],
                    ]),
                    $campo('textarea', 'descripcion', __('Descripción del paso', 'ese-latam'), [
                        'key'          => 'field_paso_desc',
                        'instructions' => __('Párrafo bajo el titular de la tarjeta. Dos o tres frases.', 'ese-latam'),
                        'rows'         => 3,
                    ]),
                    $campo('image', 'imagen', __('Foto de fondo del paso', 'ese-latam'), [
                        'key'           => 'field_paso_img',
                        'instructions'  => __('Ocupa todo el ancho detrás de la tarjeta y cambia al elegir el paso. WebP o JPG · 1920×1080 px (16:9) · máx. 400 KB. Se recorta, algo por encima del centro. Vacía: se mantiene la foto del paso anterior; sin foto en el primer paso, el fondo queda azul oscuro.', 'ese-latam'),
                        'return_format' => 'url',
                        'preview_size'  => 'medium',
                    ]),
                ],
            ]),
        ],
    ]);
});
