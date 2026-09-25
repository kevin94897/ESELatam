<?php
/**
 * Contenido compartido por varias plantillas: certificaciones, distribuidores,
 * residuos inteligentes y el bloque de cierre "Contactemos".
 *
 * Vive en páginas de opciones (menú "ESE Latam") porque es el MISMO contenido
 * en toda la web: la lista de distribuidores alimenta el globo de la portada y
 * la página "Encuentra un distribuidor"; la de sellos, la franja que sale en
 * la portada, en la ficha de producto y en las singles de sector. Editarlo una
 * vez lo cambia en todos lados.
 *
 * Los sectores son la excepción: se editan en la propia página Inicio
 * (inc/pcf-home.php), porque es donde el cliente los mira.
 *
 * Además, cada página (y cada ficha de producto) recibe un grupo "Secciones
 * compartidas" con un interruptor por sección: es el equivalente editable de
 * los `$args` que hoy pasa cada plantilla a get_template_part() para cambiar
 * el copy en Nosotros, Impacto, Certificaciones, etc.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Los campos del bloque "Contactemos", en su versión global o de override.
 * Son los mismos dos veces (una en la página de opciones, otra en cada
 * página con el infijo `_pag_`), así que se arman una sola vez.
 *
 * En la versión de override un campo vacío no oculta nada: se usa el valor
 * global. Por eso el "Vacío: …" de la ayuda cambia según el prefijo.
 *
 * @param string $prefijo 'contacto_' o 'contacto_pag_'
 * @param array<string, mixed> $extra Ajustes comunes (p. ej. conditional_logic).
 * @return list<array<string, mixed>>
 */
function ese_latam_campos_contacto(string $prefijo, array $extra = []): array {
    $campo = static fn (string $type, string $name, string $label, array $args = []): array =>
        ese_latam_campo_def($type, $prefijo . $name, $label, array_merge($args, $extra));

    $pag   = str_contains($prefijo, '_pag_');
    $vacio = static fn (string $global): string => $pag
        ? __('Vacío: se usa el del menú ESE Latam → Contactemos.', 'ese-latam')
        : $global;

    return [
        $campo('text', 'kicker', __('Antetítulo del bloque Contactemos', 'ese-latam'), [
            'instructions' => __('Frase corta sobre el titular, dentro de la tarjeta translúcida, precedida por una barra. Hasta 30 caracteres.', 'ese-latam') . ' ' . $vacio(__('Vacío: no se muestra.', 'ese-latam')),
            'placeholder'  => __('Contactemos', 'ese-latam'),
        ]),
        $campo('textarea', 'titulo', __('Titular del bloque Contactemos', 'ese-latam'), [
            'instructions' => __('Frase grande de la tarjeta, sobre la foto de fondo. No escribas el signo de pregunta final: lo añade el interruptor de abajo.', 'ese-latam') . ' ' . ese_latam_ayuda_titulo() . ' ' . $vacio(__('Vacío: no se muestra.', 'ese-latam')),
            'rows'         => 2,
            'placeholder'  => __('¿Listo para llevar tu gestión de residuos al |siguiente nivel|', 'ese-latam'),
        ]),
        $campo('true_false', 'pregunta', __('¿Cerrar el titular con signo de pregunta?', 'ese-latam'), [
            'instructions'  => $pag
                ? __('Encendido: se añade “?” al final del titular. Con la personalización encendida, este interruptor reemplaza siempre al global.', 'ese-latam')
                : __('Encendido: se añade “?” al final del titular. Apágalo si el titular no es una pregunta o ya trae su propio signo.', 'ese-latam'),
            'default_value' => 1,
            'ui'            => 1,
        ]),
        $campo('textarea', 'desc', __('Bajada del bloque Contactemos', 'ese-latam'), [
            'instructions' => __('Párrafo bajo la línea divisoria, a la izquierda del botón. Una o dos frases, hasta 200 caracteres.', 'ese-latam') . ' ' . $vacio(__('Vacía: no se muestra.', 'ese-latam')),
            'rows'         => 3,
        ]),
        $campo('image', 'fondo', __('Foto de fondo del bloque Contactemos', 'ese-latam'), [
            'instructions'  => __('Ocupa todo el ancho detrás de la tarjeta, oscurecida un poco para que el texto se lea. WebP o JPG · 1920×1080 px (16:9) · máx. 400 KB. Se recorta al centro para cubrir el bloque.', 'ese-latam') . ' ' . $vacio(__('Vacía: la tarjeta queda sin foto detrás.', 'ese-latam')),
            'return_format' => 'url',
            'preview_size'  => 'medium',
        ]),
        $campo('link', 'cta', __('Botón del bloque Contactemos (texto y enlace)', 'ese-latam'), [
            'instructions' => __('Botón a la derecha de la bajada. Escribe el texto del botón y la página a la que lleva (por lo general, Contacto).', 'ese-latam') . ' ' . $vacio(__('Vacío: no se muestra. Si también quedan vacíos el titular y la bajada, el bloque entero no se muestra.', 'ese-latam')),
        ]),
    ];
}

