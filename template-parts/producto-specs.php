<?php
/**
 * Ficha de producto — sección de especificaciones técnicas. Autocontenido,
 * igual que producto-hero.php: se llama desde single-producto.php dentro del
 * loop, lee el post actual directamente.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

// Mismo cálculo que producto-hero.php (litraje activo, a partir del mismo
// campo ACF `litrajes`): se repite acá en vez de recibirlo por parámetro
// para que este archivo no dependa del orden en que se llamen las partes,
// pero al salir de la misma fuente de datos no puede desincronizarse.
$ese_litrajes = get_field('litrajes');
if (! is_array($ese_litrajes) || empty($ese_litrajes)) {
    $ese_litrajes = array_map(
        static fn (string $v): array => ['valor' => $v, 'predeterminado' => '120L' === $v],
        ['80L', '120L', '140L', '180L', '240L', '360L']
    );
}

$ese_litraje_default = $ese_litrajes[0]['valor'] ?? '';
foreach ($ese_litrajes as $ese_lit) {
    if (! empty($ese_lit['predeterminado'])) {
        $ese_litraje_default = $ese_lit['valor'];
        break;
    }
}

$ese_caracteristicas = get_field('caracteristicas');
if (! is_array($ese_caracteristicas) || empty($ese_caracteristicas)) {
    $ese_caracteristicas = [
        ['etiqueta' => __('Volumen', 'ese-latam'),      'valor' => $ese_litraje_default],
        ['etiqueta' => __('Peso', 'ese-latam'),         'valor' => '9 KG'],
        ['etiqueta' => __('Carga máxima', 'ese-latam'), 'valor' => '50 KG'],
        ['etiqueta' => __('Material', 'ese-latam'),     'valor' => __('Polietileno de alta densidad (HDPE)', 'ese-latam')],
        ['etiqueta' => __('Ruedas', 'ese-latam'),       'valor' => __('2 ruedas de jebe Ø 200 mm', 'ese-latam')],
    ];
}

// Ícono de cada spec por palabra clave de la etiqueta (mismo criterio que
// template-parts/catalogo-grid.php usa para material/norma).
$ese_spec_icon = static function (string $etiqueta): string {
    $e = mb_strtolower($etiqueta);
    foreach (['volumen' => 'volumen', 'litraje' => 'volumen', 'peso' => 'peso',
              'carga' => 'carga', 'material' => 'material', 'rueda' => 'ruedas'] as $needle => $icon) {
        if (false !== mb_strpos($e, $needle)) {
            return $icon;
        }
    }
    return 'material';
};
?>

<section class="producto-specs">
    <?php // Shape divider: la onda se dibuja DENTRO de specs con el color del
    // hero (#001545, la parada exterior de su degradado radial) en vez de
    // colgar hacia arriba. Así no tapa el indicador "Desliza para ver más",
    // que vive pegado al pie del hero. Dos capas para dar profundidad, igual
    // que el fondo del hero (degradado + puntos + rejilla). ?>
    <div class="producto-specs__divider" aria-hidden="true">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none" focusable="false">
            <path class="producto-specs__divider-back"
                d="M0,0 H1440 V60 C1230,114 1060,52 760,88 C460,124 260,34 0,84 Z" />
            <path class="producto-specs__divider-front"
                d="M0,0 H1440 V44 C1200,100 1020,38 720,76 C420,114 240,14 0,68 Z" />
        </svg>
    </div>

    <div class="producto-specs__inner">
        <header class="producto-specs__header" data-reveal-header>
            <p class="type-kicker text-white/90">/ <?php esc_html_e('Especificaciones', 'ese-latam'); ?></p>
        </header>

        <?php // Mismo markup en los dos tamaños: bajo 64rem Embla lo mueve como
        // carril; arriba se desactiva (data-embla-active-below) y el track se
        // maqueta como grilla desde CSS. Clases propias en vez de .embla__*
        // para no heredar el gap y el flex-basis de 3-por-vista del carrusel
        // genérico. ?>
        <div class="producto-specs__cards" data-embla data-embla-active-below="64rem"
            data-embla-contain="false">
            <div class="producto-specs__viewport" data-embla-viewport>
                <div class="producto-specs__track" data-reveal-stagger>
                    <?php foreach ($ese_caracteristicas as $ese_caract) : ?>
                        <article class="producto-spec">
                            <span class="producto-spec__icon" aria-hidden="true">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <?php echo ese_latam_icon_svg($ese_spec_icon((string) $ese_caract['etiqueta'])); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                                </svg>
                            </span>
                            <p class="producto-spec__label"><?php echo esc_html($ese_caract['etiqueta']); ?></p>
                            <p class="producto-spec__value"
                                <?php echo 0 === mb_strpos(mb_strtolower((string) $ese_caract['etiqueta']), 'volumen') ? 'data-producto-volumen' : ''; ?>>
                                <?php echo esc_html($ese_caract['valor']); ?>
                            </p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="embla__dots producto-specs__dots" data-embla-dots></div>
        </div>
    </div>
</section>
