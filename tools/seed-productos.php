<?php
/**
 * Siembra el catálogo de ejemplo (CPT `producto`) con los renders reales ya
 * normalizados por tools/normalizar-renders.sh.
 *
 *   cd app/public && php wp-content/themes/ese-latam/tools/seed-productos.php
 *
 * Es IDEMPOTENTE: cada adjunto lleva la meta `_ese_seed_key` y cada producto
 * la meta `_ese_seed_producto`, así que volver a correrlo actualiza en vez de
 * duplicar. Un producto que ya exista con el mismo slug (cargado a mano desde
 * wp-admin) se reutiliza en lugar de crear otro.
 *
 * La matriz de fotos por (litraje × color) va al repeater anidado `imagenes`
 * de cada color — ver inc/acf-productos.php y el mapa `imgs` que arma
 * template-parts/producto-hero.php para product-config.ts.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if ('cli' !== PHP_SAPI) {
    exit("Solo por línea de comandos.\n");
}

$ese_wp_load = getcwd() . '/wp-load.php';
if (! file_exists($ese_wp_load)) {
    exit("Ejecutalo desde app/public (no se encontró wp-load.php).\n");
}
require $ese_wp_load;

if (! function_exists('update_field')) {
    exit("ACF no está activo.\n");
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

/*
 * Los renders ya no viven dentro del theme (el repositorio no carga los ~90 MB
 * de PNG originales): salieron a la carpeta de imágenes del cliente. Las rutas
 * relativas que usa este script cuelgan de acá, y se puede pisar con
 * --base <directorio>.
 */
$ese_base_i = array_search('--base', $argv, true);
define(
    'ESE_SEED_BASE',
    false !== $ese_base_i && isset($argv[$ese_base_i + 1])
        ? rtrim(strtr($argv[$ese_base_i + 1], '\\', '/'), '/')
        : 'C:/Users/Usuario/Desktop/Kevin DATA/ESE Latam Productos Imagenes 2026/Contenedores con ruedas (renders originales)'
);

/**
 * Copia un archivo del theme a la biblioteca de medios, una sola vez.
 *
 * @return int ID del adjunto, 0 si el archivo no existe.
 */
function ese_seed_attachment(string $rel_path, string $seed_key, string $title, string $alt): int
{
    $existing = get_posts([
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_ese_seed_key',
        'meta_value'     => $seed_key,
    ]);
    if (! empty($existing)) {
        return (int) $existing[0];
    }

    $src = ESE_SEED_BASE . '/' . $rel_path;
    if (! file_exists($src)) {
        fwrite(STDERR, "  !! no existe: $rel_path\n");
        return 0;
    }

    $uploads = wp_upload_dir();
    $dest    = $uploads['path'] . '/' . wp_unique_filename($uploads['path'], basename($src));

    if (! copy($src, $dest)) {
        fwrite(STDERR, "  !! no se pudo copiar: $rel_path\n");
        return 0;
    }

    $id = wp_insert_attachment([
        'post_mime_type' => wp_check_filetype($dest)['type'] ?: 'image/webp',
        'post_title'     => $title,
        'post_status'    => 'inherit',
    ], $dest);

    if (is_wp_error($id) || 0 === $id) {
        return 0;
    }

    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $dest));
    update_post_meta($id, '_wp_attachment_image_alt', $alt);
    update_post_meta($id, '_ese_seed_key', $seed_key);

    return (int) $id;
}

/** Devuelve el ID del término, creándolo si hace falta. */
function ese_seed_term(string $name, string $taxonomy): int
{
    $term = get_term_by('name', $name, $taxonomy);
    if ($term instanceof WP_Term) {
        return $term->term_id;
    }
    $created = wp_insert_term($name, $taxonomy);
    return is_wp_error($created) ? 0 : (int) $created['term_id'];
}

/**
 * Crea o actualiza un producto.
 *
 * @param array<string, mixed> $data
 */
function ese_seed_producto(string $seed_key, array $data): int
{
    $existing = get_posts([
        'post_type'      => 'producto',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_ese_seed_producto',
        'meta_value'     => $seed_key,
    ]);

    if (empty($existing)) {
        $existing = get_posts([
            'post_type'      => 'producto',
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'name'           => $data['slug'],
        ]);
    }

    $postarr = [
        'post_type'   => 'producto',
        'post_status' => 'publish',
        'post_title'  => $data['titulo'],
        'post_name'   => $data['slug'],
        'menu_order'  => $data['orden'] ?? 0,
    ];
    if (! empty($existing)) {
        $postarr['ID'] = (int) $existing[0];
    }

    $id = empty($existing) ? wp_insert_post($postarr, true) : wp_update_post($postarr, true);

    if (is_wp_error($id)) {
        fwrite(STDERR, "  !! {$data['titulo']}: " . $id->get_error_message() . "\n");
        return 0;
    }

    $id = (int) $id;
    update_post_meta($id, '_ese_seed_producto', $seed_key);

    if (! empty($data['thumb_id'])) {
        set_post_thumbnail($id, (int) $data['thumb_id']);
    }

    wp_set_object_terms($id, $data['categorias'] ?? [], 'producto_categoria');
    wp_set_object_terms($id, $data['certificaciones'] ?? [], 'producto_certificacion');

    update_field('descripcion_corta', $data['descripcion'], $id);
    update_field('enlace_compra', $data['enlace_compra'] ?? home_url('/contacto/'), $id);
    update_field('litrajes', $data['litrajes'] ?? [], $id);
    update_field('colores', $data['colores'] ?? [], $id);
    update_field('caracteristicas', $data['caracteristicas'] ?? [], $id);

    return $id;
}

