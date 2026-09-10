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

$ese_datos = ese_latam_contacto_datos();

// Sectores: los mismos ocho de la sección "Sectores" de la home. Se listan
// acá (solo los nombres) en vez de importarlos de front-page.php para que
// esta parte siga siendo autocontenida, igual que el resto de template-parts.
$ese_sectores_opts = [
    __('Municipalidades y gobiernos locales', 'ese-latam'),
    __('Empresas de recolección', 'ese-latam'),
    __('Inmobiliarias', 'ese-latam'),
    __('Hospitalarios', 'ese-latam'),
    __('Uso doméstico', 'ese-latam'),
    __('Supermercados y aeropuertos', 'ese-latam'),
    __('Restaurantes y hostelería', 'ese-latam'),
    __('Industria y manufactura', 'ese-latam'),
    __('Otro', 'ese-latam'),
];

// Los 13 países de Latinoamérica donde opera ESE Latam (mismo set que el
// riel de Distribuidores de la home).
$ese_paises_opts = [
    __('Perú', 'ese-latam'),
    __('Chile', 'ese-latam'),
    __('Colombia', 'ese-latam'),
    __('Ecuador', 'ese-latam'),
    __('Bolivia', 'ese-latam'),
    __('Argentina', 'ese-latam'),
    __('Uruguay', 'ese-latam'),
    __('Paraguay', 'ese-latam'),
    __('Brasil', 'ese-latam'),
    __('México', 'ese-latam'),
    __('Panamá', 'ese-latam'),
    __('Costa Rica', 'ese-latam'),
    __('República Dominicana', 'ese-latam'),
    __('Otro', 'ese-latam'),
];

// Productos reales del CPT; si el cliente todavía no publicó ninguno, el
// select cae a las familias de producto del catálogo.
$ese_productos_query = new WP_Query([
    'post_type'      => 'producto',
    'post_status'    => 'publish',
    'posts_per_page' => 30,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'fields'         => 'ids',
]);
$ese_productos_opts = array_map('get_the_title', $ese_productos_query->posts);
if (empty($ese_productos_opts)) {
    $ese_productos_opts = [
        __('Contenedores con ruedas', 'ese-latam'),
        __('Contenedores soterrados', 'ese-latam'),
        __('Papeleras urbanas', 'ese-latam'),
        __('Contenedores hospitalarios', 'ese-latam'),
    ];
}

// Resultado del envío sin JS (ver inc/contacto.php).
$ese_estado = isset($_GET['contacto']) ? sanitize_key(wp_unslash($_GET['contacto'])) : '';

