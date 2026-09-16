<?php
/**
 * Página Sectores — hero claro (Figma 3510-6983): breadcrumb + titular
 * centrado "Soluciones por SECTOR" + bajada. El breadcrumb reutiliza
 * .nos-crumb (página Nosotros), que ya tiene el mismo look del Figma.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}
?>

<section class="sec-hero">
    <nav class="nos-crumb sec-hero__crumb" aria-label="<?php esc_attr_e('Ruta de navegación', 'ese-latam'); ?>" data-reveal="fade">
        <a href="<?php echo esc_url(home_url('/')); ?>">
            <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M10 5.27979V10.56C10 10.6767 9.9561 10.7886 9.87796 10.8711C9.79982 10.9536 9.69384 11 9.58333 11H6.66667C6.55616 11 6.45018 10.9536 6.37204 10.8711C6.2939 10.7886 6.25 10.6767 6.25 10.56V7.69988C6.25 7.64153 6.22805 7.58557 6.18898 7.54431C6.14991 7.50305 6.09692 7.47987 6.04167 7.47987H3.95833C3.90308 7.47987 3.85009 7.50305 3.81102 7.54431C3.77195 7.58557 3.75 7.64153 3.75 7.69988V10.56C3.75 10.6767 3.7061 10.7886 3.62796 10.8711C3.54982 10.9536 3.44384 11 3.33333 11H0.416667C0.30616 11 0.200179 10.9536 0.122039 10.8711C0.0438988 10.7886 0 10.6767 0 10.56V5.27979C0.000102442 5.04643 0.0879669 4.82267 0.244271 4.65772L4.41094 0.257552C4.5672 0.0926383 4.77908 0 5 0C5.22092 0 5.4328 0.0926383 5.58906 0.257552L9.75573 4.65772C9.91203 4.82267 9.9999 5.04643 10 5.27979Z" fill="currentColor"/>
            </svg>
            <?php esc_html_e('Inicio', 'ese-latam'); ?>
        </a>
        <span class="nos-crumb__sep" aria-hidden="true">
            <svg width="4" height="7" viewBox="0 0 4 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3.86395 3.8233L0.788805 6.86609C0.70215 6.95183 0.58462 7 0.462071 7C0.339522 7 0.221993 6.95183 0.135337 6.86609C0.0486823 6.78034 0 6.66405 0 6.54279C0 6.42153 0.0486823 6.30524 0.135337 6.21949L2.88413 3.50038L0.136106 0.780507C0.0931991 0.738051 0.059163 0.687648 0.0359418 0.632177C0.0127205 0.576706 0.000768656 0.517252 0.000768656 0.45721C0.000768656 0.397169 0.0127205 0.337715 0.0359418 0.282243C0.059163 0.226772 0.0931991 0.17637 0.136106 0.133914C0.179014 0.0914579 0.229952 0.0577801 0.286013 0.0348031C0.342074 0.0118261 0.40216 0 0.46284 0C0.52352 0 0.583606 0.0118261 0.639667 0.0348031C0.695728 0.0577801 0.746666 0.0914579 0.789574 0.133914L3.86471 3.1767C3.90767 3.21916 3.94173 3.26958 3.96494 3.32509C3.98816 3.38059 4.00007 3.44009 4 3.50016C3.99993 3.56023 3.98787 3.61969 3.96453 3.67515C3.94118 3.7306 3.907 3.78094 3.86395 3.8233Z" fill="currentColor"/>
            </svg>
        </span>
        <span aria-current="page"><?php esc_html_e('Soluciones', 'ese-latam'); ?></span>
    </nav>

    <?php
    $ese_sh_id     = (int) get_queried_object_id();
    $ese_sh_titulo = trim((string) ese_latam_campo('sectores_hero_titulo', $ese_sh_id, ''));
    $ese_sh_desc   = ese_latam_texto_rico((string) ese_latam_campo('sectores_hero_desc', $ese_sh_id, ''));
    ?>
    <?php if ('' !== $ese_sh_titulo || '' !== $ese_sh_desc) : ?>
        <header class="sec-hero__header" data-reveal-header>
            <?php if ('' !== $ese_sh_titulo) : ?>
                <h1 class="sec-hero__title">
                    <?php echo ese_latam_titulo($ese_sh_titulo, 'span', 'hl'); ?>
                </h1>
            <?php endif; ?>
            <?php if ('' !== $ese_sh_desc) : ?>
                <p class="sec-hero__desc" data-reveal-desc><?php echo $ese_sh_desc; ?></p>
            <?php endif; ?>
        </header>
    <?php endif; ?>
</section>
