<?php
/**
 * Hero de ficha — artículo del blog (Figma 3873-10142) y caso de éxito
 * (Figma 3891-4495): la foto destacada a pantalla completa bajo un degradado
 * oscuro, el breadcrumb, el titular a la izquierda y, bajo una línea, el
 * chip de sección con datos sueltos separados por un punto.
 *
 * Las dos pantallas son el mismo hero con distinta procedencia de los datos
 * —categoría vs. sector, lugar vs. ciudad—, así que no los deduce: los
 * recibe ya resueltos y solo decide qué pinta y qué no.
 *
 * A diferencia del hero del listado (casos-hero.php) el titular va alineado
 * a la izquierda y sin resaltado: es el título de la entrada.
 *
 * Debe ir dentro del wrapper `.nosotros[data-nosotros]` para que lo anime
 * initHero de nosotros.ts.
 *
 * @param array{
 *     titulo?: string, desc?: string, foto?: string,
 *     chip?: string, chip_url?: string, datos?: list<string>,
 *     crumb_label?: string, crumb_url?: string
 * } $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_ah = wp_parse_args($args ?? [], [
    'titulo'      => '',
    'desc'        => '',
    'foto'        => '',
    'chip'        => '',
    'chip_url'    => '',
    'datos'       => [],
    'crumb_label' => '',
    'crumb_url'   => '',
]);

$ese_ah_titulo = trim((string) $ese_ah['titulo']);
if ('' === $ese_ah_titulo) {
    return;
}

$ese_ah_datos = array_values(array_filter(
    array_map(static fn ($d): string => trim((string) $d), (array) $ese_ah['datos']),
    static fn (string $d): bool => '' !== $d
));

$ese_ah_chip = trim((string) $ese_ah['chip']);
$ese_ah_meta = '' !== $ese_ah_chip || [] !== $ese_ah_datos;
?>

<section class="nos-hero nos-hero--interna art-hero" data-nos-hero>
    <?php if ('' !== $ese_ah['foto']) : ?>
        <div class="nos-hero__bg" aria-hidden="true" data-nos-hero-bg>
            <img src="<?php echo esc_url($ese_ah['foto']); ?>" alt="" decoding="async" fetchpriority="high">
        </div>
    <?php endif; ?>
    <div class="nos-hero__shade art-hero__shade" aria-hidden="true"></div>
    <div class="nos-hero__glow" aria-hidden="true" data-nos-hero-glow></div>

    <div class="nos-hero__content art-hero__content" data-nos-hero-content>
        <nav class="nos-crumb" aria-label="<?php esc_attr_e('Ruta de navegación', 'ese-latam'); ?>" data-nos-hero-crumb>
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M10 5.27979V10.56C10 10.6767 9.9561 10.7886 9.87796 10.8711C9.79982 10.9536 9.69384 11 9.58333 11H6.66667C6.55616 11 6.45018 10.9536 6.37204 10.8711C6.2939 10.7886 6.25 10.6767 6.25 10.56V7.69988C6.25 7.64153 6.22805 7.58557 6.18898 7.54431C6.14991 7.50305 6.09692 7.47987 6.04167 7.47987H3.95833C3.90308 7.47987 3.85009 7.50305 3.81102 7.54431C3.77195 7.58557 3.75 7.64153 3.75 7.69988V10.56C3.75 10.6767 3.7061 10.7886 3.62796 10.8711C3.54982 10.9536 3.44384 11 3.33333 11H0.416667C0.30616 11 0.200179 10.9536 0.122039 10.8711C0.0438988 10.7886 0 10.6767 0 10.56V5.27979C0.000102442 5.04643 0.0879669 4.82267 0.244271 4.65772L4.41094 0.257552C4.5672 0.0926383 4.77908 0 5 0C5.22092 0 5.4328 0.0926383 5.58906 0.257552L9.75573 4.65772C9.91203 4.82267 9.9999 5.04643 10 5.27979Z" fill="currentColor"/>
                </svg>
                <?php esc_html_e('Inicio', 'ese-latam'); ?>
            </a>
            <?php if ('' !== $ese_ah['crumb_url'] && '' !== $ese_ah['crumb_label']) : ?>
                <span class="nos-crumb__sep" aria-hidden="true">
                    <svg width="4" height="7" viewBox="0 0 4 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.86395 3.8233L0.788805 6.86609C0.70215 6.95183 0.58462 7 0.462071 7C0.339522 7 0.221993 6.95183 0.135337 6.86609C0.0486823 6.78034 0 6.66405 0 6.54279C0 6.42153 0.0486823 6.30524 0.135337 6.21949L2.88413 3.50038L0.136106 0.780507C0.0931991 0.738051 0.059163 0.687648 0.0359418 0.632177C0.0127205 0.576706 0.000768656 0.517252 0.000768656 0.45721C0.000768656 0.397169 0.0127205 0.337715 0.0359418 0.282243C0.059163 0.226772 0.0931991 0.17637 0.136106 0.133914C0.179014 0.0914579 0.229952 0.0577801 0.286013 0.0348031C0.342074 0.0118261 0.40216 0 0.46284 0C0.52352 0 0.583606 0.0118261 0.639667 0.0348031C0.695728 0.0577801 0.746666 0.0914579 0.789574 0.133914L3.86471 3.1767C3.90767 3.21916 3.94173 3.26958 3.96494 3.32509C3.98816 3.38059 4.00007 3.44009 4 3.50016C3.99993 3.56023 3.98787 3.61969 3.96453 3.67515C3.94118 3.7306 3.907 3.78094 3.86395 3.8233Z" fill="currentColor"/></svg>
                </span>
                <a href="<?php echo esc_url($ese_ah['crumb_url']); ?>"><?php echo esc_html($ese_ah['crumb_label']); ?></a>
            <?php endif; ?>
            <span class="nos-crumb__sep" aria-hidden="true">
                <svg width="4" height="7" viewBox="0 0 4 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.86395 3.8233L0.788805 6.86609C0.70215 6.95183 0.58462 7 0.462071 7C0.339522 7 0.221993 6.95183 0.135337 6.86609C0.0486823 6.78034 0 6.66405 0 6.54279C0 6.42153 0.0486823 6.30524 0.135337 6.21949L2.88413 3.50038L0.136106 0.780507C0.0931991 0.738051 0.059163 0.687648 0.0359418 0.632177C0.0127205 0.576706 0.000768656 0.517252 0.000768656 0.45721C0.000768656 0.397169 0.0127205 0.337715 0.0359418 0.282243C0.059163 0.226772 0.0931991 0.17637 0.136106 0.133914C0.179014 0.0914579 0.229952 0.0577801 0.286013 0.0348031C0.342074 0.0118261 0.40216 0 0.46284 0C0.52352 0 0.583606 0.0118261 0.639667 0.0348031C0.695728 0.0577801 0.746666 0.0914579 0.789574 0.133914L3.86471 3.1767C3.90767 3.21916 3.94173 3.26958 3.96494 3.32509C3.98816 3.38059 4.00007 3.44009 4 3.50016C3.99993 3.56023 3.98787 3.61969 3.96453 3.67515C3.94118 3.7306 3.907 3.78094 3.86395 3.8233Z" fill="currentColor"/></svg>
            </span>
            <span aria-current="page"><?php echo esc_html($ese_ah_titulo); ?></span>
        </nav>

        <div class="art-hero__body">
            <h1 class="nos-hero__title art-hero__title" data-nos-hero-title><?php echo esc_html($ese_ah_titulo); ?></h1>
            <?php if ('' !== $ese_ah['desc']) : ?>
                <p class="nos-hero__desc art-hero__desc" data-nos-hero-desc><?php echo esc_html($ese_ah['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php if ($ese_ah_meta) : ?>
            <div class="art-hero__meta">
                <?php if ('' !== $ese_ah_chip) : ?>
                    <?php if ('' !== $ese_ah['chip_url']) : ?>
                        <a class="art-hero__chip" href="<?php echo esc_url($ese_ah['chip_url']); ?>"><?php echo esc_html($ese_ah_chip); ?></a>
                    <?php else : ?>
                        <span class="art-hero__chip"><?php echo esc_html($ese_ah_chip); ?></span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php foreach ($ese_ah_datos as $ese_ah_i => $ese_ah_dato) : ?>
                    <?php if ($ese_ah_i > 0) : ?>
                        <span class="art-hero__sep" aria-hidden="true"></span>
                    <?php endif; ?>
                    <span class="art-hero__dato"><?php echo esc_html($ese_ah_dato); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
