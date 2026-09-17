<?php
/**
 * Hero oscuro de página interna (Figma "Frame 427320376", compartido por
 * Solución por sector 3551-5050, Certificaciones 3785-4090 e Impacto
 * 3824-3689). Es el mismo hero de Nosotros (.nos-hero, animado por
 * initHero en nosotros.ts) más dos piezas que ese no tenía: un kicker sobre
 * el titular y un CTA bajo la bajada.
 *
 * Debe ir dentro del wrapper `.nosotros[data-nosotros]` (ver page-*.php):
 * de ahí toma los gutters, la regla `.is-js` que esconde las partes hasta
 * que el JS las anima, y el módulo que las anima.
 *
 * @param array{
 *     kicker?: string, title?: string, desc?: string,
 *     cta_label?: string, cta_href?: string, cta_target?: string,
 *     crumb?: list<array{label: string, url: string}>, current?: string, bg?: string
 * } $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_hero = wp_parse_args($args ?? [], [
    'kicker'       => '',
    'title'        => '',
    'desc'         => '',
    'cta_label'    => '',
    'cta_href'     => '',
    'cta_target'   => '',
    'crumb'        => [],
    'current'      => '',
    'bg'           => ESE_LATAM_URI . '/assets/imgs/hero-poster.webp',
]);
?>

<section class="nos-hero nos-hero--interna" data-nos-hero>
    <div class="nos-hero__bg" aria-hidden="true" data-nos-hero-bg>
        <img src="<?php echo esc_url($ese_hero['bg']); ?>" alt="" decoding="async" fetchpriority="high">
    </div>
    <div class="nos-hero__shade" aria-hidden="true"></div>
    <div class="nos-hero__glow" aria-hidden="true" data-nos-hero-glow></div>

    <div class="nos-hero__content" data-nos-hero-content>
        <?php
        get_template_part('template-parts/breadcrumbs', null, [
            'items'   => $ese_hero['crumb'],
            'current' => $ese_hero['current'],
            'attrs'   => 'data-nos-hero-crumb',
        ]);
        ?>

        <div class="nos-hero__bottom">
            <div class="nos-hero__heading">
                <?php if ('' !== $ese_hero['kicker']) : ?>
                    <p class="nos-hero__kicker" data-nos-hero-kicker><?php echo esc_html($ese_hero['kicker']); ?></p>
                <?php endif; ?>
                <h1 class="nos-hero__title" data-nos-hero-title>
                    <?php echo ese_latam_titulo($ese_hero['title']); ?>
                </h1>
            </div>

            <div class="nos-hero__aside">
                <?php if ('' !== $ese_hero['desc']) : ?>
                    <p class="nos-hero__desc" data-nos-hero-desc><?php echo esc_html($ese_hero['desc']); ?></p>
                <?php endif; ?>
                <?php if ('' !== $ese_hero['cta_label']) : ?>
                    <div class="nos-hero__cta" data-nos-hero-cta>
                        <?php
                        ese_latam_cta_button([
                            'href'   => $ese_hero['cta_href'] ?: (string) get_post_type_archive_link('producto'),
                            'label'  => $ese_hero['cta_label'],
                            'target' => $ese_hero['cta_target'],
                        ]);
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="nos-hero__scroll" aria-hidden="true" data-nos-hero-scroll>
        <span class="nos-hero__scroll-line"></span>
        <span class="nos-hero__scroll-text"><?php esc_html_e('Scroll', 'ese-latam'); ?></span>
    </div>
</section>
