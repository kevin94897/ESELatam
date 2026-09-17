<?php
/**
 * Página de Contacto — bloque 1: titular + formulario de asesoría + panel
 * de información de contacto (Figma 3941-8419).
 *
 * El formulario postea a admin-ajax (`action=ese_latam_contacto`, ver
 * inc/contacto.php). Con JS, contacto-page.ts intercepta el submit y pinta
 * el resultado en la misma tarjeta; sin JS el POST es normal y vuelve acá
 * con `?contacto=ok|error`, que es lo que lee $ese_estado.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_id  = (int) get_queried_object_id();
$ese_cmp = static fn (string $name, $def = '') => (string) ese_latam_campo($name, $ese_id, $def);

$ese_datos = ese_latam_contacto_datos();

// La salida para quien no se ve en la lista; se añade al final de los tres
// desplegables si está cargada.
$ese_otro = $ese_cmp('ctc_f_otro');

// País: los mismos de Distribuidores (taxonomía `pais`), no una lista
// aparte que haya que mantener sincronizada a mano.
$ese_paises_opts = array_map(
    static fn (WP_Term $t): string => $t->name,
    array_filter((array) get_terms(['taxonomy' => 'pais', 'hide_empty' => false]), static fn ($t): bool => $t instanceof WP_Term)
);

// Sector: el módulo "Sectores", que es el que alimenta el menú y la página.
$ese_sectores_opts = array_column(ese_latam_sectores(), 'title');

// Producto: el catálogo.
$ese_productos_query = new WP_Query([
    'post_type'      => 'producto',
    'post_status'    => 'publish',
    'posts_per_page' => 30,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'fields'         => 'ids',
]);
$ese_productos_opts = array_map('get_the_title', $ese_productos_query->posts);

foreach (['ese_paises_opts', 'ese_sectores_opts', 'ese_productos_opts'] as $ese_lista) {
    if ('' !== $ese_otro && ! empty($$ese_lista)) {
        $$ese_lista[] = $ese_otro;
    }
}

// Resultado del envío sin JS (ver inc/contacto.php).
$ese_estado = isset($_GET['contacto']) ? sanitize_key(wp_unslash($_GET['contacto'])) : '';

// Ítems del panel derecho. El ícono es el SVG exportado del Figma (80×80,
// ya trae su propia caja gris): se usa tal cual, sin re-dibujarlo. El dato
// viene de los globales y el rótulo de la página; sin dato, no hay ítem.
$ese_info = array_values(array_filter([
    [
        'icon'  => 'phone.svg',
        'label' => $ese_cmp('ctc_info_telefono'),
        'value' => $ese_datos['telefono'],
        'href'  => '' !== $ese_datos['telefono_link'] ? 'tel:' . $ese_datos['telefono_link'] : '',
    ],
    [
        'icon'  => 'mail.svg',
        'label' => $ese_cmp('ctc_info_email'),
        'value' => $ese_datos['email'],
        'href'  => '' !== $ese_datos['email'] ? 'mailto:' . $ese_datos['email'] : '',
    ],
    [
        'icon'  => 'pin.svg',
        'label' => $ese_cmp('ctc_info_direccion'),
        'value' => $ese_datos['direccion'],
        'href'  => $ese_datos['maps_url'],
    ],
    [
        'icon'  => 'clock.svg',
        'label' => $ese_cmp('ctc_info_horario'),
        'value' => $ese_datos['horario'],
        'href'  => '',
    ],
], static fn (array $i): bool => '' !== $i['value']));

/**
 * Imprime un campo del formulario. Todos comparten estructura (label +
 * control), así que el markup vive en un solo sitio: cambiar el diseño de
 * un input no obliga a editar siete bloques casi idénticos.
 *
 * @param array{
 *     name: string, label: string, type?: string, placeholder?: string,
 *     required?: bool, options?: list<string>, autocomplete?: string, full?: bool
 * } $args
 */
