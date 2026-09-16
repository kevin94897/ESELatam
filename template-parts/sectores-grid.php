<?php
/**
 * Página Sectores — grilla bento de los 8 sectores (Figma 3510-7023).
 *
 * Los sectores salen del módulo "Sectores" vía ese_latam_sectores(): la
 * misma lista que usa el slider de la home y el submenú del header, así
 * los tres no pueden divergir. Esta página solo aporta lo suyo:
 *   · la bajada de cada tarjeta (copy propio del Figma de esta página,
 *     más largo que el del slider), por índice del sector;
 *   · el tamaño de cada tarjeta en la grilla (`area`, ver .sec-grid en
 *     main.css: dos anchas, dos altas, cuatro normales);
 *   · una foto distinta por tarjeta rotando las que ya tiene el theme (el
 *     Figma repite un mismo placeholder en las ocho).
 *
 * Las tarjetas enlazan al catálogo: todavía no hay página por sector.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_sectores = ese_latam_sectores();

if ([] === $ese_sectores) {
    return;
}

// Composición bento del Figma: a/h anchas (2 columnas), b/e altas (2 filas),
// el resto 1×1 (ver grid-template-areas en main.css). Las áreas se reparten
// en el orden del módulo "Sectores", así que el orden de las tarjetas se
// cambia desde el campo "Orden" de cada entrada, sin tocar la plantilla.
// A partir de la novena, las tarjetas caen en filas nuevas por flujo normal.
$ese_areas = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];

?>

<section class="sec-grid" id="sectores">
    <ul class="sec-grid__list" data-sec-grid>
        <?php foreach ($ese_sectores as $ese_i => $ese_sector) :
            $ese_area = $ese_areas[$ese_i] ?? '';
            ?>
            <li class="sec-card<?php echo '' !== $ese_area ? ' sec-card--' . esc_attr($ese_area) : ''; ?>" data-sec-card>
                <a class="sec-card__link" href="<?php echo esc_url(ese_latam_sector_url($ese_sector)); ?>">
                    <?php if ('' !== $ese_sector['grid_img']) : ?>
                        <img class="sec-card__img" src="<?php echo esc_url($ese_sector['grid_img']); ?>"
                            alt="" loading="lazy" decoding="async" data-sec-card-img>
                    <?php endif; ?>
                    <span class="sec-card__shade" aria-hidden="true"></span>

                    <span class="sec-card__body">
                        <span class="sec-card__title"><?php echo esc_html($ese_sector['title']); ?></span>
                        <?php if ('' !== $ese_sector['grid_desc']) : ?>
                            <span class="sec-card__desc"><?php echo esc_html($ese_sector['grid_desc']); ?></span>
                        <?php endif; ?>
                    </span>

                    <span class="sec-card__arrow" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.9216 7.00884L17.7878 15.0195C17.7836 15.2703 17.6799 15.5125 17.4996 15.6928C17.3193 15.8731 17.0771 15.9768 16.8263 15.981C16.5754 15.9851 16.3366 15.8895 16.1622 15.7151C15.9878 15.5408 15.8922 15.3019 15.8964 15.0511L15.9932 9.32123L7.67907 17.6354C7.49906 17.8154 7.25728 17.9188 7.0069 17.923C6.75652 17.9272 6.51805 17.8318 6.34397 17.6577C6.16988 17.4836 6.07443 17.2451 6.07861 16.9947C6.08279 16.7444 6.18627 16.5026 6.36627 16.3226L14.6804 8.00843L8.95007 8.10251C8.69926 8.1067 8.46038 8.01108 8.28599 7.83669C8.1116 7.66231 8.01598 7.42343 8.02017 7.17261C8.02436 6.9218 8.12802 6.67959 8.30834 6.49928C8.48865 6.31896 8.73086 6.21531 8.98167 6.21111L16.9923 6.07727C17.1166 6.07505 17.2394 6.09741 17.3535 6.14308C17.4675 6.18874 17.5707 6.25681 17.6571 6.34337C17.7434 6.42994 17.8113 6.53328 17.8566 6.64749C17.902 6.76169 17.9241 6.88449 17.9216 7.00884Z" fill="currentColor"/>
                        </svg>
                    </span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
