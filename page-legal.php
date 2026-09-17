<?php
/**
 * Template Name: Legal
 *
 * Páginas legales (Términos y condiciones, Políticas de privacidad).
 * inc/paginas.php las crea y les asigna esta plantilla; el grupo de campos
 * se ubica por plantilla y no por ID, así que cualquier página legal nueva
 * hereda los mismos campos (inc/pcf-legal.php).
 *
 * Composición, siguiendo lo que ya usa el resto de la web:
 *   1. Hero oscuro centrado (casos-hero.php, sin foto: negro liso)
 *   2. Índice pegajoso + cuerpo, dos columnas
 *   3. Contactemos (template-parts/contacto.php)
 *
 * El índice se arma solo con los títulos de las secciones y va marcando en
 * cuál estás al hacer scroll (src/ts/modules/legal.ts). El ancla de cada
 * sección sale de su título, igual que en el aside de "Objetivos".
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();

$ese_id  = (int) get_queried_object_id();
$ese_cmp = static fn (string $name, $def = '') => ese_latam_campo($name, $ese_id, $def);

// Secciones: cada fila es una entrada del índice y un bloque del cuerpo.
$ese_secciones = [];
foreach ((array) $ese_cmp('legal_secciones', []) as $ese_fila) {
    $ese_t = trim((string) ($ese_fila['titulo'] ?? ''));
    if ('' === $ese_t) {
        continue;
    }
    $ese_secciones[] = [
        'id'     => sanitize_title($ese_t),
        'titulo' => $ese_t,
        'texto'  => (string) ($ese_fila['texto'] ?? ''),
    ];
}

$ese_actualizado = trim((string) $ese_cmp('legal_actualizado'));
$ese_indice      = trim((string) $ese_cmp('legal_indice_titulo'));
?>

<div class="nosotros nosotros--legal" data-nosotros>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>

    <?php
    get_template_part('template-parts/casos-hero', null, [
        'kicker'  => (string) $ese_cmp('legal_kicker'),
        'title'   => (string) $ese_cmp('legal_titulo'),
        'desc'    => (string) $ese_cmp('legal_desc'),
        'current' => get_the_title($ese_id),
        'bg'      => ese_latam_img_url($ese_cmp('legal_imagen')),
    ]);
    ?>

    <?php if ([] !== $ese_secciones) : ?>
        <section class="legal" data-legal>
            <aside class="legal__aside">
                <nav class="legal__indice" aria-label="<?php echo esc_attr('' !== $ese_indice ? $ese_indice : __('Índice', 'ese-latam')); ?>">
                    <?php if ('' !== $ese_indice) : ?>
                        <p class="legal__indice-title"><?php echo esc_html($ese_indice); ?></p>
                    <?php endif; ?>
                    <ol class="legal__indice-list">
                        <?php foreach ($ese_secciones as $ese_i => $ese_sec) : ?>
                            <li>
                                <a class="legal__indice-link<?php echo 0 === $ese_i ? ' is-active' : ''; ?>"
                                   href="#<?php echo esc_attr($ese_sec['id']); ?>"
                                   data-legal-link="<?php echo (int) $ese_i; ?>">
                                    <span class="legal__indice-num"><?php echo esc_html(str_pad((string) ($ese_i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                                    <span><?php echo esc_html($ese_sec['titulo']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                    <?php if ('' !== $ese_actualizado) : ?>
                        <p class="legal__fecha"><?php echo esc_html($ese_actualizado); ?></p>
                    <?php endif; ?>
                </nav>
            </aside>

            <div class="legal__cuerpo">
                <?php foreach ($ese_secciones as $ese_i => $ese_sec) : ?>
                    <article class="legal__seccion" id="<?php echo esc_attr($ese_sec['id']); ?>" data-legal-seccion="<?php echo (int) $ese_i; ?>">
                        <h2 class="legal__titulo">
                            <span class="legal__num" aria-hidden="true"><?php echo esc_html(str_pad((string) ($ese_i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                            <?php echo esc_html($ese_sec['titulo']); ?>
                        </h2>
                        <div class="art-cuerpo legal__texto"><?php echo wp_kses_post($ese_sec['texto']); ?></div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php
    get_template_part('template-parts/contacto', null, [
        'class' => 'contacto--upper',
    ]);
    ?>
</div>

<?php
get_footer();
