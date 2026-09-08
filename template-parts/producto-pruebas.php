<?php
/**
 * Ficha de producto — "Pruebas de rigurosidad" (Figma 3694-6617). A
 * diferencia de producto-hero.php/producto-specs.php, este bloque es copy
 * genérico de marca sobre control de calidad: no depende del post actual, así
 * que también podría llamarse fuera de un single de producto si hiciera
 * falta más adelante.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_pruebas = [
    ['title' => __('Pruebas de caída libre', 'ese-latam'), 'desc' => __('Resiste impactos reales sin quebrarse ni deformarse.', 'ese-latam')],
    ['title' => __('Pruebas de elevación', 'ese-latam'), 'desc' => __('Verificamos la compatibilidad con los camiones recolectores más usados.', 'ese-latam')],
    ['title' => __('Pruebas de ruedas', 'ese-latam'), 'desc' => __('Miles de ciclos simulados garantizan un traslado suave y duradero.', 'ese-latam')],
    ['title' => __('Pruebas de caída de bala', 'ese-latam'), 'desc' => __('Medimos la resistencia estructural frente a impactos del uso diario.', 'ese-latam')],
    ['title' => __('Prueba de la guillotina', 'ese-latam'), 'desc' => __('Comprobamos que el contenedor resista cortes e impactos.', 'ese-latam')],
    ['title' => __('Agente humectante', 'ese-latam'), 'desc' => __('Material impermeable que no absorbe líquidos ni genera filtraciones.', 'ese-latam')],
];

// Insignia compartida por las 6 tarjetas (Figma 3710:6735): un solo SVG, dos
// capas (forma + glifo) recoloreadas por CSS según .is-active — no hace
// falta duplicar el asset por estado.
$ese_pruebas_badge = '<svg viewBox="0 0 61 77" fill="none" aria-hidden="true">'
    . '<path class="pruebas__card-badge-shape" d="M27.7487 1.66987C29.2957 0.776709 31.2017 0.776709 32.7487 1.66987L51.9974 12.7831C53.5444 13.6763 54.4974 15.3269 54.4974 17.1132V39.3397C54.4974 41.1261 53.5444 42.7767 51.9974 43.6699L32.7487 54.7831C31.2017 55.6763 29.2957 55.6763 27.7487 54.7831L8.5 43.6699C6.95299 42.7767 6 41.1261 6 39.3397V17.1132C6 15.3269 6.95299 13.6763 8.5 12.7831L27.7487 1.66987Z"/>'
    . '<path class="pruebas__card-badge-glyph" d="M38.9893 21.2554L29.9893 18.2554C29.8737 18.2168 29.7487 18.2168 29.6331 18.2554L20.6331 21.2554C20.5212 21.2928 20.4238 21.3644 20.3548 21.4602C20.2859 21.5559 20.2487 21.6709 20.2487 21.7889V29.2889C20.2487 29.4381 20.308 29.5811 20.4135 29.6866C20.519 29.7921 20.662 29.8514 20.8112 29.8514C20.9604 29.8514 21.1035 29.7921 21.209 29.6866C21.3144 29.5811 21.3737 29.4381 21.3737 29.2889V22.5689L25 23.7783C24.5427 24.4541 24.2328 25.2186 24.0905 26.0221C23.9482 26.8255 23.9767 27.65 24.1741 28.4417C24.3714 29.2335 24.7333 29.9748 25.2361 30.6174C25.739 31.2601 26.3714 31.7896 27.0925 32.1717C25.2878 32.7839 23.7175 34.0083 22.5925 35.7342C22.5278 35.8581 22.5115 36.0015 22.5469 36.1367C22.5822 36.2719 22.6666 36.3891 22.7835 36.4655C22.9005 36.5419 23.0418 36.572 23.1797 36.55C23.3177 36.528 23.4426 36.4554 23.53 36.3464C24.9803 34.1254 27.2687 32.8514 29.8112 32.8514C32.3537 32.8514 34.6421 34.1254 36.0925 36.3464C36.1793 36.4577 36.3049 36.5323 36.4442 36.5553C36.5835 36.5784 36.7264 36.5481 36.8445 36.4707C36.9625 36.3932 37.0472 36.2742 37.0815 36.1372C37.1159 36.0003 37.0975 35.8554 37.03 35.7314C35.905 34.0083 34.3365 32.7839 32.53 32.1689C33.2506 31.7864 33.8826 31.2565 34.385 30.6137C34.8873 29.9709 35.2487 29.2295 35.4456 28.4378C35.6426 27.646 35.6706 26.8217 35.528 26.0185C35.3854 25.2152 35.0752 24.451 34.6178 23.7754L38.9893 22.3223C39.1012 22.2849 39.1985 22.2133 39.2674 22.1175C39.3363 22.0218 39.3734 21.9068 39.3734 21.7889C39.3734 21.6709 39.3363 21.556 39.2674 21.4602C39.1985 21.3645 39.1012 21.2929 38.9893 21.2554ZM34.4987 27.0389C34.499 27.7858 34.3209 28.522 33.979 29.1861C33.6372 29.8503 33.1416 30.4231 32.5336 30.8569C31.9255 31.2907 31.2226 31.5729 30.4834 31.68C29.7442 31.7871 28.9901 31.716 28.2839 31.4727C27.5777 31.2293 26.9399 30.8207 26.4237 30.2809C25.9074 29.7411 25.5276 29.0857 25.3159 28.3694C25.1042 27.6531 25.0668 26.8966 25.2067 26.1629C25.3466 25.4292 25.6598 24.7395 26.1203 24.1514L29.6331 25.3223C29.7487 25.3609 29.8737 25.3609 29.9893 25.3223L33.5021 24.1514C34.1488 24.9748 34.4998 25.9918 34.4987 27.0389ZM29.8112 24.1964L22.5925 21.7889L29.8112 19.3814L37.03 21.7889L29.8112 24.1964Z"/>'
    . '</svg>';

// Flecha compartida (Figma "arrow_forward_ios", 3710:6741): mismo trato,
// color/opacidad por CSS según .is-active.
$ese_pruebas_arrow = '<svg viewBox="0 0 32 32" fill="none" aria-hidden="true">'
    . '<path d="M13.1535 24L12 22.8974L18.6929 16.5L12 10.1026L13.1535 9L21 16.5L13.1535 24Z"/>'
    . '</svg>';
?>

<section id="pruebas-de-rigurosidad" class="pruebas">
    <header class="pruebas__header" data-reveal-header>
        <p class="type-kicker text-secondary">/ <?php esc_html_e('Pruebas de rigurosidad', 'ese-latam'); ?></p>
        <h2 class="type-h2 uppercase text-center">
            <?php esc_html_e('¿qué hace que', 'ese-latam'); ?><br>
            <span class="hl"><?php esc_html_e('sea excelente?', 'ese-latam'); ?></span>
        </h2>
        <p class="pruebas__desc">
            <?php esc_html_e('Antes de llegar a tu ciudad o industria, cada contenedor ESE pasa por', 'ese-latam'); ?>
            <span class="hl-accent font-extrabold"><?php esc_html_e('pruebas que simulan años de uso real', 'ese-latam'); ?></span>:
            <?php esc_html_e('caídas, impactos, presión y exposición al clima. Así nos aseguramos de que siga funcionando bien, incluso en las condiciones más exigentes.', 'ese-latam'); ?>
        </p>
    </header>

    <?php // Insignia + flecha son UN solo SVG reutilizado en las 6 tarjetas
    // (ver $ese_pruebas_badge/$ese_pruebas_arrow más arriba): el color de
    // cada estado sale de CSS vía .is-active, no de assets duplicados. ?>
    <div class="pruebas__frame" data-pruebas data-reveal="up">
        <div class="pruebas__cards pruebas__cards--left">
            <?php foreach (array_slice($ese_pruebas, 0, 3) as $ese_i => $ese_prueba) : ?>
                <button type="button" class="pruebas__card<?php echo 0 === $ese_i ? ' is-active' : ''; ?>"
                    data-pruebas-card>
                    <span class="pruebas__card-badge" aria-hidden="true">
                        <?php echo $ese_pruebas_badge; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </span>
                    <span class="pruebas__card-text">
                        <span class="pruebas__card-title"><?php echo esc_html($ese_prueba['title']); ?></span>
                        <span class="pruebas__card-desc"><?php echo esc_html($ese_prueba['desc']); ?></span>
                    </span>
                    <span class="pruebas__card-arrow" aria-hidden="true">
                        <?php echo $ese_pruebas_arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </span>
                </button>
            <?php endforeach; ?>
        </div>

        <?php // Sin video real todavía: placeholder decorativo (fondo navy +
        // botón play), listo para recibir un <video>/poster cuando exista el
        // asset — mismo criterio que "Descargar ficha técnica" deshabilitado. ?>
        <div class="pruebas__media" aria-hidden="true">
            <span class="pruebas__play">
                <svg viewBox="0 0 80 80" fill="none">
                    <path d="M0 32C0 14.3269 14.3269 0 32 0H48C65.6731 0 80 14.3269 80 32V48C80 65.6731 65.6731 80 48 80H32C14.3269 80 0 65.6731 0 48V32Z"
                        fill="white" fill-opacity="0.15" />
                    <path d="M32 0.5H48C65.397 0.5 79.5 14.603 79.5 32V48C79.5 65.397 65.397 79.5 48 79.5H32C14.603 79.5 0.5 65.397 0.5 48V32C0.5 14.603 14.603 0.5 32 0.5Z"
                        stroke="white" stroke-opacity="0.2" />
                    <path d="M30.9647 26.1687L53.3286 38.8419C53.5323 38.9575 53.7019 39.1256 53.8198 39.329C53.9378 39.5323 54 39.7636 54 39.9992C54 40.2347 53.9378 40.466 53.8198 40.6694C53.7019 40.8727 53.5323 41.0408 53.3286 41.1564L30.9647 53.8296C30.764 53.9433 30.5372 54.002 30.307 53.9999C30.0767 53.9979 29.851 53.9352 29.6523 53.8179C29.4536 53.7007 29.2888 53.5332 29.1744 53.3319C29.0599 53.1306 28.9998 52.9027 29 52.6707V27.3276C29.0001 27.0958 29.0604 26.8681 29.175 26.667C29.2896 26.466 29.4544 26.2986 29.653 26.1816C29.8517 26.0646 30.0773 26.002 30.3074 26C30.5375 25.9981 30.7641 26.0551 30.9647 26.1687Z"
                        fill="white" />
                </svg>
            </span>
        </div>

        <div class="pruebas__cards pruebas__cards--right">
            <?php foreach (array_slice($ese_pruebas, 3, 3) as $ese_prueba) : ?>
                <button type="button" class="pruebas__card" data-pruebas-card>
                    <span class="pruebas__card-badge" aria-hidden="true">
                        <?php echo $ese_pruebas_badge; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </span>
                    <span class="pruebas__card-text">
                        <span class="pruebas__card-title"><?php echo esc_html($ese_prueba['title']); ?></span>
                        <span class="pruebas__card-desc"><?php echo esc_html($ese_prueba['desc']); ?></span>
                    </span>
                    <span class="pruebas__card-arrow" aria-hidden="true">
                        <?php echo $ese_pruebas_arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
