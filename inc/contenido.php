<?php
/**
 * Listas de contenido compartidas que se editan en el menú "ESE Latam"
 * (inc/pcf-globales.php).
 *
 * No traen valores por defecto: si el cliente no cargó nada, devuelven una
 * lista vacía y la plantilla que las consume oculta su sección. El theme
 * nunca inventa contenido.
 *
 * Los sectores y los distribuidores tienen su propio archivo desde antes
 * (inc/template-tags.php e inc/distribuidores.php) y ahí se quedaron; acá
 * viven las dos listas que hasta ahora estaban escritas dentro de su
 * template-part, donde ninguna otra plantilla podía leerlas.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Sellos, leídos del módulo "Certificaciones" (inc/modulos.php). Una sola
 * lista para la franja de la portada, el hero de la ficha de producto y la
 * página de Certificaciones.
 *
 * `img` es una URL absoluta, o cadena vacía si el sello no tiene logo: en ese
 * caso la plantilla imprime solo el nombre.
 *
 * @return list<array{id: int, name: string, desc: string, img: string, sub: string, detalle: string, criterios: list<string>, destacado: bool}>
 */
function ese_latam_certificaciones(): array {
    static $cache = null;
    if (null !== $cache) {
        return $cache;
    }

    $lista = [];

    foreach (ese_latam_modulo_entradas('certificacion') as $sello) {
        $lista[] = [
            'id'         => $sello->ID,
            'name'       => get_the_title($sello->ID),
            'desc'       => trim((string) ese_latam_campo('resumen', $sello->ID, '')),
            'img'        => (string) (get_the_post_thumbnail_url($sello->ID, 'medium') ?: ''),
            'sub'        => trim((string) ese_latam_campo('subtitulo', $sello->ID, '')),
            'detalle'    => ese_latam_texto_rico((string) ese_latam_campo('detalle', $sello->ID, '')),
            'criterios'  => array_values(array_filter(array_map(
                static fn (array $f): string => trim((string) ($f['texto'] ?? '')),
                (array) ese_latam_campo('criterios', $sello->ID, [])
            ), static fn (string $x): bool => '' !== $x)),
            'destacado'  => true === get_field('destacado', $sello->ID),
        ];
    }

    return $cache = $lista;
}

/**
 * Solo los sellos marcados para la franja corta (la de la portada, la ficha
 * de producto y las páginas de sector). Si no hay ninguno marcado se usan
 * todos: así la franja nunca queda vacía por un descuido.
 *
 * @return list<array<string, mixed>>
 */
function ese_latam_certificaciones_destacadas(): array {
    $todas = ese_latam_certificaciones();

    $destacadas = array_values(array_filter(
        $todas,
        static fn (array $sello): bool => ! empty($sello['destacado'])
    ));

    return [] !== $destacadas ? $destacadas : $todas;
}

/**
 * Servicios del selector "Residuos inteligentes"
 * (template-parts/residuos.php, compartido con la página Impacto).
 *
 * `icon` e `img` son URLs absolutas (o cadena vacía): salen de la biblioteca
 * de medios, así que la plantilla ya no antepone ninguna ruta.
 *
 * @return list<array{slug: string, title: string, sub: string, desc: string, icon: string, img: string}>
 */
function ese_latam_servicios_residuos(): array {
    static $cache = null;
    if (null !== $cache) {
        return $cache;
    }

    $lista = [];

    foreach ((array) ese_latam_opcion('residuos_servicios', []) as $fila) {
        $titulo = trim((string) ($fila['titulo'] ?? ''));
        if ('' === $titulo) {
            continue;
        }

        $slug = sanitize_title((string) ($fila['slug'] ?? ''));

        $lista[] = [
            'slug'  => '' !== $slug ? $slug : sanitize_title($titulo),
            'title' => $titulo,
            'sub'   => trim((string) ($fila['subtitulo'] ?? '')),
            'desc'  => (string) ($fila['descripcion'] ?? ''),
            'icon'  => ese_latam_img_url($fila['icono'] ?? ''),
            'img'   => ese_latam_img_url($fila['imagen'] ?? ''),
        ];
    }

    return $cache = $lista;
}
