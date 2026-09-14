<?php
/**
 * Single de sector — "Nuestro criterio de adaptabilidad" (Figma 3585-5235):
 * cita + párrafo a la izquierda, contenedor con callouts (Hormigón / Aislado
 * / HDPE Premium) a la derecha.
 *
 * @param array{quote?: string, desc?: string, product?: string, callouts?: list<array{label: string, pos: string}>} $args
 *   `quote` admite span.hl-accent; `product` es una imagen relativa a
 *   assets/imgs/; `pos` es una de: left-top, left-bottom, right.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_c = wp_parse_args($args ?? [], [
    'kicker'   => __('ESE Latam', 'ese-latam'),
    'title'    => __('Nuestro criterio', 'ese-latam'),
    'title_strong' => __('de adaptabilidad', 'ese-latam'),
    'quote'    => '',
    'desc'     => '',
    'product'  => 'productos/contenedores-2-ruedas/120L_FC020.png',
    'callouts' => [
        ['label' => __('Hormigón', 'ese-latam'),     'pos' => 'left-top'],
        ['label' => __('Aislado', 'ese-latam'),      'pos' => 'left-bottom'],
        ['label' => __('HDPE Premium', 'ese-latam'), 'pos' => 'right'],
    ],
]);

$ese_img = static fn (string $file): string => ESE_LATAM_URI . '/assets/imgs/' . $file;
?>

<section class="criterio" id="criterio">
    <div class="criterio__text">
        <header data-reveal-header>
            <p class="type-kicker text-secondary">/ <?php echo esc_html($ese_c['kicker']); ?></p>
            <h2 class="type-h2 uppercase">
                <?php echo esc_html($ese_c['title']); ?><br>
                <span class="hl"><?php echo esc_html($ese_c['title_strong']); ?></span>
            </h2>
        </header>

        <?php if ('' !== $ese_c['quote']) : ?>
            <blockquote class="criterio__quote" data-reveal="up">
                <p>“<?php echo wp_kses($ese_c['quote'], ['span' => ['class' => []]]); ?>”</p>
            </blockquote>
        <?php endif; ?>

        <?php if ('' !== $ese_c['desc']) : ?>
            <p class="nos-desc criterio__desc" data-reveal="up" data-reveal-delay="0.1"><?php echo esc_html($ese_c['desc']); ?></p>
        <?php endif; ?>

        <hr class="criterio__divider" data-reveal="fade">

        <div class="criterio__brand" data-reveal="up">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M23.1249 12.4163L18.8442 18.8325C18.7075 19.0375 18.5224 19.2056 18.3052 19.322C18.0881 19.4384 17.8456 19.4995 17.5992 19.5H3.00049C2.86467 19.5001 2.73138 19.4633 2.61485 19.3935C2.49831 19.3238 2.4029 19.2237 2.3388 19.104C2.27469 18.9843 2.24431 18.8494 2.25088 18.7137C2.25745 18.5781 2.30074 18.4467 2.37611 18.3337L6.59955 12L2.3808 5.66625C2.30564 5.5536 2.26238 5.42271 2.25562 5.28746C2.24885 5.15221 2.27883 5.01765 2.34237 4.89807C2.40591 4.77849 2.50065 4.67834 2.61652 4.60825C2.73239 4.53816 2.86507 4.50076 3.00049 4.5H17.5992C17.8456 4.50046 18.0881 4.5616 18.3052 4.67801C18.5224 4.79443 18.7075 4.96255 18.8442 5.1675L23.122 11.5837C23.2047 11.7067 23.2491 11.8514 23.2496 11.9996C23.2501 12.1477 23.2067 12.2927 23.1249 12.4163Z" fill="currentColor"/>
            </svg>
            <span><?php esc_html_e('ESE Latam', 'ese-latam'); ?></span>
        </div>
        <p class="criterio__brand-sub" data-reveal="up" data-reveal-delay="0.1"><?php esc_html_e('Equipo ejecutivo de desarrollo sostenible', 'ese-latam'); ?></p>
    </div>

    <div class="criterio__scene" data-reveal="fade">
        <span class="criterio__shadow" aria-hidden="true"></span>
        <img class="criterio__product" src="<?php echo esc_url($ese_img($ese_c['product'])); ?>"
            alt="<?php esc_attr_e('Contenedor ESE de dos ruedas', 'ese-latam'); ?>" loading="lazy" decoding="async"
            data-float data-float-distance="8" data-float-duration="4.2">
        <?php foreach ($ese_c['callouts'] as $ese_i => $ese_call) : ?>
            <span class="criterio__callout criterio__callout--<?php echo esc_attr($ese_call['pos']); ?>"
                data-reveal="fade" data-reveal-delay="<?php echo esc_attr((string) (0.4 + $ese_i * 0.15)); ?>">
                <span class="criterio__callout-text"><?php echo esc_html($ese_call['label']); ?></span>
                <span class="criterio__callout-line" aria-hidden="true"></span>
                <span class="criterio__callout-dot" aria-hidden="true"></span>
            </span>
        <?php endforeach; ?>
    </div>
</section>
