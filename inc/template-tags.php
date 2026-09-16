<?php
/**
 * Componentes reutilizables de marcado (template tags).
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * CTA primario ("píldora" + chip de flecha) reutilizable en varias páginas.
 * El hover (color, escala, morph de las siluetas SVG) lo maneja por completo
 * el CSS/JS existente — `.hero-cta*` en main.css y hero-cta.ts — así que
 * cualquier instancia que imprima este marcado queda animada automáticamente
 * en cuanto `initHeroCta()` corre (busca todos los `.hero-cta` del documento).
 *
 * @param array{
 *     href?: string,
 *     label?: string,
 *     reveal?: bool,
 *     class?: string,
 *     target?: string,  '_blank' si el campo pidió abrir en otra pestaña
 * } $args
 */
function ese_latam_cta_button(array $args = []): void {
    $args = wp_parse_args($args, [
        'href'   => '#',
        'label'  => __('Explorar productos', 'ese-latam'),
        'reveal' => false,
        'class'  => '',
        'target' => '',
    ]);

    $classes = trim('hero-cta ' . $args['class']);
    ?>
    <a href="<?php echo esc_url($args['href']); ?>"
       class="<?php echo esc_attr($classes); ?>"<?php echo ese_latam_target_attr((string) $args['target']); ?>
        <?php echo $args['reveal'] ? 'data-hero-reveal' : ''; ?>>
        <span class="hero-cta__label">
            <?php echo esc_html($args['label']); ?>
            <span class="hero-cta__corner" aria-hidden="true">
                <svg viewBox="0 0 18 48" preserveAspectRatio="none">
                    <path d="M0 0h5.63c7.808 0 13.536 7.337 11.642 14.91l-6.09 24.359A11.527 11.527 0 0 1 0 48V0Z"></path>
                </svg>
            </span>
        </span>
        <span class="hero-cta__arrow" aria-hidden="true">
            <svg viewBox="0 0 73 58" preserveAspectRatio="none">
                <path d="M73 50C73 54.4183 69.4183 58 65 58H11.4975C5.92468 58 2.05929 52.4447 3.99626 47.2194L19.5652 5.21938C20.7282 2.08215 23.7206 0 27.0664 0H65C69.4183 0 73 3.58172 73 8V50Z"></path>
            </svg>
        </span>
    </a>
    <?php
}

/**
 * Íconos de trazo inline (stroke="currentColor", 24×24) compartidos por las
 * fichas de producto (template-parts/producto-hero.php y producto-specs.php).
 * Decorativos y siempre acompañados de texto, así que no ameritan un archivo
 * SVG propio por ícono en assets/icons/ — devuelve solo el/los `<path>`, para
 * envolver en el `<svg>` de cada sitio con su propio tamaño.
 *
 * @return string Markup de `<path>`/`<circle>`/`<rect>`, o cadena vacía si
 *                 `$name` no existe.
 */
function ese_latam_icon_svg(string $name): string {
    $icons = [
        'shield'   => '<path d="M12 2 4 5.5v6c0 5 3.4 8.9 8 10.5 4.6-1.6 8-5.5 8-10.5v-6L12 2Z"/>',
        'wheel'    => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><path d="M12 3v6M12 15v6M3 12h6M15 12h6"/>',
        'check'    => '<path d="M12 2 4 5.5v6c0 5 3.4 8.9 8 10.5 4.6-1.6 8-5.5 8-10.5v-6L12 2Z"/><path d="m9 12 2 2 4-4"/>',
        'leaf'     => '<path d="M20 4c0 9-5.5 14-14 14 0-9 5-14 14-14Z"/><path d="M6 18c3-4 6-6 10-8"/>',
        'volumen'  => '<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4 7.5 8 4.5 8-4.5M12 12v9"/>',
        'peso'     => '<path d="M7 8h10l2 12H5L7 8Z"/><path d="M9.5 8a2.5 2.5 0 1 1 5 0"/>',
        'carga'    => '<path d="M12 3v10M8 9l4 4 4-4"/><path d="M4 17h16v4H4z"/>',
        'material' => '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5"/>',
        'ruedas'   => '<circle cx="6" cy="17" r="3"/><circle cx="18" cy="17" r="3"/><path d="M9 17h6M12 17V7M8 7h8"/>',
        'cart'     => '<circle cx="9" cy="20" r="1.6"/><circle cx="18" cy="20" r="1.6"/><path d="M2 3h3l2.5 12h11L21 7H6"/>',
        'download' => '<path d="M12 3v11M8 10l4 4 4-4"/><path d="M4 19h16"/>',
        'mouse'    => '<rect x="8" y="3" width="8" height="14" rx="4"/><path d="M12 6.5v2.5"/>',
        'plus'     => '<path d="M12 6v12M6 12h12"/>',
        'arrow'    => '<path d="M4 12h15M13 6l6 6-6 6"/>',
    ];
    return $icons[$name] ?? '';
}

/**
 * Datos de una card de producto (`.product-card`) a partir de un post del
 * CPT: categoría (taxonomía producto_categoria), litraje por defecto,
 * material (del repeater `caracteristicas`), foto y enlace a la ficha.
 *
 * Una sola extracción para el slider de la home (front-page.php) y para
 * "Soluciones recomendadas" (template-parts/producto-recomendados.php).
 * catalogo-grid.php la hace dentro de su propio loop porque además muestra
 * la norma; si la grilla gana más campos compartidos, conviene traerla acá.
 *
 * @return array{cat: string, name: string, litraje: string, material: string, img: string, href: string}
 */
