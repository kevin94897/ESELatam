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
            <?php
            get_template_part('template-parts/breadcrumbs', null, [
                'current' => get_the_title($ese_id),
                'attrs'   => 'data-nos-hero-crumb',
            ]);
            ?>

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