/*
 * Colores ESE por código FC. El hex se midió sobre el cuerpo del contenedor
 * en los renders de 2 ruedas (los de 3 ruedas están mucho más apagados por
 * la iluminación de ese modelo, y es el mismo esmalte): así el swatch del
 * panel es exactamente el color de la pieza que se ve al lado.
 */
const ESE_COLORES = [
    '040' => ['nombre' => 'Verde',       'hex' => '#45753C'],
    '081' => ['nombre' => 'Azul',        'hex' => '#006CBF'],
    '050' => ['nombre' => 'Amarillo',    'hex' => '#E4C100'],
    '020' => ['nombre' => 'Negro',       'hex' => '#1B1A23'],
    '030' => ['nombre' => 'Gris oscuro', 'hex' => '#32313D'],
    '090' => ['nombre' => 'Marrón',      'hex' => '#5B3A23'],
];

/**
 * Arma el repeater `colores` completo de una familia de renders.
 *
 * @param list<string> $litrajes    Ej. ['80L', '120L'].
 * @param string       $carpeta     Carpeta /web relativa al theme.
 * @param string       $sufijo      '' para 2 ruedas, '-3pl' para 3 ruedas.
 * @param string       $clave       Prefijo de la meta de siembra.
 * @param string       $etiqueta    Nombre del producto para título/alt.
 * @param string       $litraje_base Litraje cuya foto queda como "base".
 * @return array{0: list<array<string, mixed>>, 1: int} Repeater y ID de la destacada.
 */
function ese_seed_colores(
    array $litrajes,
    string $carpeta,
    string $sufijo,
    string $clave,
    string $etiqueta,
    string $litraje_base
): array {
    $colores = [];
    $thumb   = 0;

    foreach (ESE_COLORES as $codigo => $color) {
        $variantes = [];
        $base      = 0;

        foreach ($litrajes as $litraje) {
            $litros = (int) rtrim($litraje, 'L');
            $rel    = sprintf('%s/%dl%s-fc%s.webp', $carpeta, $litros, $sufijo, $codigo);

            $att_id = ese_seed_attachment(
                $rel,
                sprintf('%s-%dl-fc%s', $clave, $litros, $codigo),
                sprintf('%s %s — %s', $etiqueta, $litraje, $color['nombre']),
                sprintf('%s de %s en color %s', $etiqueta, $litraje, mb_strtolower($color['nombre']))
            );

            if (0 === $att_id) {
                continue;
            }

            $variantes[] = ['litraje' => $litraje, 'imagen' => $att_id];

            if ($litraje === $litraje_base) {
                $base = $att_id;
            }
        }

        $colores[] = [
            'nombre'   => $color['nombre'],
            'color'    => $color['hex'],
            'imagen'   => $base,
            'imagenes' => $variantes,
        ];

        if ('040' === $codigo) {
            $thumb = $base;
        }

        echo "  {$color['nombre']}: " . count($variantes) . " litrajes\n";
    }

    return [$colores, $thumb];
}

/* ------------------------------------------------------------------ */

$cat_contenedores = ese_seed_term('Contenedores', 'producto_categoria');
$cat_papeleras    = ese_seed_term('Papeleras', 'producto_categoria');
$cat_soterrados   = ese_seed_term('Soterrados', 'producto_categoria');

foreach (['DIN EN 840', 'PKN', 'TÜV SÜD', 'Blue Angel'] as $ese_cert) {
    ese_seed_term($ese_cert, 'producto_certificacion');
}

// ---------- Contenedor 2 ruedas ----------

echo "Contenedor 2 ruedas\n";
[$ese_col_2r, $ese_thumb_2r] = ese_seed_colores(
    ['80L', '120L', '180L', '240L', '360L'],
    'contenedores-2-ruedas/web',
    '',
    'c2r',
    'Contenedor 2 ruedas',
    '120L'
);

