<?php
/**
 * Página Certificaciones — "Nuestras certificaciones" en detalle (Figma
 * 3807-1079): grilla 3×3 de sellos (cada uno un tab) y a la derecha la
 * ficha del sello activo (logo, nombre, subtítulo, descripción y criterios).
 * Cambio de ficha por tab-panels.ts (data-tabs).
 *
 * Solo Blue Angel trae copy del Figma; las otras ocho fichas siguen el
 * mismo formato con la descripción pública de cada norma — a validar con
 * el cliente antes de publicar.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

// La página elige con un campo de relación qué sellos muestra y en qué
// orden; vacío, salen todos los del módulo.
$ese_cd_id     = (int) get_queried_object_id();
$ese_cd_elegidos = array_map('intval', (array) ese_latam_campo('certpag_sellos_lista', $ese_cd_id, []));
$ese_sellos    = ese_latam_certificaciones();

if ([] !== $ese_cd_elegidos) {
    $ese_por_id = [];
    foreach ($ese_sellos as $ese_s) {
        $ese_por_id[$ese_s['id']] = $ese_s;
    }
    $ese_sellos = array_values(array_filter(array_map(
        static fn (int $id): ?array => $ese_por_id[$id] ?? null,
        $ese_cd_elegidos
    )));
}

if ([] === $ese_sellos) {
    return;
}

$ese_cd_kicker = trim((string) ese_latam_campo('certpag_sellos_kicker', $ese_cd_id, ''));
$ese_cd_titulo = trim((string) ese_latam_campo('certpag_sellos_titulo', $ese_cd_id, ''));
$ese_cd_desc   = ese_latam_texto_rico((string) ese_latam_campo('certpag_sellos_desc', $ese_cd_id, ''));

$ese_check = '<svg width="18" height="18" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M7.23015 12.306L13.2455 6.29067L12.3026 5.34784L7.23015 10.4203L4.68015 7.87033L3.73732 8.81316L7.23015 12.306ZM8.50157 17C7.32588 17 6.22081 16.7769 5.18634 16.3307C4.15188 15.8846 3.25207 15.279 2.48692 14.5142C1.72177 13.7493 1.11596 12.8499 0.669487 11.8159C0.223162 10.7819 0 9.6771 0 8.50157C0 7.32588 0.223088 6.22081 0.669263 5.18634C1.11544 4.15188 1.72095 3.25207 2.4858 2.48692C3.25065 1.72177 4.15009 1.11596 5.18411 0.669487C6.21812 0.223162 7.3229 0 8.49843 0C9.67412 0 10.7792 0.223087 11.8137 0.669263C12.8481 1.11544 13.7479 1.72095 14.5131 2.4858C15.2782 3.25065 15.884 4.15009 16.3305 5.18411C16.7768 6.21812 17 7.3229 17 8.49843C17 9.67412 16.7769 10.7792 16.3307 11.8137C15.8846 12.8481 15.279 13.7479 14.5142 14.5131C13.7493 15.2782 12.8499 15.884 11.8159 16.3305C10.7819 16.7768 9.6771 17 8.50157 17ZM8.5 15.6579C10.4982 15.6579 12.1908 14.9645 13.5776 13.5776C14.9645 12.1908 15.6579 10.4982 15.6579 8.5C15.6579 6.50175 14.9645 4.80921 13.5776 3.42237C12.1908 2.03553 10.4982 1.34211 8.5 1.34211C6.50175 1.34211 4.80921 2.03553 3.42237 3.42237C2.03553 4.80921 1.34211 6.50175 1.34211 8.5C1.34211 10.4982 2.03553 12.1908 3.42237 13.5776C4.80921 14.9645 6.50175 15.6579 8.5 15.6579Z" fill="currentColor"/></svg>';
?>

<section class="certdet" id="nuestras-certificaciones" data-tabs>
    <header class="certdet__header" data-reveal-header>
        <div>
            <?php if ('' !== $ese_cd_kicker) : ?>
                <p class="type-kicker text-secondary">/ <?php echo esc_html($ese_cd_kicker); ?></p>
            <?php endif; ?>
            <?php if ('' !== $ese_cd_titulo) : ?>
                <h2 class="type-h2 uppercase">
                    <?php echo ese_latam_titulo($ese_cd_titulo, 'span', 'hl'); ?>
                </h2>
            <?php endif; ?>
        </div>
        <?php if ('' !== $ese_cd_desc) : ?>
            <p class="nos-desc" data-reveal-desc><?php echo $ese_cd_desc; ?></p>
        <?php endif; ?>
    </header>

    <div class="certdet__layout">
        <ul class="certdet__tiles" role="tablist" data-reveal-stagger>
            <?php foreach ($ese_sellos as $ese_i => $ese_sello) : ?>
                <li>
                    <button type="button" role="tab"
                        class="certdet__tile<?php echo 0 === $ese_i ? ' is-active' : ''; ?>"
                        aria-selected="<?php echo 0 === $ese_i ? 'true' : 'false'; ?>" data-tab>
                        <img src="<?php echo esc_url($ese_sello['img']); ?>" alt="" loading="lazy" decoding="async">
                        <span><?php echo esc_html($ese_sello['name']); ?></span>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="certdet__panel" data-reveal="up" data-reveal-delay="0.2">
            <?php foreach ($ese_sellos as $ese_i => $ese_sello) : ?>
                <article class="certdet__detail<?php echo 0 === $ese_i ? ' is-active' : ''; ?>" data-tab-panel>
                    <div class="certdet__detail-head">
                        <div class="certdet__detail-logo">
                            <img src="<?php echo esc_url($ese_sello['img']); ?>" alt="" loading="lazy" decoding="async">
                        </div>
                        <div>
                            <h3 class="certdet__detail-name"><?php echo esc_html($ese_sello['name']); ?></h3>
                            <p class="certdet__detail-sub"><?php echo esc_html($ese_sello['sub']); ?></p>
                        </div>
                    </div>

                    <p class="certdet__label"><?php esc_html_e('Descripción', 'ese-latam'); ?></p>
                    <p class="certdet__detail-desc"><?php echo $ese_sello['detalle']; ?></p>

                    <p class="certdet__label"><?php esc_html_e('Criterios', 'ese-latam'); ?></p>
                    <ul class="certdet__criterios">
                        <?php foreach ($ese_sello['criterios'] as $ese_crit) : ?>
                            <li><?php echo $ese_check; // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html($ese_crit); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
