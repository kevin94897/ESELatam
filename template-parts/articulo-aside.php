<?php
/**
 * Barra lateral del artículo (Figma 3873-10142): categoría con su
 * descripción, etiquetas y botones para compartir.
 *
 * La descripción bajo la categoría es la del término (Entradas →
 * Categorías → Descripción); si está vacía, no se pinta. Los tres rótulos
 * salen de los campos de la página "Blog", así que se escriben una vez y
 * valen para todos los artículos: vaciar un rótulo apaga su bloque.
 *
 * @param array{post_id?: int, blog_url?: string, blog_id?: int} $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_aa = wp_parse_args($args ?? [], [
    'post_id'  => 0,
    'blog_url' => '',
    'blog_id'  => 0,
]);

$ese_aa_id   = (int) $ese_aa['post_id'];
$ese_aa_blog = (int) $ese_aa['blog_id'];
if ($ese_aa_id <= 0) {
    return;
}

$ese_aa_cmp = static fn (string $name): string => (string) ese_latam_campo($name, $ese_aa_blog, '');

$ese_aa_cats = get_the_category($ese_aa_id);
$ese_aa_cat  = is_array($ese_aa_cats) && ! empty($ese_aa_cats) ? $ese_aa_cats[0] : null;

$ese_aa_tags = get_the_tags($ese_aa_id);
$ese_aa_tags = is_array($ese_aa_tags) ? $ese_aa_tags : [];

$ese_aa_cat_label   = $ese_aa_cmp('blog_art_categoria_label');
$ese_aa_tags_label  = $ese_aa_cmp('blog_art_etiquetas_label');
$ese_aa_share_label = $ese_aa_cmp('blog_art_compartir_label');
$ese_aa_copiado     = $ese_aa_cmp('blog_art_copiado');

$ese_aa_url    = (string) get_permalink($ese_aa_id);
$ese_aa_titulo = (string) get_the_title($ese_aa_id);

$ese_aa_hay_cat   = null !== $ese_aa_cat && '' !== $ese_aa_cat_label;
$ese_aa_hay_tags  = [] !== $ese_aa_tags && '' !== $ese_aa_tags_label;
$ese_aa_hay_share = '' !== $ese_aa_share_label;

if (! $ese_aa_hay_cat && ! $ese_aa_hay_tags && ! $ese_aa_hay_share) {
    return;
}

// Redes donde compartir: cada una arma su propia URL con la del artículo.
$ese_aa_redes = [
    [
        'label' => __('Compartir en LinkedIn', 'ese-latam'),
        'url'   => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($ese_aa_url),
        'icon'  => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1.92299 0C1.67046 0 1.4204 0.0498244 1.18709 0.146628C0.953785 0.243432 0.741796 0.38532 0.56323 0.564191C0.384664 0.743061 0.243018 0.955411 0.146379 1.18912C0.0497396 1.42282 0 1.67331 0 1.92627C0 2.17923 0.0497396 2.42971 0.146379 2.66342C0.243018 2.89712 0.384664 3.10947 0.56323 3.28835C0.741796 3.46722 0.953785 3.6091 1.18709 3.70591C1.4204 3.80271 1.67046 3.85254 1.92299 3.85254C2.17552 3.85254 2.42558 3.80271 2.65888 3.70591C2.89219 3.6091 3.10418 3.46722 3.28275 3.28835C3.46131 3.10947 3.60296 2.89712 3.6996 2.66342C3.79624 2.42971 3.84598 2.17923 3.84598 1.92627C3.84598 1.67331 3.79624 1.42282 3.6996 1.18912C3.60296 0.955411 3.46131 0.743061 3.28275 0.564191C3.10418 0.38532 2.89219 0.243432 2.65888 0.146628C2.42558 0.0498244 2.17552 0 1.92299 0ZM5.66174 5.3122V15.9991H8.97424V10.7142C8.97424 9.31969 9.23614 7.96919 10.9623 7.96919C12.6647 7.96919 12.6857 9.56355 12.6857 10.8022V16H16V10.1393C16 7.26048 15.3813 5.04809 12.0222 5.04809C10.4094 5.04809 9.32843 5.93463 8.88635 6.77363H8.84153V5.3122H5.66174ZM0.263664 5.3122H3.58143V15.9991H0.263664V5.3122Z" fill="currentColor"/></svg>',
    ],
    [
        'label' => __('Compartir en Facebook', 'ese-latam'),
        'url'   => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($ese_aa_url),
        'icon'  => '<svg width="14" height="20" viewBox="0 0 14 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M10.0435 0C6.56052 0 3.65217 2.5827 3.65217 5.67568V7.56757H0.304348C0.22363 7.56757 0.146217 7.59604 0.0891412 7.64673C0.0320649 7.69741 0 7.76616 0 7.83784V12.1622C0 12.3114 0.136348 12.4324 0.304348 12.4324H3.65217V19.7297C3.65217 19.8789 3.78852 20 3.95652 20H8.82609C8.90681 20 8.98422 19.9715 9.04129 19.9208C9.09837 19.8702 9.13043 19.8014 9.13043 19.7297V12.4324H12.4783C12.5461 12.4323 12.612 12.412 12.6654 12.3747C12.7187 12.3375 12.7566 12.2855 12.7729 12.227L13.9903 7.9027C14.0013 7.86295 14.0021 7.82148 13.9925 7.78144C13.9828 7.74139 13.9631 7.7038 13.9347 7.67153C13.9063 7.63925 13.8701 7.61312 13.8287 7.59511C13.7872 7.5771 13.7418 7.56768 13.6957 7.56757H9.13043V5.67568C9.13359 5.46151 9.2308 5.2569 9.40135 5.10544C9.5719 4.95399 9.8023 4.86766 10.0435 4.86486H13.6957C13.7764 4.86486 13.8538 4.83639 13.9109 4.7857C13.9679 4.73502 14 4.66627 14 4.59459V0.27027C14 0.19859 13.9679 0.129846 13.9109 0.0791604C13.8538 0.0284749 13.7764 0 13.6957 0H10.0435Z" fill="currentColor"/></svg>',
    ],
    [
        'label' => __('Compartir por WhatsApp', 'ese-latam'),
        'url'   => 'https://wa.me/?text=' . rawurlencode($ese_aa_titulo . ' ' . $ese_aa_url),
        'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm0 18.15h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.11.82.83-3.04-.2-.31a8.17 8.17 0 0 1-1.25-4.38c0-4.54 3.7-8.23 8.24-8.23 2.2 0 4.27.86 5.82 2.42a8.17 8.17 0 0 1 2.41 5.82c0 4.54-3.7 8.23-8.25 8.23Zm4.52-6.16c-.25-.13-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.24-.64.8-.78.97-.15.16-.29.18-.54.06-.25-.13-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.43.13-.15.17-.25.25-.41.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.87.86-.87 2.09s.9 2.43 1.02 2.6c.12.16 1.76 2.69 4.27 3.77.6.26 1.06.41 1.42.53.6.19 1.14.16 1.57.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.14-1.18-.06-.11-.22-.17-.47-.29Z"/></svg>',
    ],
];
?>

<aside class="art-aside">
    <?php if ($ese_aa_hay_cat) : ?>
        <div class="art-aside__bloque">
            <p class="art-aside__label"><?php echo esc_html($ese_aa_cat_label); ?></p>
            <a class="art-aside__cat" href="<?php echo esc_url(add_query_arg('categoria', $ese_aa_cat->slug, $ese_aa['blog_url'])); ?>">
                <span class="art-aside__punto" aria-hidden="true"></span>
                <?php echo esc_html($ese_aa_cat->name); ?>
            </a>
            <?php $ese_aa_cat_desc = trim((string) term_description($ese_aa_cat->term_id, 'category')); ?>
            <?php if ('' !== $ese_aa_cat_desc) : ?>
                <div class="art-aside__cat-desc"><?php echo wp_kses_post($ese_aa_cat_desc); ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($ese_aa_hay_tags) : ?>
        <div class="art-aside__bloque">
            <p class="art-aside__label"><?php echo esc_html($ese_aa_tags_label); ?></p>
            <ul class="art-aside__tags">
                <?php foreach ($ese_aa_tags as $ese_aa_tag) : ?>
                    <li>
                        <a class="art-aside__tag" href="<?php echo esc_url(add_query_arg('etiqueta', $ese_aa_tag->slug, $ese_aa['blog_url'])); ?>">
                            <?php echo esc_html($ese_aa_tag->name); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($ese_aa_hay_share) : ?>
        <?php // data-art-share envuelve a los botones Y al aviso: articulo-share.ts
        // busca el aviso dentro de este bloque. ?>
        <div class="art-aside__bloque" data-art-share>
            <p class="art-aside__label"><?php echo esc_html($ese_aa_share_label); ?></p>
            <div class="art-aside__share">
                <?php foreach ($ese_aa_redes as $ese_aa_red) : ?>
                    <a class="art-aside__red" href="<?php echo esc_url($ese_aa_red['url']); ?>"
                       target="_blank" rel="noopener" aria-label="<?php echo esc_attr($ese_aa_red['label']); ?>">
                        <?php echo $ese_aa_red['icon']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </a>
                <?php endforeach; ?>
                <button type="button" class="art-aside__red" data-art-copy="<?php echo esc_url($ese_aa_url); ?>"
                        aria-label="<?php esc_attr_e('Copiar el enlace del artículo', 'ese-latam'); ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M9 9V5.25A2.25 2.25 0 0 1 11.25 3h7.5A2.25 2.25 0 0 1 21 5.25v7.5A2.25 2.25 0 0 1 18.75 15H15M5.25 9h7.5A2.25 2.25 0 0 1 15 11.25v7.5A2.25 2.25 0 0 1 12.75 21h-7.5A2.25 2.25 0 0 1 3 18.75v-7.5A2.25 2.25 0 0 1 5.25 9Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <?php if ('' !== $ese_aa_copiado) : ?>
                <p class="art-aside__copiado" data-art-copiado hidden><?php echo esc_html($ese_aa_copiado); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</aside>
