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
 * } $args
 */
function ese_latam_cta_button(array $args = []): void {
    $args = wp_parse_args($args, [
        'href'   => '#',
        'label'  => __('Explorar productos', 'ese-latam'),
        'reveal' => false,
        'class'  => '',
    ]);

    $classes = trim('hero-cta ' . $args['class']);
    ?>
    <a href="<?php echo esc_url($args['href']); ?>"
       class="<?php echo esc_attr($classes); ?>"
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
