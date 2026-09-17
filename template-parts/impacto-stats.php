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

// Todas las celdas del mosaico salen de los campos de la página
// (inc/pcf-impacto.php). La composición es fija; lo que no esté cargado
// simplemente no se pinta.
$ese_imp_id = (int) get_queried_object_id();
$ese_imp    = static fn (string $name) => ese_latam_campo($name, $ese_imp_id, '');
$ese_imp_img = static fn (string $name): string => ese_latam_img_url(ese_latam_campo($name, $ese_imp_id, ''));

$ese_imp_kicker = trim((string) $ese_imp('impacto_kicker'));
$ese_imp_titulo = trim((string) $ese_imp('impacto_titulo'));
$ese_imp_cielo  = $ese_imp_img('impacto_cielo');

$ese_imp_cifras = [];
foreach ((array) ese_latam_campo('impacto_cifras', $ese_imp_id, []) as $ese_fila) {
    $ese_etiqueta = trim((string) ($ese_fila['etiqueta'] ?? ''));
    if ('' === $ese_etiqueta) {
        continue;
    }
    $ese_imp_cifras[] = [
        'num'    => (string) ($ese_fila['numero'] ?? ''),
        'sufijo' => (string) ($ese_fila['sufijo'] ?? ''),
        'label'  => $ese_etiqueta,
    ];
}

$ese_imp_marca_fondo = $ese_imp_img('impacto_marca_fondo');
$ese_imp_marca_logo  = $ese_imp_img('impacto_marca_logo');

$ese_imp_ppal_titulo = trim((string) $ese_imp('impacto_principal_titulo'));
$ese_imp_ppal_desc   = trim((string) $ese_imp('impacto_principal_desc'));
$ese_imp_ppal_img    = $ese_imp_img('impacto_principal_imagen');

$ese_imp_foto_texto = trim((string) $ese_imp('impacto_foto_texto'));
$ese_imp_foto_img   = $ese_imp_img('impacto_foto_imagen');

$ese_imp_alc_kicker = trim((string) $ese_imp('impacto_alcance_kicker'));
$ese_imp_alc_label  = trim((string) $ese_imp('impacto_alcance_etiqueta'));
// Sin número propio se cuentan los países cargados en Distribuidores.
$ese_imp_alc_num    = (string) $ese_imp('impacto_alcance_numero');
if ('' === $ese_imp_alc_num) {
    $ese_imp_alc_num = (string) count(ese_latam_distribuidores());
}

// Sin titular ni celdas no hay mosaico que mostrar.
if ('' === $ese_imp_titulo && [] === $ese_imp_cifras && '' === $ese_imp_ppal_titulo) {
    return;
}
?>

<section class="imp-stats" id="impacto">
    <?php if ('' !== $ese_imp_cielo) : ?>
        <div class="imp-stats__bg" aria-hidden="true">
            <img src="<?php echo esc_url($ese_imp_cielo); ?>" alt="" loading="lazy" decoding="async"
                data-parallax data-parallax-from="-8" data-parallax-to="8">
        </div>
    <?php endif; ?>
    <div class="imp-stats__rings" aria-hidden="true">
        <span class="imp-stats__ring"></span><span class="imp-stats__ring"></span><span class="imp-stats__ring"></span>
    </div>

    <header class="imp-stats__header" data-reveal-header>
        <?php if ('' !== $ese_imp_kicker) : ?>
            <p class="imp-stats__kicker"><?php echo esc_html($ese_imp_kicker); ?></p>
        <?php endif; ?>
        <?php if ('' !== $ese_imp_titulo) : ?>
            <h2 class="imp-stats__title"><?php echo ese_latam_titulo($ese_imp_titulo); ?></h2>
        <?php endif; ?>
    </header>

    <div class="imp-stats__mosaic" data-reveal-stagger>
        <?php if ('' !== $ese_imp_marca_fondo || '' !== $ese_imp_marca_logo) : ?>
            <div class="imp-tile imp-tile--logo">
                <?php if ('' !== $ese_imp_marca_fondo) : ?>
                    <img class="imp-tile__bg" src="<?php echo esc_url($ese_imp_marca_fondo); ?>" alt="" loading="lazy" decoding="async">
                <?php endif; ?>
                <?php if ('' !== $ese_imp_marca_logo) : ?>
                    <img class="imp-tile__mark" src="<?php echo esc_url($ese_imp_marca_logo); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" loading="lazy" decoding="async"
                        data-float data-float-distance="6" data-float-duration="4">
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ([] !== $ese_imp_cifras) : ?>
            <div class="imp-stats__col">
                <?php foreach ($ese_imp_cifras as $ese_i => $ese_cifra) : ?>
                    <div class="imp-tile imp-tile--num<?php echo 1 === $ese_i ? ' imp-tile--green' : ''; ?>">
                        <p class="imp-tile__num"><span data-count-to="<?php echo esc_attr($ese_cifra['num']); ?>" data-count-suffix="<?php echo esc_attr($ese_cifra['sufijo']); ?>"><?php echo esc_html($ese_cifra['num'] . $ese_cifra['sufijo']); ?></span></p>
                        <p class="imp-tile__label"><?php echo esc_html($ese_cifra['label']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ('' !== $ese_imp_ppal_titulo || '' !== $ese_imp_ppal_img) : ?>
            <article class="imp-tile imp-tile--main">
                <?php if ('' !== $ese_imp_ppal_img) : ?>
                    <img class="imp-tile__bg" src="<?php echo esc_url($ese_imp_ppal_img); ?>" alt="" loading="lazy" decoding="async">
                    <span class="imp-tile__shade" aria-hidden="true"></span>
                <?php endif; ?>
                <?php if ('' !== $ese_imp_ppal_titulo) : ?>
                    <h3 class="imp-tile__title"><?php echo esc_html($ese_imp_ppal_titulo); ?></h3>
                <?php endif; ?>
                <?php if ('' !== $ese_imp_ppal_desc) : ?>
                    <p class="imp-tile__desc"><?php echo esc_html($ese_imp_ppal_desc); ?></p>
                <?php endif; ?>
            </article>
        <?php endif; ?>

        <?php if ('' !== $ese_imp_foto_img || '' !== $ese_imp_foto_texto) : ?>
            <div class="imp-tile imp-tile--photo">
                <?php if ('' !== $ese_imp_foto_img) : ?>
                    <img class="imp-tile__bg" src="<?php echo esc_url($ese_imp_foto_img); ?>" alt="" loading="lazy" decoding="async">
                    <span class="imp-tile__shade" aria-hidden="true"></span>
                <?php endif; ?>
                <?php if ('' !== $ese_imp_foto_texto) : ?>
                    <p class="imp-tile__caption"><?php echo esc_html($ese_imp_foto_texto); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ('' !== $ese_imp_alc_label) : ?>
            <div class="imp-tile imp-tile--reach">
                <?php if ('' !== $ese_imp_alc_kicker) : ?>
                    <p class="imp-tile__kicker"><?php echo esc_html($ese_imp_alc_kicker); ?></p>
                <?php endif; ?>
                <p class="imp-tile__num"><span data-count-to="<?php echo esc_attr($ese_imp_alc_num); ?>"><?php echo esc_html($ese_imp_alc_num); ?></span></p>
                <p class="imp-tile__label"><?php echo esc_html($ese_imp_alc_label); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>