ese_seed_producto('contenedor-2-ruedas', [
    'titulo'      => 'Contenedor 2 ruedas',
    'slug'        => 'contenedor-2-ruedas',
    'orden'       => 0,
    'thumb_id'    => $ese_thumb_2r,
    'descripcion' => 'Contenedor de carga trasera fabricado en HDPE virgen por inyección en una sola pieza, con tapa plana y ruedas macizas de 200 mm. Disponible en cinco capacidades y seis colores de segregación, compatible con todos los sistemas de levante por peine europeo.',
    'categorias'  => array_filter([$cat_contenedores]),
    'certificaciones' => ['DIN EN 840', 'PKN', 'TÜV SÜD'],
    'litrajes'    => array_map(
        static fn (string $v): array => ['valor' => $v, 'predeterminado' => '120L' === $v],
        ['80L', '120L', '180L', '240L', '360L']
    ),
    'colores'     => $ese_col_2r,
    'caracteristicas' => [
        ['etiqueta' => 'Volumen',      'valor' => '120L'],
        ['etiqueta' => 'Material',     'valor' => 'HDPE virgen de alta densidad'],
        ['etiqueta' => 'Carga máxima', 'valor' => '48 kg'],
        ['etiqueta' => 'Ruedas',       'valor' => '2 macizas de 200 mm'],
        ['etiqueta' => 'Norma',        'valor' => 'DIN EN 840-1'],
        ['etiqueta' => 'Temperatura',  'valor' => '-40 °C a 80 °C'],
    ],
]);

// ---------- Contenedor 3 ruedas ----------

echo "\nContenedor 3 ruedas\n";
[$ese_col_3r, $ese_thumb_3r] = ese_seed_colores(
    ['240L', '370L'],
    'contenedores-3-ruedas/web',
    '-3pl',
    'c3r',
    'Contenedor 3 ruedas',
    '240L'
);

ese_seed_producto('contenedor-3-ruedas', [
    'titulo'      => 'Contenedor 3 ruedas',
    'slug'        => 'contenedor-3-ruedas',
    'orden'       => 1,
    'thumb_id'    => $ese_thumb_3r,
    'descripcion' => 'Contenedor de carga trasera con tercera rueda pivotante: el peso se reparte en tres apoyos y el equipo gira sobre su propio eje, así una sola persona maniobra cargas completas en veredas angostas y rampas. Cuerpo en HDPE virgen con tapa reforzada y pedal opcional.',
    'categorias'  => array_filter([$cat_contenedores]),
    'certificaciones' => ['DIN EN 840', 'Blue Angel'],
    'litrajes'    => [
        ['valor' => '240L', 'predeterminado' => true],
        ['valor' => '370L', 'predeterminado' => false],
    ],
    'colores'     => $ese_col_3r,
    'caracteristicas' => [
        ['etiqueta' => 'Volumen',      'valor' => '240L'],
        ['etiqueta' => 'Material',     'valor' => 'HDPE virgen de alta densidad'],
        ['etiqueta' => 'Carga máxima', 'valor' => '96 kg'],
        ['etiqueta' => 'Ruedas',       'valor' => '2 traseras + 1 pivotante'],
        ['etiqueta' => 'Norma',        'valor' => 'DIN EN 840-2'],
        ['etiqueta' => 'Temperatura',  'valor' => '-40 °C a 80 °C'],
    ],
]);

// ---------- Contenedor 4 ruedas ----------

echo "
Contenedor 4 ruedas
";
[$ese_col_4r, $ese_thumb_4r] = ese_seed_colores(
    ['400L', '500L', '660L', '770L', '1100L'],
    'contenedores-4-ruedas/web',
    '-4r',
    'c4r',
    'Contenedor 4 ruedas',
    '770L'
);

ese_seed_producto('contenedor-4-ruedas', [
    'titulo'      => 'Contenedor 4 ruedas',
    'slug'        => 'contenedor-4-ruedas',
    'orden'       => 2,
    'thumb_id'    => $ese_thumb_4r,
    'descripcion' => 'Contenedor de gran volumen sobre cuatro ruedas giratorias, para recolección municipal e industrial. Cuerpo y tapa en HDPE virgen, con refuerzos verticales, tapa abatible con amortiguación y dos ruedas con freno. Compatible con levante por peine y por muñones.',
    'categorias'  => array_filter([$cat_contenedores]),
    'certificaciones' => ['DIN EN 840', 'TÜV SÜD', 'PKN'],
    'litrajes'    => array_map(
        static fn (string $v): array => ['valor' => $v, 'predeterminado' => '770L' === $v],
        ['400L', '500L', '660L', '770L', '1100L']
    ),
    'colores'     => $ese_col_4r,
    'caracteristicas' => [
        ['etiqueta' => 'Volumen',      'valor' => '770L'],
        ['etiqueta' => 'Material',     'valor' => 'HDPE virgen de alta densidad'],
        ['etiqueta' => 'Carga máxima', 'valor' => '308 kg'],
        ['etiqueta' => 'Ruedas',       'valor' => '4 giratorias de 200 mm, 2 con freno'],
        ['etiqueta' => 'Norma',        'valor' => 'DIN EN 840-3'],
        ['etiqueta' => 'Temperatura',  'valor' => '-40 °C a 80 °C'],
    ],
]);

