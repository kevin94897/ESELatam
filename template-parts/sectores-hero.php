<?php
/**
 * Página Sectores — hero claro (Figma 3510-6983): breadcrumb + titular
 * centrado "Soluciones por SECTOR" + bajada. El breadcrumb reutiliza
 * template-parts/breadcrumbs.php, que ya tiene el mismo look del Figma.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}
?>

<section class="sec-hero">
    <?php
    get_template_part('template-parts/breadcrumbs', null, [
        'current' => __('Soluciones', 'ese-latam'),
        'class'   => 'sec-hero__crumb',
        'attrs'   => 'data-reveal="fade"',
    ]);
    ?>

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
