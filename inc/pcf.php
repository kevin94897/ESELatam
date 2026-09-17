<?php
/**
 * Puente entre los campos de PCF (Prompt Custom Fields) y las plantillas.
 *
 * Regla de oro: **los campos son la única fuente del contenido**. Estos
 * lectores devuelven cadena vacía o lista vacía cuando no hay nada cargado, y
 * cada plantilla decide si imprime el elemento o se salta la sección entera.
 * El theme no trae textos ni imágenes de respaldo.
 *
 * Precedencia para las secciones compartidas (contacto, certificaciones,
 * residuos), de mayor a menor — ver ese_latam_seccion_args():
 *
 *   1. Campos de override de la página actual (si el interruptor está activo).
 *   2. `$args` que pasa la plantilla (el copy por página que hoy vive en PHP).
 *   3. Campos globales de la página de opciones "ESE Latam".
 *   4. Nada: la sección no se pinta.
 *
 * PCF expone la misma API que ACF (get_field, have_rows…), así que si algún
 * día se activa ACF PRO en vez de PCF todo esto sigue funcionando igual.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * ¿Hay un motor de campos disponible (PCF o ACF)? Sin él, todos los lectores
 * devuelven sus valores por defecto y el sitio se ve exactamente como antes.
 */
function ese_latam_campos_activos(): bool {
    return function_exists('get_field');
}

/**
 * ID de la página fijada como portada. 0 si el sitio todavía muestra las
 * últimas entradas (instalación recién clonada, antes de que corra
 * ese_latam_asegurar_paginas()).
 */
function ese_latam_home_id(): int {
    return 'page' === get_option('show_on_front') ? (int) get_option('page_on_front') : 0;
}

/**
 * ¿Un valor de campo cuenta como "sin llenar"? `0` y `'0'` sí son valores
 * legítimos (un litraje, un true_false en falso), por eso no basta con empty().
 *
 * @param mixed $valor
 */
function ese_latam_campo_vacio($valor): bool {
    if (null === $valor || false === $valor || '' === $valor) {
        return true;
    }

    return is_array($valor) && [] === $valor;
}

/**
 * Lee un campo de un post concreto y cae al valor por defecto si está vacío.
 *
 * @param mixed $default
 * @return mixed
 */
function ese_latam_campo(string $name, int $post_id, $default = null) {
    if (! ese_latam_campos_activos() || $post_id <= 0) {
        return $default;
    }

    $valor = get_field($name, $post_id);

    return ese_latam_campo_vacio($valor) ? $default : $valor;
}

/**
 * Igual que ese_latam_campo(), pero contra la portada.
 *
 * @param mixed $default
 * @return mixed
 */
function ese_latam_home(string $name, $default = null) {
    return ese_latam_campo($name, ese_latam_home_id(), $default);
}

/**
 * Igual que ese_latam_campo(), pero contra las páginas de opciones (los
 * valores globales, compartidos por todas las plantillas).
 *
 * @param mixed $default
 * @return mixed
 */
function ese_latam_opcion(string $name, $default = null) {
    if (! ese_latam_campos_activos()) {
        return $default;
    }

    $valor = get_field($name, 'option');

    return ese_latam_campo_vacio($valor) ? $default : $valor;
}

/**
 * URL de una imagen de campo. Acepta lo que devuelva PCF según el
 * `return_format` (array de adjunto, ID o URL) y cae al asset del theme.
 *
 * @param mixed $valor
 */
function ese_latam_img_url($valor, string $default = ''): string {
    if (is_array($valor)) {
        $valor = $valor['url'] ?? ($valor['ID'] ?? ($valor['id'] ?? ''));
    }

    if (is_numeric($valor)) {
        $url = wp_get_attachment_url((int) $valor);
        return is_string($url) && '' !== $url ? $url : $default;
    }

    return is_string($valor) && '' !== $valor ? $valor : $default;
}

/**
 * URL de un asset del theme por ruta relativa a assets/imgs/.
 */
function ese_latam_asset(string $ruta): string {
    return ESE_LATAM_URI . '/assets/imgs/' . ltrim($ruta, '/');
}

/**
 * Normaliza un campo `link` de PCF (`{url, title, target}`) a las claves que
 * usan las plantillas, completando lo que falte con los valores por defecto.
 *
 * @param mixed $valor
 * @return array{href: string, label: string, target: string}
 */
function ese_latam_enlace($valor, string $label_default = '', string $href_default = ''): array {
    $link = is_array($valor) ? $valor : ['url' => is_string($valor) ? $valor : ''];

    $href  = (string) ($link['url'] ?? '');
    $label = (string) ($link['title'] ?? '');

    return [
        'href'   => '' !== $href ? $href : $href_default,
        'label'  => '' !== $label ? $label : $label_default,
        'target' => '_blank' === ($link['target'] ?? '') ? '_blank' : '',
    ];
}

/**
 * URL de una imagen que puede venir de un campo (URL absoluta) o de una ruta
 * relativa a assets/imgs/ escrita en una plantilla.
 *
 * Las partes compartidas reciben las dos cosas mientras quedan páginas sin
 * migrar: anteponer la ruta del theme a una URL absoluta genera enlaces rotos
 * del tipo `assets/imgs/http://…`.
 */