/*
 * Los dos que siguen todavía no tienen renders propios: van con la foto que
 * ya estaba cargada y datos de ejemplo, para que la grilla del catálogo, los
 * filtros y el carrusel de "Soluciones recomendadas" (necesita 3 productos)
 * tengan con qué trabajar. Cuando lleguen sus renders se resuelven igual que
 * los de arriba, con ese_seed_colores().
 */

echo "\nResto del catálogo de ejemplo\n";

$ese_otros = [
    [
        'seed'   => 'papelera-urbana',
        'titulo' => 'Papelera Urbana',
        'slug'   => 'papelera-urbana',
        'orden'  => 3,
        'descripcion' => 'Papelera compacta para espacios públicos de alto tránsito. Cuerpo monobloque resistente a rayos UV y vandalismo, con anclaje a piso y sistema de vaciado rápido por bolsa interior.',
        'categorias' => array_filter([$cat_papeleras]),
        'certificaciones' => ['PKN'],
        'litrajes' => [
            ['valor' => '50L', 'predeterminado' => true],
            ['valor' => '80L', 'predeterminado' => false],
        ],
        'colores' => [
            ['nombre' => 'Gris oscuro', 'color' => '#32313D', 'imagen' => 0, 'imagenes' => []],
            ['nombre' => 'Verde',       'color' => '#45753C', 'imagen' => 0, 'imagenes' => []],
        ],
        'caracteristicas' => [
            ['etiqueta' => 'Volumen',      'valor' => '50L'],
            ['etiqueta' => 'Material',     'valor' => 'HDPE con protección UV'],
            ['etiqueta' => 'Carga máxima', 'valor' => '20 kg'],
            ['etiqueta' => 'Instalación',  'valor' => 'Anclaje a piso'],
        ],
    ],
    [
        'seed'   => 'contenedor-soterrado',
        'titulo' => 'Contenedor Soterrado',
        'slug'   => 'contenedor-soterrado',
        'orden'  => 4,
        'descripcion' => 'Sistema soterrado de gran capacidad para recolección municipal. El depósito va bajo nivel de vereda y solo el buzón queda a la vista, reduciendo olores, ocupación de espacio público e impacto visual.',
        'categorias' => array_filter([$cat_soterrados]),
        'certificaciones' => ['DIN EN 840', 'TÜV SÜD'],
        'litrajes' => [
            ['valor' => '3000L', 'predeterminado' => true],
            ['valor' => '5000L', 'predeterminado' => false],
        ],
        'colores' => [
            ['nombre' => 'Gris oscuro', 'color' => '#32313D', 'imagen' => 0, 'imagenes' => []],
        ],
        'caracteristicas' => [
            ['etiqueta' => 'Volumen',      'valor' => '3000L'],
            ['etiqueta' => 'Material',     'valor' => 'HDPE y acero galvanizado'],
            ['etiqueta' => 'Carga máxima', 'valor' => '1200 kg'],
            ['etiqueta' => 'Izaje',        'valor' => 'Gancho simple / doble'],
        ],
    ],
];

foreach ($ese_otros as $ese_p) {
    $ese_seed = $ese_p['seed'];
    unset($ese_p['seed']);
    $ese_pid = ese_seed_producto($ese_seed, $ese_p);
    echo "  #$ese_pid — {$ese_p['titulo']}\n";
}

/* ------------------------------------------------------------------ */

echo "\nCatálogo:\n";
$ese_all = new WP_Query([
    'post_type'      => 'producto',
    'posts_per_page' => -1,
    'post_status'    => 'any',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
]);

foreach ($ese_all->posts as $ese_post) {
    $ese_c = get_field('colores', $ese_post->ID);
    $ese_l = get_field('litrajes', $ese_post->ID);
    $ese_t = get_the_terms($ese_post->ID, 'producto_categoria');
    printf(
        "  #%-3d %-22s %-13s %d litrajes × %d colores  destacada:%s\n",
        $ese_post->ID,
        $ese_post->post_title,
        is_array($ese_t) && $ese_t ? $ese_t[0]->name : '—',
        is_array($ese_l) ? count($ese_l) : 0,
        is_array($ese_c) ? count($ese_c) : 0,
        get_the_post_thumbnail_url($ese_post->ID, 'large') ? 'sí' : 'NO'
    );
}

echo "\nListo.\n";