/**
 * Campos del encabezado de "Certificaciones" (global y override).
 *
 * @return list<array<string, mixed>>
 */
function ese_latam_campos_certificaciones(string $prefijo, array $extra = []): array {
    $campo = static fn (string $type, string $name, string $label, array $args = []): array =>
        ese_latam_campo_def($type, $prefijo . $name, $label, array_merge($args, $extra));

    $pag   = str_contains($prefijo, '_pag_');
    $vacio = static fn (string $global): string => $pag
        ? __('Vacío: se usa el del menú ESE Latam → Certificaciones.', 'ese-latam')
        : $global;

    return [
        $campo('text', 'kicker', __('Antetítulo de la franja de certificaciones', 'ese-latam'), [
            'instructions' => __('Frase corta en azul sobre el titular, precedida por una barra. Hasta 30 caracteres.', 'ese-latam') . ' ' . $vacio(__('Vacío: no se muestra.', 'ese-latam')),
            'placeholder'  => __('Estándar global', 'ese-latam'),
            'wrapper'      => ['width' => '50'],
        ]),
        $campo('textarea', 'titulo', __('Titular de la franja de certificaciones', 'ese-latam'), [
            'instructions' => __('Titular arriba a la izquierda, sobre la fila de logos de los sellos.', 'ese-latam') . ' ' . ese_latam_ayuda_titulo() . ' ' . $vacio(__('Vacío: no se muestra.', 'ese-latam')),
            'rows'         => 2,
            'placeholder'  => __('nuestras |certificaciones|', 'ese-latam'),
            'wrapper'      => ['width' => '50'],
        ]),
        $campo('wysiwyg', 'desc', __('Bajada bajo el titular de certificaciones', 'ese-latam'), [
            'instructions' => __('Párrafo corto bajo el titular. Una o dos frases.', 'ese-latam') . ' ' . $vacio(__('Vacía: no se muestra. Sin sellos, titular ni bajada, la franja entera no se muestra.', 'ese-latam')),
            'tabs'         => 'visual',
            'toolbar'      => 'basic',
            'media_upload' => 0,
        ]),
        $campo('link', 'enlace', __('Enlace del encabezado de certificaciones', 'ese-latam'), [
            'instructions' => __('Enlace con flecha a la derecha del encabezado. Escribe el texto (por ejemplo, Explora todas nuestras certificaciones) y la página a la que lleva.', 'ese-latam') . ' ' . $vacio(__('Vacío: no se muestra.', 'ese-latam')),
        ]),
    ];
}

/**
 * Campos de "Pruebas de rigurosidad" (global y override).
 *
 * @return list<array<string, mixed>>
 */
