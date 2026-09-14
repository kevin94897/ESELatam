<?php
/**
 * Aside sticky + lista de paneles con foto (Figma "Objetivos con propósito"
 * 3454-379 en Nosotros, "Calidad superior desde el origen" 3797-993 en
 * Certificaciones). Antes vivía inline en page-nosotros.php; se extrajo
 * para reutilizarla con otro copy. El nav que sigue al scroll y el chevron
 * son initObjetivos en nosotros.ts: la página que la use debe ir dentro del
 * wrapper `.nosotros[data-nosotros]`.
 *
 * @param array{
 *     id?: string, kicker?: string, title?: string, title_strong?: string,
 *     desc?: string (HTML mínimo: span.hl-accent / strong),
 *     items?: list<array{id: string, title: string, img: string, text: array{0: string, 1: string, 2: string}}>,
 *     metas_title?: string, metas?: list<string>, skip_label?: string, skip_href?: string
 * } $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_os = wp_parse_args($args ?? [], [
    'id'           => 'objetivos',
    'kicker'       => __('Sobre nosotros', 'ese-latam'),
    'title'        => __('Objetivos con', 'ese-latam'),
    'title_strong' => __('propósito', 'ese-latam'),
    'desc'         => '',
    'items'        => [],
    'metas_title'  => __('Algunas de nuestras metas y objetivos del programa:', 'ese-latam'),
    'metas'        => [],
    'skip_label'   => '',
    'skip_href'    => '#',
]);

if (empty($ese_os['items'])) {
    return;
}

$ese_img = static fn (string $file): string => ESE_LATAM_URI . '/assets/imgs/' . $file;

// Check-circle (Figma check_circle 17px).
$ese_check_svg = '<svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M7.23015 12.306L13.2455 6.29067L12.3026 5.34784L7.23015 10.4203L4.68015 7.87033L3.73732 8.81316L7.23015 12.306ZM8.50157 17C7.32588 17 6.22081 16.7769 5.18634 16.3307C4.15188 15.8846 3.25207 15.279 2.48692 14.5142C1.72177 13.7493 1.11596 12.8499 0.669487 11.8159C0.223162 10.7819 0 9.6771 0 8.50157C0 7.32588 0.223088 6.22081 0.669263 5.18634C1.11544 4.15188 1.72095 3.25207 2.4858 2.48692C3.25065 1.72177 4.15009 1.11596 5.18411 0.669487C6.21812 0.223162 7.3229 0 8.49843 0C9.67412 0 10.7792 0.223087 11.8137 0.669263C12.8481 1.11544 13.7479 1.72095 14.5131 2.4858C15.2782 3.25065 15.884 4.15009 16.3305 5.18411C16.7768 6.21812 17 7.3229 17 8.49843C17 9.67412 16.7769 10.7792 16.3307 11.8137C15.8846 12.8481 15.279 13.7479 14.5142 14.5131C13.7493 15.2782 12.8499 15.884 11.8159 16.3305C10.7819 16.7768 9.6771 17 8.50157 17ZM8.5 15.6579C10.4982 15.6579 12.1908 14.9645 13.5776 13.5776C14.9645 12.1908 15.6579 10.4982 15.6579 8.5C15.6579 6.50175 14.9645 4.80921 13.5776 3.42237C12.1908 2.03553 10.4982 1.34211 8.5 1.34211C6.50175 1.34211 4.80921 2.03553 3.42237 3.42237C2.03553 4.80921 1.34211 6.50175 1.34211 8.5C1.34211 10.4982 2.03553 12.1908 3.42237 13.5776C4.80921 14.9645 6.50175 15.6579 8.5 15.6579Z" fill="currentColor"/></svg>';
?>

<section id="<?php echo esc_attr($ese_os['id']); ?>" class="nos-objetivos" data-nos-objetivos>
    <header class="nos-objetivos__header" data-reveal-header>
        <div>
            <p class="type-kicker text-secondary">/ <?php echo esc_html($ese_os['kicker']); ?></p>
            <h2 class="type-h2 uppercase">
                <?php echo esc_html($ese_os['title']); ?><br>
                <span class="hl"><?php echo esc_html($ese_os['title_strong']); ?></span>
            </h2>
        </div>
        <p class="nos-desc" data-reveal-desc>
            <?php echo wp_kses($ese_os['desc'], ['span' => ['class' => []], 'strong' => []]); ?>
        </p>
    </header>

    <div class="nos-objetivos__layout">
        <aside class="nos-objetivos__aside">
            <div class="nos-objetivos__sticky">
                <nav class="nos-objetivos__nav" aria-label="<?php echo esc_attr($ese_os['title'] . ' ' . $ese_os['title_strong']); ?>" data-nos-obj-nav>
                    <span class="nos-objetivos__chevron" aria-hidden="true" data-nos-obj-chevron>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M23.1249 12.4163L18.8442 18.8325C18.7075 19.0375 18.5224 19.2056 18.3052 19.322C18.0881 19.4384 17.8456 19.4995 17.5992 19.5H3.00049C2.86467 19.5001 2.73138 19.4633 2.61485 19.3935C2.49831 19.3238 2.4029 19.2237 2.3388 19.104C2.27469 18.9843 2.24431 18.8494 2.25088 18.7137C2.25745 18.5781 2.30074 18.4467 2.37611 18.3337L6.59955 12L2.3808 5.66625C2.30564 5.5536 2.26238 5.42271 2.25562 5.28746C2.24885 5.15221 2.27883 5.01765 2.34237 4.89807C2.40591 4.77849 2.50065 4.67834 2.61652 4.60825C2.73239 4.53816 2.86507 4.50076 3.00049 4.5H17.5992C17.8456 4.50046 18.0881 4.5616 18.3052 4.67801C18.5224 4.79443 18.7075 4.96255 18.8442 5.1675L23.122 11.5837C23.2047 11.7067 23.2491 11.8514 23.2496 11.9996C23.2501 12.1477 23.2067 12.2927 23.1249 12.4163Z" fill="currentColor"/>
                        </svg>
                    </span>
                    <?php foreach ($ese_os['items'] as $ese_i => $ese_obj): ?>
                        <a href="#<?php echo esc_attr($ese_obj['id']); ?>"
                           class="nos-objetivos__nav-item<?php echo 0 === $ese_i ? ' is-active' : ''; ?>"
                           data-nos-obj-link="<?php echo (int) $ese_i; ?>">
                            <?php echo esc_html($ese_obj['title']); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <?php if ('' !== $ese_os['skip_label']) : ?>
                    <a href="<?php echo esc_url($ese_os['skip_href']); ?>" class="nos-objetivos__skip" data-reveal="fade">
                        <?php echo esc_html($ese_os['skip_label']); ?>
                    </a>
                <?php endif; ?>

                <?php if (! empty($ese_os['metas'])) : ?>
                <div class="nos-objetivos__metas" data-reveal="up" data-reveal-delay="0.1">
                    <p class="nos-objetivos__metas-title"><?php echo esc_html($ese_os['metas_title']); ?></p>
                    <ul class="nos-objetivos__metas-list">
                        <?php foreach ($ese_os['metas'] as $ese_meta): ?>
                            <li>
                                <span class="nos-objetivos__metas-icon"><?php echo $ese_check_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
                                <span><?php echo esc_html($ese_meta); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </aside>

        <div class="nos-objetivos__list">
            <?php foreach ($ese_os['items'] as $ese_i => $ese_obj): ?>
                <article id="<?php echo esc_attr($ese_obj['id']); ?>" class="nos-objetivo" data-nos-obj="<?php echo (int) $ese_i; ?>">
                    <div class="nos-objetivo__panel" data-nos-obj-panel>
                        <img src="<?php echo esc_url($ese_img($ese_obj['img'])); ?>" alt="" loading="lazy" decoding="async"
                             data-nos-obj-img>
                        <span class="nos-objetivo__num" aria-hidden="true"><?php echo esc_html(str_pad((string) ($ese_i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                        <h3 class="nos-objetivo__title"><?php echo esc_html($ese_obj['title']); ?></h3>
                    </div>
                    <p class="nos-objetivo__text" data-reveal="up">
                        <?php echo esc_html($ese_obj['text'][0]); ?>
                        <span class="hl-accent"><?php echo esc_html($ese_obj['text'][1]); ?></span><?php echo esc_html($ese_obj['text'][2]); ?>
                    </p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

