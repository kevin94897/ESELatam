<?php
/**
 * Grilla de productos publicados (Figma node 3618-3354) — buscador +
 * filtro por línea de producto (taxonomía producto_categoria) + grilla de
 * cards + paginación. Va debajo de template-parts/catalogo-banner.php.
 *
 * El filtro "Sector de aplicación" queda deshabilitado por ahora: el CPT
 * producto todavía no tiene una taxonomía de sectores (solo categoría y
 * certificación, ver inc/cpt-productos.php) — falta que el cliente defina
 * esa lista antes de darle campo real.
 *
 * La paginación usa un query var propio (?pagina=N) en vez de /page/N/: es
 * una plantilla de página estática (page-catalogo-de-productos.php), no el
 * listado principal de WordPress, así que el rewrite de paginado nativo no
 * aplica aquí sin trabajo extra.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_catalogo_base_url = strtok($_SERVER['REQUEST_URI'], '?');
$ese_catalogo_s         = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
$ese_catalogo_linea     = isset($_GET['linea']) ? sanitize_title(wp_unslash($_GET['linea'])) : '';
$ese_catalogo_paged     = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;

$ese_catalogo_lineas = get_terms([
    'taxonomy'   => 'producto_categoria',
    'hide_empty' => true,
]);
if (! is_array($ese_catalogo_lineas)) {
    $ese_catalogo_lineas = [];
}

$ese_grid_args = [
    'post_type'      => 'producto',
    'post_status'    => 'publish',
    'posts_per_page' => 8,
    'paged'          => $ese_catalogo_paged,
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
];
if ('' !== $ese_catalogo_s) {
    $ese_grid_args['s'] = $ese_catalogo_s;
}
if ('' !== $ese_catalogo_linea) {
    $ese_grid_args['tax_query'] = [[
        'taxonomy' => 'producto_categoria',
        'field'    => 'slug',
        'terms'    => $ese_catalogo_linea,
    ]];
}

$ese_catalogo_grid_query = new WP_Query($ese_grid_args);

// Arma el link de una página del resultado conservando los filtros activos.
$ese_catalogo_link = static function (int $page) use ($ese_catalogo_base_url, $ese_catalogo_s, $ese_catalogo_linea): string {
    $args = array_filter([
        's'      => $ese_catalogo_s,
        'linea'  => $ese_catalogo_linea,
        'pagina' => $page > 1 ? $page : null,
    ]);
    return esc_url(add_query_arg($args, $ese_catalogo_base_url));
};
?>

<section class="catalogo-productos">
    <form class="catalogo-filters" method="get" action="<?php echo esc_url($ese_catalogo_base_url); ?>">
        <div class="catalogo-filters__field catalogo-filters__field--search">
            <div class="catalogo-filters__control">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M17.7036 16.2895L13.7507 12.3352C14.9359 10.7908 15.4893 8.85338 15.2985 6.91602C15.1077 4.97867 14.1871 3.18641 12.7235 1.90282C11.2598 0.619235 9.36266 -0.0595708 7.41689 0.00410682C5.47112 0.0677844 3.62243 0.869177 2.24582 2.24572C0.869217 3.62226 0.0677876 5.47087 0.00410701 7.41655C-0.0595736 9.36223 0.619263 11.2593 1.90291 12.7229C3.18656 14.1865 4.9789 15.107 6.91634 15.2978C8.85379 15.4886 10.7913 14.9352 12.3357 13.7501L16.2919 17.707C16.3848 17.7999 16.4951 17.8736 16.6165 17.9238C16.7379 17.9741 16.868 18 16.9994 18C17.1308 18 17.2609 17.9741 17.3823 17.9238C17.5037 17.8736 17.614 17.7999 17.7069 17.707C17.7999 17.614 17.8736 17.5038 17.9238 17.3824C17.9741 17.261 18 17.1309 18 16.9995C18 16.8681 17.9741 16.738 17.9238 16.6166C17.8736 16.4952 17.7999 16.3849 17.7069 16.292L17.7036 16.2895ZM2.01446 7.67415C2.01446 6.55475 2.34641 5.46049 2.96835 4.52974C3.59028 3.59899 4.47426 2.87357 5.5085 2.44519C6.54274 2.01681 7.68079 1.90473 8.77873 2.12312C9.87667 2.3415 10.8852 2.88054 11.6768 3.67208C12.4683 4.46361 13.0074 5.47209 13.2258 6.56998C13.4442 7.66787 13.3321 8.80587 12.9037 9.84006C12.4753 10.8742 11.7499 11.7582 10.8191 12.3801C9.88827 13.002 8.79396 13.3339 7.67451 13.3339C6.17384 13.3324 4.73508 12.7356 3.67395 11.6745C2.61282 10.6134 2.016 9.17475 2.01446 7.67415Z" fill="#8A98B6"/>
                </svg>
                <input type="search" id="catalogo-buscador" name="s" value="<?php echo esc_attr($ese_catalogo_s); ?>"
                    placeholder="<?php esc_attr_e('Buscar producto...', 'ese-latam'); ?>">
            </div>
        </div>

        <div class="catalogo-filters__row">
            <div class="catalogo-filters__field">
                <label class="catalogo-filters__label" for="catalogo-linea"><?php esc_html_e('Línea de producto', 'ese-latam'); ?></label>
                <div class="catalogo-filters__control">
                    <select id="catalogo-linea" name="linea" onchange="this.form.submit()">
                        <option value=""><?php esc_html_e('Todos', 'ese-latam'); ?></option>
                        <?php foreach ($ese_catalogo_lineas as $ese_linea_term) : ?>
                            <option value="<?php echo esc_attr($ese_linea_term->slug); ?>" <?php selected($ese_catalogo_linea, $ese_linea_term->slug); ?>>
                                <?php echo esc_html($ese_linea_term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M1 1.5L6 6.5L11 1.5" stroke="#001E61" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>

            <div class="catalogo-filters__field">
                <label class="catalogo-filters__label" for="catalogo-sector"><?php esc_html_e('Sector de aplicación', 'ese-latam'); ?></label>
                <div class="catalogo-filters__control">
                    <select id="catalogo-sector" name="sector" disabled>
                        <option><?php esc_html_e('Todos', 'ese-latam'); ?></option>
                    </select>
                    <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M1 1.5L6 6.5L11 1.5" stroke="#001E61" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </form>

    <div class="catalogo-results">
        <p class="catalogo-results__count">
            <?php
            printf(
                /* translators: %s: número de resultados */
                esc_html__('Mostrando %s resultados', 'ese-latam'),
                '<strong>' . esc_html((string) $ese_catalogo_grid_query->found_posts) . '</strong>'
            );
            ?>
        </p>

        <div class="catalogo-results__right">
            <?php if ('' !== $ese_catalogo_s || '' !== $ese_catalogo_linea) : ?>
                <a class="catalogo-results__reset" href="<?php echo esc_url($ese_catalogo_base_url); ?>">
                    <?php esc_html_e('Restablecer', 'ese-latam'); ?>
                </a>
            <?php endif; ?>

            <div class="catalogo-results__sort">
                <select name="sort_order" class="catalogo-results__sort-select" aria-label="<?php esc_attr_e('Ordenar resultados', 'ese-latam'); ?>">
                    <option value="relevantes"><?php esc_html_e('Más relevantes', 'ese-latam'); ?></option>
                    <option value="recientes"><?php esc_html_e('Más recientes', 'ese-latam'); ?></option>
                </select>
                <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M1 1L5 5L9 1" stroke="#001E61" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>

    <?php if (! $ese_catalogo_grid_query->have_posts()) : ?>
        <p class="catalogo-empty">
            <?php esc_html_e('Aún no hay productos publicados con estos filtros.', 'ese-latam'); ?>
        </p>
    <?php else : ?>
        <div class="catalogo-grid">
            <?php while ($ese_catalogo_grid_query->have_posts()) : $ese_catalogo_grid_query->the_post(); ?>
                <?php
                $ese_categoria_terms = get_the_terms(get_the_ID(), 'producto_categoria');
                $ese_categoria_nombre = is_array($ese_categoria_terms) && ! empty($ese_categoria_terms)
                    ? $ese_categoria_terms[0]->name
                    : __('Producto ESE Latam', 'ese-latam');

                $ese_litrajes = get_field('litrajes');
                $ese_litraje_valor = '—';
                if (is_array($ese_litrajes) && ! empty($ese_litrajes)) {
                    $ese_litraje_valor = $ese_litrajes[0]['valor'];
                    foreach ($ese_litrajes as $ese_litraje_item) {
                        if (! empty($ese_litraje_item['predeterminado'])) {
                            $ese_litraje_valor = $ese_litraje_item['valor'];
                            break;
                        }
                    }
                }

                $ese_caracteristicas = get_field('caracteristicas');
                $ese_material = '—';
                $ese_norma    = '—';
                if (is_array($ese_caracteristicas)) {
                    foreach ($ese_caracteristicas as $ese_caract) {
                        $ese_etiqueta = mb_strtolower($ese_caract['etiqueta'] ?? '');
                        if (false !== strpos($ese_etiqueta, 'material')) {
                            $ese_material = $ese_caract['valor'];
                        } elseif (false !== strpos($ese_etiqueta, 'norma')) {
                            $ese_norma = $ese_caract['valor'];
                        }
                    }
                }

                $ese_card_img = get_the_post_thumbnail_url(get_the_ID(), 'large')
                    ?: (ESE_LATAM_URI . '/assets/imgs/catalogo/contenedor-3-ruedas.png');
                ?>
                <article class="product-card product-card--grid">
                    <div class="product-card__media">
                        <span class="product-card__shadow" aria-hidden="true"></span>
                        <img class="product-card__img" src="<?php echo esc_url($ese_card_img); ?>"
                            alt="<?php the_title_attribute(); ?>" loading="lazy" decoding="async">
                    </div>
                    <div class="product-card__body">
                        <p class="product-card__cat"><?php echo esc_html($ese_categoria_nombre); ?></p>
                        <h3 class="product-card__name"><?php the_title(); ?></h3>
                        <dl class="product-card__specs">
                            <div>
                                <dt><?php esc_html_e('Litraje', 'ese-latam'); ?></dt>
                                <dd><?php echo esc_html($ese_litraje_valor); ?></dd>
                            </div>
                            <div>
                                <dt><?php esc_html_e('Material', 'ese-latam'); ?></dt>
                                <dd><?php echo esc_html($ese_material); ?></dd>
                            </div>
                            <div>
                                <dt><?php esc_html_e('Norma', 'ese-latam'); ?></dt>
                                <dd><?php echo esc_html($ese_norma); ?></dd>
                            </div>
                        </dl>
                    </div>
                    <a class="product-card__chip" href="<?php the_permalink(); ?>"
                        aria-label="<?php echo esc_attr(sprintf(__('Ver %s', 'ese-latam'), get_the_title())); ?>">
                        <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/>
                        </svg>
                    </a>
                </article>
            <?php endwhile; ?>
        </div>

        <?php
        $ese_total_pages = (int) $ese_catalogo_grid_query->max_num_pages;
        if ($ese_total_pages > 1) :
            $ese_window_start = max(1, min($ese_catalogo_paged - 2, $ese_total_pages - 4));
            $ese_window_end   = min($ese_total_pages, $ese_window_start + 4);
            ?>
            <nav class="catalogo-pagination" aria-label="<?php esc_attr_e('Paginación de productos', 'ese-latam'); ?>">
                <a class="catalogo-pagination__side catalogo-pagination__side--prev"
                    href="<?php echo 1 === $ese_catalogo_paged ? '#' : $ese_catalogo_link($ese_catalogo_paged - 1); ?>"
                    aria-disabled="<?php echo 1 === $ese_catalogo_paged ? 'true' : 'false'; ?>">
                    <svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M6 1.41L4.42 0 0 5l4.42 5L6 8.59 3.18 5 6 1.41Z" fill="currentColor"/>
                    </svg>
                    <?php esc_html_e('Anterior', 'ese-latam'); ?>
                </a>

                <div class="catalogo-pagination__pages">
                    <?php for ($ese_page = $ese_window_start; $ese_page <= $ese_window_end; $ese_page++) : ?>
                        <a class="catalogo-pagination__page<?php echo $ese_page === $ese_catalogo_paged ? ' is-active' : ''; ?>"
                            href="<?php echo $ese_catalogo_link($ese_page); ?>">
                            <?php echo esc_html((string) $ese_page); ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <a class="catalogo-pagination__side catalogo-pagination__side--next"
                    href="<?php echo $ese_catalogo_paged === $ese_total_pages ? '#' : $ese_catalogo_link($ese_catalogo_paged + 1); ?>"
                    aria-disabled="<?php echo $ese_catalogo_paged === $ese_total_pages ? 'true' : 'false'; ?>">
                    <?php esc_html_e('Siguiente', 'ese-latam'); ?>
                    <svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M0 8.59 1.58 10 6 5 1.58 0 0 1.41 2.82 5 0 8.59Z" fill="currentColor"/>
                    </svg>
                </a>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
</section>
