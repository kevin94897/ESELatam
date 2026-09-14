<?php
/**
 * Página Impacto — "Gestionar residuos es una decisión humana" (Figma
 * 3824-3737): titular centrado sobre cielo con nubes y un mosaico de
 * tiles (marca, 500+ proyectos, 60+ marcas, tarjeta central con foto,
 * foto lateral y alcance 13 países). Los números cuentan desde 0 al
 * entrar en pantalla (count-up.ts).
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_img = static fn (string $file): string => ESE_LATAM_URI . '/assets/imgs/' . $file;
?>

<section class="imp-stats" id="impacto">
    <div class="imp-stats__bg" aria-hidden="true">
        <img src="<?php echo esc_url($ese_img('nosotros/cielo.webp')); ?>" alt="" loading="lazy" decoding="async"
            data-parallax data-parallax-from="-8" data-parallax-to="8">
    </div>
    <div class="imp-stats__rings" aria-hidden="true">
        <span class="imp-stats__ring"></span><span class="imp-stats__ring"></span><span class="imp-stats__ring"></span>
    </div>

    <header class="imp-stats__header" data-reveal-header>
        <p class="imp-stats__kicker"><?php esc_html_e('Respaldo comprobado', 'ese-latam'); ?></p>
        <h2 class="imp-stats__title">
            <?php esc_html_e('Gestionar residuos es una', 'ese-latam'); ?><br>
            <strong><?php esc_html_e('decisión humana', 'ese-latam'); ?></strong>
        </h2>
    </header>

    <div class="imp-stats__mosaic" data-reveal-stagger>
        <div class="imp-tile imp-tile--logo">
            <img class="imp-tile__bg" src="<?php echo esc_url($ese_img('impacto/logo-tile.webp')); ?>" alt="" loading="lazy" decoding="async">
            <img class="imp-tile__mark" src="<?php echo esc_url($ese_img('impacto/ese-mark.png')); ?>" alt="ESE" width="302" height="309" loading="lazy" decoding="async"
                data-float data-float-distance="6" data-float-duration="4">
        </div>

        <div class="imp-stats__col">
            <div class="imp-tile imp-tile--num">
                <p class="imp-tile__num"><span data-count-to="500" data-count-suffix="+">500+</span></p>
                <p class="imp-tile__label"><?php esc_html_e('Proyectos en Latam', 'ese-latam'); ?></p>
            </div>
            <div class="imp-tile imp-tile--num imp-tile--green">
                <p class="imp-tile__num"><span data-count-to="60" data-count-suffix="+">60+</span></p>
                <p class="imp-tile__label"><?php esc_html_e('Marcas aliadas', 'ese-latam'); ?></p>
            </div>
        </div>

        <article class="imp-tile imp-tile--main">
            <img class="imp-tile__bg" src="<?php echo esc_url($ese_img('impacto/preservar.webp')); ?>" alt="" loading="lazy" decoding="async">
            <span class="imp-tile__shade" aria-hidden="true"></span>
            <h3 class="imp-tile__title"><?php esc_html_e('Preservar la salud de nuestra tierra', 'ese-latam'); ?></h3>
            <p class="imp-tile__desc"><?php esc_html_e('Antes de llegar a tu ciudad o industria, cada contenedor ESE supera pruebas que simulan años de uso para garantizar un desempeño confiable.', 'ese-latam'); ?></p>
        </article>

        <div class="imp-tile imp-tile--photo">
            <img class="imp-tile__bg" src="<?php echo esc_url($ese_img('impacto/empatia.webp')); ?>" alt="" loading="lazy" decoding="async">
            <span class="imp-tile__shade" aria-hidden="true"></span>
            <p class="imp-tile__caption"><?php esc_html_e('Inspirar la empatía con el entorno', 'ese-latam'); ?></p>
        </div>

        <div class="imp-tile imp-tile--reach">
            <p class="imp-tile__kicker"><?php esc_html_e('Alcance Latam', 'ese-latam'); ?></p>
            <p class="imp-tile__num"><span data-count-to="13">13</span></p>
            <p class="imp-tile__label"><?php esc_html_e('Países', 'ese-latam'); ?></p>
        </div>
    </div>
</section>
