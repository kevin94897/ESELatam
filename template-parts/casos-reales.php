<?php
/**
 * "Casos reales en gestión urbana" (Figma componente Frame 427320457):
 * cuatro tarjetas de proyecto con foto, chip de categoría y enlace "Ver
 * artículo". Compartida por Solución por sector, Certificaciones e Impacto.
 *
 * Los casos son de ejemplo (el Figma repite "Proyecto Quito" ×4): no hay
 * todavía un CPT de casos ni artículos publicados. Cuando exista, este
 * bloque debería leerlos de ahí — reemplazar $ese_casos por la query.
 *
 * @param array{kicker?: string, title?: string, title_strong?: string, desc?: string, link_label?: string, link_href?: string} $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_cr = wp_parse_args($args ?? [], [
    'kicker'       => __('Casos reales', 'ese-latam'),
    'title'        => __('Casos reales en', 'ese-latam'),
    'title_strong' => __('gestión urbana', 'ese-latam'),
    'desc'         => '',
    'link_label'   => '',
    'link_href'    => '#',
]);

$ese_casos = [
    ['title' => __('Proyecto Quito', 'ese-latam'), 'tag' => __('Gestión urbana', 'ese-latam'), 'img' => 'casos/quito.webp', 'href' => '#'],
    ['title' => __('Proyecto Quito', 'ese-latam'), 'tag' => __('Gestión urbana', 'ese-latam'), 'img' => 'casos/quito.webp', 'href' => '#'],
    ['title' => __('Proyecto Quito', 'ese-latam'), 'tag' => __('Gestión urbana', 'ese-latam'), 'img' => 'casos/quito.webp', 'href' => '#'],
    ['title' => __('Proyecto Quito', 'ese-latam'), 'tag' => __('Gestión urbana', 'ese-latam'), 'img' => 'casos/quito.webp', 'href' => '#'],
];
?>

<section class="casos" id="casos-reales">
    <header class="casos__header" data-reveal-header>
        <div class="casos__heading">
            <p class="type-kicker text-secondary">/ <?php echo esc_html($ese_cr['kicker']); ?></p>
            <h2 class="type-h2 uppercase">
                <?php echo esc_html($ese_cr['title']); ?><br>
                <span class="hl"><?php echo esc_html($ese_cr['title_strong']); ?></span>
            </h2>
            <?php if ('' !== $ese_cr['desc']) : ?>
                <p class="nos-desc" data-reveal-desc><?php echo esc_html($ese_cr['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php if ('' !== $ese_cr['link_label']) : ?>
            <a href="<?php echo esc_url($ese_cr['link_href']); ?>" class="link-arrow" data-reveal="up" data-reveal-delay="0.4">
                <span class="link-arrow__text"><?php echo esc_html($ese_cr['link_label']); ?></span>
                <span class="link-arrow__icon" aria-hidden="true">
                    <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/></svg>
                </span>
            </a>
        <?php endif; ?>
    </header>

    <ul class="casos__grid" data-reveal-stagger>
        <?php foreach ($ese_casos as $ese_caso) : ?>
            <li class="caso">
                <a class="caso__link" href="<?php echo esc_url($ese_caso['href']); ?>">
                    <img class="caso__img" src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/' . $ese_caso['img']); ?>"
                        alt="" loading="lazy" decoding="async">
                    <span class="caso__shade" aria-hidden="true"></span>
                    <span class="caso__tag"><?php echo esc_html($ese_caso['tag']); ?></span>
                    <span class="caso__body">
                        <span class="caso__title"><?php echo esc_html($ese_caso['title']); ?></span>
                        <span class="caso__cta">
                            <span class="caso__cta-text"><?php esc_html_e('Ver artículo', 'ese-latam'); ?></span>
                            <span class="caso__cta-icon" aria-hidden="true">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.9216 7.00884L17.7878 15.0195C17.7836 15.2703 17.6799 15.5125 17.4996 15.6928C17.3193 15.8731 17.0771 15.9768 16.8263 15.981C16.5754 15.9851 16.3366 15.8895 16.1622 15.7151C15.9878 15.5408 15.8922 15.3019 15.8964 15.0511L15.9932 9.32123L7.67907 17.6354C7.49906 17.8154 7.25728 17.9188 7.0069 17.923C6.75652 17.9272 6.51805 17.8318 6.34397 17.6577C6.16988 17.4836 6.07443 17.2451 6.07861 16.9947C6.08279 16.7444 6.18627 16.5026 6.36627 16.3226L14.6804 8.00843L8.95007 8.10251C8.69926 8.1067 8.46038 8.01108 8.28599 7.83669C8.1116 7.66231 8.01598 7.42343 8.02017 7.17261C8.02436 6.9218 8.12802 6.67959 8.30834 6.49928C8.48865 6.31896 8.73086 6.21531 8.98167 6.21111L16.9923 6.07727C17.1166 6.07505 17.2394 6.09741 17.3535 6.14308C17.4675 6.18874 17.5707 6.25681 17.6571 6.34337C17.7434 6.42994 17.8113 6.53328 17.8566 6.64749C17.902 6.76169 17.9241 6.88449 17.9216 7.00884Z" fill="currentColor"/></svg>
                            </span>
                        </span>
                    </span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
