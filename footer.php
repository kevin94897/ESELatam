<?php
/**
 * Footer (Figma node 4898-1919): card gris con columnas de links, newsletter,
 * redes y el logo gigante "ESE" como marca de agua con parallax de scroll.
 *
 * @package EseLatam
 */
declare(strict_types=1);

$ese_footer_cols = [
    [
        'title' => __('Soluciones', 'ese-latam'),
        'links' => [
            ['label' => __('Sectores', 'ese-latam'),        'url' => ese_latam_pagina_url('sectores', home_url('/#sectores'))],
            // Nada de anclas sueltas (#productos): footer.php se comparte en
            // todas las páginas vía get_footer(), así que cada link va a su
            // página real (o a la sección de la home como fallback).
            ['label' => __('Productos', 'ese-latam'),       'url' => get_post_type_archive_link('producto')],
            ['label' => __('Certificaciones', 'ese-latam'), 'url' => ese_latam_pagina_url('certificaciones', home_url('/#certificaciones'))],
            ['label' => __('Impacto', 'ese-latam'),         'url' => ese_latam_pagina_url('impacto', home_url('/#impacto'))],
        ],
    ],
    [
        'title' => __('ESE Latam', 'ese-latam'),
        'links' => [
            ['label' => __('Nosotros', 'ese-latam'),       'url' => home_url('/nosotros/')],
            ['label' => __('Contacto', 'ese-latam'),       'url' => ese_latam_contacto_url()],
            ['label' => __('Blog', 'ese-latam'),           'url' => ese_latam_blog_url()],
            ['label' => __('Casos de éxito', 'ese-latam'), 'url' => ese_latam_casos_url()],
            ['label' => __('Distribuidores', 'ese-latam'), 'url' => ese_latam_distribuidores_url()],
        ],
    ],
    [
        'title' => __('Legal', 'ese-latam'),
        'links' => [
            ['label' => __('Políticas de privacidad', 'ese-latam'), 'url' => ese_latam_pagina_url('politicas-de-privacidad')],
            ['label' => __('Términos y condiciones', 'ese-latam'),  'url' => ese_latam_pagina_url('terminos-y-condiciones')],
        ],
    ],
];
?>
</main>

<footer class="site-footer">
    <div class="footer-card">
        <div class="footer-card__top">
            <div class="footer-brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
                    <img class="footer-brand__logo"
                         src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/logo-ese.png'); ?>"
                         alt="<?php echo esc_attr(get_bloginfo('name')); ?>" width="196" height="58">
                </a>
                <p class="footer-brand__text">
                    <?php esc_html_e('Distribuimos contenedores de residuos sólidos fabricados en Alemania y Francia.', 'ese-latam'); ?>
                    <strong><?php esc_html_e('Calidad europea, transformando la recolección de residuos en Latinoamérica.', 'ese-latam'); ?></strong>
                </p>
            </div>

            <nav class="footer-links" aria-label="<?php esc_attr_e('Enlaces del footer', 'ese-latam'); ?>">
                <?php foreach ($ese_footer_cols as $col) : ?>
                    <div class="footer-links__col">
                        <p class="footer-heading"><?php echo esc_html($col['title']); ?></p>
                        <ul>
                            <?php foreach ($col['links'] as $link) : ?>
                                <li><a href="<?php echo esc_url($link['url']); ?>"><?php echo esc_html($link['label']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </nav>

            <div class="footer-news">
                <div class="footer-news__block">
                    <p class="footer-heading"><?php esc_html_e('Suscríbete al newsletter', 'ese-latam'); ?></p>
                    <form class="footer-news__form" action="#" method="post">
                        <label class="sr-only" for="footer-newsletter-email"><?php esc_html_e('Correo electrónico', 'ese-latam'); ?></label>
                        <input id="footer-newsletter-email" type="email" name="email" required
                               placeholder="<?php esc_attr_e('Correo electrónico', 'ese-latam'); ?>">
                        <button type="submit" aria-label="<?php esc_attr_e('Suscribirse', 'ese-latam'); ?>">
                            <svg width="18" height="15" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/>
                            </svg>
                        </button>
                    </form>
                    <p class="footer-news__note">
                        <?php esc_html_e('Recibe noticias y actualizaciones constantes.', 'ese-latam'); ?>
                    </p>
                </div>

                <?php $ese_redes = ese_latam_redes(); ?>
                <?php if ([] !== $ese_redes) : ?>
                    <div class="footer-news__block">
                        <p class="footer-heading"><?php esc_html_e('Síguenos', 'ese-latam'); ?></p>
                        <div class="footer-social">
                            <?php foreach ($ese_redes as $ese_red) : ?>
                                <a href="<?php echo esc_url($ese_red['url']); ?>" class="footer-social__link"
                                    aria-label="<?php echo esc_attr($ese_red['label']); ?>" target="_blank" rel="noopener">
                                    <?php echo $ese_red['icon']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer-mark" aria-hidden="true">
            <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/footer-ese-mark.svg'); ?>"
                 alt="" width="1620" height="399" loading="lazy" decoding="async"
                 data-parallax data-parallax-from="45" data-parallax-to="-12">
        </div>
    </div>

    <div class="footer-legal">
        <p>© <?php echo esc_html(date('Y')); ?> <?php esc_html_e('ESE LATAM. Todos los derechos reservados.', 'ese-latam'); ?></p>
        <p><?php esc_html_e('Contener para transformar.', 'ese-latam'); ?></p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
