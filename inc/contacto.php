<?php
/**
 * Página de Contacto: URL, datos de contacto compartidos y envío del
 * formulario (AJAX + wp_mail).
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Datos de contacto de la empresa (Figma 3941-8369). Un solo origen para el
 * formulario, el bloque de "Información de contacto", la sección de la sede
 * y cualquier otra plantilla que los necesite.
 *
 * Filtrable con `ese_latam_contacto_datos` para no tener que tocar el theme
 * cuando el cliente cambie un teléfono o el horario.
 *
 * @return array{telefono: string, telefono_link: string, email: string, direccion: string, horario: string, maps_query: string, maps_url: string}
 */
function ese_latam_contacto_datos(): array {
    $direccion = __('Calle Grimaldo del Solar 162. Of. 603, Miraflores', 'ese-latam');

    return apply_filters('ese_latam_contacto_datos', [
        'telefono'      => '+51 997 171 302',
        // tel: sin espacios ni signos, como pide el esquema.
        'telefono_link' => '+51997171302',
        'email'         => 'eselatam@gmail.com',
        'direccion'     => $direccion,
        'horario'       => __('Lun a Vie: 8:00 - 17:00', 'ese-latam'),
        'maps_query'    => $direccion . ', Lima, Perú',
        'maps_url'      => 'https://www.google.com/maps/search/?api=1&query='
            . rawurlencode($direccion . ', Lima, Perú'),
    ]);
}

/**
 * URL de la página de contacto (ver inc/paginas.php). Si todavía no existe,
 * cae al ancla `#contacto` de la home — el CTA de cierre que sí está en
 * todas las páginas.
 */
function ese_latam_contacto_url(): string {
    return ese_latam_pagina_url('contacto', home_url('/#contacto'));
}

/**
 * Endpoint AJAX del formulario de contacto.
 *
 * Sin plugin de formularios: el theme valida, sanitiza y despacha por
 * wp_mail. El JS (contacto-page.ts) lo llama por fetch y pinta el estado en
 * la misma tarjeta; sin JS el <form> hace POST normal a admin-ajax y el
 * usuario ve la respuesta JSON — por eso `ese_latam_contacto_es_ajax()`
 * distingue los dos casos y redirige de vuelta con un parámetro.
 */
function ese_latam_contacto_enviar(): void {
    $ajax = ! empty($_POST['ajax']);

    $responder = static function (bool $ok, string $mensaje, array $errores = []) use ($ajax): void {
        if ($ajax) {
            wp_send_json([
                'ok'       => $ok,
                'mensaje'  => $mensaje,
                'errores'  => $errores,
            ], $ok ? 200 : 422);
        }

        wp_safe_redirect(add_query_arg(
            'contacto',
            $ok ? 'ok' : 'error',
            ese_latam_contacto_url()
        ) . '#formulario');
        exit;
    };

    if (! isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'ese_latam_contacto')) {
        $responder(false, __('Tu sesión expiró. Recarga la página e inténtalo de nuevo.', 'ese-latam'));
    }

    // Honeypot: un campo oculto que solo un bot completa.
    if (! empty($_POST['website'])) {
        $responder(true, __('¡Gracias! Recibimos tu mensaje.', 'ese-latam'));
    }

    $campos = [
        'nombre'   => sanitize_text_field(wp_unslash($_POST['nombre'] ?? '')),
        'email'    => sanitize_email(wp_unslash($_POST['email'] ?? '')),
        'telefono' => sanitize_text_field(wp_unslash($_POST['telefono'] ?? '')),
        'pais'     => sanitize_text_field(wp_unslash($_POST['pais'] ?? '')),
        'sector'   => sanitize_text_field(wp_unslash($_POST['sector'] ?? '')),
        'producto' => sanitize_text_field(wp_unslash($_POST['producto'] ?? '')),
        'mensaje'  => sanitize_textarea_field(wp_unslash($_POST['mensaje'] ?? '')),
    ];

    $errores = [];
    foreach (['nombre', 'email', 'telefono', 'pais', 'sector', 'mensaje'] as $requerido) {
        if ('' === $campos[$requerido]) {
            $errores[$requerido] = __('Completa este campo.', 'ese-latam');
        }
    }
    if ('' !== $campos['email'] && ! is_email($campos['email'])) {
        $errores['email'] = __('Escribe un correo válido.', 'ese-latam');
    }

    if (! empty($errores)) {
        $responder(false, __('Revisa los campos marcados.', 'ese-latam'), $errores);
    }

    $destino = apply_filters('ese_latam_contacto_destino', get_option('admin_email'));

    $asunto = sprintf(
        /* translators: 1: sector, 2: nombre de quien escribe */
        __('[Web] Solicitud de asesoría — %1$s (%2$s)', 'ese-latam'),
        $campos['sector'],
        $campos['nombre']
    );

    $lineas = [
        sprintf(__('Nombre: %s', 'ese-latam'), $campos['nombre']),
        sprintf(__('Correo: %s', 'ese-latam'), $campos['email']),
        sprintf(__('Teléfono: %s', 'ese-latam'), $campos['telefono']),
        sprintf(__('País: %s', 'ese-latam'), $campos['pais']),
        sprintf(__('Sector: %s', 'ese-latam'), $campos['sector']),
        sprintf(__('Producto de interés: %s', 'ese-latam'), $campos['producto'] ?: __('No indicado', 'ese-latam')),
        '',
        __('Mensaje:', 'ese-latam'),
        $campos['mensaje'],
    ];

    // Reply-To con el correo de quien escribe: responder desde el cliente de
    // correo contesta a la persona, no al buzón del sitio.
    $enviado = wp_mail(
        $destino,
        $asunto,
        implode("\n", $lineas),
        ['Reply-To: ' . $campos['nombre'] . ' <' . $campos['email'] . '>']
    );

    if (! $enviado) {
        $responder(false, __('No pudimos enviar tu mensaje. Escríbenos directamente por correo.', 'ese-latam'));
    }

    $responder(true, __('¡Gracias! Recibimos tu mensaje y te responderemos en menos de 24 horas hábiles.', 'ese-latam'));
}
add_action('wp_ajax_ese_latam_contacto', 'ese_latam_contacto_enviar');
add_action('wp_ajax_nopriv_ese_latam_contacto', 'ese_latam_contacto_enviar');
