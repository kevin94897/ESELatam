<?php
/**
 * Front page — Hero "Estado A" (Figma node 4271-1457) con animación de scroll:
 * video scrubbed, isla flotante que aparece y nubes que cubren la transición.
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();

// Todo el copy de la portada se edita en la página fijada como Inicio
// (los campos se registran en inc/pcf-home.php). Cada campo vacío cae al
// texto del diseño, así que la home se ve igual sin tocar nada.
$ese_hero = [
    'video'  => ese_latam_img_url(ese_latam_home('hero_video')),
    'poster' => ese_latam_img_url(ese_latam_home('hero_video_poster')),
    'lede'   => trim((string) ese_latam_home('hero_lede', '')),
    'stat'   => trim((string) ese_latam_home('hero_card_stat', '')),
    'tag'    => trim((string) ese_latam_home('hero_card_tag', '')),
    'desc'   => trim((string) ese_latam_home('hero_card_desc', '')),
];

$ese_hero_cta  = ese_latam_enlace(ese_latam_home('hero_cta'));
$ese_hero_card = '' !== $ese_hero['stat'] || '' !== $ese_hero['tag'] || '' !== $ese_hero['desc'];

// El titular se anima línea por línea (hero-scroll.ts busca cada
// [data-hero-line]), así que un salto de línea del campo ES una línea del
// marcado y el resaltado |así| se resuelve dentro de cada una.
$ese_hero_titulo = (string) ese_latam_home('hero_titulo', '');
$ese_hero_lineas = preg_split('/\R/', trim($ese_hero_titulo)) ?: [];
$ese_hero_lineas = array_values(array_filter(array_map('trim', $ese_hero_lineas), static fn (string $l): bool => '' !== $l));

$ese_hero_hay = [] !== $ese_hero_lineas || '' !== $ese_hero['lede'] || $ese_hero_card || '' !== $ese_hero_cta['label'];
?>


<?php if ($ese_hero_hay) : ?>
<section class="hero" data-hero>
    <?php if ('' !== $ese_hero['video']) : ?>
        <video class="hero__video" data-hero-video
            src="<?php echo esc_url($ese_hero['video']); ?>"
            <?php echo '' !== $ese_hero['poster'] ? 'poster="' . esc_url($ese_hero['poster']) . '"' : ''; ?> muted playsinline
            preload="none" aria-hidden="true" tabindex="-1"></video>
    <?php endif; ?>

    <div class="hero__shade" aria-hidden="true"></div>

    <div class="hero__island" data-hero-island aria-hidden="true">
        <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/island/island-2400.webp'); ?>"
            srcset="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/island/island-480.webp'); ?> 480w,
                <?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/island/island-768.webp'); ?> 768w,
                <?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/island/island-1200.webp'); ?> 1200w,
                <?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/island/island-2400.webp'); ?> 2400w"
            sizes="(max-width: 47.9375rem) min(120vw, 640px), min(92vw, 1720px, 142svh)" alt=""
            width="2400" height="1350"
            fetchpriority="high" decoding="async">
    </div>

    <div class="hero__content">
        <div class="hero__title-wrap" data-hero-title-wrap>
            <?php if ([] !== $ese_hero_lineas) : ?>
                <h1 class="hero__title">
                    <?php foreach ($ese_hero_lineas as $ese_linea): ?>
                        <span class="hero__title-line"><span class="hero__title-inner"
                                data-hero-line><?php echo ese_latam_titulo($ese_linea); ?></span></span>
                    <?php endforeach; ?>
                </h1>
            <?php endif; ?>
        </div>

        <div class="hero__bottom" data-hero-bottom>
            <div class="hero__intro">
                <?php
                // Duplicado de .hero__title, visible solo en mobile (ver
                // .hero__title-settled en main.css): ahí el titular de arriba
                // es puramente el adorno de la intro (centrado, grande) y se
                // desvanece del todo en vez de viajar a una posición asentada
                // — este es el que realmente queda en el layout, justo
                // arriba del lede. En desktop no se muestra (display:none) y
                // .hero__title de arriba sigue siendo el único titular.
                ?>
                <?php if ([] !== $ese_hero_lineas) : ?>
                    <p class="hero__title hero__title-settled" data-hero-reveal>
                        <?php echo ese_latam_titulo(implode(' ', $ese_hero_lineas)); ?>
                    </p>
                <?php endif; ?>

                <?php if ('' !== $ese_hero['lede']) : ?>
                    <p class="hero__lede" data-hero-reveal>
                        <?php echo esc_html($ese_hero['lede']); ?>
                    </p>
                <?php endif; ?>

                <?php
                if ('' !== $ese_hero_cta['label']) {
                    ese_latam_cta_button([
                        'href' => $ese_hero_cta['href'],
                        'label' => $ese_hero_cta['label'],
                        'target' => $ese_hero_cta['target'],
                        'reveal' => true,
                    ]);
                }
                ?>

            </div>

            <?php
            // Es EL mensaje del hero, así que entra con la intro de carga (no
            // recién con el scroll, como antes) y flota como la isla. Dos
            // capas a propósito: el wrapper recibe la entrada de hero-scroll.ts
            // (data-hero-card) y la card la flotación de float.ts (data-float)
            // — cada módulo anima su propio elemento y no se pisan los transforms.
            ?>
            <?php if ($ese_hero_card) : ?>
                <div class="hero-card-wrap" data-hero-card>
                    <aside class="hero-card" data-float data-float-distance="7" data-float-duration="4.2">
                        <div class="hero-card__body">
                            <?php if ('' !== $ese_hero['stat']) : ?>
                                <p class="hero-card__stat"><?php echo esc_html($ese_hero['stat']); ?></p>
                            <?php endif; ?>
                            <?php if ('' !== $ese_hero['tag']) : ?>
                                <p class="hero-card__tag"><?php echo esc_html($ese_hero['tag']); ?></p>
                            <?php endif; ?>
                            <?php if ('' !== $ese_hero['desc']) : ?>
                                <p class="hero-card__desc"><?php echo esc_html($ese_hero['desc']); ?></p>
                            <?php endif; ?>
                        </div>
                    </aside>
                </div>
            <?php endif; ?>
        </div>
    </div>

</section>
<?php endif; ?>

<?php
// Sección "Sectores" (Figma node 3266-2331) — slider Embla de áreas de impacto.
// La lista vive en inc/template-tags.php: el submenú "Sectores" del nav
// (inc/setup.php) muestra los mismos ocho.
$ese_sectores = ese_latam_sectores();

$ese_sec_copy = [
    'kicker'  => trim((string) ese_latam_home('sectores_kicker', '')),
    'titulo'  => trim((string) ese_latam_home('sectores_titulo', '')),
    'desc'    => ese_latam_texto_rico((string) ese_latam_home('sectores_desc', '')),
    'leyenda' => trim((string) ese_latam_home('sectores_leyenda', '')),
];
?>
<?php // El slider ES la sección: sin sectores cargados no se pinta nada. ?>
<?php if ([] !== $ese_sectores) : ?>
<section id="sectores" class="hero-next bg-white relative z-10">
    <div class="hero-next__clouds" aria-hidden="true" data-reveal="fade">
        <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/clouds.webp'); ?>" alt="" decoding="async">
    </div>

    <div class="sectores" data-embla data-embla-contain="false" data-embla-loop="true" data-embla-autoplay="6000">
        <header class="sectores__header" data-reveal-header>
            <div>
                <?php if ('' !== $ese_sec_copy['kicker']) : ?>
                    <p class="type-kicker text-secondary">/ <?php echo esc_html($ese_sec_copy['kicker']); ?></p>
                <?php endif; ?>
                <?php if ('' !== $ese_sec_copy['titulo']) : ?>
                    <h2 class="type-h2 mt-6 uppercase">
                        <?php echo ese_latam_titulo($ese_sec_copy['titulo'], 'span', 'hl'); ?>
                    </h2>
                <?php endif; ?>
            </div>
            <?php if ('' !== $ese_sec_copy['desc']) : ?>
                <p class="sectores__desc"><?php echo $ese_sec_copy['desc']; ?></p>
            <?php endif; ?>
        </header>

        <div class="sectores__slider">
            <div class="sectores__legend" data-reveal="right">
                <div class="sectores__legend-text">
                    <p class="sectores__counter">
                        <span data-embla-current>01</span>
                        <?php // slider.ts lo reescribe al iniciar; se imprime igual para que
                        // el total sea correcto antes del JS y sin él. ?>
                        <span class="sectores__counter-total" data-embla-total>/<?php echo esc_html(str_pad((string) count($ese_sectores), 2, '0', STR_PAD_LEFT)); ?></span>
                    </p>
                    <?php if ('' !== $ese_sec_copy['leyenda']) : ?>
                        <p><?php echo esc_html($ese_sec_copy['leyenda']); ?></p>
                    <?php endif; ?>
                    <div class="sectores__progress" aria-hidden="true">
                        <span class="sectores__progress-bar" data-embla-progress></span>
                    </div>
                </div>
                <div class="sectores__controls">
                    <button type="button" class="embla__arrow embla__arrow--solid is-mirrored" data-embla-prev
                        aria-label="<?php esc_attr_e('Slide anterior', 'ese-latam'); ?>">
                        <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path
                                d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                    <button type="button" class="embla__arrow embla__arrow--solid" data-embla-next
                        aria-label="<?php esc_attr_e('Slide siguiente', 'ese-latam'); ?>">
                        <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path
                                d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                                fill="currentColor" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="embla__viewport" data-embla-viewport>
                <div class="embla__container" data-reveal-stagger data-reveal-delay="0.2">
                    <?php foreach ($ese_sectores as $sector): ?>
                        <article class="embla__slide sector-card">
                            <?php if ('' !== $sector['img']) : ?>
                                <img class="sector-card__img"
                                    src="<?php echo esc_url($sector['img']); ?>"
                                    alt="<?php echo esc_attr($sector['title']); ?>" loading="lazy" decoding="async">
                            <?php endif; ?>
                            <a href="<?php echo esc_url(ese_latam_sector_url($sector)); ?>" class="sector-card__chip"
                                aria-label="<?php echo esc_attr(sprintf(__('Ver más sobre %s', 'ese-latam'), $sector['title'])); ?>">
                                <svg width="16" height="13" viewBox="0 0 16 13" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path
                                        d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                                        fill="currentColor" />
                                </svg>
                            </a>
                            <div class="sector-card__body">
                                <h3 class="sector-card__title"><?php echo esc_html($sector['title']); ?></h3>
                                <?php if ('' !== $sector['desc']) : ?>
                                    <p class="sector-card__desc"><?php echo esc_html($sector['desc']); ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php // Marquee "Transformamos" (Figma node 3287-283) — texto gigante ligado al
      // scroll. Las dos pasadas van en UNA sola sección: marquee.ts anima por
      // track ([data-marquee]), no por sección, así que la segunda conserva su
      // sentido invertido y además ambas comparten el mismo rango de scroll
      // (el trigger es el padre), con lo que se espejan exactamente. ?>
<?php // Dos frases de marca, una por pista: arriba el claim (el mismo del
// pie del footer), abajo el diferenciador. Tres copias por pista para que
// el recorrido del scrub (±12%) nunca descubra el borde. ?>
<?php
// Una palabra por pista (el lema partido en dos): el track recorre solo ±12%
// con el scroll, así que una frase larga nunca llegaba a leerse entera; una
// palabra sí cabe completa en el ancho de la pantalla.
$ese_marquee = ese_latam_home('marquee_pistas');
$ese_marquee = is_array($ese_marquee)
    ? array_values(array_filter($ese_marquee, static fn (array $p): bool => '' !== trim((string) ($p['texto'] ?? ''))))
    : [];
?>
<?php if ([] !== $ese_marquee) : ?>
<section class="marquee bg-white relative z-10" aria-hidden="true">
    <?php // data-marquee="right" invierte el recorrido del scrub (ver marquee.ts).
    // Tres copias por pista para que el recorrido nunca descubra el borde. ?>
    <?php foreach ($ese_marquee as $ese_pista):
        $ese_texto = trim((string) ($ese_pista['texto'] ?? ''));
        $ese_dir   = 'right' === ($ese_pista['direccion'] ?? '') ? 'right' : '';
        ?>
        <div class="marquee__track" <?php echo '' !== $ese_dir ? 'data-marquee="right"' : 'data-marquee'; ?>>
            <span><?php echo esc_html($ese_texto); ?></span>
            <span><?php echo esc_html($ese_texto); ?></span>
            <span><?php echo esc_html($ese_texto); ?></span>
        </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<?php
// Sección "Productos" (Figma node 3287-243) — slider de foco central con
// parallax de fondo. Productos reales del CPT (mismo criterio que
// template-parts/catalogo-banner.php): cada card enlaza a su propia ficha;
// si el cliente todavía no publicó ninguno, cae al set estático de ejemplo
// (que enlaza al catálogo). Datos de card y fallback viven en
// inc/template-tags.php, compartidos con "Soluciones recomendadas" de la
// ficha de producto.
// "Productos destacados" (campo de relación) fija cuáles salen y en qué
// orden; vacío, siguen siendo los 12 más recientes del catálogo.
$ese_productos_ids = ese_latam_home('productos_destacados');
$ese_productos_ids = is_array($ese_productos_ids) ? array_map('intval', $ese_productos_ids) : [];

if ([] !== $ese_productos_ids) {
    $ese_productos_query = new WP_Query([
        'post_type'      => 'producto',
        'post_status'    => 'publish',
        'post__in'       => $ese_productos_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => count($ese_productos_ids),
    ]);
} else {
    $ese_productos_query = new WP_Query([
        'post_type'      => 'producto',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ]);
}

$ese_productos = array_map(
    static fn (WP_Post $p): array => ese_latam_producto_card_data($p->ID),
    $ese_productos_query->posts
);

$ese_prod_copy = [
    'kicker' => trim((string) ese_latam_home('productos_kicker', '')),
    'titulo' => trim((string) ese_latam_home('productos_titulo', '')),
    'desc'   => ese_latam_texto_rico((string) ese_latam_home('productos_desc', '')),
];

$ese_productos_cta = ese_latam_enlace(ese_latam_home('productos_cta'));

$ese_producto_filtros = ese_latam_home('productos_filtros');
$ese_producto_filtros = is_array($ese_producto_filtros)
    ? array_values(array_filter(array_map(
        static fn (array $f): string => trim((string) ($f['etiqueta'] ?? '')),
        $ese_producto_filtros
    ), static fn (string $f): bool => '' !== $f))
    : [];
?>
<?php // El slider vive de productos publicados: sin ninguno, no hay sección. ?>
<?php if ([] !== $ese_productos) : ?>
<section id="productos" class="productos-wrap bg-white relative z-10">
    <div class="productos">
        <?php // Skyline de fondo: el parallax (parallax.ts) va en el <img>, no
        // en el wrapper — el wrapper es el que recorta (overflow hidden) y es
        // el trigger que mide el recorrido. ?>
        <div class="productos__bg" aria-hidden="true">
            <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/productos/bg-skyline.png'); ?>" alt=""
                loading="lazy" decoding="async" data-parallax data-parallax-from="9" data-parallax-to="-9">
        </div>
        <?php // Las hojas entran con un fade+diagonal al llegar la sección, y
        // además EMERGEN de su esquina hacia el centro de la sección con el
        // scroll (parallax en diagonal: X e Y a la vez) — dos animaciones GSAP
        // independientes, por eso van en dos elementos distintos (wrapper +
        // img, ver main.css): la entrada en el <img>, el parallax continuo en
        // el wrapper. La izquierda baja hacia la derecha; la derecha (espejada
        // por CSS en el <img>) baja hacia la izquierda. ?>
        <div class="productos__leaves-wrap productos__leaves-wrap--left" aria-hidden="true" data-parallax
            data-parallax-from="-34" data-parallax-to="18" data-parallax-x-from="-34" data-parallax-x-to="18">
            <img class="productos__leaves"
                src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/productos/leaves.png'); ?>" alt=""
                loading="lazy" decoding="async" data-reveal="corner-tl">
        </div>
        <div class="productos__leaves-wrap productos__leaves-wrap--right" aria-hidden="true" data-parallax
            data-parallax-from="-34" data-parallax-to="18" data-parallax-x-from="34" data-parallax-x-to="-18">
            <img class="productos__leaves"
                src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/productos/leaves.png'); ?>" alt=""
                loading="lazy" decoding="async" data-reveal="corner-tr" data-reveal-delay="0.18">
        </div>

        <header class="productos__header" data-reveal-header>
            <?php if ('' !== $ese_prod_copy['kicker']) : ?>
                <p class="type-kicker text-white/90">/ <?php echo esc_html($ese_prod_copy['kicker']); ?></p>
            <?php endif; ?>
            <?php if ('' !== $ese_prod_copy['titulo']) : ?>
                <h2 class="productos__title">
                    <?php echo ese_latam_titulo($ese_prod_copy['titulo']); ?>
                </h2>
            <?php endif; ?>
            <?php if ('' !== $ese_prod_copy['desc']) : ?>
                <p class="productos__desc"><?php echo $ese_prod_copy['desc']; ?></p>
            <?php endif; ?>
        </header>

        <?php if ([] !== $ese_producto_filtros) : ?>
            <div class="productos__filters" data-reveal="up" role="tablist"
                aria-label="<?php esc_attr_e('Filtrar productos', 'ese-latam'); ?>">
                <?php foreach ($ese_producto_filtros as $i => $filtro): ?>
                    <button type="button" class="productos__filter<?php echo $i === 0 ? ' is-active' : ''; ?>">
                        <?php echo esc_html($filtro); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="productos__slider" data-reveal="up">
            <button type="button" class="embla__arrow embla__arrow--glass is-mirrored" data-carousel-prev
                aria-label="<?php esc_attr_e('Producto anterior', 'ese-latam'); ?>">
                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path
                        d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                        fill="currentColor" />
                </svg>
            </button>

            <?php // Cards con el diseño de "Soluciones recomendadas" (product-card--glass),
            // pero con la animación original de la home: coverflow plano (valores por
            // defecto del carrusel: rotate 0 / depth 100 / modifier 2.5). ?>
            <div class="swiper" data-product-carousel data-carousel-visible="5">
                <div class="swiper-wrapper">
                    <?php foreach ($ese_productos as $producto): ?>
                        <article class="swiper-slide product-card product-card--glass">
                            <?php if ('' !== $producto['img']) : ?>
                                <div class="product-card__media">
                                    <span class="product-card__shadow" aria-hidden="true" data-float-shadow></span>
                                    <img class="product-card__img"
                                        src="<?php echo esc_url($producto['img']); ?>"
                                        alt="<?php echo esc_attr($producto['name']); ?>" loading="lazy" decoding="async"
                                        data-float data-float-distance="14" data-float-duration="3.2">
                                </div>
                            <?php endif; ?>
                            <div class="product-card__body">
                                <p class="product-card__cat"><?php echo esc_html($producto['cat']); ?></p>
                                <h3 class="product-card__name"><?php echo esc_html($producto['name']); ?></h3>
                                <dl class="product-card__specs">
                                    <div>
                                        <dt><?php esc_html_e('Litraje', 'ese-latam'); ?></dt>
                                        <dd><?php echo esc_html($producto['litraje']); ?></dd>
                                    </div>
                                    <div>
                                        <dt><?php esc_html_e('Material', 'ese-latam'); ?></dt>
                                        <dd><?php echo esc_html($producto['material']); ?></dd>
                                    </div>
                                </dl>
                            </div>
                            <?php // Mismo criterio que template-parts/catalogo-grid.php: el chip
                            // ES el enlace (no la card entera), así el drag/swipe del slider
                            // sobre el resto de la tarjeta no compite con la navegación. ?>
                            <a class="product-card__chip" href="<?php echo esc_url($producto['href']); ?>"
                                aria-label="<?php echo esc_attr(sprintf(__('Ver %s', 'ese-latam'), $producto['name'])); ?>">
                                <svg width="16" height="13" viewBox="0 0 16 13" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path
                                        d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                                        fill="currentColor" />
                                </svg>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="button" class="embla__arrow embla__arrow--glass" data-carousel-next
                aria-label="<?php esc_attr_e('Producto siguiente', 'ese-latam'); ?>">
                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path
                        d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                        fill="currentColor" />
                </svg>
            </button>
        </div>

        <div class="productos__pagination" aria-hidden="true"></div>

        <?php if ('' !== $ese_productos_cta['label']) : ?>
            <a href="<?php echo esc_url($ese_productos_cta['href']); ?>" class="productos__cta"<?php echo ese_latam_target_attr($ese_productos_cta['target']); ?> data-reveal="up">
                <?php echo esc_html($ese_productos_cta['label']); ?>
            </a>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php
// Sección "Distribuidores" (Figma node 3323-55) — globo 3D interactivo
// (Three.js + GSAP, ver globe-scene.ts) con lista de países a la derecha:
// al hacer clic en un país, el globo gira y hace zoom hasta señalarlo.
//
// Los datos viven en inc/distribuidores.php (compartidos con la página
// "Encuentra un distribuidor"): solo Perú y México traen contenido real.
$ese_distribuidores = ese_latam_distribuidores();

$ese_dst_copy = [
    'kicker' => trim((string) ese_latam_home('distribuidores_kicker', '')),
    'titulo' => trim((string) ese_latam_home('distribuidores_titulo', '')),
    'desc'   => trim((string) ese_latam_home('distribuidores_desc', '')),
];

$ese_dst_cta = ese_latam_enlace(ese_latam_home('distribuidores_cta'));
?>
<?php // El globo y el riel viven de la lista de países: sin ninguno, no hay sección. ?>
<?php if ([] !== $ese_distribuidores) : ?>
<section id="distribuidores" class="distribuidores relative z-10" data-globe>
    <?php // El globo es el fondo de la sección: ocupa todo y el resto flota encima. ?>
    <div class="distribuidores__globe" data-lenis-prevent aria-hidden="true">
        <?php
        // Los anillos van ADENTRO del globo, no como hermanos de la sección:
        // así heredan su centro solos. Como hermanos tenían sus propias
        // coordenadas (56%/48% de la sección) y en mobile el globo pasa a ser
        // un elemento en flujo dentro de la grilla, con lo cual los anillos
        // quedaban anclados en cualquier otro lado.
        ?>
        <div class="distribuidores__rings" aria-hidden="true">
            <span></span><span></span>
        </div>

        <div data-globe-canvas
            data-earth-map="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/distribuidores/earth-cartoon.webp'); ?>"
            data-earth-specular="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/distribuidores/earth-specular.webp'); ?>"
            data-earth-normal="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/distribuidores/earth-normal.webp'); ?>">
        </div>
    </div>

    <?php // La entrada de toda la sección (anillos, globo, textos, riel y tarjeta)
    // la coreografía distribuidores-intro.ts en un solo timeline — por eso
    // estos bloques NO llevan data-reveal como el resto de la home. ?>
    <div class="distribuidores__intro">
        <?php if ('' !== $ese_dst_copy['kicker']) : ?>
            <p class="type-kicker text-white/90">/ <?php echo esc_html($ese_dst_copy['kicker']); ?></p>
        <?php endif; ?>
        <?php if ('' !== $ese_dst_copy['titulo']) : ?>
            <h2 class="distribuidores__title">
                <?php echo ese_latam_titulo($ese_dst_copy['titulo']); ?>
            </h2>
        <?php endif; ?>
        <?php if ('' !== $ese_dst_copy['desc']) : ?>
            <p class="distribuidores__desc"><?php echo esc_html($ese_dst_copy['desc']); ?></p>
        <?php endif; ?>
    </div>

    <?php // Riel de países: los 10 visibles a la vez, sin scroll anidado. ?>
    <div class="distribuidores__rail">
        <p class="distribuidores__rail-head">
            <span class="distribuidores__rail-count"><?php echo count($ese_distribuidores); ?></span>
            <?php esc_html_e('países conectados', 'ese-latam'); ?>
        </p>

        <?php
        // Mobile: el mismo set de países en un <select> nativo. El riel de 13
        // pastillas envolvía en 9 filas irregulares y ocupaba ~500px de alto,
        // así que el globo y el control nunca entraban juntos en pantalla.
        // Va en el marcado (no armado por JS) para que exista aunque el JS
        // todavía no haya corrido; CSS decide cuál de los dos se ve. El picker
        // del sistema no se puede estilar, y está bien: es el que el usuario
        // ya conoce en su teléfono.
        ?>
        <div class="distribuidores__select">
            <label class="sr-only"
                for="distribuidores-pais"><?php esc_html_e('Elegir país', 'ese-latam'); ?></label>
            <select id="distribuidores-pais" data-country-select>
                <?php foreach ($ese_distribuidores as $i => $pais): ?>
                    <option value="<?php echo esc_attr($pais['slug']); ?>" <?php selected(0, $i); ?>>
                        <?php echo esc_html($pais['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="distribuidores__select-icon" aria-hidden="true">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12L4 7H14L9 12Z" fill="currentColor" />
                </svg>
            </span>
        </div>

        <div class="distribuidores__rail-items" data-country-list role="tablist"
            aria-label="<?php esc_attr_e('Países con distribuidor', 'ese-latam'); ?>">
            <?php foreach ($ese_distribuidores as $i => $pais): ?>
                <button type="button" class="country-pill<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab"
                    aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
                    aria-controls="distribuidor-<?php echo esc_attr($pais['slug']); ?>" data-country
                    data-country-slug="<?php echo esc_attr($pais['slug']); ?>"
                    data-lat="<?php echo esc_attr($pais['lat']); ?>" data-lng="<?php echo esc_attr($pais['lng']); ?>">
                    <span class="country-pill__dot" aria-hidden="true"></span>
                    <span class="country-pill__name"><?php echo esc_html($pais['name']); ?></span>
                    <span class="country-pill__count"><?php echo count($pais['items']); ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <?php // Tarjeta flotante: los paneles se apilan en la misma celda de grilla,
    // así la tarjeta toma el alto del más largo y no salta al cambiar de país. ?>
    <div class="distribuidores__panel">
        <div class="distribuidores__panel-stack" data-country-panels>
            <?php foreach ($ese_distribuidores as $i => $pais): ?>
                <article class="country-panel<?php echo 0 === $i ? ' is-active' : ''; ?>"
                    id="distribuidor-<?php echo esc_attr($pais['slug']); ?>"
                    data-country-panel="<?php echo esc_attr($pais['slug']); ?>" role="tabpanel">
                    <p class="country-panel__kicker"><?php esc_html_e('Distribuidores en', 'ese-latam'); ?></p>
                    <h3 class="country-panel__name"><?php echo esc_html($pais['name']); ?></h3>

                    <?php if ([] !== $pais['items']) : ?>
                    <ul class="country-panel__list">
                        <?php foreach ($pais['items'] as $item): ?>
                            <li class="country-panel__item">
                                <span class="country-panel__avatar"
                                    aria-hidden="true"><?php echo esc_html(mb_substr((string) ($item['name'] ?? ''), 0, 1)); ?></span>
                                <span class="country-panel__text">
                                    <strong><?php echo esc_html($item['name'] ?? ''); ?></strong>
                                    <?php if ('' !== (string) ($item['address'] ?? '')) : ?>
                                        <span><?php echo esc_html($item['address']); ?></span>
                                    <?php endif; ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ('' !== $ese_dst_cta['label']) : ?>
        <a href="<?php echo esc_url($ese_dst_cta['href']); ?>" class="distribuidores__link"<?php echo ese_latam_target_attr($ese_dst_cta['target']); ?>>
            <span class="distribuidores__link-text"><?php echo esc_html($ese_dst_cta['label']); ?></span>
            <span class="distribuidores__link-icon" aria-hidden="true">
                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                        fill="currentColor" />
                </svg>
            </span>
        </a>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php get_template_part('template-parts/residuos'); ?>

<?php get_template_part('template-parts/certificaciones'); ?>

<?php // Sección "Contactemos" (Figma node 535-782) — CTA de cierre, al pie de todo el contenido ?>
<?php get_template_part('template-parts/contacto'); ?>

<?php get_footer(); ?>