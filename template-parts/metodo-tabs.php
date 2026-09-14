<?php
/**
 * Tabs con barra de autoplay + foto que cambia (Figma "Método Circulogic"
 * 3454-563 en Nosotros, "Marcando la diferencia" 3807-1128 en
 * Certificaciones). Antes vivía inline en page-nosotros.php; se extrajo
 * para reutilizarla con otro copy. La animación (autoplay, crossfade de la
 * foto, hoja) es initMetodo en nosotros.ts: la página que la use debe ir
 * dentro del wrapper `.nosotros[data-nosotros]`.
 *
 * @param array{
 *     id?: string, kicker?: string, title?: string, title_strong?: string,
 *     desc?: string (HTML mínimo: span.hl-accent / strong), autoplay?: int,
 *     tabs?: list<array{title: string, desc: string, img: string}>  (img relativa a assets/imgs/)
 * } $args
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$ese_mt = wp_parse_args($args ?? [], [
    'id'           => 'metodo',
    'kicker'       => __('Sobre nosotros', 'ese-latam'),
    'title'        => __('Método', 'ese-latam'),
    'title_strong' => __('Circulogic', 'ese-latam'),
    'desc'         => '',
    'autoplay'     => 6000,
    'tabs'         => [],
]);

if (empty($ese_mt['tabs'])) {
    return;
}

$ese_img = static fn (string $file): string => ESE_LATAM_URI . '/assets/imgs/' . $file;
?>

<section id="<?php echo esc_attr($ese_mt['id']); ?>" class="nos-metodo<?php echo 4 === count($ese_mt['tabs']) ? ' nos-metodo--cols-4' : ''; ?>" data-nos-metodo data-nos-metodo-autoplay="<?php echo (int) $ese_mt['autoplay']; ?>">
    <header class="nos-metodo__header" data-reveal-header>
        <div>
            <p class="type-kicker text-secondary">/ <?php echo esc_html($ese_mt['kicker']); ?></p>
            <h2 class="type-h2 uppercase">
                <?php echo esc_html($ese_mt['title']); ?><br>
                <span class="hl"><?php echo esc_html($ese_mt['title_strong']); ?></span>
            </h2>
        </div>
        <p class="nos-desc" data-reveal-desc>
            <?php echo wp_kses($ese_mt['desc'], ['span' => ['class' => []], 'strong' => []]); ?>
        </p>
    </header>

    <div class="nos-metodo__tabs" role="tablist" data-reveal-stagger>
        <?php foreach ($ese_mt['tabs'] as $ese_i => $ese_pilar): ?>
            <button type="button" role="tab"
                    class="nos-metodo__tab<?php echo 0 === $ese_i ? ' is-active' : ''; ?>"
                    aria-selected="<?php echo 0 === $ese_i ? 'true' : 'false'; ?>"
                    data-nos-metodo-tab
                    data-img="<?php echo esc_url($ese_img($ese_pilar['img'])); ?>">
                <span class="nos-metodo__bar" aria-hidden="true"><span class="nos-metodo__bar-fill"></span></span>
                <span class="nos-metodo__tab-title"><?php echo esc_html($ese_pilar['title']); ?></span>
                <span class="nos-metodo__tab-desc"><?php echo esc_html($ese_pilar['desc']); ?></span>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="nos-metodo__media" data-nos-panel="up">
        <div class="nos-metodo__media-inner" data-nos-metodo-media>
            <img src="<?php echo esc_url($ese_img($ese_mt['tabs'][0]['img'])); ?>" alt="" width="1800" height="1200"
                 loading="lazy" decoding="async" data-nos-metodo-img data-parallax data-parallax-from="8" data-parallax-to="-8">
        </div>
        <span class="nos-metodo__leaf" aria-hidden="true" data-nos-metodo-leaf>
            <svg width="72" height="57" viewBox="0 0 72 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M59.2155 35.3469C56.3388 37.0774 53.0347 37.9876 49.6681 37.9768C46.8477 37.9547 44.0593 37.3838 41.4615 36.2968C39.4594 39.0939 38.3879 42.4389 38.396 45.8666V54.6201C38.3967 54.9457 38.3298 55.2679 38.1994 55.5667C38.069 55.8656 37.8779 56.1347 37.638 56.3574C37.398 56.5801 37.1144 56.7516 36.8046 56.8613C36.4949 56.9709 36.1657 57.0164 35.8374 56.9948C35.2207 56.9417 34.647 56.6597 34.2314 56.2056C33.8159 55.7514 33.5892 55.1586 33.5968 54.5459V50.8534L22.0128 39.3898C20.2907 40.0255 18.4696 40.358 16.6317 40.3723C14.1015 40.3784 11.6187 39.6933 9.45687 38.3924C2.92097 34.4624 -0.597444 25.4179 0.0834413 14.1888C0.117724 13.6079 0.366375 13.0596 0.782186 12.6481C1.198 12.2366 1.75202 11.9906 2.33906 11.9566C13.6862 11.2947 22.8256 14.7647 26.785 21.2326C28.3405 23.7678 29.0197 26.7353 28.7196 29.6864C28.701 29.915 28.6158 30.1334 28.4744 30.3152C28.333 30.4969 28.1414 30.6342 27.9228 30.7105C27.7042 30.7868 27.4679 30.7988 27.2426 30.7451C27.0172 30.6913 26.8125 30.5741 26.653 30.4076L20.894 24.4414C20.4403 24.0148 19.8362 23.7805 19.2105 23.7885C18.5847 23.7964 17.9869 24.0459 17.5444 24.4838C17.1019 24.9217 16.8498 25.5133 16.8418 26.1325C16.8338 26.7517 17.0705 27.3496 17.5015 27.7985L33.6628 44.1984C33.6808 43.9669 33.7018 43.7354 33.7258 43.5068C34.2505 39.104 36.2137 34.9913 39.3169 31.7939L54.4913 15.9253C54.9416 15.4801 55.1947 14.8761 55.195 14.2463C55.1953 13.6164 54.9427 13.0122 54.4928 12.5666C54.043 12.1211 53.4326 11.8706 52.7962 11.8703C52.1597 11.87 51.5491 12.12 51.0989 12.5652L36.4014 27.9469C36.2542 28.1011 36.0681 28.2135 35.8622 28.2724C35.6563 28.3313 35.4383 28.3346 35.2307 28.2819C35.0231 28.2292 34.8336 28.1224 34.6818 27.9727C34.5301 27.8229 34.4216 27.6357 34.3677 27.4304C32.9459 22.2418 33.5728 17.077 36.2874 12.6423C41.6445 3.89176 54.1104 -0.792232 69.6358 0.110134C70.2228 0.14406 70.7768 0.390125 71.1927 0.801613C71.6085 1.2131 71.8571 1.76136 71.8914 2.3423C72.7912 17.7092 68.058 30.0455 59.2155 35.3469Z" fill="currentColor"/>
            </svg>
        </span>
    </div>
</section>

