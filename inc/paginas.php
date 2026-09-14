<?php
/**
 * Páginas que el theme necesita que existan (Contacto, Sectores, single de
 * sector, Certificaciones, Impacto…): se crean una sola vez y sus URLs se
 * resuelven desde un único helper.
 *
 * Antes cada plantilla nueva repetía este mismo bloque (ver el historial
 * de inc/contacto.php); acá vive una sola vez para todas.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Slug => datos de cada página que el theme trae "de fábrica".
 *
 * `template` vacío significa que WordPress aplica page-{slug}.php solo, por
 * convención. Las singles de sector comparten UNA plantilla (page-sector.php)
 * para N slugs, así que ahí sí se asigna explícitamente al crear la página
 * (es lo mismo que elegir "Solución por sector" en el editor).
 *
 * @return array<string, array{title: string, template: string}>
 */
function ese_latam_paginas_base(): array {
    return [
        'contacto'        => ['title' => __('Contacto', 'ese-latam'),                'template' => ''],
        'sectores'        => ['title' => __('Soluciones por sector', 'ese-latam'),   'template' => ''],
        'certificaciones' => ['title' => __('Certificaciones', 'ese-latam'),         'template' => ''],
        'impacto'         => ['title' => __('Residuos inteligentes', 'ese-latam'),   'template' => ''],
        // Single de sector: por ahora solo Municipalidades tiene diseño en el
        // Figma (04 – Solución). Para sumar otra basta agregar acá su slug
        // (el mismo de ese_latam_sectores()) con la misma plantilla.
        'municipalidades' => ['title' => __('Municipalidades y gobiernos locales', 'ese-latam'), 'template' => 'page-sector.php'],
    ];
}

/**
 * URL de una página del theme por slug. Si todavía no existe (instalación
 * nueva antes de que corra `ese_latam_asegurar_paginas`), devuelve el
 * fallback — normalmente el ancla de la sección equivalente en la home.
 */
function ese_latam_pagina_url(string $slug, string $fallback = ''): string {
    $page = get_page_by_path($slug);

    if ($page instanceof WP_Post && 'publish' === $page->post_status) {
        return (string) get_permalink($page);
    }

    return '' !== $fallback ? $fallback : home_url('/');
}

/**
 * Crea las páginas base que falten, una sola vez. Idempotente: las que ya
 * existen —aunque las hayan renombrado o mandado a la papelera— no se
 * vuelven a crear, y todo queda anotado en una opción autoload para que el
 * resto de las cargas sea una lectura en memoria y nada más.
 *
 * Va en `init` y no solo en `after_switch_theme`: el theme ya está activo
 * en los entornos donde se desarrolla, así que engancharlo solo a la
 * activación no crearía nunca las páginas nuevas.
 */
function ese_latam_asegurar_paginas(): void {
    $hechas = get_option('ese_latam_paginas', []);
    if (! is_array($hechas)) {
        $hechas = [];
    }

    $pendientes = array_diff_key(ese_latam_paginas_base(), $hechas);
    if (empty($pendientes)) {
        return;
    }

    foreach ($pendientes as $slug => $datos) {
        $existente = get_page_by_path($slug);
        if ($existente instanceof WP_Post) {
            $hechas[$slug] = $existente->ID;
            continue;
        }

        $post = [
            'post_title'   => $datos['title'],
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ];
        if ('' !== $datos['template']) {
            $post['meta_input'] = ['_wp_page_template' => $datos['template']];
        }

        $id = wp_insert_post($post);

        if (! is_wp_error($id)) {
            $hechas[$slug] = $id;
        }
    }

    update_option('ese_latam_paginas', $hechas);
}
add_action('init', 'ese_latam_asegurar_paginas');
add_action('after_switch_theme', 'ese_latam_asegurar_paginas');

/**
 * El header del sitio está pensado para heros OSCUROS: el logo va en blanco
 * (filtro brightness/invert en main.css) y el nav-pill sin borde. Las
 * páginas que abren con fondo claro (Contacto, Sectores) necesitan la
 * variante inversa — esta clase en el <body> y el CSS hace el resto.
 *
 * @param list<string> $classes
 * @return list<string>
 */
function ese_latam_body_class_hero_claro(array $classes): array {
    if (is_page(['contacto', 'sectores'])
        || is_page_template(['page-contacto.php', 'page-sectores.php'])
    ) {
        $classes[] = 'has-light-hero';
    }

    return $classes;
}
add_filter('body_class', 'ese_latam_body_class_hero_claro');
