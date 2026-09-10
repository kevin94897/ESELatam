<?php
/**
 * Página de Contacto — bloque 2: "Sede central ESE Latam" (Figma 3941-8444).
 * Fila con la dirección + enlace a Google Maps, y debajo el mapa embebido.
 *
 * El iframe usa el modo `output=embed` de Google Maps: no necesita API key
 * ni script externo, y va con loading="lazy" para no pesar en la carga
 * inicial (la sección está bien por debajo del fold).
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_datos = ese_latam_contacto_datos();
?>

<section class="ctc-sede">
    <header class="ctc-sede__header" data-reveal-header>
        <p class="type-kicker text-secondary">/ <?php esc_html_e('Arquitectura del proyecto', 'ese-latam'); ?></p>
        <h2 class="type-h2 uppercase">
            <?php esc_html_e('Sede central', 'ese-latam'); ?><br>
            <span class="hl"><?php esc_html_e('ESE Latam', 'ese-latam'); ?></span>
        </h2>
    </header>

    <div class="ctc-sede__bar" data-reveal="up">
        <p class="ctc-sede__address">
            <span class="ctc-sede__pin" aria-hidden="true">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.0909 17.1429H13.2807C13.7528 16.7454 14.1979 16.3205 14.6136 15.8705C16.1733 14.1793 17 12.3964 17 10.7143C17 9.46398 16.4732 8.26488 15.5355 7.38078C14.5979 6.49668 13.3261 6 12 6C10.6739 6 9.40215 6.49668 8.46447 7.38078C7.52678 8.26488 7 9.46398 7 10.7143C7 12.3964 7.82443 14.1793 9.38636 15.8705C9.80205 16.3205 10.2472 16.7454 10.7193 17.1429H7.90909C7.78854 17.1429 7.67292 17.188 7.58768 17.2684C7.50244 17.3488 7.45455 17.4578 7.45455 17.5714C7.45455 17.6851 7.50244 17.7941 7.58768 17.8745C7.67292 17.9548 7.78854 18 7.90909 18H16.0909C16.2115 18 16.3271 17.9548 16.4123 17.8745C16.4976 17.7941 16.5455 17.6851 16.5455 17.5714C16.5455 17.4578 16.4976 17.3488 16.4123 17.2684C16.3271 17.188 16.2115 17.1429 16.0909 17.1429ZM7.90909 10.7143C7.90909 9.69131 8.3401 8.71023 9.10729 7.98687C9.87448 7.26352 10.915 6.85714 12 6.85714C13.085 6.85714 14.1255 7.26352 14.8927 7.98687C15.6599 8.71023 16.0909 9.69131 16.0909 10.7143C16.0909 13.7802 12.9392 16.3393 12 17.0357C11.0608 16.3393 7.90909 13.7802 7.90909 10.7143ZM14.2727 10.7143C14.2727 10.2905 14.1394 9.87617 13.8897 9.52378C13.64 9.17139 13.285 8.89673 12.8697 8.73454C12.4544 8.57236 11.9975 8.52992 11.5566 8.6126C11.1157 8.69529 10.7108 8.89937 10.3929 9.19906C10.0751 9.49874 9.85864 9.88056 9.77094 10.2962C9.68325 10.7119 9.72826 11.1428 9.90027 11.5343C10.0723 11.9259 10.3636 12.2605 10.7373 12.496C11.1111 12.7315 11.5505 12.8571 12 12.8571C12.6028 12.8571 13.1808 12.6314 13.6071 12.2295C14.0333 11.8277 14.2727 11.2826 14.2727 10.7143ZM10.6364 10.7143C10.6364 10.46 10.7163 10.2114 10.8662 9.99998C11.016 9.78855 11.229 9.62375 11.4782 9.52644C11.7273 9.42913 12.0015 9.40367 12.266 9.45328C12.5306 9.50289 12.7735 9.62534 12.9642 9.80515C13.1549 9.98496 13.2848 10.2141 13.3374 10.4635C13.3901 10.7129 13.363 10.9714 13.2598 11.2063C13.1566 11.4412 12.9818 11.642 12.7576 11.7833C12.5333 11.9246 12.2697 12 12 12C11.6383 12 11.2915 11.8645 11.0358 11.6234C10.78 11.3823 10.6364 11.0553 10.6364 10.7143Z"
                        fill="currentColor" />
                </svg>
            </span>
            <?php echo esc_html($ese_datos['direccion']); ?>
        </p>

        <a class="link-arrow ctc-sede__link" href="<?php echo esc_url($ese_datos['maps_url']); ?>"
            target="_blank" rel="noopener">
            <span class="link-arrow__text"><?php esc_html_e('Llegar con Google Maps', 'ese-latam'); ?></span>
            <span class="link-arrow__icon" aria-hidden="true">
                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z"
                        fill="currentColor" />
                </svg>
            </span>
        </a>
    </div>

    <div class="ctc-sede__map" data-reveal="up" data-reveal-delay="0.1">
        <iframe
            src="https://www.google.com/maps?q=<?php echo rawurlencode($ese_datos['maps_query']); ?>&amp;output=embed"
            title="<?php esc_attr_e('Mapa de la sede de ESE Latam', 'ese-latam'); ?>"
            loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
    </div>
</section>
