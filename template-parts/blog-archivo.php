<?php
/**
 * Listado del blog (Figma 3848-9017): tarjeta de filtros —buscador,
 * categorías y etiquetas—, contador con "restablecer", grilla de tarjetas
 * `.caso--blog` (4 × 2) y paginación.
 *
 * Reusa el bloque `.cx-*` del archivo de Casos de éxito: en el Figma es el
 * mismo componente, solo cambian los rótulos de los dos desplegables.
 *
 * Trabaja sobre la QUERY PRINCIPAL del índice (home.php): los filtros los
 * aplica ese_latam_blog_pre_get_posts() en inc/blog.php, así que acá solo se
 * leen para pintar el formulario. Todo el copy sale de los campos de la
 * página "Blog" (inc/pcf-blog.php).
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

global $wp_query;

$ese_bl_id   = (int) get_option('page_for_posts');
$ese_bl_cmp  = static fn (string $name, $def = '') => (string) ese_latam_campo($name, $ese_bl_id, $def);
$ese_bl_base = ese_latam_blog_url();

$ese_bl_filtros = ese_latam_blog_filtros();

$ese_bl_cats = get_terms(['taxonomy' => 'category', 'hide_empty' => true]);
$ese_bl_tags = get_terms(['taxonomy' => 'post_tag', 'hide_empty' => true]);
$ese_bl_cats = is_array($ese_bl_cats) ? $ese_bl_cats : [];
$ese_bl_tags = is_array($ese_bl_tags) ? $ese_bl_tags : [];

// Tarjetas a pintar: las entradas de la query principal.
$ese_bl_cards = [];
$ese_bl_total = (int) $wp_query->found_posts;
if (have_posts()) {
    while (have_posts()) {
        the_post();
        $ese_bl_cards[] = ese_latam_blog_card_data(get_the_ID());
    }
    wp_reset_postdata();
}

$ese_bl_paginas = (int) $wp_query->max_num_pages;
$ese_bl_actual  = max(1, (int) get_query_var('paged'));

$ese_bl_buscador_label = $ese_bl_cmp('blog_buscador_label');
$ese_bl_cats_label     = $ese_bl_cmp('blog_categorias_label');
$ese_bl_tags_label     = $ese_bl_cmp('blog_etiquetas_label');
$ese_bl_reset          = $ese_bl_cmp('blog_reset');
$ese_bl_conteo_ini     = $ese_bl_cmp('blog_conteo_ini');
$ese_bl_conteo_fin     = $ese_bl_cmp('blog_conteo_fin');
$ese_bl_anterior       = $ese_bl_cmp('blog_pag_anterior');
$ese_bl_siguiente      = $ese_bl_cmp('blog_pag_siguiente');
?>

<section class="cx-archivo" id="articulos">
    <form class="cx-filters" method="get" action="<?php echo esc_url($ese_bl_base); ?>" data-reveal="up">
        <div class="cx-filters__field">
            <?php if ('' !== $ese_bl_buscador_label) : ?>
            <label class="cx-filters__label" for="bl-q"><?php echo esc_html($ese_bl_buscador_label); ?></label>
            <?php endif; ?>
            <div class="cx-filters__control">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M21.5299 20.4694L16.8358 15.7763C18.1963 14.1429 18.8748 12.0478 18.73 9.92694C18.5852 7.80607 17.6283 5.82268 16.0584 4.38935C14.4885 2.95602 12.4264 2.18311 10.3012 2.23141C8.1759 2.27971 6.15108 3.1455 4.64791 4.64867C3.14474 6.15184 2.27895 8.17666 2.23065 10.3019C2.18235 12.4272 2.95526 14.4892 4.38859 16.0591C5.82191 17.629 7.80531 18.5859 9.92618 18.7307C12.047 18.8755 14.1421 18.197 15.7755 16.8366L20.4686 21.5306C20.5383 21.6003 20.621 21.6556 20.7121 21.6933C20.8031 21.731 20.9007 21.7504 20.9992 21.7504C21.0978 21.7504 21.1954 21.731 21.2864 21.6933C21.3775 21.6556 21.4602 21.6003 21.5299 21.5306C21.5996 21.4609 21.6548 21.3782 21.6926 21.2871C21.7303 21.1961 21.7497 21.0985 21.7497 20.9999C21.7497 20.9014 21.7303 20.8038 21.6926 20.7128C21.6548 20.6217 21.5996 20.539 21.5299 20.4694ZM3.74985 10.4999C3.74985 9.16487 4.14573 7.85988 4.88743 6.74983C5.62913 5.63979 6.68331 4.77463 7.91672 4.26375C9.15014 3.75287 10.5073 3.6192 11.8167 3.87966C13.1261 4.14012 14.3288 4.78295 15.2728 5.72694C16.2168 6.67094 16.8596 7.87366 17.1201 9.18306C17.3806 10.4925 17.2469 11.8496 16.736 13.083C16.2251 14.3165 15.36 15.3706 14.2499 16.1123C13.1399 16.854 11.8349 17.2499 10.4999 17.2499C8.71026 17.2479 6.99449 16.5361 5.72906 15.2707C4.46363 14.0053 3.75183 12.2895 3.74985 10.4999Z" fill="#999999"/>
            </svg>
            <input type="search" id="bl-q" name="q" value="<?php echo esc_attr($ese_bl_filtros['q']); ?>"
                placeholder="<?php echo esc_attr($ese_bl_cmp('blog_buscador_placeholder')); ?>">
        </div>
        </div>

        <div class="cx-filters__field">
            <?php if ('' !== $ese_bl_cats_label) : ?>
                <label class="cx-filters__label" for="bl-categoria"><?php echo esc_html($ese_bl_cats_label); ?></label>
            <?php endif; ?>
            <div class="cx-filters__control cx-filters__control--select">
                <select id="bl-categoria" name="categoria" onchange="this.form.submit()">
                    <option value=""><?php echo esc_html($ese_bl_cmp('blog_categorias_todas')); ?></option>
                    <?php foreach ($ese_bl_cats as $ese_bl_term) : ?>
                        <option value="<?php echo esc_attr($ese_bl_term->slug); ?>" <?php selected($ese_bl_filtros['categoria'], $ese_bl_term->slug); ?>>
                            <?php echo esc_html($ese_bl_term->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M20.281 8.28104L12.781 15.781C12.7114 15.8508 12.6287 15.9061 12.5376 15.9438C12.4466 15.9816 12.349 16.001 12.2504 16.001C12.1519 16.001 12.0543 15.9816 11.9632 15.9438C11.8722 15.9061 11.7894 15.8508 11.7198 15.781L4.21979 8.28104C4.07906 8.14031 4 7.94944 4 7.75042C4 7.55139 4.07906 7.36052 4.21979 7.21979C4.36052 7.07906 4.55139 7 4.75042 7C4.94944 7 5.14031 7.07906 5.28104 7.21979L12.2504 14.1901L19.2198 7.21979C19.2895 7.15011 19.3722 7.09483 19.4632 7.05712C19.5543 7.01941 19.6519 7 19.7504 7C19.849 7 19.9465 7.01941 20.0376 7.05712C20.1286 7.09483 20.2114 7.15011 20.281 7.21979C20.3507 7.28947 20.406 7.3722 20.4437 7.46324C20.4814 7.55429 20.5008 7.65187 20.5008 7.75042C20.5008 7.84896 20.4814 7.94654 20.4437 8.03759C20.406 8.12863 20.3507 8.21136 20.281 8.28104Z" fill="currentColor"/>
                </svg>
        </div>
            </div>

        <div class="cx-filters__field">
            <?php if ('' !== $ese_bl_tags_label) : ?>
                <label class="cx-filters__label" for="bl-etiqueta"><?php echo esc_html($ese_bl_tags_label); ?></label>
            <?php endif; ?>
            <div class="cx-filters__control cx-filters__control--select">
                <select id="bl-etiqueta" name="etiqueta" onchange="this.form.submit()">
                    <option value=""><?php echo esc_html($ese_bl_cmp('blog_etiquetas_todas')); ?></option>
                    <?php foreach ($ese_bl_tags as $ese_bl_term) : ?>
                        <option value="<?php echo esc_attr($ese_bl_term->slug); ?>" <?php selected($ese_bl_filtros['etiqueta'], $ese_bl_term->slug); ?>>
                            <?php echo esc_html($ese_bl_term->name); ?>
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

    <?php if ('' !== $ese_bl_conteo_ini || '' !== $ese_bl_conteo_fin || '' !== $ese_bl_reset) : ?>
        <div class="cx-results" data-reveal="fade" data-reveal-delay="0.15">
            <p class="cx-results__count">
                <?php echo esc_html($ese_bl_conteo_ini); ?>
                <strong><?php echo esc_html((string) $ese_bl_total); ?></strong>
                <?php echo esc_html($ese_bl_conteo_fin); ?>
            </p>
            <?php if ('' !== $ese_bl_reset) : ?>
                <a class="cx-results__reset" href="<?php echo esc_url($ese_bl_base); ?>">
                    <?php echo esc_html($ese_bl_reset); ?>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ([] === $ese_bl_cards) : ?>
        <?php $ese_bl_vacio = $ese_bl_cmp('blog_vacio'); ?>
        <?php if ('' !== $ese_bl_vacio) : ?>
            <p class="cx-empty"><?php echo esc_html($ese_bl_vacio); ?></p>
        <?php endif; ?>
    <?php else : ?>
        <ul class="cx-grid" data-reveal-stagger>
            <?php foreach ($ese_bl_cards as $ese_bl_card) : ?>
                <?php
                ese_latam_caso_card($ese_bl_card, [
                    'class'  => 'caso--blog',
                    'cta'    => $ese_bl_cmp('blog_card_cta'),
                    'ciudad' => false,
                ]);
                ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php
    if ($ese_bl_paginas > 1) :
        // Ventana de hasta 5 números alrededor de la página actual.
        $ese_bl_ini = max(1, min($ese_bl_actual - 2, $ese_bl_paginas - 4));
        $ese_bl_fin = min($ese_bl_paginas, $ese_bl_ini + 4);
        ?>
        <nav class="cx-pag" aria-label="<?php esc_attr_e('Paginación de artículos', 'ese-latam'); ?>">
            <?php if ('' !== $ese_bl_anterior) : ?>
                <?php if ($ese_bl_actual > 1) : ?>
                    <a class="cx-pag__btn" href="<?php echo esc_url(get_pagenum_link($ese_bl_actual - 1)); ?>" rel="prev">
                        <?php echo esc_html($ese_bl_anterior); ?>
                    </a>
                <?php else : ?>
                    <span class="cx-pag__btn" aria-disabled="true"><?php echo esc_html($ese_bl_anterior); ?></span>
                <?php endif; ?>
            <?php endif; ?>

            <div class="cx-pag__pages">
                <?php for ($ese_bl_n = $ese_bl_ini; $ese_bl_n <= $ese_bl_fin; $ese_bl_n++) : ?>
                    <?php if ($ese_bl_n === $ese_bl_actual) : ?>
                        <span class="cx-pag__btn is-active" aria-current="page"><?php echo esc_html((string) $ese_bl_n); ?></span>
                    <?php else : ?>
                        <a class="cx-pag__btn" href="<?php echo esc_url(get_pagenum_link($ese_bl_n)); ?>"><?php echo esc_html((string) $ese_bl_n); ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            <?php if ('' !== $ese_bl_siguiente) : ?>
                <?php if ($ese_bl_actual < $ese_bl_paginas) : ?>
                    <a class="cx-pag__btn" href="<?php echo esc_url(get_pagenum_link($ese_bl_actual + 1)); ?>" rel="next">
                        <?php echo esc_html($ese_bl_siguiente); ?>
                    </a>
                <?php else : ?>
                    <span class="cx-pag__btn" aria-disabled="true"><?php echo esc_html($ese_bl_siguiente); ?></span>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</section>
