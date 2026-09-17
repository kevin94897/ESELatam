<?php
/**
 * Hero de ficha — artículo del blog (Figma 3873-10142) y caso de éxito
 * (Figma 3891-4495): la foto destacada a pantalla completa bajo un degradado
 * oscuro, el breadcrumb, el titular a la izquierda y, bajo una línea, el
 * chip de sección con datos sueltos separados por un punto.
 *
 * Las dos pantallas son el mismo hero con distinta procedencia de los datos
 * —categoría vs. sector, lugar vs. ciudad—, así que no los deduce: los
 * recibe ya resueltos y solo decide qué pinta y qué no.
 *
 * A diferencia del hero del listado (casos-hero.php) el titular va alineado
 * a la izquierda y sin resaltado: es el título de la entrada.
 *
 * Debe ir dentro del wrapper `.nosotros[data-nosotros]` para que lo anime
 * initHero de nosotros.ts.
 *
 * @param array{
 *     titulo?: string, desc?: string, foto?: string,
 *     chip?: string, chip_url?: string, datos?: list<string>,
 *     crumb_label?: string, crumb_url?: string
 * } $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_ah = wp_parse_args($args ?? [], [
    'titulo'      => '',
    'desc'        => '',
    'foto'        => '',
    'chip'        => '',
    'chip_url'    => '',
    'datos'       => [],
    'crumb_label' => '',
    'crumb_url'   => '',
]);

$ese_ah_titulo = trim((string) $ese_ah['titulo']);
if ('' === $ese_ah_titulo) {
    return;
}

$ese_ah_datos = array_values(array_filter(
    array_map(static fn ($d): string => trim((string) $d), (array) $ese_ah['datos']),
    static fn (string $d): bool => '' !== $d
));

$ese_ah_chip = trim((string) $ese_ah['chip']);
$ese_ah_meta = '' !== $ese_ah_chip || [] !== $ese_ah_datos;
?>

<section class="nos-hero nos-hero--interna art-hero" data-nos-hero>
    <?php if ('' !== $ese_ah['foto']) : ?>
        <div class="nos-hero__bg" aria-hidden="true" data-nos-hero-bg>
            <img src="<?php echo esc_url($ese_ah['foto']); ?>" alt="" decoding="async" fetchpriority="high">
        </div>
    <?php endif; ?>
    <div class="nos-hero__shade art-hero__shade" aria-hidden="true"></div>
    <div class="nos-hero__glow" aria-hidden="true" data-nos-hero-glow></div>

    <div class="nos-hero__content art-hero__content" data-nos-hero-content>
        <?php
        get_template_part('template-parts/breadcrumbs', null, [
            'items'   => ('' !== $ese_ah['crumb_url'] && '' !== $ese_ah['crumb_label'])
                ? [['label' => $ese_ah['crumb_label'], 'url' => $ese_ah['crumb_url']]]
                : [],
            'current' => $ese_ah_titulo,
            'attrs'   => 'data-nos-hero-crumb',
        ]);
        ?>

        <div class="art-hero__body">
            <h1 class="nos-hero__title art-hero__title" data-nos-hero-title><?php echo esc_html($ese_ah_titulo); ?></h1>
            <?php if ('' !== $ese_ah['desc']) : ?>
                <p class="nos-hero__desc art-hero__desc" data-nos-hero-desc><?php echo esc_html($ese_ah['desc']); ?></p>
            <?php endif; ?>
        </div>

        <?php if ($ese_ah_meta) : ?>
            <div class="art-hero__meta">
                <?php if ('' !== $ese_ah_chip) : ?>
                    <?php if ('' !== $ese_ah['chip_url']) : ?>
                        <a class="art-hero__chip" href="<?php echo esc_url($ese_ah['chip_url']); ?>"><?php echo esc_html($ese_ah_chip); ?></a>
                    <?php else : ?>
                        <span class="art-hero__chip"><?php echo esc_html($ese_ah_chip); ?></span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php foreach ($ese_ah_datos as $ese_ah_i => $ese_ah_dato) : ?>
                    <?php if ($ese_ah_i > 0) : ?>
                        <span class="art-hero__sep" aria-hidden="true"></span>
                    <?php endif; ?>
                    <span class="art-hero__dato"><?php echo esc_html($ese_ah_dato); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
