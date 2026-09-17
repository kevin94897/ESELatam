<?php
/**
 * Cuerpo del blog de Casos de éxito (Figma 3891-3719): tarjeta de filtros
 * (buscador + sector + ciudad), línea de resultados, grilla de tarjetas
 * `.caso--blog` (4 × 2) y paginación.
 *
 * Trabaja sobre la QUERY PRINCIPAL del archivo (archive-caso.php): los
 * filtros los aplica ese_latam_casos_pre_get_posts() en inc/cpt-casos.php,
 * así que acá solo se leen para pintar el formulario. Sin casos publicados
 * —o sin resultados para los filtros— se muestra el aviso de lista vacía.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

global $wp_query;

$ese_cx_base    = ese_latam_casos_url();
$ese_cx_filtros = ese_latam_casos_filtros();
$ese_cx_activos = '' !== $ese_cx_filtros['q'] || '' !== $ese_cx_filtros['sector'] || '' !== $ese_cx_filtros['ciudad'];

$ese_cx_sectores = get_terms(['taxonomy' => 'caso_sector', 'hide_empty' => false]);
$ese_cx_ciudades = get_terms(['taxonomy' => 'caso_ciudad', 'hide_empty' => true]);
$ese_cx_sectores = is_array($ese_cx_sectores) ? $ese_cx_sectores : [];
$ese_cx_ciudades = is_array($ese_cx_ciudades) ? $ese_cx_ciudades : [];

// Tarjetas a pintar: los posts de la query principal.
$ese_cx_cards = [];
$ese_cx_total = (int) $wp_query->found_posts;
if (have_posts()) {
    while (have_posts()) {
        the_post();
        $ese_cx_cards[] = ese_latam_caso_card_data(get_the_ID());
    }
    wp_reset_postdata();
}

$ese_cx_paginas = (int) $wp_query->max_num_pages;
$ese_cx_actual  = max(1, (int) get_query_var('paged'));
?>

<section class="cx-archivo" id="casos">
    <form class="cx-filters" method="get" action="<?php echo esc_url($ese_cx_base); ?>" data-reveal="up">
        <div class="cx-filters__field">
            <label class="cx-filters__label" for="cx-q"><?php esc_html_e('Buscador', 'ese-latam'); ?></label>
            <div class="cx-filters__control">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M21.5299 20.4694L16.8358 15.7763C18.1963 14.1429 18.8748 12.0478 18.73 9.92694C18.5852 7.80607 17.6283 5.82268 16.0584 4.38935C14.4885 2.95602 12.4264 2.18311 10.3012 2.23141C8.1759 2.27971 6.15108 3.1455 4.64791 4.64867C3.14474 6.15184 2.27895 8.17666 2.23065 10.3019C2.18235 12.4272 2.95526 14.4892 4.38859 16.0591C5.82191 17.629 7.80531 18.5859 9.92618 18.7307C12.047 18.8755 14.1421 18.197 15.7755 16.8366L20.4686 21.5306C20.5383 21.6003 20.621 21.6556 20.7121 21.6933C20.8031 21.731 20.9007 21.7504 20.9992 21.7504C21.0978 21.7504 21.1954 21.731 21.2864 21.6933C21.3775 21.6556 21.4602 21.6003 21.5299 21.5306C21.5996 21.4609 21.6548 21.3782 21.6926 21.2871C21.7303 21.1961 21.7497 21.0985 21.7497 20.9999C21.7497 20.9014 21.7303 20.8038 21.6926 20.7128C21.6548 20.6217 21.5996 20.539 21.5299 20.4694ZM3.74985 10.4999C3.74985 9.16487 4.14573 7.85988 4.88743 6.74983C5.62913 5.63979 6.68331 4.77463 7.91672 4.26375C9.15014 3.75287 10.5073 3.6192 11.8167 3.87966C13.1261 4.14012 14.3288 4.78295 15.2728 5.72694C16.2168 6.67094 16.8596 7.87366 17.1201 9.18306C17.3806 10.4925 17.2469 11.8496 16.736 13.083C16.2251 14.3165 15.36 15.3706 14.2499 16.1123C13.1399 16.854 11.8349 17.2499 10.4999 17.2499C8.71026 17.2479 6.99449 16.5361 5.72906 15.2707C4.46363 14.0053 3.75183 12.2895 3.74985 10.4999Z" fill="#999999"/>
                </svg>
                <input type="search" id="cx-q" name="q" value="<?php echo esc_attr($ese_cx_filtros['q']); ?>"
                    placeholder="<?php esc_attr_e('Busca artículos, tags o palabras clave', 'ese-latam'); ?>">
            </div>
        </div>

        <div class="cx-filters__field">
            <label class="cx-filters__label" for="cx-sector"><?php esc_html_e('Sector', 'ese-latam'); ?></label>
            <div class="cx-filters__control cx-filters__control--select">
                <select id="cx-sector" name="rubro" onchange="this.form.submit()">
                    <option value=""><?php esc_html_e('Todos los sectores', 'ese-latam'); ?></option>
                    <?php foreach ($ese_cx_sectores as $ese_cx_term) : ?>
                        <option value="<?php echo esc_attr($ese_cx_term->slug); ?>" <?php selected($ese_cx_filtros['sector'], $ese_cx_term->slug); ?>>
                            <?php echo esc_html($ese_cx_term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M20.281 8.28104L12.781 15.781C12.7114 15.8508 12.6287 15.9061 12.5376 15.9438C12.4466 15.9816 12.349 16.001 12.2504 16.001C12.1519 16.001 12.0543 15.9816 11.9632 15.9438C11.8722 15.9061 11.7894 15.8508 11.7198 15.781L4.21979 8.28104C4.07906 8.14031 4 7.94944 4 7.75042C4 7.55139 4.07906 7.36052 4.21979 7.21979C4.36052 7.07906 4.55139 7 4.75042 7C4.94944 7 5.14031 7.07906 5.28104 7.21979L12.2504 14.1901L19.2198 7.21979C19.2895 7.15011 19.3722 7.09483 19.4632 7.05712C19.5543 7.01941 19.6519 7 19.7504 7C19.849 7 19.9465 7.01941 20.0376 7.05712C20.1286 7.09483 20.2114 7.15011 20.281 7.21979C20.3507 7.28947 20.406 7.3722 20.4437 7.46324C20.4814 7.55429 20.5008 7.65187 20.5008 7.75042C20.5008 7.84896 20.4814 7.94654 20.4437 8.03759C20.406 8.12863 20.3507 8.21136 20.281 8.28104Z" fill="currentColor"/>
                </svg>
            </div>
        </div>

        <div class="cx-filters__field">
            <label class="cx-filters__label" for="cx-ciudad"><?php esc_html_e('Filtrar por ciudad', 'ese-latam'); ?></label>
            <div class="cx-filters__control cx-filters__control--select">
                <select id="cx-ciudad" name="ciudad" onchange="this.form.submit()">
                    <option value=""><?php esc_html_e('Todas las ciudades', 'ese-latam'); ?></option>
                    <?php foreach ($ese_cx_ciudades as $ese_cx_term) : ?>
                        <option value="<?php echo esc_attr($ese_cx_term->slug); ?>" <?php selected($ese_cx_filtros['ciudad'], $ese_cx_term->slug); ?>>
                            <?php echo esc_html($ese_cx_term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M20.281 8.28104L12.781 15.781C12.7114 15.8508 12.6287 15.9061 12.5376 15.9438C12.4466 15.9816 12.349 16.001 12.2504 16.001C12.1519 16.001 12.0543 15.9816 11.9632 15.9438C11.8722 15.9061 11.7894 15.8508 11.7198 15.781L4.21979 8.28104C4.07906 8.14031 4 7.94944 4 7.75042C4 7.55139 4.07906 7.36052 4.21979 7.21979C4.36052 7.07906 4.55139 7 4.75042 7C4.94944 7 5.14031 7.07906 5.28104 7.21979L12.2504 14.1901L19.2198 7.21979C19.2895 7.15011 19.3722 7.09483 19.4632 7.05712C19.5543 7.01941 19.6519 7 19.7504 7C19.849 7 19.9465 7.01941 20.0376 7.05712C20.1286 7.09483 20.2114 7.15011 20.281 7.21979C20.3507 7.28947 20.406 7.3722 20.4437 7.46324C20.4814 7.55429 20.5008 7.65187 20.5008 7.75042C20.5008 7.84896 20.4814 7.94654 20.4437 8.03759C20.406 8.12863 20.3507 8.21136 20.281 8.28104Z" fill="currentColor"/>
                </svg>
            </div>
        </div>

        <?php // Sin JS los selects no auto-envían: botón accesible por teclado, oculto visualmente. ?>
        <button type="submit" class="sr-only"><?php esc_html_e('Filtrar', 'ese-latam'); ?></button>
    </form>

    <div class="cx-results" data-reveal="fade" data-reveal-delay="0.15">
        <p class="cx-results__count">
            <?php
            printf(
                /* translators: %s: número de artículos */
                esc_html__('Mostrando %s artículos', 'ese-latam'),
                '<strong>' . esc_html((string) $ese_cx_total) . '</strong>'
            );
            ?>
        </p>
        <a class="cx-results__reset" href="<?php echo esc_url($ese_cx_base); ?>">
            <?php esc_html_e('Restablecer filtros', 'ese-latam'); ?>
        </a>
    </div>

    <?php if (empty($ese_cx_cards)) : ?>
        <p class="cx-empty">
            <?php esc_html_e('No encontramos casos con estos filtros. Prueba con otra búsqueda o restablece los filtros.', 'ese-latam'); ?>
        </p>
    <?php else : ?>
        <ul class="cx-grid" data-reveal-stagger>
            <?php foreach ($ese_cx_cards as $ese_cx_card) : ?>
                <?php
                ese_latam_caso_card($ese_cx_card, [
                    'class'  => 'caso--blog',
                    'cta'    => __('Ver caso', 'ese-latam'),
                    'ciudad' => true,
                ]);
                ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php
    if ($ese_cx_paginas > 1) :
        // Ventana de hasta 5 números alrededor de la página actual.
        $ese_cx_ini = max(1, min($ese_cx_actual - 2, $ese_cx_paginas - 4));
        $ese_cx_fin = min($ese_cx_paginas, $ese_cx_ini + 4);
        ?>
        <nav class="cx-pag" aria-label="<?php esc_attr_e('Paginación de casos', 'ese-latam'); ?>">
            <?php if ($ese_cx_actual > 1) : ?>
                <a class="cx-pag__btn" href="<?php echo esc_url(get_pagenum_link($ese_cx_actual - 1)); ?>" rel="prev">
                    <?php esc_html_e('Anterior', 'ese-latam'); ?>
                </a>
            <?php else : ?>
                <span class="cx-pag__btn" aria-disabled="true"><?php esc_html_e('Anterior', 'ese-latam'); ?></span>
            <?php endif; ?>

            <div class="cx-pag__pages">
                <?php for ($ese_cx_n = $ese_cx_ini; $ese_cx_n <= $ese_cx_fin; $ese_cx_n++) : ?>
                    <?php if ($ese_cx_n === $ese_cx_actual) : ?>
                        <span class="cx-pag__btn is-active" aria-current="page"><?php echo esc_html((string) $ese_cx_n); ?></span>
                    <?php else : ?>
                        <a class="cx-pag__btn" href="<?php echo esc_url(get_pagenum_link($ese_cx_n)); ?>"><?php echo esc_html((string) $ese_cx_n); ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            <?php if ($ese_cx_actual < $ese_cx_paginas) : ?>
                <a class="cx-pag__btn" href="<?php echo esc_url(get_pagenum_link($ese_cx_actual + 1)); ?>" rel="next">
                    <?php esc_html_e('Siguiente', 'ese-latam'); ?>
                </a>
            <?php else : ?>
                <span class="cx-pag__btn" aria-disabled="true"><?php esc_html_e('Siguiente', 'ese-latam'); ?></span>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</section>