function ese_latam_img_ruta(string $valor): string {
    $valor = trim($valor);

    if ('' === $valor) {
        return '';
    }

    if (preg_match('#^(https?:)?//|^/#', $valor)) {
        return $valor;
    }

    return ese_latam_asset($valor);
}

/**
 * Atributos de destino de un enlace: solo salen cuando el campo tiene
 * marcada la casilla "Nueva pestaña" del modal. `noopener` acompaña siempre
 * a `_blank`.
 */
function ese_latam_target_attr(string $target): string {
    return '_blank' === $target ? ' target="_blank" rel="noopener"' : '';
}

/**
 * Titulares a dos colores.
 *
 * Convención única en todo el theme: el tramo que va en el color de marca se
 * escribe entre barras verticales, y cada salto de línea del texto es un
 * salto de línea del titular.
 *
 *     sectores que
 *     |transformamos|
 *
 * Así el cliente controla el resaltado y el corte de línea desde un solo
 * campo, sin tocar HTML y sin que la plantilla tenga que adivinar dónde va
 * el `<br>`. La etiqueta y la clase del tramo resaltado sí las fija cada
 * sección (`<strong>` o `<span class="hl">`), porque eso es diseño.
 *
 * @param string $tag   Etiqueta del tramo resaltado ('strong' o 'span').
 * @param string $class Clase del tramo resaltado (p. ej. 'hl').
 */
function ese_latam_titulo(string $texto, string $tag = 'strong', string $class = ''): string {
    $abre   = '<' . $tag . ('' !== $class ? ' class="' . esc_attr($class) . '"' : '') . '>';
    $cierra = '</' . $tag . '>';

    $html = esc_html(trim($texto));

    // Callback y no replacement: un `$` o una barra invertida en el texto
    // del cliente no deben interpretarse como referencia de grupo.
    $html = (string) preg_replace_callback(
        '/\|([^|]*)\|/u',
        static fn (array $m): string => '' === trim($m[1]) ? $m[0] : $abre . $m[1] . $cierra,
        $html
    );

    return nl2br($html, false);
}

/**
 * El mismo titular en texto plano (sin barras ni saltos), para atributos
 * como `aria-label` o `alt`, donde no cabe el marcado.
 */
function ese_latam_titulo_plano(string $texto): string {
    $plano = str_replace('|', '', trim($texto));

    return trim((string) preg_replace('/\s+/u', ' ', $plano));
}

/**
 * HTML de copy enriquecido (los párrafos con tramos en negrita o en color que
 * el cliente escribe en un WYSIWYG). Se limita al HTML de post normal y se
 * le quita el `<p>` envolvente cuando es un solo párrafo, para que pueda
 * imprimirse dentro del `<p class="…">` que ya trae el marcado.
 */
function ese_latam_texto_rico(string $html): string {
    $html = wp_kses_post(trim($html));

    if (1 === substr_count($html, '<p') && 0 === strpos($html, '<p')) {
        $html = preg_replace('#^<p[^>]*>(.*)</p>$#s', '$1', $html) ?? $html;
    }

    return trim((string) $html);
}

/**
 * Resuelve los argumentos de una sección compartida aplicando la precedencia
 * documentada arriba: override de la página → `$args` de la plantilla →
 * campos globales → valores por defecto del theme.
 *
 * @param string               $seccion   Prefijo de los campos ('contacto', 'certificaciones', 'residuos').
 * @param array<string, mixed> $args      Lo que pasó get_template_part().
 * @param array<string, mixed> $defaults  Valores por defecto del theme.
 * @param array<string, string> $mapa     Clave de la plantilla => nombre del campo (sin prefijo).
 * @param list<string>         $bools    Claves de tipo true_false: ahí `false` es
 *                                       una respuesta válida, no "campo vacío".
 * @return array<string, mixed>
 */
function ese_latam_seccion_args(string $seccion, array $args, array $defaults, array $mapa, array $bools = []): array {
    $valores = $defaults;

    // 3. Globales.
    foreach ($mapa as $clave => $campo) {
        $valor = ese_latam_opcion($seccion . '_' . $campo, null);
        if (in_array($clave, $bools, true)) {
            $valor = ese_latam_campos_activos() ? get_field($seccion . '_' . $campo, 'option') : null;
            $valor = is_bool($valor) ? $valor : null;
        }
        if (null !== $valor) {
            $valores[$clave] = $valor;
        }
    }

    // 2. Argumentos de la plantilla (el copy por página que hoy vive en PHP).
    $valores = wp_parse_args($args, $valores);

    // 1. Override de la página actual, solo si el editor activó el interruptor.
    // is_home() entra porque la página de entradas (el blog) es una página
    // con sus campos, aunque WordPress no la considere "singular".
    $post_id = (int) get_queried_object_id();
    if ($post_id > 0 && ese_latam_campos_activos() && (is_singular() || is_home())) {
        if (true === get_field($seccion . '_override', $post_id)) {
            foreach ($mapa as $clave => $campo) {
                if (in_array($clave, $bools, true)) {
                    $valor = get_field($seccion . '_pag_' . $campo, $post_id);
                    $valor = is_bool($valor) ? $valor : null;
                } else {
                    $valor = ese_latam_campo($seccion . '_pag_' . $campo, $post_id, null);
                }
                if (null !== $valor) {
                    $valores[$clave] = $valor;
                }
            }
        }
    }

    return $valores;
}