$ese_campo = static function (array $args): void {
    $args = wp_parse_args($args, [
        'type'         => 'text',
        'placeholder'  => '',
        'required'     => true,
        'options'      => [],
        'autocomplete' => '',
        'full'         => false,
    ]);

    $id    = 'ctc-' . $args['name'];
    $label = trim((string) $args['label']);

    // Sin rótulo cargado no se pinta un <label> vacío, que dejaría al campo
    // sin nombre accesible: se le pone aria-label con lo que haya a mano.
    $attrs = 'id="' . esc_attr($id) . '" name="' . esc_attr($args['name']) . '"'
        . ($args['required'] ? ' required' : '')
        . ($args['autocomplete'] ? ' autocomplete="' . esc_attr($args['autocomplete']) . '"' : '')
        . ('' === $label ? ' aria-label="' . esc_attr('' !== $args['placeholder'] ? $args['placeholder'] : $args['name']) . '"' : '');
    ?>
    <div class="ctc-field<?php echo $args['full'] ? ' ctc-field--full' : ''; ?>">
        <?php if ('' !== $label) : ?>
            <label class="ctc-field__label" for="<?php echo esc_attr($id); ?>">
                <?php echo esc_html($label); ?>
                <?php if ($args['required']) : ?>
                    <span class="ctc-field__req" aria-hidden="true">*</span>
                <?php endif; ?>
            </label>
        <?php endif; ?>

        <?php if ('select' === $args['type']) : ?>
            <div class="ctc-field__control ctc-field__control--select">
                <select <?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
                    <option value=""><?php echo esc_html($args['placeholder']); ?></option>
                    <?php foreach ($args['options'] as $opt) : ?>
                        <option value="<?php echo esc_attr($opt); ?>"><?php echo esc_html($opt); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="ctc-field__caret" aria-hidden="true">
                    <svg width="10" height="5" viewBox="0 0 10 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 5L0 0H10L5 5Z" fill="currentColor" />
                    </svg>
                </span>
            </div>
        <?php elseif ('textarea' === $args['type']) : ?>
            <div class="ctc-field__control ctc-field__control--textarea">
                <textarea <?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    rows="5" placeholder="<?php echo esc_attr($args['placeholder']); ?>"></textarea>
            </div>
        <?php else : ?>
            <div class="ctc-field__control">
                <input type="<?php echo esc_attr($args['type']); ?>" <?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    placeholder="<?php echo esc_attr($args['placeholder']); ?>">
            </div>
        <?php endif; ?>

        <p class="ctc-field__error" data-ctc-error="<?php echo esc_attr($args['name']); ?>" hidden></p>
    </div>
    <?php
};
?>

