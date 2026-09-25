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
            $campo('text', 'impacto_hero_kicker', __('Antetítulo sobre el titular del hero', 'ese-latam'), [
                'instructions' => __('Palabra corta en mayúsculas celestes encima del titular. También es el nombre de la página en la miga de pan de arriba. Hasta 30 caracteres. Vacío: no se muestra y la miga de pan usa el título de la página.', 'ese-latam'),
                'placeholder'  => __('Impacto', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('textarea', 'impacto_hero_titulo', __('Titular principal del hero', 'ese-latam'), [
                'instructions' => __('Frase grande en mayúsculas sobre la foto de fondo, abajo a la izquierda.', 'ese-latam')
                    . ' ' . ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => 'Residuos |inteligentes|',
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('textarea', 'impacto_hero_desc', __('Bajada a la derecha del titular', 'ese-latam'), [
                'instructions' => __('Párrafo corto a la derecha del titular, sobre el botón. Una o dos frases, hasta 200 caracteres. Vacía: no se muestra.', 'ese-latam'),
                'rows'         => 3,
            ]),
            $foto('impacto_hero_imagen', __('Foto de fondo del hero', 'ese-latam'), [
                'instructions' => __('Ocupa toda la pantalla detrás del titular. WebP o JPG · 1920×1080 px (16:9) · máx. 400 KB. Se muestra muy oscurecida y se recorta al centro. Vacía: el hero queda en negro liso.', 'ese-latam'),
            ]),
            $campo('link', 'impacto_hero_cta', __('Botón del hero (texto y enlace)', 'ese-latam'), [
                'instructions' => __('Botón bajo la bajada. Escribe el texto del botón y la página a la que lleva. Vacío o sin texto: el hero se muestra sin botón.', 'ese-latam'),
            ]),

            /* ---------------- Mosaico ---------------- */
            $tab('imp_mosaico', __('Respaldo comprobado', 'ese-latam')),
            $campo('text', 'impacto_kicker', __('Antetítulo del mosaico de impacto', 'ese-latam'), [
                'instructions' => __('Palabra o frase corta en mayúsculas, centrada encima del titular del mosaico. Hasta 30 caracteres. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Respaldo comprobado', 'ese-latam'),
                'wrapper'      => ['width' => '40'],
            ]),
            $campo('textarea', 'impacto_titulo', __('Titular del mosaico de impacto', 'ese-latam'), [
                'instructions' => __('Titular centrado sobre el mosaico de tarjetas; el tramo resaltado va en verde y subrayado.', 'ese-latam')
                    . ' ' . ese_latam_ayuda_titulo()
                    . ' ' . __('Vacío: no se muestra. Sin titular, sin cifras y sin título de la tarjeta grande, la sección entera no se muestra.', 'ese-latam'),
                'rows'         => 2,
                'placeholder'  => "Gestionar residuos es una\n|decisión humana|",
                'wrapper'      => ['width' => '60'],
            ]),
            $foto('impacto_cielo', __('Foto de cielo de fondo del mosaico', 'ese-latam'), [
                'instructions' => __('Ocupa todo el fondo de la sección, detrás del titular y las tarjetas, con parallax. WebP o JPG · 1920×1080 px (16:9) · máx. 300 KB. Se muestra aclarada bajo un velo blanco y se recorta al centro. Vacía: el fondo queda blanco.', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),

            $campo('message', '', __('Cómo se arma el mosaico', 'ese-latam'), [
                'key'      => 'field_impmos_msg',
                'message'  => __('El mosaico tiene una composición fija: la marca arriba a la izquierda, dos cifras apiladas, la tarjeta grande con foto, una foto con pie y el alcance. Cada celda se edita abajo; si dejas una vacía, se muestra sin ese elemento.', 'ese-latam'),
                'esc_html' => 0,
            ]),

            /* ---------------- Mosaico — marca y cifras ---------------- */
            $tab('imp_mosaico_marca', __('Respaldo comprobado — marca y cifras', 'ese-latam')),
            $foto('impacto_marca_fondo', __('Tarjeta de marca — foto de fondo', 'ese-latam'), [
                'instructions' => __('Primera tarjeta del mosaico, a la izquierda, con esquinas redondeadas. WebP o JPG · 480×520 px (12:13) · máx. 120 KB. Se recorta al centro. Vacía: la tarjeta queda sin fondo. Sin fondo ni logo, la tarjeta no se muestra.', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $foto('impacto_marca_logo', __('Tarjeta de marca — logo flotante', 'ese-latam'), [
                'instructions' => __('El símbolo que flota en el centro de la tarjeta de marca. PNG o WebP con fondo transparente · 300 px de ancho · máx. 50 KB. Vacío: la tarjeta muestra solo la foto de fondo.', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),

            $campo('repeater', 'impacto_cifras', __('Cifras apiladas del mosaico', 'ese-latam'), [
                'instructions' => __('Las dos tarjetas de número, una sobre otra, junto a la tarjeta de marca; la segunda va en verde. El número cuenta desde cero al entrar en pantalla. Máximo 2. Sin cifras, la columna no se muestra.', 'ese-latam'),
                'layout'       => 'table',
                'max'          => 2,
                'button_label' => __('Añadir cifra', 'ese-latam'),
                'sub_fields'   => [
                    $campo('number', 'numero', __('Número de la cifra', 'ese-latam'), [
                        'key'          => 'field_impcifra_num',
                        'instructions' => __('Solo un número entero, sin puntos ni símbolos. Obligatorio.', 'ese-latam'),
                        'required'     => 1,
                        'min'          => 0,
                    ]),
                    $campo('text', 'sufijo', __('Sufijo de la cifra', 'ese-latam'), [
                        'key'          => 'field_impcifra_suf',
                        'instructions' => __('Signo pegado al número, como “+” o “%”. Hasta 3 caracteres. Vacío: el número va solo.', 'ese-latam'),
                        'placeholder'  => '+',
                        'maxlength'    => 3,
                    ]),
                    $campo('text', 'etiqueta', __('Texto bajo la cifra', 'ese-latam'), [
                        'key'          => 'field_impcifra_lbl',
                        'instructions' => __('Lo que mide el número, en mayúsculas pequeñas. Hasta 40 caracteres. Obligatorio: la fila sin texto no se muestra.', 'ese-latam'),
                        'required'     => 1,
                        'maxlength'    => 40,
                    ]),
                ],
            ]),

            /* ---------------- Mosaico — tarjetas con foto ---------------- */
            $tab('imp_mosaico_fotos', __('Respaldo comprobado — tarjetas con foto', 'ese-latam')),
            $campo('text', 'impacto_principal_titulo', __('Tarjeta grande — título', 'ese-latam'), [
                'instructions' => __('Título en mayúsculas blancas al pie de la tarjeta grande del centro del mosaico. Hasta 50 caracteres. Vacío: no se muestra. Sin título ni foto, la tarjeta no se muestra.', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $foto('impacto_principal_imagen', __('Tarjeta grande — foto de fondo', 'ese-latam'), [
                'instructions' => __('Llena la tarjeta grande del centro, bajo el título y la descripción. WebP o JPG · 800×960 px (5:6) · máx. 250 KB. Se recorta al centro y se oscurece hacia abajo. Vacía: la tarjeta queda sin foto.', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('textarea', 'impacto_principal_desc', __('Tarjeta grande — descripción', 'ese-latam'), [
                'instructions' => __('Párrafo blanco bajo el título de la tarjeta grande. Hasta 160 caracteres. Solo se ve si la tarjeta tiene título o foto. Vacía: no se muestra.', 'ese-latam'),
                'rows'         => 3,
            ]),

            $campo('text', 'impacto_foto_texto', __('Foto con pie — texto del pie', 'ese-latam'), [
                'instructions' => __('Texto corto en mayúsculas blancas al pie de la tarjeta de foto, a la derecha de la tarjeta grande. Hasta 40 caracteres. Vacío: la tarjeta muestra solo la foto. Sin texto ni foto, la tarjeta no se muestra.', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $foto('impacto_foto_imagen', __('Foto con pie — imagen', 'ese-latam'), [
                'instructions' => __('Foto vertical de la tarjeta a la derecha de la tarjeta grande. WebP o JPG · 420×600 px (7:10) · máx. 150 KB. Se recorta al centro y se oscurece hacia abajo. Vacía: la tarjeta queda sin foto.', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),

            /* ---------------- Mosaico — alcance ---------------- */
            $tab('imp_mosaico_alcance', __('Respaldo comprobado — alcance', 'ese-latam')),
            $campo('text', 'impacto_alcance_kicker', __('Tarjeta de alcance — antetítulo', 'ese-latam'), [
                'instructions' => __('Texto corto en mayúsculas sobre el número de la última tarjeta del mosaico, la azul. Hasta 20 caracteres. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Alcance Latam', 'ese-latam'),
                'wrapper'      => ['width' => '34'],
            ]),
            $campo('number', 'impacto_alcance_numero', __('Tarjeta de alcance — número', 'ese-latam'), [
                'instructions' => __('Número grande de la tarjeta azul; cuenta desde cero al entrar en pantalla. Solo un número entero. Vacío: se cuentan los países cargados en Distribuidores.', 'ese-latam'),
                'min'          => 0,
                'wrapper'      => ['width' => '33'],
            ]),
            $campo('text', 'impacto_alcance_etiqueta', __('Tarjeta de alcance — texto bajo el número', 'ese-latam'), [
                'instructions' => __('Lo que mide el número, como “Países”. Hasta 20 caracteres. Vacío: la tarjeta de alcance no se muestra.', 'ese-latam'),
                'placeholder'  => __('Países', 'ese-latam'),
                'wrapper'      => ['width' => '33'],
            ]),

            /* ---------------- Franja ---------------- */
            $tab('imp_franja', __('Franja de frases', 'ese-latam')),
            $campo('repeater', 'impacto_frases', __('Frases de la franja en movimiento', 'ese-latam'), [
                'instructions' => __('La cinta verde claro que se desplaza bajo el mosaico, con las frases separadas por el chevron de la marca. Recomendado: entre 3 y 6. Sin frases, la cinta no se muestra.', 'ese-latam'),
                'layout'       => 'table',
                'button_label' => __('Añadir frase', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'texto', __('Texto de la frase', 'ese-latam'), [
                        'key'          => 'field_impfrase_txt',
                        'instructions' => __('Frase corta; se muestra en mayúsculas. Hasta 60 caracteres. Obligatorio: la fila vacía no se muestra.', 'ese-latam'),
                        'required'     => 1,
                        'maxlength'    => 60,
                    ]),
                ],
            ]),

            /* ---------------- Alianzas ---------------- */
            $tab('imp_alianzas', __('Alianzas', 'ese-latam')),
            $campo('message', '', __('Dónde se editan los logos de aliados', 'ese-latam'), [
                'key'      => 'field_impali_msg',
                'message'  => __('Los logos salen del módulo <strong>Aliados</strong>. Acá solo se define cómo se presenta la sección en esta página.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('text', 'impacto_aliados_kicker', __('Antetítulo de la sección alianzas', 'ese-latam'), [
                'instructions' => __('Palabra o frase corta en mayúsculas, con una barra delante, encima del titular de alianzas. Hasta 30 caracteres. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Alianzas', 'ese-latam'),
                'wrapper'      => ['width' => '40'],
            ]),
            $campo('textarea', 'impacto_aliados_titulo', __('Titular de la sección alianzas', 'ese-latam'), [
                'instructions' => __('Titular grande en blanco sobre el fondo azul con degradado, encima de la grilla de logos.', 'ese-latam')
                    . ' ' . ese_latam_ayuda_titulo()
                    . ' ' . __('Vacío: no se muestra. Sin aliados cargados, la sección entera no se muestra.', 'ese-latam'),
                'rows'         => 2,
                'placeholder'  => "Unidos por\n|las mejores alianzas|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('textarea', 'impacto_aliados_desc', __('Bajada bajo el titular de alianzas', 'ese-latam'), [
                'instructions' => __('Párrafo corto bajo el titular, encima de los logos. Una o dos frases, hasta 200 caracteres. Vacía: no se muestra.', 'ese-latam'),
                'rows'         => 2,
            ]),
            $foto('impacto_aliados_isla', __('Isla flotante bajo los logos', 'ese-latam'), [
                'instructions' => __('Recorte que flota bajo la grilla de logos y se inclina al mover el mouse. PNG o WebP con fondo transparente · 1200 px de ancho · máx. 400 KB. Vacía: no se muestra.', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),

            /* ---------------- Casos ---------------- */
            $tab('imp_casos', __('Casos reales', 'ese-latam')),
            $campo('message', '', __('De dónde salen las tarjetas de casos', 'ese-latam'), [
                'key'      => 'field_impcasos_msg',
                'message'  => __('Las tarjetas son los cuatro casos más recientes del módulo <strong>Casos de éxito</strong>.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('text', 'impacto_casos_kicker', __('Antetítulo de la sección casos reales', 'ese-latam'), [
                'instructions' => __('Palabra o frase corta en mayúsculas, con una barra delante, encima del titular de casos. Hasta 30 caracteres. Vacío: no se muestra.', 'ese-latam'),
                'placeholder'  => __('Casos reales', 'ese-latam'),
                'wrapper'      => ['width' => '40'],
            ]),
            $campo('textarea', 'impacto_casos_titulo', __('Titular de la sección casos reales', 'ese-latam'), [
                'instructions' => __('Titular grande arriba a la izquierda, sobre las cuatro tarjetas de casos.', 'ese-latam')
                    . ' ' . ese_latam_ayuda_titulo()
                    . ' ' . __('Vacío: no se muestra. Sin casos publicados, la sección entera no se muestra.', 'ese-latam'),
                'rows'         => 2,
                'placeholder'  => "Casos reales en\n|gestión urbana|",
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('textarea', 'impacto_casos_desc', __('Bajada bajo el titular de casos', 'ese-latam'), [
                'instructions' => __('Párrafo corto bajo el titular. Una o dos frases, hasta 200 caracteres. Vacía: no se muestra.', 'ese-latam'),
                'rows'         => 2,
            ]),
            $campo('link', 'impacto_casos_enlace', __('Enlace a todos los casos (texto y destino)', 'ese-latam'), [
                'instructions' => __('Enlace con flecha a la derecha del titular, normalmente al listado de casos de éxito. Escribe el texto y la página a la que lleva. Vacío o sin texto: la sección se muestra sin enlace.', 'ese-latam'),
            ]),
        ],
    ]);
});
