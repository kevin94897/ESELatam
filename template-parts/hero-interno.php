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
 *     kicker?: string, title?: string, title_strong?: string, desc?: string,
 *     cta_label?: string, cta_href?: string,
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
    'title_strong' => '',
    'desc'         => '',
    'cta_label'    => '',
    'cta_href'     => '',
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
        <nav class="nos-crumb" aria-label="<?php esc_attr_e('Ruta de navegación', 'ese-latam'); ?>" data-nos-hero-crumb>
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M10 5.27979V10.56C10 10.6767 9.9561 10.7886 9.87796 10.8711C9.79982 10.9536 9.69384 11 9.58333 11H6.66667C6.55616 11 6.45018 10.9536 6.37204 10.8711C6.2939 10.7886 6.25 10.6767 6.25 10.56V7.69988C6.25 7.64153 6.22805 7.58557 6.18898 7.54431C6.14991 7.50305 6.09692 7.47987 6.04167 7.47987H3.95833C3.90308 7.47987 3.85009 7.50305 3.81102 7.54431C3.77195 7.58557 3.75 7.64153 3.75 7.69988V10.56C3.75 10.6767 3.7061 10.7886 3.62796 10.8711C3.54982 10.9536 3.44384 11 3.33333 11H0.416667C0.30616 11 0.200179 10.9536 0.122039 10.8711C0.0438988 10.7886 0 10.6767 0 10.56V5.27979C0.000102442 5.04643 0.0879669 4.82267 0.244271 4.65772L4.41094 0.257552C4.5672 0.0926383 4.77908 0 5 0C5.22092 0 5.4328 0.0926383 5.58906 0.257552L9.75573 4.65772C9.91203 4.82267 9.9999 5.04643 10 5.27979Z" fill="currentColor"/>
                </svg>
                <?php esc_html_e('Inicio', 'ese-latam'); ?>
            </a>
            <?php foreach ($ese_hero['crumb'] as $ese_item) : ?>
                <span class="nos-crumb__sep" aria-hidden="true">
                    <svg width="4" height="7" viewBox="0 0 4 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.86395 3.8233L0.788805 6.86609C0.70215 6.95183 0.58462 7 0.462071 7C0.339522 7 0.221993 6.95183 0.135337 6.86609C0.0486823 6.78034 0 6.66405 0 6.54279C0 6.42153 0.0486823 6.30524 0.135337 6.21949L2.88413 3.50038L0.136106 0.780507C0.0931991 0.738051 0.059163 0.687648 0.0359418 0.632177C0.0127205 0.576706 0.000768656 0.517252 0.000768656 0.45721C0.000768656 0.397169 0.0127205 0.337715 0.0359418 0.282243C0.059163 0.226772 0.0931991 0.17637 0.136106 0.133914C0.179014 0.0914579 0.229952 0.0577801 0.286013 0.0348031C0.342074 0.0118261 0.40216 0 0.46284 0C0.52352 0 0.583606 0.0118261 0.639667 0.0348031C0.695728 0.0577801 0.746666 0.0914579 0.789574 0.133914L3.86471 3.1767C3.90767 3.21916 3.94173 3.26958 3.96494 3.32509C3.98816 3.38059 4.00007 3.44009 4 3.50016C3.99993 3.56023 3.98787 3.61969 3.96453 3.67515C3.94118 3.7306 3.907 3.78094 3.86395 3.8233Z" fill="currentColor"/></svg>
                </span>
                <a href="<?php echo esc_url($ese_item['url']); ?>"><?php echo esc_html($ese_item['label']); ?></a>
            <?php endforeach; ?>
            <?php if ('' !== $ese_hero['current']) : ?>
                <span class="nos-crumb__sep" aria-hidden="true">
                    <svg width="4" height="7" viewBox="0 0 4 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.86395 3.8233L0.788805 6.86609C0.70215 6.95183 0.58462 7 0.462071 7C0.339522 7 0.221993 6.95183 0.135337 6.86609C0.0486823 6.78034 0 6.66405 0 6.54279C0 6.42153 0.0486823 6.30524 0.135337 6.21949L2.88413 3.50038L0.136106 0.780507C0.0931991 0.738051 0.059163 0.687648 0.0359418 0.632177C0.0127205 0.576706 0.000768656 0.517252 0.000768656 0.45721C0.000768656 0.397169 0.0127205 0.337715 0.0359418 0.282243C0.059163 0.226772 0.0931991 0.17637 0.136106 0.133914C0.179014 0.0914579 0.229952 0.0577801 0.286013 0.0348031C0.342074 0.0118261 0.40216 0 0.46284 0C0.52352 0 0.583606 0.0118261 0.639667 0.0348031C0.695728 0.0577801 0.746666 0.0914579 0.789574 0.133914L3.86471 3.1767C3.90767 3.21916 3.94173 3.26958 3.96494 3.32509C3.98816 3.38059 4.00007 3.44009 4 3.50016C3.99993 3.56023 3.98787 3.61969 3.96453 3.67515C3.94118 3.7306 3.907 3.78094 3.86395 3.8233Z" fill="currentColor"/></svg>
                </span>
                <span aria-current="page"><?php echo esc_html($ese_hero['current']); ?></span>
            <?php endif; ?>
        </nav>

        <div class="nos-hero__bottom">
            <div class="nos-hero__heading">
                <?php if ('' !== $ese_hero['kicker']) : ?>
                    <p class="nos-hero__kicker" data-nos-hero-kicker><?php echo esc_html($ese_hero['kicker']); ?></p>
                <?php endif; ?>
                <h1 class="nos-hero__title" data-nos-hero-title>
                    <?php echo esc_html($ese_hero['title']); ?>
                    <?php if ('' !== $ese_hero['title_strong']) : ?>
                        <strong><?php echo esc_html($ese_hero['title_strong']); ?></strong>
                    <?php endif; ?>
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
                            'href'  => $ese_hero['cta_href'] ?: (string) get_post_type_archive_link('producto'),
                            'label' => $ese_hero['cta_label'],
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
