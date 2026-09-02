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
            ['label' => __('Sectores', 'ese-latam'),        'url' => '#sectores'],
            ['label' => __('Productos', 'ese-latam'),       'url' => '#productos'],
            ['label' => __('Certificaciones', 'ese-latam'), 'url' => '#certificaciones'],
            ['label' => __('Impacto', 'ese-latam'),         'url' => '#impacto'],
        ],
    ],
    [
        'title' => __('ESE Latam', 'ese-latam'),
        'links' => [
            ['label' => __('Nosotros', 'ese-latam'),       'url' => '#'],
            ['label' => __('Blog', 'ese-latam'),           'url' => '#'],
            ['label' => __('Casos de éxito', 'ese-latam'), 'url' => '#'],
            ['label' => __('Distribuidores', 'ese-latam'), 'url' => '#'],
        ],
    ],
    [
        'title' => __('Legal', 'ese-latam'),
        'links' => [
            ['label' => __('Políticas de privacidad', 'ese-latam'), 'url' => '#'],
            ['label' => __('Términos y condiciones', 'ese-latam'),  'url' => '#'],
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

                <div class="footer-news__block">
                    <p class="footer-heading"><?php esc_html_e('Síguenos', 'ese-latam'); ?></p>
                    <div class="footer-social">
                        <a href="#" class="footer-social__link" aria-label="LinkedIn">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M1.92299 0C1.67046 0 1.4204 0.0498244 1.18709 0.146628C0.953785 0.243432 0.741796 0.38532 0.56323 0.564191C0.384664 0.743061 0.243018 0.955411 0.146379 1.18912C0.0497396 1.42282 0 1.67331 0 1.92627C0 2.17923 0.0497396 2.42971 0.146379 2.66342C0.243018 2.89712 0.384664 3.10947 0.56323 3.28835C0.741796 3.46722 0.953785 3.6091 1.18709 3.70591C1.4204 3.80271 1.67046 3.85254 1.92299 3.85254C2.17552 3.85254 2.42558 3.80271 2.65888 3.70591C2.89219 3.6091 3.10418 3.46722 3.28275 3.28835C3.46131 3.10947 3.60296 2.89712 3.6996 2.66342C3.79624 2.42971 3.84598 2.17923 3.84598 1.92627C3.84598 1.67331 3.79624 1.42282 3.6996 1.18912C3.60296 0.955411 3.46131 0.743061 3.28275 0.564191C3.10418 0.38532 2.89219 0.243432 2.65888 0.146628C2.42558 0.0498244 2.17552 0 1.92299 0ZM5.66174 5.3122V15.9991H8.97424V10.7142C8.97424 9.31969 9.23614 7.96919 10.9623 7.96919C12.6647 7.96919 12.6857 9.56355 12.6857 10.8022V16H16V10.1393C16 7.26048 15.3813 5.04809 12.0222 5.04809C10.4094 5.04809 9.32843 5.93463 8.88635 6.77363H8.84153V5.3122H5.66174ZM0.263664 5.3122H3.58143V15.9991H0.263664V5.3122Z" fill="currentColor"/>
                            </svg>
                        </a>
                        <a href="#" class="footer-social__link" aria-label="Instagram">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M11.028 0C12.153 0.00299993 12.7239 0.00899978 13.2169 0.0229994L13.4109 0.0299992C13.6349 0.037999 13.8559 0.0479988 14.1229 0.0599985C15.1868 0.109997 15.9128 0.277993 16.5497 0.524987C17.2097 0.778981 17.7657 1.12297 18.3216 1.67796C18.8303 2.17768 19.2238 2.7824 19.4746 3.44992C19.7216 4.0869 19.8896 4.81288 19.9396 5.87786C19.9516 6.14385 19.9616 6.36485 19.9696 6.58984L19.9756 6.78384C19.9906 7.27582 19.9966 7.84681 19.9986 8.97178L19.9996 9.71776V11.0277C20.002 11.7571 19.9943 12.4865 19.9766 13.2157L19.9706 13.4097C19.9626 13.6347 19.9526 13.8557 19.9406 14.1217C19.8906 15.1866 19.7206 15.9116 19.4746 16.5496C19.2238 17.2171 18.8303 17.8218 18.3216 18.3216C17.8219 18.8302 17.2172 19.2237 16.5497 19.4745C15.9128 19.7215 15.1868 19.8895 14.1229 19.9395L13.4109 19.9695L13.2169 19.9755C12.7239 19.9895 12.153 19.9965 11.028 19.9985L10.282 19.9995H8.97312C8.24341 20.0021 7.51371 19.9944 6.78423 19.9765L6.59024 19.9705C6.35286 19.9615 6.11553 19.9512 5.87827 19.9395C4.81433 19.8895 4.08836 19.7215 3.45039 19.4745C2.78326 19.2236 2.1789 18.8301 1.67948 18.3216C1.17046 17.8219 0.776638 17.2172 0.525542 16.5496C0.278555 15.9126 0.110563 15.1866 0.0605657 14.1217L0.0305672 13.4097L0.0255676 13.2157C0.00713458 12.4865 -0.00119923 11.7571 0.000568768 11.0277V8.97178C-0.00219899 8.2424 0.00513476 7.51301 0.0225677 6.78384L0.0295673 6.58984C0.0375669 6.36485 0.0475664 6.14385 0.0595658 5.87786C0.109563 4.81288 0.277555 4.0879 0.524542 3.44992C0.776224 2.78213 1.17073 2.17738 1.68048 1.67796C2.17961 1.16952 2.78362 0.776055 3.45039 0.524987C4.08836 0.277993 4.81333 0.109997 5.87827 0.0599985C6.14426 0.0479988 6.36625 0.037999 6.59024 0.0299992L6.78423 0.0239993C7.51338 0.00623256 8.24275 -0.00143466 8.97212 0.000999903L11.028 0ZM10.0001 4.99988C8.67405 4.99988 7.40234 5.52665 6.46471 6.46431C5.52707 7.40197 5.00032 8.67371 5.00032 9.99976C5.00032 11.3258 5.52707 12.5975 6.46471 13.5352C7.40234 14.4729 8.67405 14.9996 10.0001 14.9996C11.3261 14.9996 12.5978 14.4729 13.5354 13.5352C14.4731 12.5975 14.9998 11.3258 14.9998 9.99976C14.9998 8.67371 14.4731 7.40197 13.5354 6.46431C12.5978 5.52665 11.3261 4.99988 10.0001 4.99988ZM10.0001 6.99983C10.394 6.99976 10.7841 7.07729 11.1481 7.22799C11.5121 7.37869 11.8428 7.59961 12.1214 7.87813C12.4 8.15666 12.6211 8.48733 12.7719 8.85127C12.9227 9.21522 13.0003 9.6053 13.0004 9.99926C13.0005 10.3932 12.923 10.7833 12.7723 11.1473C12.6216 11.5113 12.4006 11.8421 12.1221 12.1207C11.8436 12.3993 11.513 12.6203 11.149 12.7711C10.7851 12.922 10.395 12.9996 10.0011 12.9997C9.20545 12.9997 8.44243 12.6836 7.87985 12.121C7.31727 11.5584 7.00122 10.7954 7.00122 9.99976C7.00122 9.20413 7.31727 8.44108 7.87985 7.87849C8.44243 7.31589 9.20545 6.99983 10.0011 6.99983M15.2508 3.49991C14.9193 3.49991 14.6014 3.63161 14.367 3.86602C14.1326 4.10044 14.0009 4.41837 14.0009 4.74988C14.0009 5.0814 14.1326 5.39933 14.367 5.63375C14.6014 5.86816 14.9193 5.99985 15.2508 5.99985C15.5823 5.99985 15.9002 5.86816 16.1346 5.63375C16.369 5.39933 16.5007 5.0814 16.5007 4.74988C16.5007 4.41837 16.369 4.10044 16.1346 3.86602C15.9002 3.63161 15.5823 3.49991 15.2508 3.49991Z" fill="currentColor"/>
                            </svg>
                        </a>
                        <a href="#" class="footer-social__link" aria-label="Facebook">
                            <svg width="14" height="20" viewBox="0 0 14 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M10.0435 0C6.56052 0 3.65217 2.5827 3.65217 5.67568V7.56757H0.304348C0.22363 7.56757 0.146217 7.59604 0.0891412 7.64673C0.0320649 7.69741 0 7.76616 0 7.83784V12.1622C0 12.3114 0.136348 12.4324 0.304348 12.4324H3.65217V19.7297C3.65217 19.8789 3.78852 20 3.95652 20H8.82609C8.90681 20 8.98422 19.9715 9.04129 19.9208C9.09837 19.8702 9.13043 19.8014 9.13043 19.7297V12.4324H12.4783C12.5461 12.4323 12.612 12.412 12.6654 12.3747C12.7187 12.3375 12.7566 12.2855 12.7729 12.227L13.9903 7.9027C14.0013 7.86295 14.0021 7.82148 13.9925 7.78144C13.9828 7.74139 13.9631 7.7038 13.9347 7.67153C13.9063 7.63925 13.8701 7.61312 13.8287 7.59511C13.7872 7.5771 13.7418 7.56768 13.6957 7.56757H9.13043V5.67568C9.13359 5.46151 9.2308 5.2569 9.40135 5.10544C9.5719 4.95399 9.8023 4.86766 10.0435 4.86486H13.6957C13.7764 4.86486 13.8538 4.83639 13.9109 4.7857C13.9679 4.73502 14 4.66627 14 4.59459V0.27027C14 0.19859 13.9679 0.129846 13.9109 0.0791604C13.8538 0.0284749 13.7764 0 13.6957 0H10.0435Z" fill="currentColor"/>
                            </svg>
                        </a>
                    </div>
                </div>
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
