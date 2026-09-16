<?php
/**
 * Sección "Contactemos" (Figma node 535-782) — CTA de cierre, al pie de todo el contenido.
 *
 * Compartida por la home, el catálogo, la ficha de producto y Nosotros.
 * El copy por defecto sale de ESE Latam → Contactemos; cada página puede
 * personalizarlo desde su propio editor ("Secciones compartidas"), y las
 * plantillas siguen pudiendo pisarlo con `$args` — ver la precedencia
 * documentada en inc/pcf.php. Sin nada cargado imprime exactamente lo de
 * siempre (el copy de la home).
 *
 * @param array{
 *     class?: string,           Clase extra para <section> (p. ej. 'contacto--upper')
 *     kicker?: string,
 *     heading?: string,         Titular; el tramo |entre barras| va en bold y el "?" final va aparte
 *     question?: bool,          false para NO cerrar el titular con "?" (p. ej. cuando el "?" ya va dentro del heading)
 *     desc?: string,
 *     bg?: string,              URL de la foto de fondo
 *     cta_label?: string,
 *     cta_href?: string,
 * } $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_contacto_args = wp_parse_args($args ?? [], ['class' => '']);

// El botón viaja como un solo valor (igual que el campo `link` de PCF) para
// que la precedencia lo trate como una unidad: una plantilla que solo cambia
// el texto no debería heredar a medias el destino de otro nivel.
if (isset($ese_contacto_args['cta_label']) || isset($ese_contacto_args['cta_href'])) {
    $ese_contacto_args['cta'] = [
        'title' => (string) ($ese_contacto_args['cta_label'] ?? ''),
        'url'   => (string) ($ese_contacto_args['cta_href'] ?? ''),
    ];
}

$ese_contacto = ese_latam_seccion_args(
    'contacto',
    $ese_contacto_args,
    [
        'kicker'         => '',
        'heading'        => '',
        'question'       => true,
        'desc'           => '',
        'bg'             => '',
        'cta'            => null,
    ],
    [
        'kicker'         => 'kicker',
        'heading'        => 'titulo',
        'question'       => 'pregunta',
        'desc'           => 'desc',
        'bg'             => 'fondo',
        'cta'            => 'cta',
    ],
    ['question']
);

$ese_contacto_cta = ese_latam_enlace($ese_contacto['cta']);
$ese_contacto_bg  = ese_latam_img_url($ese_contacto['bg']);

// Sin nada que decir no se pinta la sección: ni el fondo ni la tarjeta vacía.
if ('' === trim((string) $ese_contacto['heading'])
    && '' === trim((string) $ese_contacto['desc'])
    && '' === $ese_contacto_cta['label']) {
    return;
}
?>

<section id="contacto" class="<?php echo esc_attr(trim('contacto ' . $ese_contacto_args['class'])); ?>">
    <?php if ('' !== $ese_contacto_bg) : ?>
        <div class="contacto__bg" aria-hidden="true">
            <img src="<?php echo esc_url($ese_contacto_bg); ?>" alt="" loading="lazy" decoding="async">
        </div>
    <?php endif; ?>

    <div class="contacto__card" data-reveal="fade">
        <?php if ('' !== trim((string) $ese_contacto['kicker'])) : ?>
            <p class="type-kicker text-white">/ <?php echo esc_html($ese_contacto['kicker']); ?></p>
        <?php endif; ?>

        <?php // Todo en una línea a propósito: cualquier salto entre </strong> y el
        // "?" se renderiza como un espacio ("RESPONSABLE ?"). ?>
        <?php if ('' !== trim((string) $ese_contacto['heading'])) : ?>
            <h2 class="contacto__heading" data-contacto-heading><?php echo ese_latam_titulo((string) $ese_contacto['heading']); ?><?php echo $ese_contacto['question'] ? '?' : ''; ?></h2>

            <hr class="contacto__divider">
        <?php endif; ?>

        <div class="contacto__row">
            <?php if ('' !== trim((string) $ese_contacto['desc'])) : ?>
                <p class="contacto__desc">
                    <?php echo esc_html($ese_contacto['desc']); ?>
                </p>
            <?php endif; ?>
            <?php
            if ('' !== $ese_contacto_cta['label']) {
                ese_latam_cta_button([
                    'href' => $ese_contacto_cta['href'],
                    'label' => $ese_contacto_cta['label'],
                    'target' => $ese_contacto_cta['target'],
                ]);
            }
            ?>
        </div>
    </div>
</section>