function ese_latam_campos_pruebas(string $prefijo, array $extra = []): array {
    $campo = static fn (string $type, string $name, string $label, array $args = []): array =>
        ese_latam_campo_def($type, $prefijo . $name, $label, array_merge($args, $extra));

    $pag   = str_contains($prefijo, '_pag_');
    $vacio = static fn (string $global): string => $pag
        ? __('Vacío: se usa el del menú ESE Latam → Pruebas de rigurosidad.', 'ese-latam')
        : $global;

    return [
        $campo('text', 'kicker', __('Antetítulo del bloque de pruebas', 'ese-latam'), [
            'instructions' => __('Frase corta en azul sobre el titular, precedida por una barra. Hasta 30 caracteres.', 'ese-latam') . ' ' . $vacio(__('Vacío: no se muestra.', 'ese-latam')),
            'placeholder'  => __('Pruebas de rigurosidad', 'ese-latam'),
            'wrapper'      => ['width' => '40'],
        ]),
        $campo('textarea', 'titulo', __('Titular del bloque de pruebas', 'ese-latam'), [
            'instructions' => __('Titular centrado en mayúsculas, sobre las tarjetas y el video.', 'ese-latam') . ' ' . ese_latam_ayuda_titulo() . ' ' . $vacio(__('Vacío: no se muestra.', 'ese-latam')),
            'rows'         => 2,
            'placeholder'  => "¿qué hace que\n|sea excelente?|",
            'wrapper'      => ['width' => '60'],
        ]),
        $campo('wysiwyg', 'desc', __('Bajada bajo el titular de pruebas', 'ese-latam'), [
            'instructions' => __('Párrafo corto centrado bajo el titular. Una o dos frases.', 'ese-latam') . ' ' . $vacio(__('Vacía: no se muestra.', 'ese-latam')),
            'tabs'         => 'visual',
            'toolbar'      => 'basic',
            'media_upload' => 0,
        ]),
        $campo('repeater', 'lista', __('Tarjetas de pruebas de rigurosidad', 'ese-latam'), [
            'instructions' => __('Las tarjetas se reparten a los dos lados del video, mitad y mitad. Lo ideal son 6 (tres por lado); hasta 10.', 'ese-latam') . ' ' . ($pag
                ? __('Vacía: se usan las pruebas del menú ESE Latam → Pruebas de rigurosidad.', 'ese-latam')
                : __('Sin pruebas cargadas, el bloque entero no se muestra.', 'ese-latam')),
            'layout'       => 'table',
            'max'          => 10,
            'button_label' => __('Añadir prueba', 'ese-latam'),
            'sub_fields'   => [
                ese_latam_campo_def('text', 'title', __('Nombre de la prueba', 'ese-latam'), [
                    'key'          => 'field_' . $prefijo . 'titulo_item',
                    'instructions' => __('Título de la tarjeta. Obligatorio, hasta 40 caracteres.', 'ese-latam'),
                    'required'     => 1,
                    'maxlength'    => 40,
                ]),
                ese_latam_campo_def('textarea', 'desc', __('Descripción de la prueba', 'ese-latam'), [
                    'key'          => 'field_' . $prefijo . 'desc_item',
                    'instructions' => __('Una o dos frases bajo el nombre. Vacía: la tarjeta muestra solo el nombre.', 'ese-latam'),
                    'rows'         => 2,
                ]),
            ],
        ]),
        $campo('file', 'video', __('Video central del bloque de pruebas', 'ese-latam'), [
            'instructions'  => __('Video con controles en el recuadro del centro, entre las tarjetas; el visitante lo reproduce con play. MP4 (H.264) · 1920×1080 px (16:9) · hasta 1 min · máx. 20 MB.', 'ese-latam') . ' ' . $vacio(__('Vacío: el recuadro queda azul con un botón de play decorativo.', 'ese-latam')),
            'return_format' => 'url',
            'mime_types'    => 'mp4,webm',
            'wrapper'       => ['width' => '50'],
        ]),
        $campo('image', 'poster', __('Imagen de portada del video de pruebas', 'ese-latam'), [
            'instructions'  => __('Se ve en el recuadro central antes de darle play. Usa un cuadro del mismo video. WebP o JPG · 1920×1080 px (16:9) · máx. 300 KB. Solo se usa si hay video cargado.', 'ese-latam') . ' ' . $vacio(__('Vacía: el recuadro queda liso hasta darle play.', 'ese-latam')),
            'return_format' => 'url',
            'preview_size'  => 'medium',
            'wrapper'       => ['width' => '50'],
        ]),
    ];
}

