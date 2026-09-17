<?php
/**
 * Página de Contacto — bloque 3: preguntas frecuentes (Figma 3948-9141).
 *
 * Acordeón nativo con <details>/<summary>: funciona sin JS (abrir/cerrar es
 * comportamiento del navegador) y contacto-page.ts solo le agrega la
 * animación de altura y el comportamiento de "una abierta a la vez".
 *
 * Las preguntas salen del módulo "Preguntas frecuentes"; el antetítulo y el
 * titular, de la página.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_faqs = [];

foreach (ese_latam_modulo_entradas('faq') as $ese_pregunta) {
    $ese_faqs[] = [
        'q' => get_the_title($ese_pregunta->ID),
        'a' => ese_latam_texto_rico((string) ese_latam_campo('respuesta', $ese_pregunta->ID, '')),
    ];
}

if ([] === $ese_faqs) {
    return;
}

$ese_id     = (int) get_queried_object_id();
$ese_kicker = (string) ese_latam_campo('ctc_faq_kicker', $ese_id, '');
$ese_titulo = (string) ese_latam_campo('ctc_faq_titulo', $ese_id, '');
?>

<section class="ctc-faq">
    <header class="ctc-faq__header" data-reveal-header>
        <?php if ('' !== $ese_kicker) : ?>
            <p class="type-kicker text-secondary">/ <?php echo esc_html($ese_kicker); ?></p>
        <?php endif; ?>
        <?php if ('' !== $ese_titulo) : ?>
            <h2 class="type-h2 uppercase">
                <?php echo ese_latam_titulo($ese_titulo, 'span', 'hl'); ?>
            </h2>
        <?php endif; ?>
    </header>

    <div class="ctc-faq__list" data-ctc-faq>
        <?php foreach ($ese_faqs as $ese_i => $ese_faq) : ?>
            <details class="ctc-faq__item" data-ctc-faq-item <?php echo 0 === $ese_i ? 'open' : ''; ?>>
                <summary class="ctc-faq__q">
                    <span><?php echo esc_html($ese_faq['q']); ?></span>
                    <span class="ctc-faq__icon" aria-hidden="true">
                        <svg width="10" height="5" viewBox="0 0 10 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 5L0 0H10L5 5Z" fill="currentColor" />
                        </svg>
                    </span>
                </summary>
                <div class="ctc-faq__a" data-ctc-faq-panel>
                    <p><?php echo $ese_faq['a']; ?></p>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
</section>
