<?php
/**
 * Header
 *
 * @package EseLatam
 */
declare(strict_types=1);
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-bg text-text-dark antialiased'); ?>>
<?php wp_body_open(); ?>

<a class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:bg-primary focus:text-white focus:px-4 focus:py-2 focus:rounded"
   href="#main">
    <?php esc_html_e('Saltar al contenido', 'ese-latam'); ?>
</a>

<?php
// El botón del menú se edita en ESE Latam → Cabecera (inc/pcf-globales.php).
$ese_hdr_cta = ese_latam_enlace(
    ese_latam_opcion('cabecera_cta', null),
    __('Contacto', 'ese-latam'),
    ese_latam_contacto_url()
);
?>
<header class="site-header" data-header>
    <div class="site-header__inner">
        <?php $ese_hdr_logo = ese_latam_logo(); ?>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
            <?php // Dos capas del mismo logo (blanca y a color) que se funden según el scroll — ver .site-logo en main.css. ?>
            <img class="site-logo__light" src="<?php echo esc_url($ese_hdr_logo['url']); ?>"
                 alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
                 width="<?php echo (int) $ese_hdr_logo['width']; ?>" height="<?php echo (int) $ese_hdr_logo['height']; ?>">
            <img class="site-logo__color" src="<?php echo esc_url($ese_hdr_logo['url']); ?>"
                 alt="" aria-hidden="true"
                 width="<?php echo (int) $ese_hdr_logo['width']; ?>" height="<?php echo (int) $ese_hdr_logo['height']; ?>">
        </a>

        <nav class="nav-pill" aria-label="<?php esc_attr_e('Menú principal', 'ese-latam'); ?>">
            <?php // Pastilla que se desliza bajo el link con hover/foco (header.ts). ?>
            <span class="nav-pill__indicator" aria-hidden="true"></span>
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'nav-pill__list',
                'fallback_cb'    => 'ese_latam_nav_fallback',
                'depth'          => 2,
                // Le devuelve el mega-submenú de "Sectores" al menú real
                // (el item no tiene hijos en el admin, ver inc/menus.php).
                'walker'         => new ESE_Latam_Nav_Walker(),
            ]);
            ?>

            <div class="nav-pill__actions">
                <button type="button" class="nav-pill__search" data-site-search-open
                        aria-label="<?php esc_attr_e('Buscar', 'ese-latam'); ?>"
                        aria-haspopup="dialog" aria-controls="site-search" aria-expanded="false">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M17.7036 16.2895L13.7507 12.3352C14.9359 10.7908 15.4893 8.85338 15.2985 6.91602C15.1077 4.97867 14.1871 3.18641 12.7235 1.90282C11.2598 0.619235 9.36266 -0.0595708 7.41689 0.00410682C5.47112 0.0677844 3.62243 0.869177 2.24582 2.24572C0.869217 3.62226 0.0677876 5.47087 0.00410701 7.41655C-0.0595736 9.36223 0.619263 11.2593 1.90291 12.7229C3.18656 14.1865 4.9789 15.107 6.91634 15.2978C8.85379 15.4886 10.7913 14.9352 12.3357 13.7501L16.2919 17.707C16.3848 17.7999 16.4951 17.8736 16.6165 17.9238C16.7379 17.9741 16.868 18 16.9994 18C17.1308 18 17.2609 17.9741 17.3823 17.9238C17.5037 17.8736 17.614 17.7999 17.7069 17.707C17.7999 17.614 17.8736 17.5038 17.9238 17.3824C17.9741 17.261 18 17.1309 18 16.9995C18 16.8681 17.9741 16.738 17.9238 16.6166C17.8736 16.4952 17.7999 16.3849 17.7069 16.292L17.7036 16.2895ZM2.01446 7.67415C2.01446 6.55475 2.34641 5.46049 2.96835 4.52974C3.59028 3.59899 4.47426 2.87357 5.5085 2.44519C6.54274 2.01681 7.68079 1.90473 8.77873 2.12312C9.87667 2.3415 10.8852 2.88054 11.6768 3.67208C12.4683 4.46361 13.0074 5.47209 13.2258 6.56998C13.4442 7.66787 13.3321 8.80587 12.9037 9.84006C12.4753 10.8742 11.7499 11.7582 10.8191 12.3801C9.88827 13.002 8.79396 13.3339 7.67451 13.3339C6.17384 13.3324 4.73508 12.7356 3.67395 11.6745C2.61282 10.6134 2.016 9.17475 2.01446 7.67415Z" fill="currentColor"/>
                    </svg>
                </button>

                <a href="<?php echo esc_url($ese_hdr_cta['href']); ?>" class="nav-pill__cta"<?php echo ese_latam_target_attr($ese_hdr_cta['target']); ?>>
                    <?php echo esc_html($ese_hdr_cta['label']); ?>
                    <span class="nav-pill__cta-icon" aria-hidden="true">
                        <svg width="14" height="12" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/></svg>
                    </span>
                </a>
            </div>
        </nav>

        <button type="button" class="nav-toggle" data-nav-toggle
                aria-expanded="false" aria-controls="mobile-nav"
                aria-label="<?php esc_attr_e('Abrir menú', 'ese-latam'); ?>">
            <span></span><span></span>
        </button>
    </div>

    <div class="mobile-nav" id="mobile-nav" data-nav-panel>
        <?php // Buscador móvil "expanding dock" (mobile-search.ts): en reposo es
        // solo el botón redondo con la lupa; al tocarlo se encoge y en su lugar
        // el formulario se abre con resorte hasta su ancho final. El <form> va
        // completo en el HTML (no lo arma el JS), con `hidden` hasta que el
        // módulo lo muestra; envía a /catalogo/?s= igual que antes. ?>
        <div class="mobile-search" data-mobile-search>
            <button type="button" class="mobile-search__toggle" data-mobile-search-open
                    aria-expanded="false" aria-controls="mobile-search-form"
                    aria-label="<?php esc_attr_e('Buscar productos', 'ese-latam'); ?>">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M17.7036 16.2895L13.7507 12.3352C14.9359 10.7908 15.4893 8.85338 15.2985 6.91602C15.1077 4.97867 14.1871 3.18641 12.7235 1.90282C11.2598 0.619235 9.36266 -0.0595708 7.41689 0.00410682C5.47112 0.0677844 3.62243 0.869177 2.24582 2.24572C0.869217 3.62226 0.0677876 5.47087 0.00410701 7.41655C-0.0595736 9.36223 0.619263 11.2593 1.90291 12.7229C3.18656 14.1865 4.9789 15.107 6.91634 15.2978C8.85379 15.4886 10.7913 14.9352 12.3357 13.7501L16.2919 17.707C16.3848 17.7999 16.4951 17.8736 16.6165 17.9238C16.7379 17.9741 16.868 18 16.9994 18C17.1308 18 17.2609 17.9741 17.3823 17.9238C17.5037 17.8736 17.614 17.7999 17.7069 17.707C17.7999 17.614 17.8736 17.5038 17.9238 17.3824C17.9741 17.261 18 17.1309 18 16.9995C18 16.8681 17.9741 16.738 17.9238 16.6166C17.8736 16.4952 17.7999 16.3849 17.7069 16.292L17.7036 16.2895ZM2.01446 7.67415C2.01446 6.55475 2.34641 5.46049 2.96835 4.52974C3.59028 3.59899 4.47426 2.87357 5.5085 2.44519C6.54274 2.01681 7.68079 1.90473 8.77873 2.12312C9.87667 2.3415 10.8852 2.88054 11.6768 3.67208C12.4683 4.46361 13.0074 5.47209 13.2258 6.56998C13.4442 7.66787 13.3321 8.80587 12.9037 9.84006C12.4753 10.8742 11.7499 11.7582 10.8191 12.3801C9.88827 13.002 8.79396 13.3339 7.67451 13.3339C6.17384 13.3324 4.73508 12.7356 3.67395 11.6745C2.61282 10.6134 2.016 9.17475 2.01446 7.67415Z" fill="currentColor"/>
                </svg>
            </button>

            <form class="mobile-search__form" id="mobile-search-form" role="search" method="get"
                  action="<?php echo esc_url(get_post_type_archive_link('producto') . '#catalogo-productos'); ?>"
                  data-mobile-search-form hidden>
                <span class="mobile-search__icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M17.7036 16.2895L13.7507 12.3352C14.9359 10.7908 15.4893 8.85338 15.2985 6.91602C15.1077 4.97867 14.1871 3.18641 12.7235 1.90282C11.2598 0.619235 9.36266 -0.0595708 7.41689 0.00410682C5.47112 0.0677844 3.62243 0.869177 2.24582 2.24572C0.869217 3.62226 0.0677876 5.47087 0.00410701 7.41655C-0.0595736 9.36223 0.619263 11.2593 1.90291 12.7229C3.18656 14.1865 4.9789 15.107 6.91634 15.2978C8.85379 15.4886 10.7913 14.9352 12.3357 13.7501L16.2919 17.707C16.3848 17.7999 16.4951 17.8736 16.6165 17.9238C16.7379 17.9741 16.868 18 16.9994 18C17.1308 18 17.2609 17.9741 17.3823 17.9238C17.5037 17.8736 17.614 17.7999 17.7069 17.707C17.7999 17.614 17.8736 17.5038 17.9238 17.3824C17.9741 17.261 18 17.1309 18 16.9995C18 16.8681 17.9741 16.738 17.9238 16.6166C17.8736 16.4952 17.7999 16.3849 17.7069 16.292L17.7036 16.2895ZM2.01446 7.67415C2.01446 6.55475 2.34641 5.46049 2.96835 4.52974C3.59028 3.59899 4.47426 2.87357 5.5085 2.44519C6.54274 2.01681 7.68079 1.90473 8.77873 2.12312C9.87667 2.3415 10.8852 2.88054 11.6768 3.67208C12.4683 4.46361 13.0074 5.47209 13.2258 6.56998C13.4442 7.66787 13.3321 8.80587 12.9037 9.84006C12.4753 10.8742 11.7499 11.7582 10.8191 12.3801C9.88827 13.002 8.79396 13.3339 7.67451 13.3339C6.17384 13.3324 4.73508 12.7356 3.67395 11.6745C2.61282 10.6134 2.016 9.17475 2.01446 7.67415Z" fill="currentColor"/>
                </svg>
                </span>
                <label class="sr-only" for="mobile-search-input"><?php esc_html_e('Buscar productos', 'ese-latam'); ?></label>
                <input class="mobile-search__input" id="mobile-search-input" type="search" name="s"
                       placeholder="<?php esc_attr_e('Buscar productos…', 'ese-latam'); ?>" autocomplete="off">
                <button type="button" class="mobile-search__close" data-mobile-search-close
                        aria-label="<?php esc_attr_e('Cerrar buscador', 'ese-latam'); ?>">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" aria-hidden="true">
                        <path d="M3 3l10 10M13 3L3 13"/>
                    </svg>
                </button>
            </form>
        </div>

        <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'mobile-nav__list',
            'fallback_cb'    => 'ese_latam_nav_fallback',
            'depth'          => 1,
            'walker'         => new ESE_Latam_Nav_Walker(),
        ]);
        ?>
        <a href="<?php echo esc_url($ese_hdr_cta['href']); ?>" class="nav-pill__cta mobile-nav__cta"<?php echo ese_latam_target_attr($ese_hdr_cta['target']); ?>>
            <?php echo esc_html($ese_hdr_cta['label']); ?>
        </a>
        <?php $ese_hdr_mail = ese_latam_contacto_datos()['email']; ?>
        <?php if ('' !== $ese_hdr_mail) : ?>
            <a class="mobile-nav__mail" href="mailto:<?php echo esc_attr($ese_hdr_mail); ?>"><?php echo esc_html($ese_hdr_mail); ?></a>
        <?php endif; ?>
    </div>
