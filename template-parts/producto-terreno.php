<?php
/**
 * Ficha de producto — "Funcionando en la práctica" / "En terreno"
 * (Figma 3715-6801). Copy genérico de marca (calidad, adaptabilidad, diseño,
 * sostenibilidad) + foto con cita superpuesta: no depende del post actual,
 * mismo criterio autocontenido que producto-pruebas.php.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

// Ícono compartido por las 4 tarjetas (Figma 3765-3441 y variantes: mismo
// trazo, un color distinto por asset ya exportado — no un solo SVG
// recoloreado por CSS, porque acá cada instancia es un archivo separado en
// el propio Figma, a diferencia de la insignia de producto-pruebas.php).
$ese_terreno_icon = '<svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">'
    . '<path d="M20.8749 14.9163L16.5942 21.3325C16.4575 21.5375 16.2724 21.7056 16.0552 21.822C15.8381 21.9384 15.5956 21.9995 15.3492 22H0.750487C0.614673 22.0001 0.481384 21.9633 0.364847 21.8935C0.248309 21.8238 0.152897 21.7237 0.0887952 21.604C0.024693 21.4843 -0.00569346 21.3494 0.000878849 21.2137C0.00745116 21.0781 0.0507356 20.9467 0.126112 20.8337L4.34955 14.5L0.1308 8.16625C0.0556407 8.0536 0.0123825 7.92271 0.00561652 7.78746C-0.00114949 7.65221 0.0288292 7.51765 0.0923706 7.39807C0.155912 7.27849 0.250645 7.17834 0.366516 7.10825C0.482387 7.03816 0.615071 7.00076 0.750487 7H15.3492C15.5956 7.00046 15.8381 7.0616 16.0552 7.17801C16.2724 7.29443 16.4575 7.46255 16.5942 7.6675L20.872 14.0837C20.9547 14.2067 20.9991 14.3514 20.9996 14.4996C21.0001 14.6477 20.9567 14.7927 20.8749 14.9163Z" fill="currentColor"/>'
    . '</svg>';

// Orden real del Figma (2x2, lectura por fila): diseño → adaptabilidad →
// calidad → sostenibilidad. El color del ícono es el mismo hex que trae
// cada asset exportado; el de "Sostenibilidad" difiere levemente del texto
// en el propio archivo de Figma (ícono #001E61 vs texto #2A385E) y se
// respeta tal cual, es una discrepancia menor del diseño original.
$ese_terreno_features = [
    ['color' => 'var(--color-accent)',            'icon_color' => '#8EB952', 'title' => __('Diseño funcional', 'ese-latam'), 'desc' => __('Cada detalle está pensado para quien lo usa a diario: apertura fácil, agarres ergonómicos y un manejo simple para cualquier persona.', 'ese-latam')],
    ['color' => 'var(--color-secondary-600)',     'icon_color' => '#0084BE', 'title' => __('Adaptabilidad', 'ese-latam'), 'desc' => __('Se integra a distintos espacios y operaciones, desde condominios residenciales hasta plantas industriales de alta rotación.', 'ese-latam')],
    ['color' => 'var(--color-circulogic-orange)', 'icon_color' => '#E85D28', 'title' => __('Calidad y durabilidad', 'ese-latam'), 'desc' => __('Resiste años de sol, lluvia y manipulación constante sin perder su forma — una inversión que se sostiene en el tiempo.', 'ese-latam')],
    ['color' => 'var(--color-circulogic-blue-dark)', 'icon_color' => '#001E61', 'title' => __('Sostenibilidad', 'ese-latam'), 'desc' => __('Fabricado en HDPE, pensado para que al final de su vida útil se convierta en materia prima de un nuevo contenedor.', 'ese-latam')],
];
?>

<section id="en-terreno" class="terreno">
    <div class="terreno__content">
        <header class="terreno__header" data-reveal-header>
            <p class="type-kicker text-secondary">/ <?php esc_html_e('En terreno', 'ese-latam'); ?></p>
            <h2 class="type-h2 uppercase">
                <?php esc_html_e('Funcionando en la', 'ese-latam'); ?><br>
                <span class="hl"><?php esc_html_e('práctica', 'ese-latam'); ?></span>
            </h2>
        </header>

        <div class="terreno__features" data-reveal-stagger>
            <?php foreach ($ese_terreno_features as $ese_feature) : ?>
                <article class="terreno__feature"
                    style="--feature-color: <?php echo esc_attr($ese_feature['color']); ?>; --feature-icon-color: <?php echo esc_attr($ese_feature['icon_color']); ?>;">
                    <p class="terreno__feature-title">
                        <span class="terreno__feature-icon">
                            <?php echo $ese_terreno_icon; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                        </span>
                        <?php echo esc_html($ese_feature['title']); ?>
                    </p>
                    <p class="terreno__feature-desc"><?php echo esc_html($ese_feature['desc']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

    <figure class="terreno__photo" data-reveal="up" data-reveal-delay="0.15">
        <img src="<?php echo esc_url(ESE_LATAM_URI . '/assets/imgs/terreno/en-terreno.webp'); ?>"
            alt="<?php esc_attr_e('Voluntarios recolectando residuos al aire libre', 'ese-latam'); ?>"
            loading="lazy" decoding="async">
        <span class="terreno__photo-shade" aria-hidden="true"></span>
        <blockquote class="terreno__quote">
            <?php esc_html_e('Un buen contenedor no se mide solo en el papel, se mide en el uso diario de quienes lo manipulan, lo transportan y conviven con él en su', 'ese-latam'); ?>
            <span class="hl-accent"><?php esc_html_e('comunidad', 'ese-latam'); ?></span>
        </blockquote>
    </figure>
</section>
