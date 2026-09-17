<?php
/**
 * Página Certificaciones — "Estándar Blue Angel" (Figma 3807-5473): franja
 * celeste degradada con titular blanco y el ciclo de fabricación como
 * CARRUSEL EN ARCO, a imagen de la sección "Economía circular" del sitio
 * anterior (contenedoresdebasura-ese.com#economia-circular):
 *
 *  - Los pasos se reparten sobre un arco; el del centro va grande y es el
 *    activo, con su nombre debajo (dentro del arco).
 *  - Flechas prev/next rotan el ciclo; clic (o Enter) en un paso lo trae al
 *    centro; en táctil se puede deslizar. Autoplay cada 4s hasta que el
 *    usuario interactúa. Un arco de progreso con marcador "»" indica en qué
 *    punto del ciclo se está.
 *  - Todo lo mueve src/ts/modules/blue-angel.ts (lo carga main.ts cuando
 *    existe [data-bangel]). Sin JS los pasos quedan en fila, visibles.
 *
 * Los renders son los del Figma (assets/imgs/certificaciones/blue-angel/);
 * el copy de cada paso es propuesto — solo "Fabricación de contenedores
 * nuevos" viene del diseño.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Las etapas del ciclo se editan en la página de Certificaciones
// (inc/pcf-certificaciones.php).
$ese_ba_id = (int) get_queried_object_id();
$ese_ba_pasos = [];

foreach ((array) ese_latam_campo('certpag_ba_pasos', $ese_ba_id, []) as $ese_fila) {
    $ese_label = trim((string) ($ese_fila['label'] ?? ''));
    if ('' === $ese_label) {
        continue;
    }
    $ese_ba_pasos[] = [
        'label' => $ese_label,
        'img' => ese_latam_img_url($ese_fila['img'] ?? ''),
    ];
}

if ([] === $ese_ba_pasos) {
    return;
}

$ese_ba_kicker = trim((string) ese_latam_campo('certpag_ba_kicker', $ese_ba_id, ''));
$ese_ba_titulo = trim((string) ese_latam_campo('certpag_ba_titulo', $ese_ba_id, ''));
$ese_ba_desc = ese_latam_texto_rico((string) ese_latam_campo('certpag_ba_desc', $ese_ba_id, ''));

// Posición centrada al abrir: el campo cuenta desde 1.
$ese_ba_inicial = (int) ese_latam_campo('certpag_ba_inicial', $ese_ba_id, 1) - 1;
$ese_ba_inicial = max(0, min($ese_ba_inicial, count($ese_ba_pasos) - 1));
?>

<section class="bangel" id="blue-angel" data-bangel data-bangel-autoplay="4000">
    <header class="bangel__header" data-reveal-header>
        <?php if ('' !== $ese_ba_kicker): ?>
            <p class="type-kicker text-white">/ <?php echo esc_html($ese_ba_kicker); ?></p>
        <?php endif; ?>
        <?php if ('' !== $ese_ba_titulo): ?>
            <h2 class="bangel__title"><?php echo ese_latam_titulo($ese_ba_titulo); ?></h2>
        <?php endif; ?>
        <?php if ('' !== $ese_ba_desc): ?>
            <p class="bangel__desc" data-reveal-desc><?php echo $ese_ba_desc; ?></p>
        <?php endif; ?>
    </header>

    <div class="bangel__stage" data-bangel-stage data-reveal="fade" data-reveal-delay="0.2">
        <?php // Arco de progreso (misma geometría que la banda del Figma: R 535, centro 500/585). ?>
        <svg class="bangel__arc" viewBox="0 0 1000 340" fill="none" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true" data-bangel-arc>
            <defs>
                <linearGradient id="bangel-arc-grad" gradientUnits="userSpaceOnUse" x1="60" y1="300" x2="700" y2="60">
                    <stop offset="0" stop-color="#009ee6" stop-opacity="0.35" />
                    <stop offset="0.45" stop-color="#ffffff" />
                    <stop offset="1" stop-color="#ffffff" />
                </linearGradient>
            </defs>
            <path class="bangel__arc-base" d="M28 334 A535 535 0 0 1 972 334" />
            <path class="bangel__arc-progress" d="M28 334 A535 535 0 0 1 972 334" data-bangel-progress />
            <!-- <g class="bangel__arc-marker" data-bangel-marker>
                <path d="M-12 -14 L0 0 L-12 14 M2 -14 L14 0 L2 14"/>
            </g> -->
        </svg>

        <ul class="bangel__items" data-bangel-items>
            <?php foreach ($ese_ba_pasos as $ese_i => $ese_paso): ?>
                <li class="bangel__item<?php echo $ese_i === $ese_ba_inicial ? ' is-active' : ''; ?>" data-bangel-item
                    data-bangel-name="<?php echo esc_attr($ese_paso['label']); ?>">
                    <button type="button" class="bangel__item-btn" data-bangel-go="<?php echo (int) $ese_i; ?>"
                        aria-label="<?php echo esc_attr(sprintf(__('Ver paso: %s', 'ese-latam'), $ese_paso['label'])); ?>">
                        <img src="<?php echo esc_url($ese_paso['img']); ?>" alt="" loading="lazy" decoding="async"
                            draggable="false">
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="bangel__label" data-bangel-label aria-live="polite">
            <?php echo esc_html($ese_ba_pasos[$ese_ba_inicial]['label']); ?>
        </p>

        <button type="button" class="bangel__nav bangel__nav--prev" data-bangel-prev
            aria-label="<?php esc_attr_e('Paso anterior', 'ese-latam'); ?>">
            <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true">
                <path
                    d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                    fill="currentColor" />
            </svg>
        </button>
        <button type="button" class="bangel__nav bangel__nav--next" data-bangel-next
            aria-label="<?php esc_attr_e('Paso siguiente', 'ese-latam'); ?>">
            <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true">
                <path
                    d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                    fill="currentColor" />
            </svg>
        </button>
    </div>
</section>