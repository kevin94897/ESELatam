<?php
/**
 * Página Impacto — franja de frases (Figma 3824-8301): fondo verde claro,
 * frases en itálica mayúscula separadas por el chevron de marca. Se
 * desplaza con el scroll vía data-marquee (marquee.ts), como el resto de
 * marquees del sitio.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_frases = [
    __('Compromiso social Latam', 'ese-latam'),
    __('Menos huella CO2', 'ese-latam'),
    __('Ciudades resilientes', 'ese-latam'),
    __('Impacto positivo', 'ese-latam'),
    __('Complemento ecológico', 'ese-latam'),
];

$ese_chevron = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M23.1249 12.4163L18.8442 18.8325C18.7075 19.0375 18.5224 19.2056 18.3052 19.322C18.0881 19.4384 17.8456 19.4995 17.5992 19.5H3.00049C2.86467 19.5001 2.73138 19.4633 2.61485 19.3935C2.49831 19.3238 2.4029 19.2237 2.3388 19.104C2.27469 18.9843 2.24431 18.8494 2.25088 18.7137C2.25745 18.5781 2.30074 18.4467 2.37611 18.3337L6.59955 12L2.3808 5.66625C2.30564 5.5536 2.26238 5.42271 2.25562 5.28746C2.24885 5.15221 2.27883 5.01765 2.34237 4.89807C2.40591 4.77849 2.50065 4.67834 2.61652 4.60825C2.73239 4.53816 2.86507 4.50076 3.00049 4.5H17.5992C17.8456 4.50046 18.0881 4.5616 18.3052 4.67801C18.5224 4.79443 18.7075 4.96255 18.8442 5.1675L23.122 11.5837C23.2047 11.7067 23.2491 11.8514 23.2496 11.9996C23.2501 12.1477 23.2067 12.2927 23.1249 12.4163Z" fill="currentColor"/></svg>';
?>

<div class="imp-ticker" aria-hidden="true">
    <div class="imp-ticker__track" data-marquee="left">
        <?php for ($ese_r = 0; $ese_r < 3; $ese_r++) : ?>
            <?php foreach ($ese_frases as $ese_frase) : ?>
                <span class="imp-ticker__item">
                    <?php echo $ese_chevron; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    <span><?php echo esc_html($ese_frase); ?></span>
                </span>
            <?php endforeach; ?>
        <?php endfor; ?>
    </div>
</div>
