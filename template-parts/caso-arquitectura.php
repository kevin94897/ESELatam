<?php
/**
 * Caso de éxito — "El reto, problema y solución" (Figma 3891-4495):
 * encabezado centrado, una fila de pastillas y, debajo, el panel de la
 * pastilla activa: foto a la izquierda y a la derecha la etiqueta, el
 * título, el texto y una nota al pie.
 *
 * El cambio de panel lo maneja tab-panels.ts (`data-tabs`), igual que los
 * "Desafíos" del single de sector.
 *
 * @param array{
 *     kicker?: string, title?: string, desc?: string,
 *     items?: list<array{label: string, kicker: string, title: string, texto: string, nota: string, img: string}>
 * } $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_ca = wp_parse_args($args ?? [], [
    'kicker' => '',
    'title'  => '',
    'desc'   => '',
    'items'  => [],
]);

if (empty($ese_ca['items'])) {
    return;
}
?>

<section class="caso-arq" id="arquitectura" data-tabs>
    <header class="caso-arq__header" data-reveal-header>
        <?php if ('' !== $ese_ca['kicker']) : ?>
            <p class="type-kicker text-secondary">/ <?php echo esc_html($ese_ca['kicker']); ?></p>
        <?php endif; ?>
        <?php if ('' !== $ese_ca['title']) : ?>
            <h2 class="caso-arq__title">
                <?php echo ese_latam_titulo($ese_ca['title'], 'span', 'caso-arq__title-accent'); ?>
            </h2>
        <?php endif; ?>
        <?php if ('' !== $ese_ca['desc']) : ?>
            <p class="caso-arq__desc" data-reveal-desc>
                <?php echo wp_kses($ese_ca['desc'], ['span' => ['class' => []], 'strong' => [], 'em' => [], 'br' => []]); ?>
            </p>
        <?php endif; ?>
    </header>

    <?php if (count($ese_ca['items']) > 1) : ?>
        <div class="caso-arq__pills" role="tablist" data-reveal="up">
            <?php foreach ($ese_ca['items'] as $ese_ca_i => $ese_ca_item) : ?>
                <button type="button" role="tab"
                        class="caso-arq__pill<?php echo 0 === $ese_ca_i ? ' is-active' : ''; ?>"
                        aria-selected="<?php echo 0 === $ese_ca_i ? 'true' : 'false'; ?>"
                        data-tab="<?php echo esc_attr('arq-' . $ese_ca_i); ?>">
                    <?php echo esc_html($ese_ca_item['label']); ?>
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php foreach ($ese_ca['items'] as $ese_ca_i => $ese_ca_item) : ?>
        <?php // Sin `hidden`: tab-panels.ts solo mueve `.is-active` y es el CSS
        // el que esconde a los demás (igual que Desafíos o Blue Angel). ?>
        <div class="caso-arq__panel<?php echo 0 === $ese_ca_i ? ' is-active' : ''; ?>"
             data-tab-panel="<?php echo esc_attr('arq-' . $ese_ca_i); ?>">
            <?php if ('' !== $ese_ca_item['img']) : ?>
                <figure class="caso-arq__media">
                    <img src="<?php echo esc_url($ese_ca_item['img']); ?>" alt="" loading="lazy" decoding="async">
                </figure>
            <?php endif; ?>

            <div class="caso-arq__texto">
                <?php if ('' !== $ese_ca_item['kicker']) : ?>
                    <p class="caso-arq__badge">
                        <span class="caso-arq__badge-punto" aria-hidden="true"></span>
                        <?php echo esc_html($ese_ca_item['kicker']); ?>
                    </p>
                <?php endif; ?>
                <?php if ('' !== $ese_ca_item['title']) : ?>
                    <h3 class="caso-arq__panel-title"><?php echo esc_html($ese_ca_item['title']); ?></h3>
                <?php endif; ?>
                <?php if ('' !== $ese_ca_item['texto']) : ?>
                    <p class="caso-arq__panel-desc"><?php echo esc_html($ese_ca_item['texto']); ?></p>
                <?php endif; ?>
                <?php if ('' !== $ese_ca_item['nota']) : ?>
                    <p class="caso-arq__nota">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M12 10.75v5.5M12 7.9v.2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <span><?php echo esc_html($ese_ca_item['nota']); ?></span>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>
