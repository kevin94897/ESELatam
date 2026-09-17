<?php
/**
 * Single de "Caso de éxito" (Figma 3891-4495, "12 – Caso de éxito").
 *
 * Composición, en orden del Figma. Casi todo se reutiliza:
 *   1. Hero con la foto destacada → ficha-hero.php (compartido con el blog)
 *   2. El reto, problema y solución → caso-arquitectura.php (propio)
 *   3. Cinta "rendimiento real" → .nos-marquee
 *   4. Franja de cifras → caso-cifras.php (propio)
 *   5. El relato → el editor de la entrada, con los estilos de .art-cuerpo
 *   6. Soluciones utilizadas → producto-recomendados.php (el coverflow navy)
 *   7. Casos relacionados → casos-reales.php, sin el caso actual
 *   8. Contactemos → contacto.php
 *
 * El copy de cada bloque se edita en la entrada (inc/pcf-casos.php); lo
 * nativo —titular, bajada, relato, foto, sector y ciudad— en los campos de
 * siempre.
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();

$ese_archivo = ese_latam_casos_url();
$ese_arch_lbl = __('Casos de éxito', 'ese-latam');
?>

<div class="nosotros nosotros--caso" data-nosotros>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>

    <?php
    while (have_posts()) :
        the_post();
        $ese_id  = (int) get_the_ID();
        $ese_cmp = static fn (string $name, $def = '') => ese_latam_campo($name, $ese_id, $def);

        $ese_post = get_post($ese_id);

        // Sector y ciudad son taxonomías: la primera de cada una es la que
        // sale en el hero, igual que en la tarjeta del archivo.
        $ese_sectores = get_the_terms($ese_id, 'caso_sector');
        $ese_sector   = is_array($ese_sectores) && ! empty($ese_sectores) ? $ese_sectores[0] : null;
        $ese_ciudades = get_the_terms($ese_id, 'caso_ciudad');
        $ese_ciudad   = is_array($ese_ciudades) && ! empty($ese_ciudades) ? $ese_ciudades[0]->name : '';

        $ese_anio = $ese_cmp('caso_anio', null);
        $ese_anio = null === $ese_anio || '' === $ese_anio
            ? (string) get_the_date('Y', $ese_id)
            : (string) (int) $ese_anio;

        get_template_part('template-parts/ficha-hero', null, [
            'titulo'      => (string) get_the_title($ese_id),
            // El extracto escrito a mano: get_the_excerpt() se lo inventa
            // recortando el relato.
            'desc'        => $ese_post instanceof WP_Post ? (string) $ese_post->post_excerpt : '',
            'foto'        => (string) (get_the_post_thumbnail_url($ese_id, 'full') ?: ''),
            'chip'        => null !== $ese_sector ? $ese_sector->name : '',
            'chip_url'    => null !== $ese_sector ? add_query_arg('rubro', $ese_sector->slug, $ese_archivo) : '',
            'datos'       => [$ese_ciudad, $ese_anio],
            'crumb_label' => $ese_arch_lbl,
            'crumb_url'   => $ese_archivo,
        ]);

        // ---------- 2. El reto, problema y solución ----------
        $ese_arq = [];
        foreach ((array) $ese_cmp('caso_arq_items', []) as $ese_fila) {
            $ese_l = trim((string) ($ese_fila['label'] ?? ''));
            if ('' === $ese_l) {
                continue;
            }
            $ese_arq[] = [
                'label'  => $ese_l,
                'kicker' => trim((string) ($ese_fila['kicker'] ?? '')),
                'title'  => trim((string) ($ese_fila['title'] ?? '')),
                'texto'  => trim((string) ($ese_fila['texto'] ?? '')),
                'nota'   => trim((string) ($ese_fila['nota'] ?? '')),
                'img'    => ese_latam_img_url($ese_fila['img'] ?? ''),
            ];
        }

        get_template_part('template-parts/caso-arquitectura', null, [
            'kicker' => (string) $ese_cmp('caso_arq_kicker'),
            'title'  => (string) $ese_cmp('caso_arq_titulo'),
            'desc'   => ese_latam_texto_rico((string) $ese_cmp('caso_arq_desc')),
            'items'  => $ese_arq,
        ]);

        // ---------- 3. Cinta de resultados ----------
        $ese_marquee = trim((string) $ese_cmp('caso_marquee'));
        if ('' !== $ese_marquee) :
            ?>
            <div class="nos-marquee" aria-hidden="true">
                <p class="nos-marquee__track" data-marquee="right">
                    <span><?php echo esc_html($ese_marquee); ?></span>
                    <span><?php echo esc_html($ese_marquee); ?></span>
                </p>
            </div>
            <?php
        endif;

        // ---------- 4. Cifras ----------
        $ese_cifras = [];
        foreach ((array) $ese_cmp('caso_cifras', []) as $ese_fila) {
            $ese_v = trim((string) ($ese_fila['valor'] ?? ''));
            if ('' === $ese_v) {
                continue;
            }
            $ese_cifras[] = ['valor' => $ese_v, 'etiqueta' => trim((string) ($ese_fila['etiqueta'] ?? ''))];
        }

        get_template_part('template-parts/caso-cifras', null, ['items' => $ese_cifras]);

        // ---------- 5. El relato ----------
        $ese_relato = trim((string) get_the_content());
        if ('' !== $ese_relato) :
            $ese_relato_kicker = trim((string) $ese_cmp('caso_relato_kicker'));
            ?>
            <section class="caso-relato">
                <?php if ('' !== $ese_relato_kicker) : ?>
                    <p class="type-kicker text-secondary caso-relato__kicker">/ <?php echo esc_html($ese_relato_kicker); ?></p>
                <?php endif; ?>
                <article class="art-cuerpo caso-relato__cuerpo"><?php the_content(); ?></article>
            </section>
            <?php
        endif;

        // ---------- 6. Soluciones utilizadas ----------
        get_template_part('template-parts/producto-recomendados', null, [
            'kicker'    => (string) $ese_cmp('caso_sol_kicker'),
            'title'     => (string) $ese_cmp('caso_sol_titulo'),
            'desc'      => (string) $ese_cmp('caso_sol_desc'),
            'productos' => (array) $ese_cmp('caso_productos', []),
        ]);

        // ---------- 7. Casos relacionados ----------
        $ese_rel_enlace = ese_latam_enlace($ese_cmp('caso_rel_enlace', null));

        get_template_part('template-parts/casos-reales', null, [
            'kicker'     => (string) $ese_cmp('caso_rel_kicker'),
            'title'      => (string) $ese_cmp('caso_rel_titulo'),
            'desc'       => (string) $ese_cmp('caso_rel_desc'),
            'link_label' => $ese_rel_enlace['label'],
            'link_href'  => '' !== $ese_rel_enlace['href'] ? $ese_rel_enlace['href'] : $ese_archivo,
            'excluir'    => [$ese_id],
        ]);
    endwhile;

    get_template_part('template-parts/contacto', null, [
        'class' => 'contacto--upper',
    ]);
    ?>
</div>

<?php
get_footer();
