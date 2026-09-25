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

    // Antetítulo + titular: el par que abre casi todos los bloques. Cada
    // bloque pasa sus etiquetas y ayudas, porque el manual las lee sueltas
    // y tienen que decir a qué sección pertenecen.
    $encabezado = static function (string $prefijo, string $kicker, string $titulo, array $txt) use ($campo): array {
        return [
            $campo('text', $prefijo . '_kicker', $txt['kicker_label'], [
                'instructions' => $txt['kicker_ayuda'],
                'placeholder'  => $kicker,
                'wrapper'      => ['width' => '40'],
            ]),
            $campo('textarea', $prefijo . '_titulo', $txt['titulo_label'], [
                'instructions' => $txt['titulo_ayuda'],
                'rows'         => 2,
                'placeholder'  => $titulo,
                'wrapper'      => ['width' => '60'],
            ]),
        ];
    };

    // Las bajadas admiten negrita: en esta página la negrita se pinta en
    // verde (ver .nos-desc strong / .nos-objetivo__text strong en main.css).
    $bajada = static fn (string $name, string $label, string $ayuda): array =>
        ese_latam_campo_def('wysiwyg', $name, $label, [
            'instructions' => $ayuda,
            'tabs'         => 'visual',
            'toolbar'      => 'basic',
            'media_upload' => 0,
        ]);

    // Frase fija de las ayudas de antetítulo: todos se pintan igual.
    $ayuda_kicker = static fn (string $donde): string =>
        sprintf(
            /* translators: %s: sección en la que aparece el antetítulo. */
            __('Palabra o frase corta en mayúsculas, con una barra delante, encima del titular de %s. Hasta 30 caracteres. Vacío: no se muestra.', 'ese-latam'),
            $donde
        );

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
                $campo('textarea', 'nos_hero_titulo', __('Titular principal del hero', 'ese-latam'), [
                    'instructions' => __('Frase grande en mayúsculas sobre la foto de fondo, abajo a la izquierda.', 'ese-latam')
                        . ' ' . ese_latam_ayuda_titulo()
                        . ' ' . __('Vacío: no se muestra.', 'ese-latam'),
                    'rows'         => 2,
                    'placeholder'  => 'reinventando el |entorno en latam|',
                ]),
                $campo('textarea', 'nos_hero_desc_ini', __('Bajada del hero — texto antes de la cifra', 'ese-latam'), [
                    'instructions' => __('Primera parte del párrafo a la derecha del titular. La frase se parte en tres (texto, cifra y texto) para que la cifra cuente desde cero al entrar en pantalla. Entre las tres partes, hasta 200 caracteres. Vacía: la bajada empieza por la cifra. Si las tres partes quedan vacías, no se muestra la bajada.', 'ese-latam'),
                    'rows'         => 2,
                ]),
                $campo('number', 'nos_hero_cifra', __('Bajada del hero — cifra animada', 'ese-latam'), [
                    'instructions' => __('Número que cuenta desde cero en medio de la bajada. Solo un número entero, sin puntos ni símbolos. Vacía: la bajada se muestra de corrido, sin contador.', 'ese-latam'),
                    'min'          => 0,
                    'wrapper'      => ['width' => '25'],
                ]),
                $campo('textarea', 'nos_hero_desc_fin', __('Bajada del hero — texto después de la cifra', 'ese-latam'), [
                    'instructions' => __('Última parte del párrafo, justo después de la cifra. El espacio entre la cifra y el texto se pone solo. Vacía: la bajada termina en la cifra.', 'ese-latam'),
                    'rows'    => 2,
                    'wrapper' => ['width' => '75'],
                ]),
            ],

            [$tab('nos_hero_fondo', __('Hero — foto y scroll', 'ese-latam'))],
            [
                $foto('nos_hero_imagen', __('Foto de fondo del hero', 'ese-latam'), [
                    'instructions' => __('Ocupa toda la pantalla detrás del titular. WebP o JPG · 1920×1080 px (16:9) · máx. 400 KB. Se muestra muy oscurecida y se recorta al centro. Vacía: el hero queda en negro liso.', 'ese-latam'),
                    'wrapper'      => ['width' => '60'],
                ]),
                $campo('text', 'nos_hero_scroll', __('Indicador de scroll — palabra', 'ese-latam'), [
                    'instructions' => __('Palabra corta junto a la línea vertical al pie del hero, que invita a bajar. Hasta 20 caracteres. Vacía: no se muestra el indicador.', 'ese-latam'),
                    'placeholder'  => __('Scroll', 'ese-latam'),
                    'maxlength'    => 20,
                    'wrapper'      => ['width' => '40'],
                ]),
            ],

            /* ---------------- Construimos para el futuro ---------------- */
            [$tab('nos_const', __('Construimos para el futuro', 'ese-latam'))],
            $encabezado('nos_const', __('Sobre nosotros', 'ese-latam'), "Construimos\n|para el futuro|", [
                'kicker_label' => __('Antetítulo de la sección misión y visión', 'ese-latam'),
                'kicker_ayuda' => $ayuda_kicker(__('misión y visión', 'ese-latam')),
                'titulo_label' => __('Titular de la sección misión y visión', 'ese-latam'),
                'titulo_ayuda' => __('Titular grande arriba a la izquierda, sobre la foto y los textos de misión y visión.', 'ese-latam')
                    . ' ' . ese_latam_ayuda_titulo()
                    . ' ' . __('Vacío: no se muestra.', 'ese-latam'),
            ]),
            [
                $campo('repeater', 'nos_const_bloques', __('Bloques de misión y visión', 'ese-latam'), [
                    'instructions' => __('Textos a la derecha de la foto, uno debajo del otro: normalmente misión y visión. Recomendado: 2 bloques. Sin bloques ni titular, la sección no se muestra.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir bloque', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'title', __('Título del bloque', 'ese-latam'), [
                            'key'          => 'field_nosconst_titulo',
                            'instructions' => __('Palabra en mayúsculas sobre el texto, como “Misión” o “Visión”. Hasta 30 caracteres. Obligatorio: la fila sin título no se muestra.', 'ese-latam'),
                            'required'     => 1,
                        ]),
                        $campo('textarea', 'texto', __('Texto del bloque', 'ese-latam'), [
                            'key'          => 'field_nosconst_texto',
                            'instructions' => __('Párrafo bajo el título del bloque. Hasta 400 caracteres. Vacío: se muestra solo el título.', 'ese-latam'),
                            'rows'         => 4,
                        ]),
                    ],
                ]),
            ],

            [$tab('nos_const_fotos', __('Construimos para el futuro — fotos', 'ese-latam'))],
            [
                $foto('nos_const_foto', __('Foto a la izquierda de misión y visión', 'ese-latam'), [
                    'instructions' => __('Foto con esquinas redondeadas pegada al borde izquierdo, junto a los textos. WebP o JPG · 1100×1100 px (1:1) · máx. 250 KB. Se recorta dejando ver la parte de abajo. Vacía: no se muestra.', 'ese-latam'),
                    'wrapper'      => ['width' => '60'],
                ]),
                $campo('text', 'nos_const_foto_alt', __('Texto alternativo de la foto izquierda', 'ese-latam'), [
                    'instructions' => __('Describe la foto en una frase para lectores de pantalla y buscadores. No se ve en la página. Vacío: la foto queda sin descripción.', 'ese-latam'),
                    'wrapper'      => ['width' => '40'],
                ]),
                $foto('nos_const_cielo', __('Foto del cielo (panel derecho)', 'ese-latam'), [
                    'instructions' => __('Panel vertical con esquinas redondeadas pegado al borde derecho, detrás de la isla. WebP o JPG · 1100×1640 px (2:3) · máx. 300 KB. Se recorta al centro y se oscurece un poco hacia abajo. Vacía: no se muestra el panel del cielo ni la isla.', 'ese-latam'),
                    'wrapper'      => ['width' => '34'],
                ]),
                $foto('nos_const_isla', __('Isla flotante sobre el cielo', 'ese-latam'), [
                    'instructions' => __('Recorte que flota sobre el panel del cielo y se sale hacia la izquierda. PNG o WebP con fondo transparente · 2000 px de ancho · máx. 500 KB. Solo se ve si hay foto del cielo. Vacía: el cielo queda sin isla.', 'ese-latam'),
                    'wrapper'      => ['width' => '33'],
                ]),
                $campo('text', 'nos_const_isla_alt', __('Texto alternativo de la isla', 'ese-latam'), [
                    'instructions' => __('Describe la isla en una frase para lectores de pantalla y buscadores. No se ve en la página. Vacío: la isla queda sin descripción.', 'ese-latam'),
                    'wrapper'      => ['width' => '33'],
                ]),
            ],

            /* ---------------- Objetivos con propósito ---------------- */
            [$tab('nos_obj', __('Objetivos con propósito', 'ese-latam'))],
            $encabezado('nos_obj', __('Sobre nosotros', 'ese-latam'), "Objetivos con\n|propósito|", [
                'kicker_label' => __('Antetítulo de la sección objetivos', 'ese-latam'),
                'kicker_ayuda' => $ayuda_kicker(__('objetivos', 'ese-latam')),
                'titulo_label' => __('Titular de la sección objetivos', 'ese-latam'),
                'titulo_ayuda' => __('Titular grande arriba a la izquierda, sobre el menú lateral y los paneles de objetivos.', 'ese-latam')
                    . ' ' . ese_latam_ayuda_titulo()
                    . ' ' . __('Vacío: no se muestra.', 'ese-latam'),
            ]),
            [
                $bajada(
                    'nos_obj_desc',
                    __('Bajada junto al titular de objetivos', 'ese-latam'),
                    __('Párrafo corto a la derecha del titular, una o dos frases. El tramo en <strong>negrita</strong> se muestra en verde. Vacía: no se muestra.', 'ese-latam')
                ),
                $campo('repeater', 'nos_obj_items', __('Objetivos de la sección', 'ese-latam'), [
                    'instructions' => __('Cada objetivo es un panel con foto, número y título, más una entrada del menú lateral que sigue al scroll. Recomendado: entre 3 y 6. Sin objetivos, la sección entera no se muestra.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir objetivo', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'title', __('Título del objetivo', 'ese-latam'), [
                            'key'          => 'field_nosobj_titulo',
                            'instructions' => __('Va sobre la foto del panel y en el menú lateral. Hasta 40 caracteres. Obligatorio: la fila sin título no se muestra.', 'ese-latam'),
                            'required'     => 1,
                            'wrapper'      => ['width' => '60'],
                        ]),
                        ese_latam_campo_def('image', 'img', __('Foto del panel del objetivo', 'ese-latam'), [
                            'key'           => 'field_nosobj_img',
                            'instructions'  => __('Foto ancha con esquinas redondeadas; el número y el título van encima, abajo a la izquierda. WebP o JPG · 2200×1100 px (2:1) · máx. 400 KB. Se recorta al centro y se oscurece hacia abajo. Vacía: el panel queda de color liso, solo con el número y el título.', 'ese-latam'),
                            'return_format' => 'url',
                            'preview_size'  => 'medium',
                            'wrapper'       => ['width' => '40'],
                        ]),
                        $campo('wysiwyg', 'texto', __('Texto bajo el panel del objetivo', 'ese-latam'), [
                            'key'          => 'field_nosobj_texto',
                            'instructions' => __('Párrafo bajo la foto del objetivo. El tramo en <strong>negrita</strong> se muestra en verde. Vacío: se muestra solo el panel.', 'ese-latam'),
                            'tabs'         => 'visual',
                            'toolbar'      => 'basic',
                            'media_upload' => 0,
                        ]),
                    ],
                ]),
            ],

            [$tab('nos_obj_menu', __('Objetivos con propósito — menú lateral', 'ese-latam'))],
            [
                $campo('link', 'nos_obj_salto', __('Enlace para saltar la sección', 'ese-latam'), [
                    'instructions' => __('Atajo de texto al final del menú lateral de objetivos, para pasar a la sección siguiente. Escribe el texto y el destino (por ejemplo, #aliados). Vacío o sin texto: no se muestra.', 'ese-latam'),
                ]),
                $campo('text', 'nos_obj_metas_titulo', __('Título de la lista de metas', 'ese-latam'), [
                    'instructions' => __('Frase sobre la lista de metas del menú lateral. Hasta 60 caracteres. Solo se ve si hay metas. Vacío: la lista se muestra sin título.', 'ese-latam'),
                    'placeholder'  => __('Algunas de nuestras metas y objetivos del programa:', 'ese-latam'),
                ]),
                $campo('repeater', 'nos_obj_metas', __('Metas del menú lateral', 'ese-latam'), [
                    'instructions' => __('Lista con vistos bajo el menú lateral de objetivos, que acompaña al scroll. Recomendado: entre 3 y 5. Sin metas, la lista no se muestra.', 'ese-latam'),
                    'layout'       => 'table',
                    'button_label' => __('Añadir meta', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('textarea', 'texto', __('Texto de la meta', 'ese-latam'), [
                            'key'          => 'field_nosmeta_texto',
                            'instructions' => __('Una frase corta, hasta 100 caracteres. Obligatorio: la fila vacía no se muestra.', 'ese-latam'),
                            'required'     => 1,
                            'rows'         => 2,
                        ]),
                    ],
                ]),
            ],

            /* ---------------- Aliados ---------------- */
            [$tab('nos_aliados', __('Nuestros aliados', 'ese-latam'))],
            [
                $campo('message', '', __('Dónde se editan los logos de aliados', 'ese-latam'), [
                    'key'      => 'field_nosali_msg',
                    'message'  => __('Los logos salen del módulo <strong>Aliados</strong> del menú lateral. Acá solo se define cómo se presenta la sección en esta página.', 'ese-latam'),
                    'esc_html' => 0,
                ]),
            ],
            $encabezado('nos_aliados', __('Nuestros aliados', 'ese-latam'), "Trabajamos con\n|los mejores aliados|", [
                'kicker_label' => __('Antetítulo de la sección aliados', 'ese-latam'),
                'kicker_ayuda' => $ayuda_kicker(__('aliados', 'ese-latam')),
                'titulo_label' => __('Titular de la sección aliados', 'ese-latam'),
                'titulo_ayuda' => __('Titular grande en blanco sobre el fondo con degradado, encima de la grilla de logos.', 'ese-latam')
                    . ' ' . ese_latam_ayuda_titulo()
                    . ' ' . __('Vacío: no se muestra. Sin aliados cargados, la sección entera no se muestra.', 'ese-latam'),
            ]),
            [
                $campo('textarea', 'nos_aliados_desc', __('Bajada bajo el titular de aliados', 'ese-latam'), [
                    'instructions' => __('Párrafo corto bajo el titular, encima de los logos. Una o dos frases, hasta 200 caracteres. Vacía: no se muestra.', 'ese-latam'),
                    'rows'         => 2,
                ]),
                $foto('nos_aliados_isla', __('Isla flotante bajo los logos', 'ese-latam'), [
                    'instructions' => __('Recorte que flota bajo la grilla de logos y se inclina al mover el mouse. PNG o WebP con fondo transparente · 1200 px de ancho · máx. 400 KB. Vacía: no se muestra.', 'ese-latam'),
                    'wrapper'      => ['width' => '50'],
                ]),
            ],

            /* ---------------- Marquee ---------------- */
            [$tab('nos_marquee', __('Marquee', 'ese-latam'))],
            [
                $campo('text', 'nos_marquee', __('Frase de la cinta en movimiento', 'ese-latam'), [
                    'instructions' => __('Palabra gigante en mayúsculas que se desplaza de lado a lado entre los aliados y el método. Se repite sola tres veces. Una o dos palabras, hasta 60 caracteres. Vacía: no se muestra la cinta.', 'ese-latam'),
                    'placeholder'  => 'circulogic',
                    'maxlength'    => 60,
                ]),
            ],

            /* ---------------- Método Circulogic ---------------- */
            [$tab('nos_metodo', __('Método Circulogic', 'ese-latam'))],
            $encabezado('nos_metodo', __('Sobre nosotros', 'ese-latam'), "Método\n|Circulogic|", [
                'kicker_label' => __('Antetítulo del método Circulogic', 'ese-latam'),
                'kicker_ayuda' => $ayuda_kicker(__('el método Circulogic', 'ese-latam')),
                'titulo_label' => __('Titular del método Circulogic', 'ese-latam'),
                'titulo_ayuda' => __('Titular grande arriba a la izquierda, sobre las pestañas de pilares.', 'ese-latam')
                    . ' ' . ese_latam_ayuda_titulo()
                    . ' ' . __('Vacío: no se muestra.', 'ese-latam'),
            ]),
            [
                $bajada(
                    'nos_metodo_desc',
                    __('Bajada junto al titular del método', 'ese-latam'),
                    __('Párrafo corto a la derecha del titular, una o dos frases. El tramo en <strong>negrita</strong> se muestra en verde. Vacía: no se muestra.', 'ese-latam')
                ),
                $campo('repeater', 'nos_metodo_tabs', __('Pilares del método Circulogic', 'ese-latam'), [
                    'instructions' => __('Cada pilar es una pestaña con barra de progreso; se turnan solas cada 6 segundos y cambian la foto grande de abajo. Recomendado: 3 pilares (con 4 se reparten en cuatro columnas). Sin pilares, la sección no se muestra.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir pilar', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'title', __('Nombre del pilar', 'ese-latam'), [
                            'key'          => 'field_nosmet_titulo',
                            'instructions' => __('Título de la pestaña. Hasta 30 caracteres. Obligatorio: la fila sin nombre no se muestra.', 'ese-latam'),
                            'required'     => 1,
                            'wrapper'      => ['width' => '60'],
                        ]),
                        ese_latam_campo_def('image', 'img', __('Foto del pilar', 'ese-latam'), [
                            'key'           => 'field_nosmet_img',
                            'instructions'  => __('Foto grande con esquinas redondeadas bajo las pestañas, visible mientras el pilar está activo. WebP o JPG · 2560×1280 px (2:1) · máx. 500 KB. Se recorta al centro. Vacía: el recuadro queda de color liso.', 'ese-latam'),
                            'return_format' => 'url',
                            'preview_size'  => 'medium',
                            'wrapper'       => ['width' => '40'],
                        ]),
                        $campo('textarea', 'desc', __('Descripción del pilar', 'ese-latam'), [
                            'key'          => 'field_nosmet_desc',
                            'instructions' => __('Texto bajo el nombre del pilar, una o dos frases. Hasta 150 caracteres. Vacía: la pestaña muestra solo el nombre.', 'ese-latam'),
                            'rows'         => 2,
                        ]),
                    ],
                ]),
            ],

            /* ---------------- HDPE ---------------- */
            [$tab('nos_hdpe', __('HDPE', 'ese-latam'))],
            $encabezado('nos_hdpe', __('Tecnología y sostenibilidad', 'ese-latam'), "hdpe: el futuro es\n|circular|", [
                'kicker_label' => __('Antetítulo de la sección HDPE', 'ese-latam'),
                'kicker_ayuda' => $ayuda_kicker(__('HDPE', 'ese-latam')),
                'titulo_label' => __('Titular de la sección HDPE', 'ese-latam'),
                'titulo_ayuda' => __('Titular grande arriba a la izquierda, junto al video de la escena.', 'ese-latam')
                    . ' ' . ese_latam_ayuda_titulo()
                    . ' ' . __('Vacío: no se muestra.', 'ese-latam'),
            ]),
            [
                $bajada(
                    'nos_hdpe_desc',
                    __('Bajada bajo el titular HDPE', 'ese-latam'),
                    __('Párrafo bajo el titular, a la izquierda del video. Hasta 300 caracteres. El tramo en <strong>negrita</strong> se muestra en verde. Vacía: no se muestra.', 'ese-latam')
                ),
                $campo('repeater', 'nos_hdpe_chips', __('Etiquetas con visto de HDPE', 'ese-latam'), [
                    'instructions' => __('Pastillas con un visto, en fila bajo la bajada. Recomendado: entre 2 y 4. Sin etiquetas, la fila no se muestra.', 'ese-latam'),
                    'layout'       => 'table',
                    'button_label' => __('Añadir etiqueta', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'texto', __('Texto de la etiqueta', 'ese-latam'), [
                            'key'          => 'field_noschip_texto',
                            'instructions' => __('Dos o tres palabras, hasta 40 caracteres. Obligatorio: la fila vacía no se muestra.', 'ese-latam'),
                            'required'     => 1,
                            'maxlength'    => 40,
                        ]),
                    ],
                ]),
            ],

            [$tab('nos_hdpe_escena', __('HDPE — escena y tarjetas', 'ese-latam'))],
            [
                $campo('file', 'nos_hdpe_video', __('Video de la escena HDPE', 'ese-latam'), [
                    'instructions'  => __('Recuadro oscuro con esquinas redondeadas a la derecha del titular. Se reproduce solo, en silencio y en bucle. MP4 (H.264) o WebM · 1440×960 px (3:2) · hasta 20 s · máx. 8 MB, sin audio. Se recorta al centro. Vacío: no se muestra la escena ni sus textos.', 'ese-latam'),
                    'return_format' => 'url',
                    'mime_types'    => 'webm,mp4',
                    'wrapper'       => ['width' => '50'],
                ]),
                $campo('text', 'nos_hdpe_escena_kicker', __('Escena HDPE — antetítulo sobre el video', 'ese-latam'), [
                    'instructions' => __('Texto corto en mayúsculas celestes, arriba a la izquierda del video. Hasta 30 caracteres. Solo se ve si hay video. Vacío: no se muestra.', 'ese-latam'),
                    'placeholder'  => __('Impacto positivo', 'ese-latam'),
                    'wrapper'      => ['width' => '25'],
                ]),
                $campo('text', 'nos_hdpe_escena_sub', __('Escena HDPE — texto bajo el antetítulo', 'ese-latam'), [
                    'instructions' => __('Segunda línea, en gris, bajo el antetítulo del video. Hasta 30 caracteres. Solo se ve si hay video. Vacío: no se muestra.', 'ese-latam'),
                    'placeholder'  => __('Estándar Global', 'ese-latam'),
                    'wrapper'      => ['width' => '25'],
                ]),
                $campo('repeater', 'nos_hdpe_cards', __('Tarjetas al pie de HDPE', 'ese-latam'), [
                    'instructions' => __('Tarjetas con ícono, en una fila al final de la sección. Recomendado: 3. Sin tarjetas, la fila no se muestra.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir tarjeta', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'title', __('Título de la tarjeta', 'ese-latam'), [
                            'key'          => 'field_noshdpe_titulo',
                            'instructions' => __('Texto destacado bajo el ícono. Hasta 40 caracteres. Obligatorio: la fila sin título no se muestra.', 'ese-latam'),
                            'required'     => 1,
                            'wrapper'      => ['width' => '40'],
                        ]),
                        $campo('text', 'desc', __('Descripción de la tarjeta', 'ese-latam'), [
                            'key'          => 'field_noshdpe_desc',
                            'instructions' => __('Una línea bajo el título. Hasta 80 caracteres. Vacía: la tarjeta muestra solo el título.', 'ese-latam'),
                            'wrapper'      => ['width' => '30'],
                        ]),
                        $campo('select', 'icono', __('Ícono de la tarjeta', 'ese-latam'), [
                            'key'           => 'field_noshdpe_icono',
                            'instructions'  => __('Elige uno de los dibujos de la marca. Vacío: la tarjeta va sin ícono.', 'ese-latam'),
                            'choices'       => ese_latam_iconos_hdpe_opciones(),
                            'allow_null'    => 1,
                            'return_format' => 'value',
                            'wrapper'       => ['width' => '30'],
                        ]),
                        $campo('true_false', 'gira', __('¿El ícono de la tarjeta gira?', 'ese-latam'), [
                            'key'          => 'field_noshdpe_gira',
                            'instructions' => __('Activado, el ícono da una vuelta mientras bajas por la página y medio giro al pasar el mouse. Actívalo en una sola tarjeta: con scroll solo gira la primera marcada. Apagado: el ícono queda quieto.', 'ese-latam'),
                            'ui'           => 1,
                        ]),
                    ],
                ]),
            ]
        ),
    ]);
});
