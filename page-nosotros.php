<?php
/**
 * Template Name: Nosotros
 *
 * Página "Nosotros" (Figma node 3441-370). WordPress la aplica sola a la
 * página con slug `nosotros` (page-{slug}.php) y, por el header de arriba,
 * también se puede asignar a mano desde el editor a cualquier otra página.
 *
 * Todo el copy y las fotos se editan en la propia página
 * (inc/pcf-nosotros.php); los logos de aliados salen del módulo "Aliados" y
 * el cierre de Contactemos, de ESE Latam → Contacto. Lo que no tiene campo
 * lleno no se pinta: acá no hay textos de respaldo.
 *
 * Secciones, en orden del Figma:
 *   1. Hero oscuro (breadcrumb + titular + bajada)
 *   2. Construimos para el futuro (misión / visión + fotos + isla)
 *   3. Objetivos con propósito (aside sticky + objetivos)
 *   4. Nuestros aliados (grilla de logos sobre degradado radial + isla)
 *   5. Marquee
 *   6. Método Circulogic (tabs ESG con autoplay + foto)
 *   7. HDPE: el futuro es circular (chips + escena de pellets + cards)
 *   8. Contactemos (template-parts/contacto.php)
 *
 * Las animaciones viven en src/ts/modules/nosotros.ts (carga perezosa
 * desde main.ts al detectar `[data-nosotros]`), además de los reveals
 * genéricos del theme (data-reveal, data-reveal-header, data-float,
 * data-parallax, data-marquee).
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();

$ese_id  = (int) get_queried_object_id();
$ese_cmp = static fn (string $name, $def = '') => ese_latam_campo($name, $ese_id, $def);

// ---------- Hero ----------
$ese_hero_titulo = trim((string) $ese_cmp('nos_hero_titulo'));
$ese_hero_ini    = trim((string) $ese_cmp('nos_hero_desc_ini'));
$ese_hero_fin    = trim((string) $ese_cmp('nos_hero_desc_fin'));
$ese_hero_cifra  = $ese_cmp('nos_hero_cifra', null);
$ese_hero_cifra  = null === $ese_hero_cifra || '' === $ese_hero_cifra ? '' : (string) (int) $ese_hero_cifra;
$ese_hero_img    = ese_latam_img_url($ese_cmp('nos_hero_imagen'));
$ese_hero_scroll = trim((string) $ese_cmp('nos_hero_scroll'));

// ---------- Construimos para el futuro ----------
$ese_const_bloques = [];
foreach ((array) $ese_cmp('nos_const_bloques', []) as $ese_fila) {
    $ese_t = trim((string) ($ese_fila['title'] ?? ''));
    if ('' === $ese_t) {
        continue;
    }
    $ese_const_bloques[] = ['title' => $ese_t, 'texto' => trim((string) ($ese_fila['texto'] ?? ''))];
}
$ese_const_titulo = trim((string) $ese_cmp('nos_const_titulo'));
$ese_const_kicker = trim((string) $ese_cmp('nos_const_kicker'));
$ese_const_foto   = ese_latam_img_url($ese_cmp('nos_const_foto'));
$ese_const_cielo  = ese_latam_img_url($ese_cmp('nos_const_cielo'));
$ese_const_isla   = ese_latam_img_url($ese_cmp('nos_const_isla'));

// ---------- Objetivos ----------
// El id del ancla sale del título: es lo que usa el menú que sigue al scroll.
$ese_objetivos = [];
foreach ((array) $ese_cmp('nos_obj_items', []) as $ese_fila) {
    $ese_t = trim((string) ($ese_fila['title'] ?? ''));
    if ('' === $ese_t) {
        continue;
    }
    $ese_objetivos[] = [
        'id'    => sanitize_title($ese_t),
        'title' => $ese_t,
        'img'   => ese_latam_img_url($ese_fila['img'] ?? ''),
        'text'  => (string) ($ese_fila['texto'] ?? ''),
    ];
}

$ese_metas = [];
foreach ((array) $ese_cmp('nos_obj_metas', []) as $ese_fila) {
    $ese_m = trim((string) ($ese_fila['texto'] ?? ''));
    if ('' !== $ese_m) {
        $ese_metas[] = $ese_m;
    }
}

$ese_salto = ese_latam_enlace($ese_cmp('nos_obj_salto', null));

// ---------- Método Circulogic ----------
$ese_esg = [];
foreach ((array) $ese_cmp('nos_metodo_tabs', []) as $ese_fila) {
    $ese_t = trim((string) ($ese_fila['title'] ?? ''));
    if ('' === $ese_t) {
        continue;
    }
    $ese_esg[] = [
        'title' => $ese_t,
        'desc'  => trim((string) ($ese_fila['desc'] ?? '')),
        'img'   => ese_latam_img_url($ese_fila['img'] ?? ''),
    ];
}

// ---------- HDPE ----------
$ese_chips = [];
foreach ((array) $ese_cmp('nos_hdpe_chips', []) as $ese_fila) {
    $ese_c = trim((string) ($ese_fila['texto'] ?? ''));
    if ('' !== $ese_c) {
        $ese_chips[] = $ese_c;
    }
}

$ese_hdpe_cards = [];
foreach ((array) $ese_cmp('nos_hdpe_cards', []) as $ese_fila) {
    $ese_t = trim((string) ($ese_fila['title'] ?? ''));
    if ('' === $ese_t) {
        continue;
    }
    $ese_hdpe_cards[] = [
        'title' => $ese_t,
        'desc'  => trim((string) ($ese_fila['desc'] ?? '')),
        'icon'  => ese_latam_icono_hdpe((string) ($ese_fila['icono'] ?? '')),
        'spin'  => ! empty($ese_fila['gira']),
    ];
}

$ese_hdpe_kicker = trim((string) $ese_cmp('nos_hdpe_kicker'));
$ese_hdpe_titulo = trim((string) $ese_cmp('nos_hdpe_titulo'));
$ese_hdpe_desc   = ese_latam_texto_rico((string) $ese_cmp('nos_hdpe_desc'));
$ese_hdpe_video  = ese_latam_img_url($ese_cmp('nos_hdpe_video'));
$ese_hdpe_ekick  = trim((string) $ese_cmp('nos_hdpe_escena_kicker'));
$ese_hdpe_esub   = trim((string) $ese_cmp('nos_hdpe_escena_sub'));
$ese_hdpe_hay    = '' !== $ese_hdpe_titulo || '' !== $ese_hdpe_desc || [] !== $ese_chips
    || '' !== $ese_hdpe_video || [] !== $ese_hdpe_cards;

$ese_marquee = trim((string) $ese_cmp('nos_marquee'));

// Check-circle (Figma check_circle 17px) — chips del bloque HDPE.
$ese_check_svg = '<svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M7.23015 12.306L13.2455 6.29067L12.3026 5.34784L7.23015 10.4203L4.68015 7.87033L3.73732 8.81316L7.23015 12.306ZM8.50157 17C7.32588 17 6.22081 16.7769 5.18634 16.3307C4.15188 15.8846 3.25207 15.279 2.48692 14.5142C1.72177 13.7493 1.11596 12.8499 0.669487 11.8159C0.223162 10.7819 0 9.6771 0 8.50157C0 7.32588 0.223088 6.22081 0.669263 5.18634C1.11544 4.15188 1.72095 3.25207 2.4858 2.48692C3.25065 1.72177 4.15009 1.11596 5.18411 0.669487C6.21812 0.223162 7.3229 0 8.49843 0C9.67412 0 10.7792 0.223087 11.8137 0.669263C12.8481 1.11544 13.7479 1.72095 14.5131 2.4858C15.2782 3.25065 15.884 4.15009 16.3305 5.18411C16.7768 6.21812 17 7.3229 17 8.49843C17 9.67412 16.7769 10.7792 16.3307 11.8137C15.8846 12.8481 15.279 13.7479 14.5142 14.5131C13.7493 15.2782 12.8499 15.884 11.8159 16.3305C10.7819 16.7768 9.6771 17 8.50157 17ZM8.5 15.6579C10.4982 15.6579 12.1908 14.9645 13.5776 13.5776C14.9645 12.1908 15.6579 10.4982 15.6579 8.5C15.6579 6.50175 14.9645 4.80921 13.5776 3.42237C12.1908 2.03553 10.4982 1.34211 8.5 1.34211C6.50175 1.34211 4.80921 2.03553 3.42237 3.42237C2.03553 4.80921 1.34211 6.50175 1.34211 8.5C1.34211 10.4982 2.03553 12.1908 3.42237 13.5776C4.80921 14.9645 6.50175 15.6579 8.5 15.6579Z" fill="currentColor"/></svg>';
?>

<div class="nosotros" data-nosotros>
    <?php // Marca "hay JS": solo entonces el CSS esconde el titular del hero
    // hasta que nosotros.ts lo anime (sin JS queda visible desde el HTML). ?>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>

    <?php // ---------- 1. HERO ---------- ?>
    <section class="nos-hero" data-nos-hero>
        <?php if ('' !== $ese_hero_img) : ?>
            <div class="nos-hero__bg" aria-hidden="true" data-nos-hero-bg>
                <img src="<?php echo esc_url($ese_hero_img); ?>" alt="" decoding="async" fetchpriority="high">
            </div>
        <?php endif; ?>
        <div class="nos-hero__shade" aria-hidden="true"></div>
        <div class="nos-hero__glow" aria-hidden="true" data-nos-hero-glow></div>

        <div class="nos-hero__content" data-nos-hero-content>
            <nav class="nos-crumb" aria-label="<?php esc_attr_e('Ruta de navegación', 'ese-latam'); ?>" data-nos-hero-crumb>
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M10 5.27979V10.56C10 10.6767 9.9561 10.7886 9.87796 10.8711C9.79982 10.9536 9.69384 11 9.58333 11H6.66667C6.55616 11 6.45018 10.9536 6.37204 10.8711C6.2939 10.7886 6.25 10.6767 6.25 10.56V7.69988C6.25 7.64153 6.22805 7.58557 6.18898 7.54431C6.14991 7.50305 6.09692 7.47987 6.04167 7.47987H3.95833C3.90308 7.47987 3.85009 7.50305 3.81102 7.54431C3.77195 7.58557 3.75 7.64153 3.75 7.69988V10.56C3.75 10.6767 3.7061 10.7886 3.62796 10.8711C3.54982 10.9536 3.44384 11 3.33333 11H0.416667C0.30616 11 0.200179 10.9536 0.122039 10.8711C0.0438988 10.7886 0 10.6767 0 10.56V5.27979C0.000102442 5.04643 0.0879669 4.82267 0.244271 4.65772L4.41094 0.257552C4.5672 0.0926383 4.77908 0 5 0C5.22092 0 5.4328 0.0926383 5.58906 0.257552L9.75573 4.65772C9.91203 4.82267 9.9999 5.04643 10 5.27979Z" fill="currentColor"/>
                    </svg>
                    <?php esc_html_e('Inicio', 'ese-latam'); ?>
                </a>
                <span class="nos-crumb__sep" aria-hidden="true">
                    <svg width="4" height="7" viewBox="0 0 4 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.86395 3.8233L0.788805 6.86609C0.70215 6.95183 0.58462 7 0.462071 7C0.339522 7 0.221993 6.95183 0.135337 6.86609C0.0486823 6.78034 0 6.66405 0 6.54279C0 6.42153 0.0486823 6.30524 0.135337 6.21949L2.88413 3.50038L0.136106 0.780507C0.0931991 0.738051 0.059163 0.687648 0.0359418 0.632177C0.0127205 0.576706 0.000768656 0.517252 0.000768656 0.45721C0.000768656 0.397169 0.0127205 0.337715 0.0359418 0.282243C0.059163 0.226772 0.0931991 0.17637 0.136106 0.133914C0.179014 0.0914579 0.229952 0.0577801 0.286013 0.0348031C0.342074 0.0118261 0.40216 0 0.46284 0C0.52352 0 0.583606 0.0118261 0.639667 0.0348031C0.695728 0.0577801 0.746666 0.0914579 0.789574 0.133914L3.86471 3.1767C3.90767 3.21916 3.94173 3.26958 3.96494 3.32509C3.98816 3.38059 4.00007 3.44009 4 3.50016C3.99993 3.56023 3.98787 3.61969 3.96453 3.67515C3.94118 3.7306 3.907 3.78094 3.86395 3.8233Z" fill="currentColor"/>
                    </svg>
                </span>
                <span aria-current="page"><?php echo esc_html(get_the_title($ese_id)); ?></span>
            </nav>

            <div class="nos-hero__bottom">
                <?php if ('' !== $ese_hero_titulo) : ?>
                    <h1 class="nos-hero__title" data-nos-hero-title>
                        <?php echo ese_latam_titulo($ese_hero_titulo); ?>
                    </h1>
                <?php endif; ?>
                <?php if ('' !== $ese_hero_ini || '' !== $ese_hero_cifra || '' !== $ese_hero_fin) : ?>
                    <p class="nos-hero__desc" data-nos-hero-desc>
                        <?php echo esc_html($ese_hero_ini); ?>
                        <?php if ('' !== $ese_hero_cifra) : ?>
                            <span class="nos-hero__count" data-nos-count="<?php echo esc_attr($ese_hero_cifra); ?>"><?php echo esc_html($ese_hero_cifra); ?></span>
                        <?php endif; ?>
                        <?php echo esc_html($ese_hero_fin); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ('' !== $ese_hero_scroll) : ?>
            <div class="nos-hero__scroll" aria-hidden="true" data-nos-hero-scroll>
                <span class="nos-hero__scroll-line"></span>
                <span class="nos-hero__scroll-text"><?php echo esc_html($ese_hero_scroll); ?></span>
            </div>
        <?php endif; ?>
    </section>

    <?php // ---------- 2. CONSTRUIMOS PARA EL FUTURO ---------- ?>
    <?php if ([] !== $ese_const_bloques || '' !== $ese_const_titulo) : ?>
        <section class="nos-construimos" data-nos-construimos>
            <header class="nos-construimos__header" data-reveal-header>
                <?php if ('' !== $ese_const_kicker) : ?>
                    <p class="type-kicker text-secondary">/ <?php echo esc_html($ese_const_kicker); ?></p>
                <?php endif; ?>
                <?php if ('' !== $ese_const_titulo) : ?>
                    <h2 class="type-h2 uppercase">
                        <?php echo ese_latam_titulo($ese_const_titulo, 'span', 'hl'); ?>
                    </h2>
                <?php endif; ?>
            </header>

            <?php if ('' !== $ese_const_foto) : ?>
                <figure class="nos-construimos__photo" data-nos-panel="left">
                    <img src="<?php echo esc_url($ese_const_foto); ?>"
                         alt="<?php echo esc_attr((string) $ese_cmp('nos_const_foto_alt')); ?>"
                         width="1024" height="683" loading="lazy" decoding="async" data-nos-panel-img>
                </figure>
            <?php endif; ?>

            <div class="nos-construimos__text">
                <?php foreach ($ese_const_bloques as $ese_i => $ese_bloque) : ?>
                    <div class="nos-construimos__block" data-reveal="up"<?php echo $ese_i > 0 ? ' data-reveal-delay="' . esc_attr((string) round($ese_i * 0.15, 2)) . '"' : ''; ?>>
                        <h3><?php echo esc_html($ese_bloque['title']); ?></h3>
                        <?php if ('' !== $ese_bloque['texto']) : ?>
                            <p><?php echo esc_html($ese_bloque['texto']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ('' !== $ese_const_cielo) : ?>
                <div class="nos-construimos__sky">
                    <div class="nos-construimos__sky-frame" data-nos-panel="right">
                        <img class="nos-construimos__sky-img" src="<?php echo esc_url($ese_const_cielo); ?>" alt=""
                             width="1400" height="935" loading="lazy" decoding="async" data-nos-panel-img>
                    </div>
                    <?php if ('' !== $ese_const_isla) : ?>
                        <div class="nos-construimos__island" data-parallax data-parallax-from="12" data-parallax-to="-12">
                            <div class="nos-construimos__island-enter" data-nos-island>
                                <img src="<?php echo esc_url($ese_const_isla); ?>"
                                     alt="<?php echo esc_attr((string) $ese_cmp('nos_const_isla_alt')); ?>"
                                     width="1370" height="955" loading="lazy" decoding="async"
                                     data-float data-float-distance="14" data-float-duration="3.6">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php // ---------- 3. OBJETIVOS CON PROPÓSITO (template-parts/objetivos-sticky.php) ---------- ?>
    <?php
    get_template_part('template-parts/objetivos-sticky', null, [
        'id'          => 'objetivos',
        'kicker'      => (string) $ese_cmp('nos_obj_kicker'),
        'title'       => (string) $ese_cmp('nos_obj_titulo'),
        'desc'        => ese_latam_texto_rico((string) $ese_cmp('nos_obj_desc')),
        'items'       => $ese_objetivos,
        'metas_title' => (string) $ese_cmp('nos_obj_metas_titulo'),
        'metas'       => $ese_metas,
        'skip_label'  => $ese_salto['label'],
        'skip_href'   => $ese_salto['href'],
    ]);
    ?>

    <?php // ---------- 4. NUESTROS ALIADOS (template-parts/aliados.php) ---------- ?>
    <?php
    get_template_part('template-parts/aliados', null, [
        'kicker' => (string) $ese_cmp('nos_aliados_kicker'),
        'title'  => (string) $ese_cmp('nos_aliados_titulo'),
        'desc'   => (string) $ese_cmp('nos_aliados_desc'),
        'isla'   => ese_latam_img_url($ese_cmp('nos_aliados_isla')),
    ]);
    ?>

    <?php // ---------- 5. MARQUEE ---------- ?>
    <?php if ('' !== $ese_marquee) : ?>
        <div class="nos-marquee" aria-hidden="true">
            <p class="nos-marquee__track" data-marquee="right">
                <span><?php echo esc_html($ese_marquee); ?></span>
                <span><?php echo esc_html($ese_marquee); ?></span>
                <span><?php echo esc_html($ese_marquee); ?></span>
            </p>
        </div>
    <?php endif; ?>

    <?php // ---------- 6. MÉTODO CIRCULOGIC (template-parts/metodo-tabs.php) ---------- ?>
    <?php
    get_template_part('template-parts/metodo-tabs', null, [
        'id'     => 'metodo',
        'kicker' => (string) $ese_cmp('nos_metodo_kicker'),
        'title'  => (string) $ese_cmp('nos_metodo_titulo'),
        'desc'   => ese_latam_texto_rico((string) $ese_cmp('nos_metodo_desc')),
        'tabs'   => $ese_esg,
    ]);
    ?>

    <?php // ---------- 7. HDPE: EL FUTURO ES CIRCULAR ---------- ?>
    <?php if ($ese_hdpe_hay) : ?>
        <section id="hdpe" class="nos-hdpe" data-nos-hdpe>
            <div class="nos-hdpe__top">
                <header class="nos-hdpe__header" data-reveal-header>
                    <?php if ('' !== $ese_hdpe_kicker) : ?>
                        <p class="type-kicker text-secondary">/ <?php echo esc_html($ese_hdpe_kicker); ?></p>
                    <?php endif; ?>
                    <?php if ('' !== $ese_hdpe_titulo) : ?>
                        <h2 class="type-h2 uppercase">
                            <?php echo ese_latam_titulo($ese_hdpe_titulo, 'span', 'hl'); ?>
                        </h2>
                    <?php endif; ?>
                    <?php if ('' !== $ese_hdpe_desc) : ?>
                        <p class="nos-desc" data-reveal-desc><?php echo $ese_hdpe_desc; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
                    <?php endif; ?>
                    <?php if ([] !== $ese_chips) : ?>
                        <ul class="nos-hdpe__chips" data-nos-chips>
                            <?php foreach ($ese_chips as $ese_chip) : ?>
                                <li class="nos-hdpe__chip">
                                    <?php echo $ese_check_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                                    <?php echo esc_html($ese_chip); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </header>

                <?php if ('' !== $ese_hdpe_video) : ?>
                    <div class="nos-hdpe__scene" data-nos-panel="right">
                        <video class="nos-hdpe__video" src="<?php echo esc_url($ese_hdpe_video); ?>" autoplay loop muted playsinline aria-hidden="true"></video>
                        <?php if ('' !== $ese_hdpe_ekick || '' !== $ese_hdpe_esub) : ?>
                            <div class="nos-hdpe__scene-label">
                                <?php if ('' !== $ese_hdpe_ekick) : ?>
                                    <p class="nos-hdpe__scene-kicker"><?php echo esc_html($ese_hdpe_ekick); ?></p>
                                <?php endif; ?>
                                <?php if ('' !== $ese_hdpe_esub) : ?>
                                    <p class="nos-hdpe__scene-sub"><?php echo esc_html($ese_hdpe_esub); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ([] !== $ese_hdpe_cards) : ?>
                <ul class="nos-hdpe__cards" data-reveal-stagger>
                    <?php foreach ($ese_hdpe_cards as $ese_card) : ?>
                        <li class="nos-hdpe__card">
                            <?php if ('' !== $ese_card['icon']) : ?>
                                <span class="nos-hdpe__card-icon<?php echo $ese_card['spin'] ? ' nos-hdpe__card-icon--spin' : ''; ?>" aria-hidden="true" data-nos-hdpe-icon>
                                    <svg width="72" height="72" viewBox="0 0 72 72" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <?php echo $ese_card['icon']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                                    </svg>
                                </span>
                            <?php endif; ?>
                            <p class="nos-hdpe__card-title"><?php echo esc_html($ese_card['title']); ?></p>
                            <?php if ('' !== $ese_card['desc']) : ?>
                                <p class="nos-hdpe__card-desc"><?php echo esc_html($ese_card['desc']); ?></p>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php // ---------- 8. CONTACTEMOS ---------- ?>
    <?php
    get_template_part('template-parts/contacto', null, [
        'class' => 'contacto--upper',
    ]);
    ?>

</div>

<?php
get_footer();
