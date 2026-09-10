<?php
/**
 * Página de Contacto — bloque 3: preguntas frecuentes (Figma 3948-9141).
 *
 * Acordeón nativo con <details>/<summary>: funciona sin JS (abrir/cerrar es
 * comportamiento del navegador) y contacto-page.ts solo le agrega la
 * animación de altura y el comportamiento de "una abierta a la vez".
 *
 * Las respuestas 2 a 6 son un borrador redactado a partir del contenido
 * técnico del resto del sitio (EN 840, RAL, HDPE): el Figma solo trae el
 * texto de la primera. Revisar con el cliente antes de publicar.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_faqs = [
    [
        'q' => __('¿Qué certificaciones avalan la durabilidad de sus contenedores?', 'ese-latam'),
        'a' => __('Todos nuestros contenedores de carga lateral, trasera y soterrada están fabricados bajo la estricta normativa europea EN 840 y cuentan con la marca de calidad RAL (otorgada por laboratorios independientes alemanes como SKZ o TÜV SÜD). Esto garantiza que soportan más de 12,000 ciclos de elevación, resistencia estructural ante impactos mecánicos a temperaturas extremas (-40°C a +80°C) y poseen protección avanzada contra radiación ultravioleta.', 'ese-latam'),
    ],
    [
        'q' => __('¿Cuál es el tiempo promedio de entrega para pedidos municipales o industriales?', 'ese-latam'),
        'a' => __('Para stock disponible en Perú despachamos entre 5 y 10 días hábiles. Los pedidos de gran volumen o con personalización se producen en fábrica y se coordinan por cronograma: el plazo referencial es de 8 a 12 semanas desde la orden de compra, incluyendo tránsito marítimo y nacionalización. En cada caso entregamos un cronograma firmado antes de iniciar la producción.', 'ese-latam'),
    ],
    [
        'q' => __('¿Cuentan con garantía contra actos vandálicos, incendios o impactos de camión?', 'ese-latam'),
        'a' => __('La garantía de fábrica cubre defectos de material y fabricación por 5 años, e incluye la resistencia estructural declarada en la norma EN 840. Los daños por vandalismo, incendio provocado o maniobras incorrectas del camión recolector no entran en la garantía, pero mantenemos stock de repuestos (tapas, ejes, ruedas y pedales) para reponer piezas sin cambiar el contenedor completo.', 'ese-latam'),
    ],
    [
        'q' => __('¿Ofrecen asesoría técnica en la diagramación de planos y rutas de acopio?', 'ese-latam'),
        'a' => __('Sí. Nuestro equipo realiza el diagnóstico de generación de residuos, propone el dimensionamiento de puntos de acopio y entrega los planos de distribución junto con la frecuencia de recolección recomendada. También acompañamos la puesta en marcha y capacitamos al personal operativo.', 'ese-latam'),
    ],
    [
        'q' => __('¿Se pueden personalizar los contenedores corporativos con colores y logos modernos?', 'ese-latam'),
        'a' => __('Sí. Trabajamos la carta de colores RAL para el cuerpo y la tapa, y aplicamos logotipos y señalética de segregación por serigrafía o vinilo de alta durabilidad. Para pedidos corporativos enviamos una prueba de color y un montaje digital antes de producir.', 'ese-latam'),
    ],
    [
        'q' => __('¿Los contenedores son compatibles con los camiones recolectores en Perú y LATAM?', 'ese-latam'),
        'a' => __('Sí. Nuestros modelos usan los enganches estándar de la norma EN 840 (peine DIN y trunnion), que son los que emplean las flotas de carga trasera y lateral que operan en la región. Si tu flota tiene un sistema particular, validamos la compatibilidad antes de cotizar.', 'ese-latam'),
    ],
];
?>

<section class="ctc-faq">
    <header class="ctc-faq__header" data-reveal-header>
        <p class="type-kicker text-secondary">/ <?php esc_html_e('Respondemos dudas', 'ese-latam'); ?></p>
        <h2 class="type-h2 uppercase">
            <?php esc_html_e('Preguntas', 'ese-latam'); ?><br>
            <span class="hl"><?php esc_html_e('frecuentes', 'ese-latam'); ?></span>
        </h2>
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
                    <p><?php echo esc_html($ese_faq['a']); ?></p>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
</section>