// Ítems del panel derecho. El ícono es el SVG exportado del Figma (80×80,
// ya trae su propia caja gris): se usa tal cual, sin re-dibujarlo.
$ese_info = [
    [
        'icon'  => 'phone.svg',
        'label' => __('Teléfono', 'ese-latam'),
        'value' => $ese_datos['telefono'],
        'href'  => 'tel:' . $ese_datos['telefono_link'],
    ],
    [
        'icon'  => 'mail.svg',
        'label' => __('Correo electrónico', 'ese-latam'),
        'value' => $ese_datos['email'],
        'href'  => 'mailto:' . $ese_datos['email'],
    ],
    [
        'icon'  => 'pin.svg',
        'label' => __('Dirección', 'ese-latam'),
        'value' => $ese_datos['direccion'],
        'href'  => $ese_datos['maps_url'],
    ],
    [
        'icon'  => 'clock.svg',
        'label' => __('Horario de atención', 'ese-latam'),
        'value' => $ese_datos['horario'],
        'href'  => '',
    ],
];

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
    $attrs = 'id="' . esc_attr($id) . '" name="' . esc_attr($args['name']) . '"'
        . ($args['required'] ? ' required' : '')
        . ($args['autocomplete'] ? ' autocomplete="' . esc_attr($args['autocomplete']) . '"' : '');
    ?>
    <div class="ctc-field<?php echo $args['full'] ? ' ctc-field--full' : ''; ?>">
        <label class="ctc-field__label" for="<?php echo esc_attr($id); ?>">
            <?php echo esc_html($args['label']); ?>
            <?php if ($args['required']) : ?>
                <span class="ctc-field__req" aria-hidden="true">*</span>
            <?php endif; ?>
        </label>

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
        <p class="type-kicker text-secondary">/ <?php esc_html_e('Contacto', 'ese-latam'); ?></p>
        <h1 class="ctc-intro__title">
            <?php esc_html_e('Cuéntanos tu', 'ese-latam'); ?><br>
            <span class="hl"><?php esc_html_e('operación', 'ese-latam'); ?></span>
        </h1>
        <p class="ctc-intro__lede" data-reveal-desc>
            <?php esc_html_e('Te responderemos en menos de', 'ese-latam'); ?>
            <span class="hl-accent"><?php esc_html_e('24 horas', 'ese-latam'); ?></span>
            <?php esc_html_e('hábiles.', 'ese-latam'); ?>
        </p>
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
                        'label'        => __('Nombre y apellido', 'ese-latam'),
                        'placeholder'  => __('Escribe tu nombre y apellido...', 'ese-latam'),
                        'autocomplete' => 'name',
                    ]);
                    $ese_campo([
                        'name'         => 'email',
                        'type'         => 'email',
                        'label'        => __('Correo electrónico', 'ese-latam'),
                        'placeholder'  => 'ejemplo@entidad.com',
                        'autocomplete' => 'email',
                    ]);
                    $ese_campo([
                        'name'         => 'telefono',
                        'type'         => 'tel',
                        'label'        => __('Teléfono', 'ese-latam'),
                        'placeholder'  => '+51 999 999 999',
                        'autocomplete' => 'tel',
                    ]);
                    $ese_campo([
                        'name'        => 'pais',
                        'type'        => 'select',
                        'label'       => __('País', 'ese-latam'),
                        'placeholder' => __('Selecciona tu país...', 'ese-latam'),
                        'options'     => $ese_paises_opts,
                    ]);
                    $ese_campo([
                        'name'        => 'sector',
                        'type'        => 'select',
                        'label'       => __('Sector', 'ese-latam'),
                        'placeholder' => __('Selecciona tu sector...', 'ese-latam'),
                        'options'     => $ese_sectores_opts,
                    ]);
                    $ese_campo([
                        'name'        => 'producto',
                        'type'        => 'select',
                        'label'       => __('Producto de interés', 'ese-latam'),
                        'placeholder' => __('Selecciona un producto...', 'ese-latam'),
                        'options'     => $ese_productos_opts,
                        'required'    => false,
                    ]);
                    $ese_campo([
                        'name'        => 'mensaje',
                        'type'        => 'textarea',
                        'label'       => __('Mensaje adicional o descripción de requerimiento', 'ese-latam'),
                        'placeholder' => __('Cuéntanos volúmenes, plazos y el detalle de tu operación...', 'ese-latam'),
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
                            <span class="ctc-submit__text"><?php esc_html_e('Solicitar asesoría', 'ese-latam'); ?></span>
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

                    <p class="ctc-form__note">
                        <?php esc_html_e('Los campos con', 'ese-latam'); ?>
                        <span class="ctc-field__req">*</span>
                        <?php esc_html_e('son obligatorios.', 'ese-latam'); ?>
                    </p>
                </div>

                <?php // Estado del envío: lo rellena contacto-page.ts, o llega
                // ya resuelto desde el POST sin JS (?contacto=ok|error). ?>
                <p class="ctc-form__status<?php echo $ese_estado ? ' is-visible is-' . esc_attr('ok' === $ese_estado ? 'ok' : 'error') : ''; ?>"
                    data-ctc-status role="status" aria-live="polite">
                    <?php
                    if ('ok' === $ese_estado) {
                        esc_html_e('¡Gracias! Recibimos tu mensaje y te responderemos en menos de 24 horas hábiles.', 'ese-latam');
                    } elseif ($ese_estado) {
                        esc_html_e('No pudimos enviar tu mensaje. Revisa los campos e inténtalo de nuevo.', 'ese-latam');
                    }
                    ?>
                </p>
            </form>
        </div>

        <aside class="ctc-aside">
            <?php // Tarjeta de marca en el hueco que el Figma deja como
            // placeholder gris: foto urbana del theme + dato de respaldo. ?>
            <div class="ctc-aside__media" data-reveal="fade">
                <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/contacto-bg.jpg'); ?>" alt=""
                    loading="lazy" decoding="async">
                <div class="ctc-aside__media-body">
                    <p class="ctc-aside__media-kicker"><?php esc_html_e('Sede Miraflores', 'ese-latam'); ?></p>
                    <p class="ctc-aside__media-text">
                        <?php esc_html_e('Atendemos operaciones en 13 países de Latinoamérica.', 'ese-latam'); ?>
                    </p>
                </div>
            </div>

            <p class="ctc-aside__title"><?php esc_html_e('Información de contacto', 'ese-latam'); ?></p>

            <ul class="ctc-info" data-reveal-stagger>
                <?php foreach ($ese_info as $ese_item) : ?>
                    <li class="ctc-info__item">
                        <img class="ctc-info__icon"
                            src="<?php echo esc_url(ESE_LATAM_URI . '/assets/icons/contacto/' . $ese_item['icon']); ?>"
                            alt="" width="80" height="80" loading="lazy" decoding="async">
                        <span class="ctc-info__text">
                            <span class="ctc-info__label"><?php echo esc_html($ese_item['label']); ?></span>
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