function ese_latam_producto_card_data(int $post_id): array {
    $terms = get_the_terms($post_id, 'producto_categoria');
    $cat   = is_array($terms) && ! empty($terms)
        ? $terms[0]->name
        : __('Producto ESE Latam', 'ese-latam');

    $litrajes = get_field('litrajes', $post_id);
    $litraje  = '—';
    if (is_array($litrajes) && ! empty($litrajes)) {
        $litraje = (string) $litrajes[0]['valor'];
        foreach ($litrajes as $item) {
            if (! empty($item['predeterminado'])) {
                $litraje = (string) $item['valor'];
                break;
            }
        }
    }

    $caracteristicas = get_field('caracteristicas', $post_id);
    $material        = '—';
    if (is_array($caracteristicas)) {
        foreach ($caracteristicas as $caract) {
            if (false !== mb_strpos(mb_strtolower((string) ($caract['etiqueta'] ?? '')), 'material')) {
                $material = (string) $caract['valor'];
                break;
            }
        }
    }

    return [
        'cat'      => $cat,
        'name'     => get_the_title($post_id),
        'litraje'  => $litraje,
        'material' => $material,
        'img'      => (string) (get_the_post_thumbnail_url($post_id, 'large') ?: ''),
        'href'     => (string) get_permalink($post_id),
    ];
}


/**
 * Sectores / áreas de impacto (Figma 3266-2331), leídos del módulo "Sectores"
 * (CPT `sector`, ver inc/cpt-sectores.php). Una sola lista para el slider de
 * la portada, el submenú del nav (inc/setup.php), la grilla de "Soluciones por
 * sector" y las etiquetas de los casos de éxito.
 *
 * El orden es el del campo "Orden" de cada entrada. Sin sectores publicados
 * devuelve una lista vacía y cada plantilla oculta su sección.
 *
 * `img`/`grid_img` son URLs absolutas (o cadena vacía); `grid_desc` cae al
 * resumen cuando el sector no trae una bajada propia para la grilla.
 *
 * @return list<array{id: int, slug: string, title: string, desc: string, img: string, url: string, grid_desc: string, grid_img: string}>
 */
function ese_latam_sectores(): array {
    static $cache = null;
    if (null !== $cache) {
        return $cache;
    }

    $entradas = get_posts([
        'post_type'        => 'sector',
        'post_status'      => 'publish',
        'posts_per_page'   => -1,
        'orderby'          => 'menu_order title',
        'order'            => 'ASC',
        'suppress_filters' => false,
    ]);

    $lista = [];

    foreach ($entradas as $entrada) {
        $foto    = (string) (get_the_post_thumbnail_url($entrada->ID, 'large') ?: '');
        $resumen = trim((string) ese_latam_campo('resumen', $entrada->ID, ''));

        $lista[] = [
            'id'        => $entrada->ID,
            'slug'      => $entrada->post_name,
            'title'     => get_the_title($entrada->ID),
            'desc'      => $resumen,
            'img'       => $foto,
            'url'       => (string) get_permalink($entrada->ID),
            'grid_desc' => trim((string) ese_latam_campo('grilla_desc', $entrada->ID, '')) ?: $resumen,
            'grid_img'  => ese_latam_img_url(ese_latam_campo('grilla_imagen', $entrada->ID, ''), $foto),
        ];
    }

    return $cache = $lista;
}

/**
 * Traduce las filas de "Dolores"/"Alivios" (ficha del sector) al contrato que
 * espera template-parts/sector-desafios.php. Descarta las filas sin título.
 *
 * @param mixed $filas
 * @return list<array{title: string, sub: string, desc: string, img: string}>
 */
function ese_latam_sector_tarjetas($filas): array {
    $out = [];

    foreach ((array) $filas as $fila) {
        $titulo = trim((string) ($fila['titulo'] ?? ''));
        if ('' === $titulo) {
            continue;
        }

        $out[] = [
            'title' => $titulo,
            'sub'   => (string) ($fila['sub'] ?? ''),
            'desc'  => (string) ($fila['descripcion'] ?? ''),
            'img'   => ese_latam_img_url($fila['imagen'] ?? ''),
        ];
    }

    return $out;
}

/**
 * URL de la página de un sector (`/sectores/{slug}/`). La trae ya resuelta
 * ese_latam_sectores(); si por lo que sea faltara, cae al catálogo, que es
 * donde el sector puede ver productos.
 *
 * @param array{slug?: string} $sector Un ítem de ese_latam_sectores().
 */
function ese_latam_sector_url(array $sector): string {
    $url = isset($sector['url']) && is_string($sector['url']) ? $sector['url'] : '';

    return '' !== $url ? $url : (string) get_post_type_archive_link('producto');
}

/**
 * Chips de sugerencia del buscador global (header.php). Si ya hay líneas de
 * producto (taxonomía producto_categoria con productos), cada chip filtra
 * el catálogo por esa línea (`?linea=`, que catalogo-grid.php entiende);
 * mientras no las haya, cae a búsquedas frecuentes por texto (`?s=`).
 *
 * @return list<array{label: string, url: string}>
 */
function ese_latam_buscador_chips(): array {
    $catalogo = (string) get_post_type_archive_link('producto');

    $terms = get_terms(['taxonomy' => 'producto_categoria', 'hide_empty' => true, 'number' => 6]);
    if (is_array($terms) && ! empty($terms)) {
        return array_map(
            static fn (WP_Term $t): array => ['label' => $t->name, 'url' => add_query_arg('linea', $t->slug, $catalogo)],
            $terms
        );
    }

    return array_map(
        static fn (string $q): array => ['label' => $q, 'url' => add_query_arg('s', $q, $catalogo)],
        [__('Contenedor', 'ese-latam'), __('Papelera', 'ese-latam'), __('Soterrado', 'ese-latam'), '120L', __('HDPE', 'ese-latam')]
    );
}
