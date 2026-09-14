<?php
/**
 * Página Sectores — grilla bento de los 8 sectores (Figma 3510-7023).
 *
 * Los sectores salen de ese_latam_sectores() (inc/template-tags.php): la
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

// Orden de la grilla (índices de ese_latam_sectores) y área de cada uno:
// a/h anchas (2 columnas), b/e altas (2 filas), el resto 1×1. Sigue la
// composición del Figma: municipalidades arriba a lo ancho, recolección
// alta a la derecha, industria alta a la izquierda, doméstico ancha al pie.
$ese_layout = [
    ['i' => 0, 'area' => 'a', 'img' => 'sectores/municipalidades.webp'],
    ['i' => 1, 'area' => 'b', 'img' => 'sectores/recoleccion.webp'],
    ['i' => 2, 'area' => 'c', 'img' => 'nosotros/parque.webp'],
    ['i' => 3, 'area' => 'd', 'img' => 'terreno/en-terreno.webp'],
    ['i' => 7, 'area' => 'e', 'img' => 'sectores/recoleccion.webp'],
    ['i' => 5, 'area' => 'f', 'img' => 'sectores/municipalidades.webp'],
    ['i' => 6, 'area' => 'g', 'img' => 'terreno/en-terreno.webp'],
    ['i' => 4, 'area' => 'h', 'img' => 'nosotros/parque.webp'],
];

// Bajadas del Figma de ESTA página, por índice de ese_latam_sectores().
$ese_bajadas = [
    0 => __('Sistemas de contención para ciudades más limpias y ordenadas.', 'ese-latam'),
    1 => __('Contenedores EN-840 para volteo mecánico, diseñados para uso logístico intensivo.', 'ese-latam'),
    2 => __('Infraestructura de residuos integrada desde el diseño del proyecto.', 'ese-latam'),
    3 => __('Hermeticidad y bioseguridad para entornos de alta exigencia sanitaria.', 'ese-latam'),
    4 => __('Contenedores prácticos y duraderos para separar residuos en casa, ideales para el reciclaje diario.', 'ese-latam'),
    5 => __('Soluciones de alto volumen para flujos continuos de residuos.', 'ese-latam'),
    6 => __('Higiene, orden y cumplimiento normativo en espacios de alto tráfico.', 'ese-latam'),
    7 => __('Contenedores resistentes para entornos de trabajo exigente.', 'ese-latam'),
];

?>

<section class="sec-grid" id="sectores">
    <ul class="sec-grid__list" data-sec-grid>
        <?php foreach ($ese_layout as $ese_item) :
            $ese_sector = $ese_sectores[$ese_item['i']] ?? null;
            if (null === $ese_sector) {
                continue;
            }
            ?>
            <li class="sec-card sec-card--<?php echo esc_attr($ese_item['area']); ?>" data-sec-card>
                <a class="sec-card__link" href="<?php echo esc_url(ese_latam_sector_url($ese_sector)); ?>">
                    <img class="sec-card__img"
                        src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/' . $ese_item['img']); ?>"
                        alt="" loading="lazy" decoding="async" data-sec-card-img>
                    <span class="sec-card__shade" aria-hidden="true"></span>

                    <span class="sec-card__body">
                        <span class="sec-card__title"><?php echo esc_html($ese_sector['title']); ?></span>
                        <span class="sec-card__desc"><?php echo esc_html($ese_bajadas[$ese_item['i']] ?? $ese_sector['desc']); ?></span>
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
