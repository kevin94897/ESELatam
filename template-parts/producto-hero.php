<?php
/**
 * Ficha de producto — hero "panel de configuración". Se llama desde
 * single-producto.php DENTRO del loop (the_post() ya corrió), así que lee el
 * post actual directamente — mismo criterio autocontenido que
 * template-parts/catalogo-banner.php.
 *
 * Composición (desktop):
 *
 *   breadcrumb ────────────────────────────────────────
 *   [ panel de configuración ]   [ escena isla+producto ]
 *   [ barra de atributos: capacidad · colores · specs ACF … ]
 *
 * Solo el producto y sus atributos, sin columnas laterales extra. En mobile
 * todo se apila: breadcrumb → escena → panel → atributos.
 *
 * Casi todo sale de los campos ACF de inc/acf-productos.php. Mientras el
 * cliente no los complete, cada bloque cae a un valor estático que calca el
 * diseño — así la ficha se ve entera desde el día uno y se va "poblando"
 * sola a medida que cargan datos reales, sin tocar esta plantilla.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_producto_id = get_the_ID();

// ---------- Datos ACF (con fallback estático al diseño) ----------

$ese_desc = get_field('descripcion_corta') ?: get_the_excerpt();

// Las capacidades y los colores salen de los modelos, no del producto: ver
// más abajo, donde se arma $ese_modelos.

$ese_compra = ese_latam_enlace(
    ese_latam_campo('enlace_compra', $ese_producto_id, null),
    __('Comprar', 'ese-latam'),
    ese_latam_contacto_url()
);
$ese_ficha      = get_field('ficha_tecnica');
// El campo devuelve la URL; se acepta también el array por si cambia el return_format.
$ese_ficha_url  = is_array($ese_ficha) ? (string) ($ese_ficha['url'] ?? '') : (string) $ese_ficha;

// Sin foto propia no se pone una ajena: antes caía a un contenedor de 3
// ruedas del theme, así que la Papelera y el Soterrado se mostraban con una
// pieza que no era la suya.
$ese_img_default = (string) (get_the_post_thumbnail_url($ese_producto_id, 'large') ?: '');

// ---------- Modelos ----------
//
// Un producto del catálogo es una FAMILIA ("Papeleras") y agrupa a sus
// modelos (Open Dinova, Campus Goool, Venta…). Cada modelo trae sus propias
// capacidades, sus colores y su matriz de fotos; el agrupado vive en
// inc/template-tags.php porque las tarjetas resuelven su foto igual.
//
// Con un solo modelo la lista no se dibuja y la ficha se ve como antes.
$ese_modelos = ese_latam_producto_modelos($ese_producto_id);

// Sin nada cargado, el fallback estático del diseño: así la ficha se ve
// entera desde el día uno y se va poblando a medida que cargan datos.
if ([] === $ese_modelos) {
    $ese_modelos = [[
        'nombre'      => '',
        'descripcion' => '',
        'litrajes'    => array_map(
            static fn (string $v): array => ['valor' => $v, 'predeterminado' => '120L' === $v],
            ['80L', '120L', '180L', '240L', '360L']
        ),
        'colores'     => [],
    ]];
}

/** Capacidad marcada por defecto dentro de un modelo. */
$ese_litraje_de = static function (array $modelo): string {
    foreach ($modelo['litrajes'] as $item) {
        if ($item['predeterminado']) {
            return $item['valor'];
        }
    }
    return (string) ($modelo['litrajes'][0]['valor'] ?? '');
};

$ese_modelo_activo   = $ese_modelos[0];
$ese_litraje_default = $ese_litraje_de($ese_modelo_activo);

// Foto inicial: la del primer color del primer modelo en su capacidad por
// defecto; si no, la foto de ese color, y recién al final la destacada. Sin
// esto el hero pintaba la destacada y saltaba al primer clic.
$ese_img_inicial = $ese_img_default;
if (isset($ese_modelo_activo['colores'][0])) {
    $ese_primer_color = $ese_modelo_activo['colores'][0];
    $ese_img_inicial  = $ese_primer_color['imgs'][$ese_litraje_default]
        ?? ($ese_primer_color['img'] ?: $ese_img_default);
}

// Lo que viaja a product-config.ts: la lista entera de modelos, para que el
// panel pueda cambiar de modelo sin volver al servidor.
$ese_modelos_data = array_map(
    static fn (array $m): array => [
        'nombre'      => $m['nombre'],
        'descripcion' => $m['descripcion'],
        'litrajes'    => array_values(array_map(
            static fn (array $l): array => ['valor' => $l['valor'], 'default' => $l['predeterminado']],
            $m['litrajes']
        )),
        'colores'     => array_values(array_map(
            static fn (array $c): array => [
                'nombre' => $c['nombre'],
                'color'  => $c['color'],
                'img'    => $c['img'],
                'imgs'   => (object) $c['imgs'],
            ],
            $m['colores']
        )),
    ],
    $ese_modelos
);

