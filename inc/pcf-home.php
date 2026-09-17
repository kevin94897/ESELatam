<?php
/**
 * Campos de la portada (front-page.php), registrados por PHP igual que los
 * del CPT producto (inc/acf-productos.php): así viven en el theme, versionados
 * con el resto del código, y no dependen de que nadie los exporte a mano.
 *
 * Se cuelgan de la página fijada como portada (Ajustes → Lectura), que crea
 * inc/paginas.php. Las secciones que la home comparte con otras plantillas
 * —certificaciones, distribuidores, residuos y el cierre de contacto— NO
 * están acá: viven en inc/pcf-globales.php, porque su contenido es el mismo
 * en todo el sitio.
 *
 * Los sectores tampoco: son su propio módulo (inc/cpt-sectores.php), porque
 * cada uno tiene además su página. Acá quedan el hero, el marquee y los
 * titulares con los que cada sección se presenta en la portada.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Ayuda que acompaña a todos los campos de titular: la convención del
 * resaltado es la misma en toda la web (ver ese_latam_titulo() en inc/pcf.php).
 */
function ese_latam_ayuda_titulo(): string {
    return __('Encierra entre barras el tramo que va en color de marca: <code>sectores que |transformamos|</code>. Cada salto de línea es un renglón del titular.', 'ese-latam');
}

/**
 * Atajos para no repetir el mismo array 60 veces. `$k` arma la key a partir
 * del nombre, que es lo único que las plantillas necesitan conocer.
 *
 * @return array<string, mixed>
 */
function ese_latam_campo_def(string $type, string $name, string $label, array $extra = []): array {
    return array_merge([
        'key'   => 'field_' . $name,
        'name'  => $name,
        'type'  => $type,
        'label' => $label,
    ], $extra);
}

/**
 * Pestaña separadora dentro de un grupo.
 *
 * @return array<string, mixed>
 */
function ese_latam_campo_tab(string $key, string $label): array {
    return [
        'key'       => 'field_tab_' . $key,
        'name'      => '',
        'type'      => 'tab',
        'label'     => $label,
        'placement' => 'top',
    ];
}