/**
 * Campos del encabezado de "Residuos inteligentes" (global y override).
 *
 * @return list<array<string, mixed>>
 */
function ese_latam_campos_residuos(string $prefijo, array $extra = []): array {
    $campo = static fn (string $type, string $name, string $label, array $args = []): array =>
        ese_latam_campo_def($type, $prefijo . $name, $label, array_merge($args, $extra));

    $pag   = str_contains($prefijo, '_pag_');
    $vacio = static fn (string $global): string => $pag
        ? __('Vacío: se usa el del menú ESE Latam → Residuos inteligentes.', 'ese-latam')
        : $global;

    return [
        $campo('text', 'kicker', __('Antetítulo de Residuos inteligentes', 'ese-latam'), [
            'instructions' => __('Frase corta en azul sobre el titular, precedida por una barra. Hasta 30 caracteres.', 'ese-latam') . ' ' . $vacio(__('Vacío: no se muestra.', 'ese-latam')),
            'placeholder'  => __('Residuos inteligentes', 'ese-latam'),
            'wrapper'      => ['width' => '50'],
        ]),
        $campo('textarea', 'titulo', __('Titular de Residuos inteligentes', 'ese-latam'), [
            'instructions' => __('Titular centrado en mayúsculas, sobre el selector de servicios.', 'ese-latam') . ' ' . ese_latam_ayuda_titulo() . ' ' . $vacio(__('Vacío: no se muestra.', 'ese-latam')),
            'rows'         => 2,
            'placeholder'  => "ingeniería de\n|alto desempeño|",
            'wrapper'      => ['width' => '50'],
        ]),
        $campo('wysiwyg', 'desc', __('Bajada bajo el titular de Residuos', 'ese-latam'), [
            'instructions' => __('Párrafo corto centrado bajo el titular. Una o dos frases.', 'ese-latam') . ' ' . $vacio(__('Vacía: no se muestra.', 'ese-latam')),
            'tabs'         => 'visual',
            'toolbar'      => 'basic',
            'media_upload' => 0,
        ]),
        $campo('link', 'enlace', __('Enlace al pie de Residuos inteligentes', 'ese-latam'), [
            'instructions' => __('Enlace con flecha bajo el selector de servicios. Escribe el texto (por ejemplo, Conoce nuestro impacto) y la página a la que lleva.', 'ese-latam') . ' ' . $vacio(__('Vacío: no se muestra.', 'ese-latam')),
        ]),
    ];
}

/* -------------------------------------------------------------------------
 * Menú de opciones
 * ---------------------------------------------------------------------- */

add_action('acf/init', static function (): void {
    if (! function_exists('acf_add_options_page')) {
        return;
    }

    acf_add_options_page([
        'page_title'  => __('Contenido ESE Latam', 'ese-latam'),
        'menu_title'  => __('ESE Latam', 'ese-latam'),
        'menu_slug'   => 'ese-latam',
        'capability'  => 'edit_pages',
        'icon_url'    => 'dashicons-trash',
        'position'    => 58,
        'redirect'    => true,
        'description' => __('Contenido que se repite en varias páginas de la web.', 'ese-latam'),
    ]);

    $subpaginas = [
        'cabecera'        => __('Cabecera', 'ese-latam'),
        'certificaciones' => __('Certificaciones', 'ese-latam'),
        'distribuidores'  => __('Distribuidores', 'ese-latam'),
        'pruebas'         => __('Pruebas de rigurosidad', 'ese-latam'),
        'residuos'        => __('Residuos inteligentes', 'ese-latam'),
        'contacto'        => __('Contactemos', 'ese-latam'),
    ];

    foreach ($subpaginas as $slug => $titulo) {
        acf_add_options_sub_page([
            'page_title'  => $titulo,
            'menu_title'  => $titulo,
            'menu_slug'   => 'ese-latam-' . $slug,
            'parent_slug' => 'ese-latam',
            'capability'  => 'edit_pages',
        ]);
    }
});