// Kicker: la categoría real del producto (taxonomía producto_categoria).
$ese_terms  = get_the_terms($ese_producto_id, 'producto_categoria');
$ese_kicker = is_array($ese_terms) && ! empty($ese_terms)
    ? $ese_terms[0]->name
    : __('Producto ESE Latam', 'ese-latam');

// El título va en dos renglones: la primera palabra en peso light y el
// resto en bold celeste (ver .producto-hero__title-accent).
$ese_titulo       = get_the_title();
$ese_titulo_parts = explode(' ', $ese_titulo, 2);
$ese_titulo_l1    = $ese_titulo_parts[0];
$ese_titulo_l2    = $ese_titulo_parts[1] ?? '';

?>

<section class="producto-hero" data-product-config data-producto-hero
    data-modelos="<?php echo esc_attr(wp_json_encode($ese_modelos_data)); ?>">
    <?php // Fondo propio de la ficha: degradado radial de marca, no la foto de
    // montaña del banner de catálogo. Al no cargar imagen, el hero pinta en
    // el primer frame y el LCP pasa a ser el título. ?>
    <?php // Marca "hay JS": solo entonces el CSS esconde las partes del hero
    // hasta que producto-hero-intro.ts las anime (sin JS quedan visibles). ?>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>
    <div class="producto-hero__bg" aria-hidden="true"></div>
    <div class="producto-hero__dots" aria-hidden="true"></div>
    <div class="catalogo-banner__grid" aria-hidden="true"></div>

    <div class="producto-hero__inner">
        <?php
        get_template_part('template-parts/breadcrumbs', null, [
            'items'   => [[
                'label' => __('Productos', 'ese-latam'),
                'url'   => get_post_type_archive_link('producto'),
            ]],
            'current' => $ese_titulo,
            'class'   => 'producto-hero__crumb crumbs--light',
        ]);
        ?>

        <?php // La entrada de TODO el hero (panel, escena, atributos) la coreografía
        // producto-hero-intro.ts en un solo timeline — por eso acá no hay
        // data-reveal como en el resto del sitio. ?>
        <div class="producto-hero__panel">
            <div class="producto-hero__kicker">
                <span><?php echo esc_html($ese_kicker); ?></span>
            </div>

            <h1 class="producto-hero__title">
                <span class="producto-hero__title-light"><?php echo esc_html($ese_titulo_l1); ?></span>
                <?php if ('' !== $ese_titulo_l2) : ?>
                    <span class="producto-hero__title-accent"><?php echo esc_html($ese_titulo_l2); ?></span>
                <?php endif; ?>
            </h1>

            <?php if ($ese_desc) : ?>
                <p class="producto-hero__desc"><?php echo esc_html($ese_desc); ?></p>
            <?php endif; ?>

            <?php
            // Las píldoras y las muestras de TODOS los modelos se imprimen de
            // una vez y las del modelo inactivo van con `hidden`: así la ficha
            // es correcta sin JavaScript, y product-config.ts solo alterna ese
            // atributo en vez de rearmar el panel a mano.
            ?>
            <?php if (count($ese_modelos) > 1) : ?>
                <div class="producto-hero__group">
                    <p class="producto-hero__group-label">
                        <span class="producto-hero__group-num"></span>
                        <span class="producto-hero__group-text"><?php esc_html_e('Elige el modelo', 'ese-latam'); ?></span>
                        <span class="producto-hero__group-value" data-producto-modelo-nombre><?php echo esc_html($ese_modelo_activo['nombre']); ?></span>
                    </p>
                    <div class="producto-hero__models">
                        <?php foreach ($ese_modelos as $ese_mi => $ese_modelo) : ?>
                            <button type="button"
                                class="producto-hero__model<?php echo 0 === $ese_mi ? ' is-active' : ''; ?>"
                                data-producto-modelo="<?php echo (int) $ese_mi; ?>">
                                <?php echo esc_html($ese_modelo['nombre']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="producto-hero__group" data-producto-grupo="litraje">
                <p class="producto-hero__group-label">
                    <span class="producto-hero__group-num"></span>
                    <span class="producto-hero__group-text"><?php esc_html_e('Elige la capacidad', 'ese-latam'); ?></span>
                </p>
                <div class="producto-hero__pills">
                    <?php foreach ($ese_modelos as $ese_mi => $ese_modelo) :
                        $ese_def = $ese_litraje_de($ese_modelo);
                        $ese_visto = false;
                        foreach ($ese_modelo['litrajes'] as $ese_litraje) :
                            $ese_activo = ! $ese_visto && $ese_litraje['valor'] === $ese_def;
                            if ($ese_activo) {
                                $ese_visto = true;
                            }
                            ?>
                            <button type="button"
                                class="producto-hero__pill<?php echo $ese_activo ? ' is-active' : ''; ?>"
                                data-producto-modelo="<?php echo (int) $ese_mi; ?>"
                                data-producto-litraje="<?php echo esc_attr($ese_litraje['valor']); ?>"
                                <?php echo 0 === $ese_mi ? '' : 'hidden'; ?>>
                                <?php echo esc_html($ese_litraje['valor']); ?>
                            </button>
                        <?php endforeach;
                    endforeach; ?>
                </div>
            </div>

            <?php
            $ese_hay_colores = (bool) array_filter($ese_modelos, static fn (array $m): bool => [] !== $m['colores']);
            ?>
            <?php if ($ese_hay_colores) : ?>
                <div class="producto-hero__group" data-producto-grupo="color">
                    <p class="producto-hero__group-label">
                        <span class="producto-hero__group-num"></span>
                        <span class="producto-hero__group-text"><?php esc_html_e('Selecciona el color', 'ese-latam'); ?></span>
                        <span class="producto-hero__group-value" data-producto-color-name><?php echo esc_html($ese_modelo_activo['colores'][0]['nombre'] ?? ''); ?></span>
                    </p>
                    <div class="producto-hero__swatches">
                        <?php foreach ($ese_modelos as $ese_mi => $ese_modelo) : ?>
                            <?php foreach ($ese_modelo['colores'] as $ese_i => $ese_color) : ?>
                                <button type="button"
                                    class="producto-hero__swatch<?php echo 0 === $ese_i ? ' is-active' : ''; ?>"
                                    style="--swatch: <?php echo esc_attr($ese_color['color']); ?>;"
                                    data-producto-modelo="<?php echo (int) $ese_mi; ?>"
                                    data-producto-color="<?php echo (int) $ese_i; ?>"
                                    title="<?php echo esc_attr($ese_color['nombre']); ?>"
                                    aria-label="<?php echo esc_attr($ese_color['nombre']); ?>"
                                    <?php echo 0 === $ese_mi ? '' : 'hidden'; ?>></button>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="producto-hero__actions">
                <?php
                ese_latam_cta_button([
                    'href'   => $ese_compra['href'],
                    'label'  => $ese_compra['label'],
                    'target' => $ese_compra['target'],
                    'class'  => 'hero-cta--light',
                ]);
                ?>

                <a class="producto-hero__ficha"
                    href="<?php echo esc_url($ese_ficha_url ?: '#'); ?>"
                    <?php echo $ese_ficha_url ? 'download' : 'aria-disabled="true"'; ?>>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <?php echo ese_latam_icon_svg('download'); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </svg>
                    <?php esc_html_e('Ficha técnica', 'ese-latam'); ?>
                </a>
            </div>
        </div>

        <?php // Escena: mismo patrón que template-parts/catalogo-banner.php — la sombra
        // debe ser HERMANA del <img data-float> dentro del mismo wrapper (float.ts la
        // busca en el parentElement del elemento que flota). El foco de luz y los
        // anillos son solo CSS, dan profundidad detrás del producto. ?>
        <div class="producto-hero__scene">
            <div class="producto-hero__spot" aria-hidden="true"></div>
            <div class="producto-hero__ring" aria-hidden="true"></div>

            <div class="producto-hero__pedestal" aria-hidden="true">
                <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/island/island-catalogo.webp'); ?>"
                    alt="" loading="lazy" decoding="async">
            </div>

            <?php if ('' !== $ese_img_inicial) : ?>
                <div class="producto-hero__product" data-product>
                    <span class="product-card__shadow" aria-hidden="true" data-float-shadow></span>
                    <img data-producto-img src="<?php echo esc_url($ese_img_inicial); ?>"
                        alt="<?php echo esc_attr($ese_titulo); ?>" decoding="async" fetchpriority="high" data-float
                        data-float-distance="12" data-float-duration="3.4">
                </div>
            <?php endif; ?>
        </div>


    </div>

    <?php // Mismo cue que el hero de Nosotros (.nos-hero__scroll): línea vertical
    // con degradado que "cae" en bucle + la palabra Scroll, abajo a la derecha. ?>
    <a href="#atributos" class="producto-hero__scroll-hint" aria-label="<?php esc_attr_e('Ir a los datos clave', 'ese-latam'); ?>">
        <span class="producto-hero__scroll-line" aria-hidden="true"></span>
        <span class="producto-hero__scroll-text"><?php esc_html_e('Scroll', 'ese-latam'); ?></span>
    </a>
</section>