</header>

<?php // Buscador global (lo abre .nav-pill__search, ver search-overlay.ts).
// Fuera de <header> a propósito: el header recibe transforms de GSAP
// (entrada, hero-scroll) y cualquier transform en un ancestro convierte a
// ese ancestro en el containing block de un `position: fixed` — el overlay
// se mediría contra el header y no contra el viewport. Envía al archivo del
// CPT (/catalogo/?s=): catalogo-grid.php entiende `s` y pinta los resultados
// con la grilla real; la página "catalogo-de-productos" con ?s= cae al
// index.php genérico (WP la trata como búsqueda global). ?>
<div class="site-search" id="site-search" data-site-search role="dialog" aria-modal="true"
     aria-label="<?php esc_attr_e('Buscar productos', 'ese-latam'); ?>" hidden>
    <div class="site-search__backdrop" data-site-search-close></div>

    <div class="site-search__panel">
        <button type="button" class="site-search__close" data-site-search-close
                aria-label="<?php esc_attr_e('Cerrar búsqueda', 'ese-latam'); ?>">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" aria-hidden="true">
                <path d="M3 3l10 10M13 3L3 13"/>
            </svg>
        </button>

        <form class="site-search__form" role="search" method="get"
              action="<?php echo esc_url(get_post_type_archive_link('producto') . '#catalogo-productos'); ?>">
            <svg class="site-search__icon" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M17.7036 16.2895L13.7507 12.3352C14.9359 10.7908 15.4893 8.85338 15.2985 6.91602C15.1077 4.97867 14.1871 3.18641 12.7235 1.90282C11.2598 0.619235 9.36266 -0.0595708 7.41689 0.00410682C5.47112 0.0677844 3.62243 0.869177 2.24582 2.24572C0.869217 3.62226 0.0677876 5.47087 0.00410701 7.41655C-0.0595736 9.36223 0.619263 11.2593 1.90291 12.7229C3.18656 14.1865 4.9789 15.107 6.91634 15.2978C8.85379 15.4886 10.7913 14.9352 12.3357 13.7501L16.2919 17.707C16.3848 17.7999 16.4951 17.8736 16.6165 17.9238C16.7379 17.9741 16.868 18 16.9994 18C17.1308 18 17.2609 17.9741 17.3823 17.9238C17.5037 17.8736 17.614 17.7999 17.7069 17.707C17.7999 17.614 17.8736 17.5038 17.9238 17.3824C17.9741 17.261 18 17.1309 18 16.9995C18 16.8681 17.9741 16.738 17.9238 16.6166C17.8736 16.4952 17.7999 16.3849 17.7069 16.292L17.7036 16.2895ZM2.01446 7.67415C2.01446 6.55475 2.34641 5.46049 2.96835 4.52974C3.59028 3.59899 4.47426 2.87357 5.5085 2.44519C6.54274 2.01681 7.68079 1.90473 8.77873 2.12312C9.87667 2.3415 10.8852 2.88054 11.6768 3.67208C12.4683 4.46361 13.0074 5.47209 13.2258 6.56998C13.4442 7.66787 13.3321 8.80587 12.9037 9.84006C12.4753 10.8742 11.7499 11.7582 10.8191 12.3801C9.88827 13.002 8.79396 13.3339 7.67451 13.3339C6.17384 13.3324 4.73508 12.7356 3.67395 11.6745C2.61282 10.6134 2.016 9.17475 2.01446 7.67415Z" fill="currentColor"/>
            </svg>
            <label class="sr-only" for="site-search-input"><?php esc_html_e('Buscar productos', 'ese-latam'); ?></label>
            <input class="site-search__input" id="site-search-input" type="search" name="s"
                   placeholder="<?php esc_attr_e('Busca un producto, litraje o material…', 'ese-latam'); ?>"
                   autocomplete="off" spellcheck="false" value="<?php echo esc_attr(get_search_query()); ?>">
            <button type="submit" class="site-search__submit">
                <span><?php esc_html_e('Buscar', 'ese-latam'); ?></span>
                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/>
                </svg>
            </button>
        </form>

        <?php $ese_search_chips = ese_latam_buscador_chips(); ?>
        <?php if (! empty($ese_search_chips)) : ?>
            <div class="site-search__suggest">
                <p class="site-search__suggest-label"><?php esc_html_e('Sugerencias', 'ese-latam'); ?></p>
                <ul class="site-search__chips">
                    <?php foreach ($ese_search_chips as $ese_chip) : ?>
                        <li><a class="site-search__chip" href="<?php echo esc_url($ese_chip['url']); ?>"><?php echo esc_html($ese_chip['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <p class="site-search__kbd"><kbd>Esc</kbd> <?php esc_html_e('para cerrar', 'ese-latam'); ?></p>
    </div>
</div>

<main id="main" class="site-main">
