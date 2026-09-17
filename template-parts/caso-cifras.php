<?php
/**
 * Caso de éxito — franja de resultados (Figma 3891-4495): tres cifras
 * grandes en azul con su etiqueta debajo, repartidas en el ancho.
 *
 * Los valores son texto y no número a propósito: en el diseño son "-45%",
 * "1,200m²" y "50 seg", así que el formato lo escribe quien edita.
 *
 * @param array{items?: list<array{valor: string, etiqueta: string}>} $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_cc = wp_parse_args($args ?? [], ['items' => []]);

if (empty($ese_cc['items'])) {
    return;
}
?>

<section class="caso-cifras" data-reveal-stagger>
    <ul class="caso-cifras__lista">
        <?php foreach ($ese_cc['items'] as $ese_cc_item) : ?>
            <li class="caso-cifra">
                <p class="caso-cifra__valor"><?php echo esc_html($ese_cc_item['valor']); ?></p>
                <?php if ('' !== $ese_cc_item['etiqueta']) : ?>
                    <p class="caso-cifra__label"><?php echo esc_html($ese_cc_item['etiqueta']); ?></p>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
