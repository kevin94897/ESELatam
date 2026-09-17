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
 * @param string $prefijo 'contacto_' o 'contacto_pag_'
 * @param array<string, mixed> $extra Ajustes comunes (p. ej. conditional_logic).
 * @return list<array<string, mixed>>
 */
function ese_latam_campos_contacto(string $prefijo, array $extra = []): array {
    $campo = static fn (string $type, string $name, string $label, array $args = []): array =>
        ese_latam_campo_def($type, $prefijo . $name, $label, array_merge($args, $extra));

    return [
        $campo('text', 'kicker', __('Antetítulo', 'ese-latam'), [
            'placeholder' => __('Contactemos', 'ese-latam'),
        ]),
        $campo('textarea', 'titulo', __('Titular', 'ese-latam'), [
            'instructions' => ese_latam_ayuda_titulo(),
            'rows'         => 2,
            'placeholder'  => __('¿Listo para llevar tu gestión de residuos al |siguiente nivel|', 'ese-latam'),
        ]),
        $campo('true_false', 'pregunta', __('Cerrar el titular con “?”', 'ese-latam'), [
            'default_value' => 1,
            'ui'            => 1,
        ]),
        $campo('textarea', 'desc', __('Bajada', 'ese-latam'), ['rows' => 3]),
        $campo('image', 'fondo', __('Foto de fondo', 'ese-latam'), [
            'return_format' => 'url',
            'preview_size'  => 'medium',
        ]),
        $campo('link', 'cta', __('Botón', 'ese-latam'), [
            'instructions' => __('Vacío: “Contactar” hacia la página de Contacto.', 'ese-latam'),
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

    return [
        $campo('text', 'kicker', __('Antetítulo', 'ese-latam'), [
            'placeholder' => __('Estándar global', 'ese-latam'),
            'wrapper'     => ['width' => '50'],
        ]),
        $campo('textarea', 'titulo', __('Titular', 'ese-latam'), [
            'instructions' => ese_latam_ayuda_titulo(),
            'rows'         => 2,
            'placeholder'  => __('nuestras |certificaciones|', 'ese-latam'),
            'wrapper'      => ['width' => '50'],
        ]),
        $campo('wysiwyg', 'desc', __('Bajada', 'ese-latam'), [
            'tabs'         => 'visual',
            'toolbar'      => 'basic',
            'media_upload' => 0,
        ]),
        $campo('link', 'enlace', __('Enlace del encabezado', 'ese-latam'), [
            'instructions' => __('Vacío: “Explora todas nuestras certificaciones” hacia la página de Certificaciones.', 'ese-latam'),
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

    return [
        $campo('text', 'kicker', __('Antetítulo', 'ese-latam'), [
            'placeholder' => __('Pruebas de rigurosidad', 'ese-latam'),
            'wrapper'     => ['width' => '40'],
        ]),
        $campo('textarea', 'titulo', __('Titular', 'ese-latam'), [
            'instructions' => ese_latam_ayuda_titulo(),
            'rows'         => 2,
            'placeholder'  => "¿qué hace que\n|sea excelente?|",
            'wrapper'      => ['width' => '60'],
        ]),
        $campo('wysiwyg', 'desc', __('Bajada', 'ese-latam'), [
            'tabs'         => 'visual',
            'toolbar'      => 'basic',
            'media_upload' => 0,
        ]),
        $campo('repeater', 'lista', __('Pruebas', 'ese-latam'), [
            'instructions' => __('Las tarjetas se reparten a los dos lados del video, mitad y mitad. Sin pruebas cargadas, el bloque no se muestra.', 'ese-latam'),
            'layout'       => 'table',
            'max'          => 10,
            'button_label' => __('Añadir prueba', 'ese-latam'),
            'sub_fields'   => [
                ese_latam_campo_def('text', 'title', __('Nombre', 'ese-latam'), [
                    'key'       => 'field_' . $prefijo . 'titulo_item',
                    'required'  => 1,
                    'maxlength' => 40,
                ]),
                ese_latam_campo_def('textarea', 'desc', __('Descripción', 'ese-latam'), [
                    'key'  => 'field_' . $prefijo . 'desc_item',
                    'rows' => 2,
                ]),
            ],
        ]),
        $campo('file', 'video', __('Video', 'ese-latam'), [
            'instructions'  => __('MP4 optimizado. Sin video, el centro queda con el botón de play.', 'ese-latam'),
            'return_format' => 'url',
            'mime_types'    => 'mp4,webm',
            'wrapper'       => ['width' => '50'],
        ]),
        $campo('image', 'poster', __('Imagen del video', 'ese-latam'), [
            'instructions'  => __('Se ve antes de darle al play.', 'ese-latam'),
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

    return [
        $campo('text', 'kicker', __('Antetítulo', 'ese-latam'), [
            'placeholder' => __('Residuos inteligentes', 'ese-latam'),
            'wrapper'     => ['width' => '50'],
        ]),
        $campo('textarea', 'titulo', __('Titular', 'ese-latam'), [
            'instructions' => ese_latam_ayuda_titulo(),
            'rows'         => 2,
            'placeholder'  => "ingeniería de\n|alto desempeño|",
            'wrapper'      => ['width' => '50'],
        ]),
        $campo('wysiwyg', 'desc', __('Bajada', 'ese-latam'), [
            'tabs'         => 'visual',
            'toolbar'      => 'basic',
            'media_upload' => 0,
        ]),
        $campo('link', 'enlace', __('Enlace del pie', 'ese-latam'), [
            'instructions' => __('Vacío: “Conoce nuestro impacto” hacia la sección de impacto.', 'ese-latam'),
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
            $campo('link', 'cabecera_cta', __('Botón del menú', 'ese-latam'), [
                'instructions' => __('El botón azul de la barra superior, que se repite en el menú móvil. Vacío: “Contacto” hacia la página de Contacto.', 'ese-latam'),
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
            ese_latam_campos_certificaciones('certificaciones_'),
            [
                $tab('cert_sellos', __('Sellos', 'ese-latam')),
                $campo('repeater', 'certificaciones_lista', __('Sellos', 'ese-latam'), [
                    'layout'       => 'block',
                    'button_label' => __('Añadir sello', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'nombre', __('Nombre', 'ese-latam'), [
                            'key'     => 'field_cert_nombre',
                            'wrapper' => ['width' => '40'],
                        ]),
                        $campo('text', 'descripcion', __('Descripción', 'ese-latam'), [
                            'key'     => 'field_cert_desc',
                            'wrapper' => ['width' => '60'],
                        ]),
                        $campo('image', 'logo', __('Logo', 'ese-latam'), [
                            'key'           => 'field_cert_logo',
                            'return_format' => 'url',
                            'preview_size'  => 'thumbnail',
                        ]),
                    ],
                ]),
            ]
        ),
        __('La franja de sellos que aparece en la portada, en la ficha de producto y en las singles de sector.', 'ese-latam')
    ));

    // ---------- Distribuidores ----------
    acf_add_local_field_group($base(
        'distribuidores',
        __('Distribuidores', 'ese-latam'),
        'ese-latam-distribuidores',
        [
            $campo('text', 'distribuidores_pendiente_nombre', __('Países sin distribuidor — título de la ficha', 'ese-latam'), [
                'instructions' => __('Se muestra en los países que todavía no tienen empresas cargadas. Vacío en ambos campos: esos países aparecen sin ficha.', 'ese-latam'),
                'placeholder'  => __('Distribuidor autorizado', 'ese-latam'),
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('text', 'distribuidores_pendiente_texto', __('Países sin distribuidor — texto', 'ese-latam'), [
                'placeholder' => __('Datos de contacto próximamente', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('repeater', 'distribuidores_paises', __('Países conectados', 'ese-latam'), [
                'instructions' => __('Cada país es un punto en el globo de la portada y una pastilla en el riel. Se pueden añadir los que hagan falta.', 'ese-latam'),
                'layout'       => 'block',
                'button_label' => __('Añadir país', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'nombre', __('País', 'ese-latam'), [
                        'key'     => 'field_pais_nombre',
                        'wrapper' => ['width' => '40'],
                    ]),
                    $campo('text', 'slug', __('Slug', 'ese-latam'), [
                        'key'     => 'field_pais_slug',
                        'wrapper' => ['width' => '20'],
                    ]),
                    $campo('number', 'lat', __('Latitud', 'ese-latam'), [
                        'key'          => 'field_pais_lat',
                        'instructions' => __('Centro del país, para que el globo lo apunte.', 'ese-latam'),
                        'step'         => 'any',
                        'wrapper'      => ['width' => '20'],
                    ]),
                    $campo('number', 'lng', __('Longitud', 'ese-latam'), [
                        'key'     => 'field_pais_lng',
                        'step'    => 'any',
                        'wrapper' => ['width' => '20'],
                    ]),
                    $campo('repeater', 'empresas', __('Distribuidores del país', 'ese-latam'), [
                        'key'          => 'field_pais_empresas',
                        'instructions' => __('Sin ninguno, el país se muestra como “Datos de contacto próximamente”.', 'ese-latam'),
                        'layout'       => 'block',
                        'button_label' => __('Añadir distribuidor', 'ese-latam'),
                        'sub_fields'   => [
                            $campo('text', 'nombre', __('Empresa', 'ese-latam'), [
                                'key'     => 'field_dst_nombre',
                                'wrapper' => ['width' => '60'],
                            ]),
                            $campo('text', 'ciudad', __('Ciudad', 'ese-latam'), [
                                'key'     => 'field_dst_ciudad',
                                'wrapper' => ['width' => '40'],
                            ]),
                            $campo('textarea', 'descripcion', __('Descripción', 'ese-latam'), [
                                'key'  => 'field_dst_desc',
                                'rows' => 2,
                            ]),
                            $campo('text', 'representante', __('Representante', 'ese-latam'), [
                                'key'     => 'field_dst_rep',
                                'wrapper' => ['width' => '50'],
                            ]),
                            $campo('text', 'telefono', __('Teléfono', 'ese-latam'), [
                                'key'     => 'field_dst_tel',
                                'wrapper' => ['width' => '50'],
                            ]),
                            $campo('email', 'email', __('Email', 'ese-latam'), [
                                'key'     => 'field_dst_email',
                                'wrapper' => ['width' => '50'],
                            ]),
                            $campo('text', 'whatsapp', __('WhatsApp', 'ese-latam'), [
                                'key'          => 'field_dst_wa',
                                'instructions' => __('Solo dígitos, con código de país (51999888777).', 'ese-latam'),
                                'wrapper'      => ['width' => '50'],
                            ]),
                            $campo('text', 'direccion', __('Dirección', 'ese-latam'), [
                                'key' => 'field_dst_dir',
                            ]),
                            $campo('url', 'web', __('Sitio web', 'ese-latam'), [
                                'key'     => 'field_dst_web',
                                'wrapper' => ['width' => '60'],
                            ]),
                            $campo('image', 'logo', __('Logo', 'ese-latam'), [
                                'key'           => 'field_dst_logo',
                                'return_format' => 'url',
                                'preview_size'  => 'thumbnail',
                                'wrapper'       => ['width' => '40'],
                            ]),
                        ],
                    ]),
                ],
            ]),
        ],
        __('La red que alimenta el globo de la portada y la página “Encuentra un distribuidor”.', 'ese-latam')
    ));

    // ---------- Pruebas de rigurosidad ----------
    acf_add_local_field_group($base(
        'pruebas',
        __('Pruebas de rigurosidad', 'ese-latam'),
        'ese-latam-pruebas',
        ese_latam_campos_pruebas('pruebas_'),
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
                $campo('repeater', 'residuos_servicios', __('Servicios', 'ese-latam'), [
                    'instructions' => __('Cada servicio es una pestaña del selector; al elegirla cambian la foto, el título y la descripción del panel.', 'ese-latam'),
                    'layout'       => 'block',
                    'button_label' => __('Añadir servicio', 'ese-latam'),
                    'sub_fields'   => [
                        $campo('text', 'titulo', __('Nombre', 'ese-latam'), [
                            'key'     => 'field_serv_titulo',
                            'wrapper' => ['width' => '50'],
                        ]),
                        $campo('text', 'slug', __('Slug', 'ese-latam'), [
                            'key'     => 'field_serv_slug',
                            'wrapper' => ['width' => '50'],
                        ]),
                        $campo('text', 'subtitulo', __('Línea secundaria', 'ese-latam'), [
                            'key'         => 'field_serv_sub',
                            'placeholder' => __('Información clara para tomar decisiones', 'ese-latam'),
                        ]),
                        $campo('textarea', 'descripcion', __('Descripción', 'ese-latam'), [
                            'key'  => 'field_serv_desc',
                            'rows' => 3,
                        ]),
                        $campo('image', 'icono', __('Ícono', 'ese-latam'), [
                            'key'           => 'field_serv_icono',
                            'instructions'  => __('SVG o PNG monocromo.', 'ese-latam'),
                            'return_format' => 'url',
                            'preview_size'  => 'thumbnail',
                            'wrapper'       => ['width' => '30'],
                        ]),
                        $campo('image', 'imagen', __('Foto del panel', 'ese-latam'), [
                            'key'           => 'field_serv_img',
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
    acf_add_local_field_group($base(
        'contacto',
        __('Contactemos', 'ese-latam'),
        'ese-latam-contacto',
        ese_latam_campos_contacto('contacto_'),
        __('El bloque de cierre que aparece al pie de casi todas las páginas. Cada página puede personalizarlo desde su propio editor.', 'ese-latam')
    ));

    // ---------- Override por página ----------
    $interruptor = static fn (string $seccion, string $label): array =>
        ese_latam_campo_def('true_false', $seccion . '_override', $label, [
            'instructions'  => __('Apagado: se usa el contenido global del menú ESE Latam.', 'ese-latam'),
            'default_value' => 0,
            'ui'            => 1,
        ]);

    $solo_si = static fn (string $seccion): array => [
        'conditional_logic' => [[['field' => 'field_' . $seccion . '_override', 'operator' => '==', 'value' => '1']]],
    ];

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
        'description'           => __('Permite cambiar en esta página el copy de las secciones que se repiten en toda la web.', 'ese-latam'),
        'fields'                => array_merge(
            [
                $tab('pag_contacto', __('Contactemos', 'ese-latam')),
                $interruptor('contacto', __('Personalizar “Contactemos” en esta página', 'ese-latam')),
            ],
            ese_latam_campos_contacto('contacto_pag_', $solo_si('contacto')),
            [
                $tab('pag_certificaciones', __('Certificaciones', 'ese-latam')),
                $interruptor('certificaciones', __('Personalizar “Certificaciones” en esta página', 'ese-latam')),
            ],
            ese_latam_campos_certificaciones('certificaciones_pag_', $solo_si('certificaciones')),
            [
                $tab('pag_residuos', __('Residuos inteligentes', 'ese-latam')),
                $interruptor('residuos', __('Personalizar “Residuos inteligentes” en esta página', 'ese-latam')),
            ],
            ese_latam_campos_residuos('residuos_pag_', $solo_si('residuos')),
            [
                $tab('pag_pruebas', __('Pruebas de rigurosidad', 'ese-latam')),
                $interruptor('pruebas', __('Personalizar “Pruebas de rigurosidad” en esta página', 'ese-latam')),
            ],
            ese_latam_campos_pruebas('pruebas_pag_', $solo_si('pruebas'))
        ),
    ]);
});
