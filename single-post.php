<?php
/**
 * Artículo del blog (Figma 3873-10142, "10 – Blog: Proyecto Quito").
 *
 * Se llama single-post.php y no single.php a propósito: single.php también
 * atraparía a `caso`, que tiene su propio diseño pendiente.
 *
 * Composición, en orden del Figma:
 *   1. Hero con la foto destacada (ficha-hero.php, compartido con el caso)
 *   2. Cuerpo del editor + barra lateral (articulo-aside.php)
 *   3. Enlace de vuelta al blog
 *   4. Contactemos → template-parts/contacto.php
 *
 * El contenido del artículo es el editor de la entrada: titulares, párrafos,
 * citas e imágenes con pie se escriben ahí y los estiliza `.art-cuerpo` en
 * main.css. Los rótulos que se repiten en todos los artículos se editan una
 * sola vez en la página "Blog" (inc/pcf-blog.php).
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();

$ese_blog_id  = (int) get_option('page_for_posts');
$ese_blog_url = ese_latam_blog_url();
$ese_blog_lbl = $ese_blog_id > 0 ? (string) get_the_title($ese_blog_id) : '';
$ese_volver   = (string) ese_latam_campo('blog_art_volver', $ese_blog_id, '');
?>

<div class="nosotros nosotros--articulo" data-nosotros>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>

    <?php
    while (have_posts()) :
        the_post();
        $ese_post_id  = (int) get_the_ID();
        $ese_entradilla = trim((string) ese_latam_campo('articulo_entradilla', $ese_post_id, ''));

        // El hero es el mismo que el del caso de éxito (ficha-hero.php), así
        // que recibe los datos resueltos: acá la categoría, el lugar y el año.
        $ese_post      = get_post($ese_post_id);
        $ese_cats      = get_the_category($ese_post_id);
        $ese_cat       = is_array($ese_cats) && ! empty($ese_cats) ? $ese_cats[0] : null;
        $ese_anio      = ese_latam_campo('articulo_anio', $ese_post_id, null);
        $ese_anio      = null === $ese_anio || '' === $ese_anio
            ? (string) get_the_date('Y', $ese_post_id)
            : (string) (int) $ese_anio;

        get_template_part('template-parts/ficha-hero', null, [
            'titulo'      => (string) get_the_title($ese_post_id),
            // El extracto ESCRITO a mano, no get_the_excerpt(): ese se lo
            // inventa WordPress recortando el cuerpo y la bajada del hero
            // repetiría las primeras líneas del artículo.
            'desc'        => $ese_post instanceof WP_Post ? (string) $ese_post->post_excerpt : '',
            'foto'        => (string) (get_the_post_thumbnail_url($ese_post_id, 'full') ?: ''),
            'chip'        => null !== $ese_cat ? $ese_cat->name : '',
            'chip_url'    => null !== $ese_cat ? add_query_arg('categoria', $ese_cat->slug, $ese_blog_url) : '',
            'datos'       => [(string) ese_latam_campo('articulo_lugar', $ese_post_id, ''), $ese_anio],
            'crumb_label' => $ese_blog_lbl,
            'crumb_url'   => $ese_blog_url,
        ]);
        ?>

        <section class="art-layout">
            <article class="art-cuerpo">
                <?php if ('' !== $ese_entradilla) : ?>
                    <p class="art-cuerpo__entradilla"><?php echo esc_html($ese_entradilla); ?></p>
                <?php endif; ?>
                <?php the_content(); ?>
            </article>

            <?php
            get_template_part('template-parts/articulo-aside', null, [
                'post_id'  => $ese_post_id,
                'blog_url' => $ese_blog_url,
                'blog_id'  => $ese_blog_id,
            ]);
            ?>

            <?php if ('' !== $ese_volver) : ?>
                <div class="art-volver">
                    <a class="link-arrow link-arrow--back" href="<?php echo esc_url($ese_blog_url); ?>">
                        <span class="link-arrow__icon" aria-hidden="true">
                            <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/></svg>
                        </span>
                        <span class="link-arrow__text"><?php echo esc_html($ese_volver); ?></span>
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <?php
    endwhile;

    get_template_part('template-parts/contacto', null, [
        'class' => 'contacto--upper',
    ]);
    ?>
</div>

<?php
get_footer();