add_action('acf/init', static function (): void {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    $campo = 'ese_latam_campo_def';
    $tab   = 'ese_latam_campo_tab';

    acf_add_local_field_group([
        'key'      => 'group_home',
        'title'    => __('Contenido de la portada', 'ese-latam'),
        'location' => [
            [
                ['param' => 'page_type', 'operator' => '==', 'value' => 'front_page'],
            ],
        ],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => __('Cada campo vacío se rellena solo con el texto que ya trae el diseño: se puede editar de a poco.', 'ese-latam'),
        'fields'                => [

            // ---------- Hero ----------
            $tab('home_hero', __('Hero', 'ese-latam')),
            $campo('textarea', 'hero_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => "Contener\npara |transformar|",
            ]),
            $campo('textarea', 'hero_lede', __('Bajada', 'ese-latam'), [
                'rows'        => 3,
                'placeholder' => __('Diseñamos y distribuimos soluciones de contención de residuos…', 'ese-latam'),
            ]),
            $campo('link', 'hero_cta', __('Botón', 'ese-latam'), [
                'instructions' => __('Texto y destino del botón principal. Vacío: “Explorar productos” hacia la sección Sectores.', 'ese-latam'),
            ]),
            $campo('image', 'hero_video_poster', __('Imagen de respaldo del video', 'ese-latam'), [
                'instructions'  => __('Se ve mientras el video carga y en dispositivos que no lo reproducen.', 'ese-latam'),
                'return_format' => 'url',
                'preview_size'  => 'medium',
            ]),
            $campo('image', 'hero_isla', __('Isla flotante', 'ese-latam'), [
                'instructions'  => __('El PNG o WebP con fondo transparente que flota sobre el video. Conviene subirlo a 2400px de ancho: WordPress genera solo los tamaños chicos para móvil. Vacía: no se muestra la isla.', 'ese-latam'),
                'return_format' => 'id',
                'preview_size'  => 'medium',
            ]),
            $campo('number', 'hero_isla_ancho', __('Isla — ancho máximo', 'ese-latam'), [
                'instructions' => __('En píxeles, para achicarla si la imagen se ve muy grande. Vacío: 1720. Su alto se limita solo, según la proporción de la imagen.', 'ese-latam'),
                'min'          => 320,
                'max'          => 3000,
                'append'       => 'px',
                'wrapper'      => ['width' => '40'],
            ]),
            $campo('file', 'hero_video', __('Video de fondo', 'ese-latam'), [
                'instructions'  => __('MP4 optimizado. Vacío: el video que trae el theme.', 'ese-latam'),
                'return_format' => 'url',
                'mime_types'    => 'mp4,webm',
            ]),
            $campo('text', 'hero_card_stat', __('Tarjeta — cifra', 'ese-latam'), [
                'placeholder' => '100%',
                'wrapper'     => ['width' => '30'],
            ]),
            $campo('text', 'hero_card_tag', __('Tarjeta — etiqueta', 'ese-latam'), [
                'placeholder' => __('HDPE de alta calidad', 'ese-latam'),
                'wrapper'     => ['width' => '70'],
            ]),
            $campo('textarea', 'hero_card_desc', __('Tarjeta — descripción', 'ese-latam'), [
                'rows'        => 2,
                'placeholder' => __('Contenedores de residuos sólidos para municipios y empresas en Latinoamérica', 'ese-latam'),
            ]),

            // ---------- Sectores ----------
            $tab('home_sectores', __('Sectores', 'ese-latam')),
            $campo('message', '', __('Dónde se usa esta lista', 'ese-latam'), [
                'key'      => 'field_msg_sectores',
                'message'  => __('Las tarjetas del slider salen del módulo <strong>Sectores</strong> del menú lateral: ahí se añaden, se ordenan y se edita la página de cada uno. Acá solo se define cómo se presenta la sección en la portada.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('text', 'sectores_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('Áreas de impacto', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('textarea', 'sectores_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => "sectores que\n|transformamos|",
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('wysiwyg', 'sectores_desc', __('Bajada', 'ese-latam'), [
                'instructions' => __('Se puede resaltar en negrita cualquier tramo.', 'ese-latam'),
                'tabs'         => 'visual',
                'toolbar'      => 'basic',
                'media_upload' => 0,
            ]),
            $campo('textarea', 'sectores_leyenda', __('Texto junto al contador del slider', 'ese-latam'), [
                'rows'        => 2,
                'placeholder' => __('Contenedores adaptados a cada sector…', 'ese-latam'),
            ]),

            // ---------- Marquee ----------
            $tab('home_marquee', __('Marquee', 'ese-latam')),
            $campo('repeater', 'marquee_pistas', __('Pistas', 'ese-latam'), [
                'instructions' => __('Una palabra por pista: el recorrido con el scroll es corto, así que una frase larga no se alcanza a leer entera.', 'ese-latam'),
                'layout'       => 'table',
                'min'          => 0,
                'max'          => 4,
                'button_label' => __('Añadir pista', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'texto', __('Texto', 'ese-latam'), ['key' => 'field_marquee_texto']),
                    $campo('select', 'direccion', __('Sentido', 'ese-latam'), [
                        'key'     => 'field_marquee_direccion',
                        'choices' => [
                            'left'  => __('Hacia la izquierda', 'ese-latam'),
                            'right' => __('Hacia la derecha', 'ese-latam'),
                        ],
                        'default_value' => 'left',
                    ]),
                ],
            ]),

            // ---------- Productos ----------
            $tab('home_productos', __('Productos', 'ese-latam')),
            $campo('text', 'productos_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('Nuestra gama de productos', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('textarea', 'productos_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => 'soluciones para |cada necesidad|',
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('wysiwyg', 'productos_desc', __('Bajada', 'ese-latam'), [
                'tabs'         => 'visual',
                'toolbar'      => 'basic',
                'media_upload' => 0,
            ]),
            $campo('relationship', 'productos_destacados', __('Productos destacados', 'ese-latam'), [
                'instructions'  => __('Fija qué productos aparecen en el slider y en qué orden. Vacío: los 12 más recientes del catálogo.', 'ese-latam'),
                'post_type'     => ['producto'],
                'filters'       => ['search'],
                'return_format' => 'id',
                'max'           => 12,
            ]),
            $campo('repeater', 'productos_filtros', __('Filtros', 'ese-latam'), [
                'instructions' => __('Las pastillas sobre el slider. Vacío: las líneas de producto del diseño.', 'ese-latam'),
                'layout'       => 'table',
                'button_label' => __('Añadir filtro', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'etiqueta', __('Etiqueta', 'ese-latam'), ['key' => 'field_productos_filtro_etiqueta']),
                ],
            ]),
            $campo('link', 'productos_cta', __('Enlace al catálogo', 'ese-latam'), [
                'instructions' => __('Vacío: “Explora todo el catálogo” hacia el archivo de productos.', 'ese-latam'),
            ]),

            // ---------- Distribuidores ----------
            $tab('home_distribuidores', __('Distribuidores', 'ese-latam')),
            $campo('message', '', __('Dónde se edita la lista', 'ese-latam'), [
                'key'      => 'field_msg_distribuidores',
                'message'  => __('Los países y sus distribuidores se editan en <strong>ESE Latam → Distribuidores</strong>: la misma lista alimenta el globo de la portada y la página “Encuentra un distribuidor”.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('text', 'distribuidores_kicker', __('Antetítulo', 'ese-latam'), [
                'placeholder' => __('Distribuidores', 'ese-latam'),
                'wrapper'     => ['width' => '50'],
            ]),
            $campo('textarea', 'distribuidores_titulo', __('Titular', 'ese-latam'), [
                'instructions' => ese_latam_ayuda_titulo(),
                'rows'         => 2,
                'placeholder'  => "Presencia |sin|\n|fronteras|",
                'wrapper'      => ['width' => '50'],
            ]),
            $campo('textarea', 'distribuidores_desc', __('Bajada', 'ese-latam'), [
                'rows'        => 2,
                'placeholder' => __('Nuestra red de distribuidores autorizados…', 'ese-latam'),
            ]),
            $campo('link', 'distribuidores_cta', __('Enlace de la tarjeta', 'ese-latam'), [
                'instructions' => __('Vacío: “Contactar distribuidor” hacia la sección de contacto.', 'ese-latam'),
            ]),
        ],
    ]);
});