<section class="ctc-intro" id="formulario">
    <header class="ctc-intro__header" data-reveal-header>
        <?php $ese_kicker = $ese_cmp('ctc_kicker'); ?>
        <?php if ('' !== $ese_kicker) : ?>
            <p class="type-kicker text-secondary">/ <?php echo esc_html($ese_kicker); ?></p>
        <?php endif; ?>
        <?php $ese_titulo = $ese_cmp('ctc_titulo'); ?>
        <?php if ('' !== $ese_titulo) : ?>
            <h1 class="ctc-intro__title">
                <?php echo ese_latam_titulo($ese_titulo, 'span', 'hl'); ?>
            </h1>
        <?php endif; ?>
        <?php $ese_lede = ese_latam_texto_rico($ese_cmp('ctc_lede')); ?>
        <?php if ('' !== $ese_lede) : ?>
            <p class="ctc-intro__lede" data-reveal-desc><?php echo $ese_lede; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
        <?php endif; ?>
    </header>

    <div class="ctc-layout">
        <div class="ctc-card" data-reveal="up">
            <form class="ctc-form" method="post"
                action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
                data-contacto-form novalidate>
                <input type="hidden" name="action" value="ese_latam_contacto">
                <?php wp_nonce_field('ese_latam_contacto'); ?>

                <?php // Honeypot: invisible para personas, irresistible para bots. ?>
                <div class="ctc-form__hp" aria-hidden="true">
                    <label for="ctc-website"><?php esc_html_e('No completar', 'ese-latam'); ?></label>
                    <input type="text" id="ctc-website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="ctc-form__grid">
                    <?php
                    $ese_campo([
                        'name'         => 'nombre',
                        'label'        => $ese_cmp('ctc_f_nombre_label'),
                        'placeholder'  => $ese_cmp('ctc_f_nombre_ph'),
                        'autocomplete' => 'name',
                    ]);
                    $ese_campo([
                        'name'         => 'email',
                        'type'         => 'email',
                        'label'        => $ese_cmp('ctc_f_email_label'),
                        'placeholder'  => $ese_cmp('ctc_f_email_ph'),
                        'autocomplete' => 'email',
                    ]);
                    $ese_campo([
                        'name'         => 'telefono',
                        'type'         => 'tel',
                        'label'        => $ese_cmp('ctc_f_telefono_label'),
                        'placeholder'  => $ese_cmp('ctc_f_telefono_ph'),
                        'autocomplete' => 'tel',
                    ]);
                    $ese_campo([
                        'name'        => 'pais',
                        'type'        => 'select',
                        'label'       => $ese_cmp('ctc_f_pais_label'),
                        'placeholder' => $ese_cmp('ctc_f_pais_ph'),
                        'options'     => $ese_paises_opts,
                    ]);
                    $ese_campo([
                        'name'        => 'sector',
                        'type'        => 'select',
                        'label'       => $ese_cmp('ctc_f_sector_label'),
                        'placeholder' => $ese_cmp('ctc_f_sector_ph'),
                        'options'     => $ese_sectores_opts,
                    ]);
                    $ese_campo([
                        'name'        => 'producto',
                        'type'        => 'select',
                        'label'       => $ese_cmp('ctc_f_producto_label'),
                        'placeholder' => $ese_cmp('ctc_f_producto_ph'),
                        'options'     => $ese_productos_opts,
                        'required'    => false,
                    ]);
                    $ese_campo([
                        'name'        => 'mensaje',
                        'type'        => 'textarea',
                        'label'       => $ese_cmp('ctc_f_mensaje_label'),
                        'placeholder' => $ese_cmp('ctc_f_mensaje_ph'),
                        'full'        => true,
                    ]);
                    ?>
                </div>

                <div class="ctc-form__foot">
                    <?php
                    // El CTA del theme es un <a>; acá tiene que enviar el
                    // formulario, así que se imprime el mismo marcado dentro
                    // de un <button> (ver .hero-cta en main.css: los estilos
                    // y el morph de hero-cta.ts van por clase, no por tag).
                    ?>
                    <button type="submit" class="hero-cta ctc-submit" data-ctc-submit>
                        <span class="hero-cta__label">
                            <span class="ctc-submit__text"><?php echo esc_html($ese_cmp('ctc_boton')); ?></span>
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
                    </button>

                    <?php // El asterisco es marcado, no texto: se pinta donde el
                    // cliente escriba un * en la nota. ?>
                    <?php $ese_nota = $ese_cmp('ctc_nota'); ?>
                    <?php if ('' !== $ese_nota) : ?>
                        <p class="ctc-form__note">
                            <?php
                            $ese_partes = explode('*', $ese_nota, 2);
                            echo esc_html($ese_partes[0]);
                            if (isset($ese_partes[1])) {
                                echo '<span class="ctc-field__req">*</span>' . esc_html($ese_partes[1]);
                            }
                            ?>
                        </p>
                    <?php endif; ?>
                </div>

                <?php // Estado del envío: lo rellena contacto-page.ts, o llega
                // ya resuelto desde el POST sin JS (?contacto=ok|error). ?>
                <p class="ctc-form__status<?php echo $ese_estado ? ' is-visible is-' . esc_attr('ok' === $ese_estado ? 'ok' : 'error') : ''; ?>"
                    data-ctc-status role="status" aria-live="polite">
                    <?php
                    if ('ok' === $ese_estado) {
                        echo esc_html($ese_cmp('ctc_msg_ok'));
                    } elseif ($ese_estado) {
                        echo esc_html($ese_cmp('ctc_msg_error'));
                    }
                    ?>
                </p>
            </form>
        </div>

        <aside class="ctc-aside">
            <?php // Tarjeta de marca en el hueco que el Figma deja como
            // placeholder gris: foto urbana del theme + dato de respaldo. ?>
            <?php
            $ese_aside_img    = ese_latam_img_url($ese_cmp('ctc_aside_imagen'));
            $ese_aside_kicker = $ese_cmp('ctc_aside_kicker');
            $ese_aside_texto  = $ese_cmp('ctc_aside_texto');
            ?>
            <?php if ('' !== $ese_aside_img) : ?>
                <div class="ctc-aside__media" data-reveal="fade">
                    <img src="<?php echo esc_url($ese_aside_img); ?>" alt="" loading="lazy" decoding="async">
                    <?php if ('' !== $ese_aside_kicker || '' !== $ese_aside_texto) : ?>
                        <div class="ctc-aside__media-body">
                            <?php if ('' !== $ese_aside_kicker) : ?>
                                <p class="ctc-aside__media-kicker"><?php echo esc_html($ese_aside_kicker); ?></p>
                            <?php endif; ?>
                            <?php if ('' !== $ese_aside_texto) : ?>
                                <p class="ctc-aside__media-text"><?php echo esc_html($ese_aside_texto); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php $ese_aside_titulo = $ese_cmp('ctc_aside_titulo'); ?>
            <?php if ('' !== $ese_aside_titulo && [] !== $ese_info) : ?>
                <p class="ctc-aside__title"><?php echo esc_html($ese_aside_titulo); ?></p>
            <?php endif; ?>

            <ul class="ctc-info" data-reveal-stagger>
                <?php foreach ($ese_info as $ese_item) : ?>
                    <li class="ctc-info__item">
                        <img class="ctc-info__icon"
                            src="<?php echo esc_url(ESE_LATAM_URI . '/assets/icons/contacto/' . $ese_item['icon']); ?>"
                            alt="" width="80" height="80" loading="lazy" decoding="async">
                        <span class="ctc-info__text">
                            <?php if ('' !== $ese_item['label']) : ?>
                                <span class="ctc-info__label"><?php echo esc_html($ese_item['label']); ?></span>
                            <?php endif; ?>
                            <?php if ($ese_item['href']) : ?>
                                <a class="ctc-info__value" href="<?php echo esc_url($ese_item['href']); ?>"
                                    <?php echo 0 === strpos($ese_item['href'], 'http') ? 'target="_blank" rel="noopener"' : ''; ?>>
                                    <?php echo esc_html($ese_item['value']); ?>
                                </a>
                            <?php else : ?>
                                <span class="ctc-info__value"><?php echo esc_html($ese_item['value']); ?></span>
                            <?php endif; ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </div>
</section>