/* -------------------------------------------------------------------------
 * Grupos de campos
 * ---------------------------------------------------------------------- */

add_action('acf/init', static function (): void {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    $campo = 'ese_latam_campo_def';
    $tab   = 'ese_latam_campo_tab';

    $base = static fn (string $key, string $title, string $opciones, array $fields, string $desc = ''): array => [
        'key'                   => 'group_' . $key,
        'title'                 => $title,
        'location'              => [[['param' => 'options_page', 'operator' => '==', 'value' => $opciones]]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => $desc,
        'fields'                => $fields,
    ];

    // ---------- Cabecera ----------
    acf_add_local_field_group($base(
        'cabecera',
        __('Cabecera', 'ese-latam'),
        'ese-latam-cabecera',
        [
            $campo('link', 'cabecera_cta', __('Botón de la barra de menú (texto y enlace)', 'ese-latam'), [
                'instructions' => __('El botón azul a la derecha de la barra superior, que se repite en el menú móvil, en todas las páginas. Escribe el texto y la página a la que lleva. Vacío: “Contacto” hacia la página de Contacto.', 'ese-latam'),
            ]),
        ],
        __('El botón que acompaña al menú principal en todas las páginas.', 'ese-latam')
    ));

    // ---------- Certificaciones ----------
    acf_add_local_field_group($base(
        'certificaciones',
        __('Certificaciones', 'ese-latam'),
        'ese-latam-certificaciones',
        array_merge(
            [$tab('cert_encabezado', __('Encabezado', 'ese-latam'))],
            ese_latam_campos_certificaciones('certificaciones_')
        ),
        __('El encabezado de la franja de sellos que aparece en la portada, en la ficha de producto y en las páginas de sector. Los sellos se editan en el módulo Certificaciones del menú lateral.', 'ese-latam')
    ));

    // ---------- Distribuidores ----------
    acf_add_local_field_group($base(
        'distribuidores',
        __('Distribuidores', 'ese-latam'),
        'ese-latam-distribuidores',
        [
            $campo('text', 'distribuidores_pendiente_nombre', __('Países sin distribuidor — título de la ficha', 'ese-latam'), [
                'instructions' => __('Título de la ficha que se muestra, en el globo de la portada y en “Encuentra un distribuidor”, para los países que todavía no tienen empresas cargadas. Hasta 40 caracteres. Vacío en ambos campos: esos países aparecen sin ficha.', 'ese-latam'),
                'placeholder'  => __('Distribuidor autorizado', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('text', 'distribuidores_pendiente_texto', __('Países sin distribuidor — texto de la ficha', 'ese-latam'), [
                'instructions' => __('Línea bajo el título de esa misma ficha (por ejemplo, Datos de contacto próximamente). Hasta 60 caracteres. Vacío: la ficha muestra solo el título.', 'ese-latam'),
                'placeholder'  => __('Datos de contacto próximamente', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
        ],
        __('La ficha de los países del globo que todavía no tienen distribuidor. Los países y las empresas, que alimentan el globo de la portada y la página “Encuentra un distribuidor”, se editan en el módulo Distribuidores del menú lateral.', 'ese-latam')
    ));

    // ---------- Pruebas de rigurosidad ----------
    // Seis campos: se parten en dos pestañas para no pasar de cinco por lámina.
    $pruebas = ese_latam_campos_pruebas('pruebas_');
    acf_add_local_field_group($base(
        'pruebas',
        __('Pruebas de rigurosidad', 'ese-latam'),
        'ese-latam-pruebas',
        array_merge(
            [$tab('pruebas_tarjetas', __('Pruebas de rigurosidad', 'ese-latam'))],
            array_slice($pruebas, 0, 4),
            [$tab('pruebas_video', __('Pruebas de rigurosidad — video', 'ese-latam'))],
            array_slice($pruebas, 4)
        ),
        __('El bloque “¿Qué hace que sea excelente?”, que sale en cada ficha de producto y en la página de Certificaciones. Cada una puede personalizarlo desde su propio editor.', 'ese-latam')
    ));

    // ---------- Residuos inteligentes ----------
    acf_add_local_field_group($base(
        'residuos',
        __('Residuos inteligentes', 'ese-latam'),
        'ese-latam-residuos',
        array_merge(
            [$tab('res_encabezado', __('Encabezado', 'ese-latam'))],
            ese_latam_campos_residuos('residuos_'),
            [
                $tab('res_servicios', __('Servicios', 'ese-latam')),
                $campo('repeater', 'residuos_servicios', __('Servicios del selector de residuos', 'ese-latam'), [
                    'instructions' => __('Cada servicio es una pestaña del selector (Educar, Segregar, Transformar); al elegirla cambian la foto, el título y la descripción del panel. Entre 2 y 4 servicios. Sin servicios, la sección entera no se muestra, ni en la portada ni en Impacto.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir servicio', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'titulo', __('Nombre del servicio', 'ese-latam'), [
                            'key'          => 'field_serv_titulo',
                            'instructions' => __('Título en mayúsculas de la pestaña y del panel. Una o dos palabras. Vacío: el servicio no se muestra.', 'ese-latam'),
                            'wrapper'      => ['width' => '50'],
                        ]),
                        $campo('text', 'slug', __('Slug del servicio', 'ese-latam'), [
                            'key'          => 'field_serv_slug',
                            'instructions' => __('Identificador interno, en minúsculas y sin espacios ni tildes (por ejemplo, segregar). Vacío: se genera a partir del nombre.', 'ese-latam'),
                            'wrapper'      => ['width' => '50'],
                        ]),
                        $campo('text', 'subtitulo', __('Línea bajo el nombre del servicio', 'ese-latam'), [
                            'key'          => 'field_serv_sub',
                            'instructions' => __('Frase corta bajo el nombre, dentro de la pestaña. En móvil no se muestra. Hasta 50 caracteres. Vacía: no se muestra.', 'ese-latam'),
                            'placeholder'  => __('Información clara para tomar decisiones', 'ese-latam'),
                        ]),
                        $campo('textarea', 'descripcion', __('Descripción del servicio', 'ese-latam'), [
                            'key'          => 'field_serv_desc',
                            'instructions' => __('Texto bajo la foto del panel al elegir el servicio. Dos o tres frases. Vacía: el panel queda sin texto.', 'ese-latam'),
                            'rows'         => 3,
                        ]),
                        $campo('image', 'icono', __('Ícono del servicio', 'ese-latam'), [
                            'key'           => 'field_serv_icono',
                            'instructions'  => __('Ícono dentro del círculo de la pestaña; se vuelve blanco en la pestaña activa. SVG, o PNG monocromo con fondo transparente · 96×96 px (1:1) · máx. 20 KB. Vacío: la pestaña va sin ícono.', 'ese-latam'),
                            'return_format' => 'url',
                            'preview_size'  => 'thumbnail',
                            'wrapper'       => ['width' => '30'],
                        ]),
                        $campo('image', 'imagen', __('Foto del panel del servicio', 'ese-latam'), [
                            'key'           => 'field_serv_img',
                            'instructions'  => __('Foto grande a la derecha de las pestañas, con un velo oscuro y un botón de play decorativo encima. WebP o JPG · 1480×786 px (proporción 700:372, casi 16:9) · máx. 300 KB. Se recorta al centro. Vacía: el panel queda sin foto.', 'ese-latam'),
                            'return_format' => 'url',
                            'preview_size'  => 'medium',
                            'wrapper'       => ['width' => '70'],
                        ]),
                    ],
                ]),
            ]
        ),
        __('El selector Educar / Segregar / Transformar de la portada y de la página Impacto.', 'ese-latam')
    ));

    // ---------- Contactemos ----------
    // Seis campos: se parten en dos pestañas para no pasar de cinco por lámina.
    $contacto = ese_latam_campos_contacto('contacto_');
    acf_add_local_field_group($base(
        'contacto',
        __('Contactemos', 'ese-latam'),
        'ese-latam-contacto',
        array_merge(
            [$tab('contacto_textos', __('Contactemos', 'ese-latam'))],
            array_slice($contacto, 0, 4),
            [$tab('contacto_foto', __('Contactemos — foto y botón', 'ese-latam'))],
            array_slice($contacto, 4)
        ),
        __('El bloque de cierre que aparece al pie de casi todas las páginas. Cada página puede personalizarlo desde su propio editor.', 'ese-latam')
    ));

    // ---------- Override por página ----------
    $interruptor = static fn (string $seccion, string $label): array =>
        ese_latam_campo_def('true_false', $seccion . '_override', $label, [
            'instructions'  => __('Encendido: los campos de abajo reemplazan el contenido global solo en esta página, y los que dejes vacíos siguen usando el global. Apagado: se usa el contenido global del menú ESE Latam.', 'ese-latam'),
            'default_value' => 0,
            'ui'            => 1,
        ]);

    $solo_si = static fn (string $seccion): array => [
        'conditional_logic' => [[['field' => 'field_' . $seccion . '_override', 'operator' => '==', 'value' => '1']]],
    ];

    $contacto_pag = ese_latam_campos_contacto('contacto_pag_', $solo_si('contacto'));
    $pruebas_pag  = ese_latam_campos_pruebas('pruebas_pag_', $solo_si('pruebas'));

    acf_add_local_field_group([
        'key'      => 'group_secciones_pagina',
        'title'    => __('Secciones compartidas', 'ese-latam'),
        'location' => [
            [['param' => 'post_type', 'operator' => '==', 'value' => 'page']],
            [['param' => 'post_type', 'operator' => '==', 'value' => 'producto']],
            [['param' => 'post_type', 'operator' => '==', 'value' => 'sector']],
            [['param' => 'post_type', 'operator' => '==', 'value' => 'post']],
        ],
        'menu_order'            => 20,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => __('Cambia solo en esta página el contenido de los bloques que se repiten en toda la web. Con el interruptor apagado, cada bloque usa el contenido del menú ESE Latam. Solo tiene efecto si la página muestra ese bloque.', 'ese-latam'),
        'fields'                => array_merge(
            [
                $tab('pag_contacto', __('Contactemos', 'ese-latam')),
                $interruptor('contacto', __('¿Personalizar “Contactemos” en esta página?', 'ese-latam')),
            ],
            array_slice($contacto_pag, 0, 4),
            [$tab('pag_contacto_foto', __('Contactemos — foto y botón', 'ese-latam'))],
            array_slice($contacto_pag, 4),
            [
                $tab('pag_certificaciones', __('Certificaciones', 'ese-latam')),
                $interruptor('certificaciones', __('¿Personalizar “Certificaciones” en esta página?', 'ese-latam')),
            ],
            ese_latam_campos_certificaciones('certificaciones_pag_', $solo_si('certificaciones')),
            [
                $tab('pag_residuos', __('Residuos inteligentes', 'ese-latam')),
                $interruptor('residuos', __('¿Personalizar “Residuos inteligentes” en esta página?', 'ese-latam')),
            ],
            ese_latam_campos_residuos('residuos_pag_', $solo_si('residuos')),
            [
                $tab('pag_pruebas', __('Pruebas de rigurosidad', 'ese-latam')),
                $interruptor('pruebas', __('¿Personalizar “Pruebas de rigurosidad” en esta página?', 'ese-latam')),
            ],
            array_slice($pruebas_pag, 0, 4),
            [$tab('pag_pruebas_video', __('Pruebas de rigurosidad — video', 'ese-latam'))],
            array_slice($pruebas_pag, 4)
        ),
    ]);
});
