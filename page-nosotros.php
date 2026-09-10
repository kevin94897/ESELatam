<?php
/**
 * Template Name: Nosotros
 *
 * Página "Nosotros" (Figma node 3441-370). WordPress la aplica sola a la
 * página con slug `nosotros` (page-{slug}.php) y, por el header de arriba,
 * también se puede asignar a mano desde el editor a cualquier otra página.
 *
 * Secciones, en orden del Figma:
 *   1. Hero oscuro (breadcrumb + titular + bajada)
 *   2. Construimos para el futuro (misión / visión + fotos + isla)
 *   3. Objetivos con propósito (aside sticky + 3 objetivos)
 *   4. Nuestros aliados (grilla de logos sobre degradado radial + isla)
 *   5. Marquee "circulogic"
 *   6. Método Circulogic (tabs ESG con autoplay + foto)
 *   7. HDPE: el futuro es circular (chips + escena de pellets + 3 cards)
 *   8. Contactemos (template-parts/contacto.php, con copy propio)
 *
 * Las animaciones viven en src/ts/modules/nosotros.ts (carga perezosa
 * desde main.ts al detectar `[data-nosotros]`), además de los reveals
 * genéricos del theme (data-reveal, data-reveal-header, data-float,
 * data-parallax, data-marquee).
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();

$ese_img = static fn (string $file): string => ESE_LATAM_URI . '/assets/imgs/' . $file;

// ---------- Datos de las secciones ----------

$ese_objetivos = [
    [
        'id'    => 'eficiencia-operativa',
        'title' => __('Eficiencia operativa', 'ese-latam'),
        'img'   => 'sectores/municipalidades.webp',
        'text'  => [
            __('Optimizamos la gestión de residuos urbanos e industriales mediante sistemas de', 'ese-latam'),
            __('contenerización diseñados para durar', 'ese-latam'),
            __('. Reducimos los costos logísticos y los tiempos de recolección gracias a una infraestructura altamente funcional.', 'ese-latam'),
        ],
    ],
    [
        'id'    => 'alianzas-estrategicas',
        'title' => __('Alianzas estratégicas', 'ese-latam'),
        'img'   => 'sectores/recoleccion.webp',
        'text'  => [
            __('Colaboramos activamente con gobiernos locales, industrias y operadores para transformar la gestión ambiental pública y privada. Creamos', 'ese-latam'),
            __('sinergias a largo plazo', 'ese-latam'),
            __(' basadas en la confianza, el cumplimiento de normativas internacionales y el respaldo técnico continuo.', 'ese-latam'),
        ],
    ],
    [
        'id'    => 'economia-circular',
        'title' => __('Economía Circular', 'ese-latam'),
        'img'   => 'terreno/en-terreno.webp',
        'text'  => [
            __('Transformamos el ciclo de vida de los materiales integrando contenedores fabricados bajo', 'ese-latam'),
            __('estrictos criterios de sostenibilidad', 'ese-latam'),
            __('. Promovemos la correcta segregación en la fuente para facilitar el reciclaje masivo y mitigar el impacto ambiental.', 'ese-latam'),
        ],
    ],
];

$ese_metas = [
    __('Promover la economía circular y reducir la generación de residuos en la región.', 'ese-latam'),
    __('Fomentar la creación de infraestructuras sostenibles para la gestión de residuos.', 'ese-latam'),
    __('Desarrollar soluciones integrales y personalizadas: diagnósticos, estrategias de circularidad, capacitaciones, gestión de proyectos, estudios técnicos y más.', 'ese-latam'),
];

// Los tres pilares ESG: cada tab trae su foto (crossfade en nosotros.ts).
$ese_esg = [
    [
        'title' => __('Ambiental', 'ese-latam'),
        'desc'  => __('Reducción de huella ambiental mediante reciclaje de plásticos y optimización de residuos.', 'ese-latam'),
        'img'   => 'nosotros/metodo.webp',
    ],
    [
        'title' => __('Social', 'ese-latam'),
        'desc'  => __('Fomento de entornos inclusivos, seguros y ergonómicos para toda nuestra comunidad.', 'ese-latam'),
        'img'   => 'nosotros/parque.webp',
    ],
    [
        'title' => __('Governanza', 'ese-latam'),
        'desc'  => __('Garantía de transparencia mediante certificaciones internacionales y auditorías de calidad.', 'ese-latam'),
        'img'   => 'terreno/en-terreno.webp',
    ],
];

$ese_chips = [
    __('Certificación Internacional', 'ese-latam'),
    __('Ingeniería Alemana', 'ese-latam'),
    __('Trazabilidad Total', 'ese-latam'),
    __('Acompañamiento experto', 'ese-latam'),
    __('100% Reciclable', 'ese-latam'),
];

// Íconos de las cards HDPE (Figma: ArrowClockwise / Leaf / ShieldCheck, 72px).
$ese_hdpe_cards = [
    [
        'title' => __('Circularidad', 'ese-latam'),
        'desc'  => __('Residuo cero', 'ese-latam'),
        'icon'  => '<path d="M51.5 26.2489V32.7492C51.5 33.0366 51.3906 33.3121 51.1959 33.5153C51.0011 33.7185 50.737 33.8326 50.4616 33.8326H44.231C43.9555 33.8326 43.6914 33.7185 43.4967 33.5153C43.3019 33.3121 43.1925 33.0366 43.1925 32.7492C43.1925 32.4619 43.3019 32.1863 43.4967 31.9832C43.6914 31.78 43.9555 31.6658 44.231 31.6658H47.7876L44.3361 28.3669L44.3036 28.3344C42.8603 26.8292 41.0238 25.801 39.0237 25.3784C37.0235 24.9557 34.9483 25.1573 33.0573 25.9579C31.1662 26.7585 29.5433 28.1228 28.3911 29.8801C27.2389 31.6375 26.6086 33.71 26.5789 35.839C26.5492 37.9679 27.1215 40.0587 28.2242 41.8503C29.3268 43.6419 30.9111 45.0548 32.779 45.9125C34.6469 46.7701 36.7156 47.0346 38.7267 46.6728C40.7378 46.3111 42.6022 45.3391 44.0869 43.8783C44.2871 43.6807 44.5543 43.5743 44.8297 43.5823C45.1051 43.5903 45.3663 43.7121 45.5556 43.9209C45.745 44.1298 45.847 44.4086 45.8393 44.6959C45.8317 44.9833 45.7149 45.2557 45.5147 45.4532C43.2061 47.7375 40.1436 49.0074 36.9619 49H36.7906C34.7497 48.9708 32.7469 48.4191 30.9583 47.3933C29.1697 46.3675 27.65 44.8991 26.5327 43.117C25.4155 41.335 24.7349 39.2939 24.5508 37.1732C24.3667 35.0524 24.6847 32.917 25.477 30.9545C26.2692 28.9921 27.5113 27.2627 29.0942 25.9184C30.6771 24.574 32.5522 23.6559 34.5549 23.2447C36.5575 22.8335 38.6263 22.9418 40.5794 23.5601C42.5326 24.1784 44.3102 25.2877 45.7562 26.7906L49.4231 30.2845V26.2489C49.4231 25.9616 49.5325 25.686 49.7273 25.4829C49.922 25.2797 50.1862 25.1655 50.4616 25.1655C50.737 25.1655 51.0011 25.2797 51.1959 25.4829C51.3906 25.686 51.5 25.9616 51.5 26.2489Z"/>',
        'spin'  => true,
    ],
    [
        'title' => __('Eco-eficiencia', 'ese-latam'),
        'desc'  => __('Menos CO2', 'ese-latam'),
        'icon'  => '<path d="M50.4536 24.9854C50.4393 24.7407 50.3357 24.5098 50.1624 24.3366C49.9891 24.1633 49.7581 24.0596 49.5135 24.0454C43.0424 23.6703 37.8466 25.6379 35.6137 29.323C34.1385 31.7594 34.141 34.7183 35.5937 37.5409C34.7668 38.5251 34.1625 39.6764 33.8222 40.916L31.7881 38.8747C32.7657 36.8333 32.7282 34.7058 31.6631 32.9382C30.0128 30.2143 26.2034 28.7543 21.4739 29.0318C21.2292 29.0461 20.9983 29.1497 20.825 29.323C20.6517 29.4963 20.548 29.7272 20.5337 29.9718C20.255 34.7008 21.7164 38.5096 24.4406 40.1597C25.3396 40.7089 26.3726 40.9995 27.4261 40.9997C28.4487 40.9871 29.4553 40.7449 30.3716 40.291L33.4971 43.4161V47C33.4971 47.2652 33.6025 47.5196 33.79 47.7071C33.9776 47.8946 34.232 48 34.4973 48C34.7625 48 35.0169 47.8946 35.2045 47.7071C35.3921 47.5196 35.4974 47.2652 35.4974 47V43.3136C35.493 41.7226 36.0344 40.1783 37.0314 38.9384C38.3178 39.6106 39.7445 39.9703 41.1959 39.9885C42.5991 39.993 43.9762 39.6097 45.1753 38.8809C48.8609 36.6508 50.8337 31.4556 50.4536 24.9854ZM25.4721 38.4496C23.5542 37.2883 22.4691 34.5395 22.4953 30.9994C26.0359 30.9694 28.7851 32.0582 29.9465 33.9757C30.5529 34.9758 30.6516 36.1421 30.2541 37.3434L27.2023 34.292C27.0132 34.1124 26.7614 34.0137 26.5006 34.017C26.2398 34.0204 25.9907 34.1254 25.8062 34.3099C25.6218 34.4943 25.5167 34.7434 25.5134 35.0042C25.51 35.265 25.6087 35.5167 25.7884 35.7058L28.8401 38.7572C27.6387 39.1547 26.4735 39.0559 25.4721 38.4496ZM44.1388 37.1721C42.4636 38.1859 40.4945 38.2634 38.4942 37.4221L45.2065 30.7094C45.3862 30.5203 45.4848 30.2685 45.4815 30.0078C45.4782 29.747 45.3731 29.4978 45.1886 29.3134C45.0042 29.129 44.755 29.0239 44.4942 29.0206C44.2334 29.0173 43.9816 29.1159 43.7925 29.2955L37.0789 35.9996C36.2338 33.9995 36.3101 32.0294 37.329 30.3556C39.0718 27.4805 43.2049 25.8779 48.497 26.0017C48.6171 31.2919 47.0168 35.4295 44.1388 37.1721Z"/>',
        'spin'  => false,
    ],
    [
        'title' => __('Durabilidad', 'ese-latam'),
        'desc'  => __('Vida extendida', 'ese-latam'),
        'icon'  => '<path d="M47.3333 23H25.6667C25.092 23 24.5409 23.2276 24.1346 23.6326C23.7283 24.0377 23.5 24.5871 23.5 25.16V32.72C23.5 39.8372 26.9558 44.1504 29.8551 46.5156C32.9778 49.0617 36.0843 49.9257 36.2197 49.9621C36.4059 50.0126 36.6022 50.0126 36.7884 49.9621C36.9239 49.9257 40.0263 49.0617 43.153 46.5156C46.0442 44.1504 49.5 39.8372 49.5 32.72V25.16C49.5 24.5871 49.2717 24.0377 48.8654 23.6326C48.4591 23.2276 47.908 23 47.3333 23ZM47.3333 32.72C47.3333 37.7244 45.4835 41.7865 41.8354 44.7916C40.2474 46.0953 38.4413 47.1099 36.5 47.7886C34.5841 47.1217 32.8003 46.1252 31.2296 44.8443C27.5381 41.8338 25.6667 37.7555 25.6667 32.72V25.16H47.3333V32.72ZM30.3169 36.7241C30.1136 36.5214 29.9994 36.2466 29.9994 35.96C29.9994 35.6734 30.1136 35.3985 30.3169 35.1959C30.5202 34.9932 30.7959 34.8794 31.0833 34.8794C31.3708 34.8794 31.6465 34.9932 31.8498 35.1959L34.3333 37.6731L41.1502 30.8759C41.2509 30.7755 41.3704 30.6959 41.5019 30.6416C41.6334 30.5873 41.7743 30.5594 41.9167 30.5594C42.059 30.5594 42.2 30.5873 42.3315 30.6416C42.463 30.6959 42.5825 30.7755 42.6831 30.8759C42.7838 30.9762 42.8636 31.0953 42.9181 31.2264C42.9726 31.3576 43.0006 31.4981 43.0006 31.64C43.0006 31.7819 42.9726 31.9224 42.9181 32.0535C42.8636 32.1846 42.7838 32.3037 42.6831 32.4041L35.0998 39.9641C34.9992 40.0645 34.8797 40.1441 34.7482 40.1985C34.6167 40.2528 34.4757 40.2808 34.3333 40.2808C34.191 40.2808 34.05 40.2528 33.9185 40.1985C33.787 40.1441 33.6675 40.0645 33.5669 39.9641L30.3169 36.7241Z"/>',
        'spin'  => false,
    ],
];

// Check-circle (Figma check_circle 17px) — compartido por chips y metas.
$ese_check_svg = '<svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M7.23015 12.306L13.2455 6.29067L12.3026 5.34784L7.23015 10.4203L4.68015 7.87033L3.73732 8.81316L7.23015 12.306ZM8.50157 17C7.32588 17 6.22081 16.7769 5.18634 16.3307C4.15188 15.8846 3.25207 15.279 2.48692 14.5142C1.72177 13.7493 1.11596 12.8499 0.669487 11.8159C0.223162 10.7819 0 9.6771 0 8.50157C0 7.32588 0.223088 6.22081 0.669263 5.18634C1.11544 4.15188 1.72095 3.25207 2.4858 2.48692C3.25065 1.72177 4.15009 1.11596 5.18411 0.669487C6.21812 0.223162 7.3229 0 8.49843 0C9.67412 0 10.7792 0.223087 11.8137 0.669263C12.8481 1.11544 13.7479 1.72095 14.5131 2.4858C15.2782 3.25065 15.884 4.15009 16.3305 5.18411C16.7768 6.21812 17 7.3229 17 8.49843C17 9.67412 16.7769 10.7792 16.3307 11.8137C15.8846 12.8481 15.279 13.7479 14.5142 14.5131C13.7493 15.2782 12.8499 15.884 11.8159 16.3305C10.7819 16.7768 9.6771 17 8.50157 17ZM8.5 15.6579C10.4982 15.6579 12.1908 14.9645 13.5776 13.5776C14.9645 12.1908 15.6579 10.4982 15.6579 8.5C15.6579 6.50175 14.9645 4.80921 13.5776 3.42237C12.1908 2.03553 10.4982 1.34211 8.5 1.34211C6.50175 1.34211 4.80921 2.03553 3.42237 3.42237C2.03553 4.80921 1.34211 6.50175 1.34211 8.5C1.34211 10.4982 2.03553 12.1908 3.42237 13.5776C4.80921 14.9645 6.50175 15.6579 8.5 15.6579Z" fill="currentColor"/></svg>';
?>

<div class="nosotros" data-nosotros>
    <?php // Marca "hay JS": solo entonces el CSS esconde el titular del hero
    // hasta que nosotros.ts lo anime (sin JS queda visible desde el HTML). ?>
    <script>document.currentScript.parentElement.classList.add('is-js');</script>

    <?php // ---------- 1. HERO ---------- ?>
    <section class="nos-hero" data-nos-hero>
        <div class="nos-hero__bg" aria-hidden="true" data-nos-hero-bg>
            <img src="<?php echo esc_url($ese_img('hero-poster.webp')); ?>" alt="" decoding="async" fetchpriority="high">
        </div>
        <div class="nos-hero__shade" aria-hidden="true"></div>
        <div class="nos-hero__glow" aria-hidden="true" data-nos-hero-glow></div>

        <div class="nos-hero__content" data-nos-hero-content>
            <nav class="nos-crumb" aria-label="<?php esc_attr_e('Ruta de navegación', 'ese-latam'); ?>" data-nos-hero-crumb>
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M10 5.27979V10.56C10 10.6767 9.9561 10.7886 9.87796 10.8711C9.79982 10.9536 9.69384 11 9.58333 11H6.66667C6.55616 11 6.45018 10.9536 6.37204 10.8711C6.2939 10.7886 6.25 10.6767 6.25 10.56V7.69988C6.25 7.64153 6.22805 7.58557 6.18898 7.54431C6.14991 7.50305 6.09692 7.47987 6.04167 7.47987H3.95833C3.90308 7.47987 3.85009 7.50305 3.81102 7.54431C3.77195 7.58557 3.75 7.64153 3.75 7.69988V10.56C3.75 10.6767 3.7061 10.7886 3.62796 10.8711C3.54982 10.9536 3.44384 11 3.33333 11H0.416667C0.30616 11 0.200179 10.9536 0.122039 10.8711C0.0438988 10.7886 0 10.6767 0 10.56V5.27979C0.000102442 5.04643 0.0879669 4.82267 0.244271 4.65772L4.41094 0.257552C4.5672 0.0926383 4.77908 0 5 0C5.22092 0 5.4328 0.0926383 5.58906 0.257552L9.75573 4.65772C9.91203 4.82267 9.9999 5.04643 10 5.27979Z" fill="currentColor"/>
                    </svg>
                    <?php esc_html_e('Inicio', 'ese-latam'); ?>
                </a>
                <span class="nos-crumb__sep" aria-hidden="true">
                    <svg width="4" height="7" viewBox="0 0 4 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.86395 3.8233L0.788805 6.86609C0.70215 6.95183 0.58462 7 0.462071 7C0.339522 7 0.221993 6.95183 0.135337 6.86609C0.0486823 6.78034 0 6.66405 0 6.54279C0 6.42153 0.0486823 6.30524 0.135337 6.21949L2.88413 3.50038L0.136106 0.780507C0.0931991 0.738051 0.059163 0.687648 0.0359418 0.632177C0.0127205 0.576706 0.000768656 0.517252 0.000768656 0.45721C0.000768656 0.397169 0.0127205 0.337715 0.0359418 0.282243C0.059163 0.226772 0.0931991 0.17637 0.136106 0.133914C0.179014 0.0914579 0.229952 0.0577801 0.286013 0.0348031C0.342074 0.0118261 0.40216 0 0.46284 0C0.52352 0 0.583606 0.0118261 0.639667 0.0348031C0.695728 0.0577801 0.746666 0.0914579 0.789574 0.133914L3.86471 3.1767C3.90767 3.21916 3.94173 3.26958 3.96494 3.32509C3.98816 3.38059 4.00007 3.44009 4 3.50016C3.99993 3.56023 3.98787 3.61969 3.96453 3.67515C3.94118 3.7306 3.907 3.78094 3.86395 3.8233Z" fill="currentColor"/>
                    </svg>
                </span>
                <span aria-current="page"><?php esc_html_e('Nosotros', 'ese-latam'); ?></span>
            </nav>

            <div class="nos-hero__bottom">
                <h1 class="nos-hero__title" data-nos-hero-title>
                    <?php esc_html_e('reinventando el', 'ese-latam'); ?>
                    <strong><?php esc_html_e('entorno en latam', 'ese-latam'); ?></strong>
                </h1>
                <p class="nos-hero__desc" data-nos-hero-desc>
                    <?php esc_html_e('Somos una empresa dedicada al diseño y fabricación de contenedores de residuos sólidos, con presencia en', 'ese-latam'); ?>
                    <span class="nos-hero__count" data-nos-count="13">13</span>
                    <?php esc_html_e('países de Latinoamérica y respaldo de ingeniería europea', 'ese-latam'); ?>
                </p>
            </div>
        </div>

        <div class="nos-hero__scroll" aria-hidden="true" data-nos-hero-scroll>
            <span class="nos-hero__scroll-line"></span>
            <span class="nos-hero__scroll-text"><?php esc_html_e('Scroll', 'ese-latam'); ?></span>
        </div>
    </section>

    <?php // ---------- 2. CONSTRUIMOS PARA EL FUTURO ---------- ?>
    <section class="nos-construimos" data-nos-construimos>
        <header class="nos-construimos__header" data-reveal-header>
            <p class="type-kicker text-secondary">/ <?php esc_html_e('Sobre nosotros', 'ese-latam'); ?></p>
            <h2 class="type-h2 uppercase">
                <?php esc_html_e('Construimos', 'ese-latam'); ?><br>
                <span class="hl"><?php esc_html_e('para el futuro', 'ese-latam'); ?></span>
            </h2>
        </header>

        <figure class="nos-construimos__photo" data-nos-panel="left">
            <img src="<?php echo esc_url($ese_img('nosotros/parque.webp')); ?>"
                 alt="<?php esc_attr_e('Voluntarios recogiendo residuos en un parque', 'ese-latam'); ?>"
                 width="1024" height="683" loading="lazy" decoding="async" data-nos-panel-img>
        </figure>

        <div class="nos-construimos__text">
            <div class="nos-construimos__block" data-reveal="up">
                <h3><?php esc_html_e('misión', 'ese-latam'); ?></h3>
                <p><?php esc_html_e('Proveer sistemas de contención de residuos de ingeniería europea para municipios, empresas y comunidades de Latinoamérica, con durabilidad, cumplimiento normativo y compromiso ambiental.', 'ese-latam'); ?></p>
            </div>
            <div class="nos-construimos__block" data-reveal="up" data-reveal-delay="0.15">
                <h3><?php esc_html_e('visión', 'ese-latam'); ?></h3>
                <p><?php esc_html_e('Ser el estándar de referencia en gestión de residuos sólidos en Latinoamérica, transformando la infraestructura urbana e industrial de la región desde la calidad y la responsabilidad.', 'ese-latam'); ?></p>
            </div>
        </div>

        <div class="nos-construimos__sky">
            <div class="nos-construimos__sky-frame" data-nos-panel="right">
                <img class="nos-construimos__sky-img" src="<?php echo esc_url($ese_img('nosotros/cielo.webp')); ?>" alt=""
                     width="1400" height="935" loading="lazy" decoding="async" data-nos-panel-img>
            </div>
            <div class="nos-construimos__island" data-parallax data-parallax-from="12" data-parallax-to="-12">
                <div class="nos-construimos__island-enter" data-nos-island>
                    <img src="<?php echo esc_url($ese_img('nosotros/isla.webp')); ?>"
                         alt="<?php esc_attr_e('Isla flotante con contenedores ESE', 'ese-latam'); ?>"
                         width="1370" height="955" loading="lazy" decoding="async"
                         data-float data-float-distance="14" data-float-duration="3.6">
                </div>
            </div>
        </div>
    </section>

    <?php // ---------- 3. OBJETIVOS CON PROPÓSITO ---------- ?>
    <section id="objetivos" class="nos-objetivos" data-nos-objetivos>
        <header class="nos-objetivos__header" data-reveal-header>
            <div>
                <p class="type-kicker text-secondary">/ <?php esc_html_e('Sobre nosotros', 'ese-latam'); ?></p>
                <h2 class="type-h2 uppercase">
                    <?php esc_html_e('Objetivos con', 'ese-latam'); ?><br>
                    <span class="hl"><?php esc_html_e('propósito', 'ese-latam'); ?></span>
                </h2>
            </div>
            <p class="nos-desc" data-reveal-desc>
                <?php esc_html_e('Metas claras que', 'ese-latam'); ?>
                <span class="hl-accent"><?php esc_html_e('transforman', 'ese-latam'); ?></span>
                <?php esc_html_e('y', 'ese-latam'); ?>
                <span class="hl-accent"><?php esc_html_e('mejoran', 'ese-latam'); ?></span>
                <?php esc_html_e('la gestión de residuos a nivel global.', 'ese-latam'); ?>
            </p>
        </header>

        <div class="nos-objetivos__layout">
            <aside class="nos-objetivos__aside">
                <div class="nos-objetivos__sticky">
                    <nav class="nos-objetivos__nav" aria-label="<?php esc_attr_e('Objetivos', 'ese-latam'); ?>" data-nos-obj-nav>
                        <span class="nos-objetivos__chevron" aria-hidden="true" data-nos-obj-chevron>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M23.1249 12.4163L18.8442 18.8325C18.7075 19.0375 18.5224 19.2056 18.3052 19.322C18.0881 19.4384 17.8456 19.4995 17.5992 19.5H3.00049C2.86467 19.5001 2.73138 19.4633 2.61485 19.3935C2.49831 19.3238 2.4029 19.2237 2.3388 19.104C2.27469 18.9843 2.24431 18.8494 2.25088 18.7137C2.25745 18.5781 2.30074 18.4467 2.37611 18.3337L6.59955 12L2.3808 5.66625C2.30564 5.5536 2.26238 5.42271 2.25562 5.28746C2.24885 5.15221 2.27883 5.01765 2.34237 4.89807C2.40591 4.77849 2.50065 4.67834 2.61652 4.60825C2.73239 4.53816 2.86507 4.50076 3.00049 4.5H17.5992C17.8456 4.50046 18.0881 4.5616 18.3052 4.67801C18.5224 4.79443 18.7075 4.96255 18.8442 5.1675L23.122 11.5837C23.2047 11.7067 23.2491 11.8514 23.2496 11.9996C23.2501 12.1477 23.2067 12.2927 23.1249 12.4163Z" fill="currentColor"/>
                            </svg>
                        </span>
                        <?php foreach ($ese_objetivos as $ese_i => $ese_obj): ?>
                            <a href="#<?php echo esc_attr($ese_obj['id']); ?>"
                               class="nos-objetivos__nav-item<?php echo 0 === $ese_i ? ' is-active' : ''; ?>"
                               data-nos-obj-link="<?php echo (int) $ese_i; ?>">
                                <?php echo esc_html($ese_obj['title']); ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>

                    <a href="#aliados" class="nos-objetivos__skip" data-reveal="fade">
                        <?php esc_html_e('Saltar objetivos', 'ese-latam'); ?>
                    </a>

                    <div class="nos-objetivos__metas" data-reveal="up" data-reveal-delay="0.1">
                        <p class="nos-objetivos__metas-title"><?php esc_html_e('Algunas de nuestras metas y objetivos del programa:', 'ese-latam'); ?></p>
                        <ul class="nos-objetivos__metas-list">
                            <?php foreach ($ese_metas as $ese_meta): ?>
                                <li>
                                    <span class="nos-objetivos__metas-icon"><?php echo $ese_check_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
                                    <span><?php echo esc_html($ese_meta); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </aside>

            <div class="nos-objetivos__list">
                <?php foreach ($ese_objetivos as $ese_i => $ese_obj): ?>
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

    <?php // ---------- 4. NUESTROS ALIADOS ---------- ?>
    <section id="aliados" class="nos-aliados" data-nos-aliados>
        <div class="nos-aliados__rings" aria-hidden="true">
            <span class="nos-aliados__ring" data-nos-ring></span>
            <span class="nos-aliados__ring" data-nos-ring></span>
            <span class="nos-aliados__ring" data-nos-ring></span>
        </div>

        <header class="nos-aliados__header" data-reveal-header>
            <p class="type-kicker text-white">/ <?php esc_html_e('Nuestros aliados', 'ese-latam'); ?></p>
            <h2 class="nos-aliados__title">
                <?php esc_html_e('Trabajamos con', 'ese-latam'); ?><br>
                <strong><?php esc_html_e('los mejores aliados', 'ese-latam'); ?></strong>
            </h2>
        </header>

        <ul class="nos-aliados__grid" data-nos-logos>
            <?php for ($ese_l = 0; $ese_l < 10; $ese_l++): ?>
                <li class="nos-aliados__card" data-nos-logo>
                    <img src="<?php echo esc_url($ese_img('nosotros/aliado-logo.svg')); ?>" alt="Logoipsum" width="149" height="30" loading="lazy" decoding="async">
                </li>
            <?php endfor; ?>
        </ul>

        <div class="nos-aliados__island" data-nos-aliados-island>
            <div class="nos-aliados__island-tilt" data-nos-aliados-tilt>
                <span class="nos-aliados__island-shadow" aria-hidden="true" data-float-shadow></span>
                <img src="<?php echo esc_url($ese_img('nosotros/isla.webp')); ?>" alt="" width="1370" height="955"
                     loading="lazy" decoding="async" data-float data-float-distance="16" data-float-duration="4">
            </div>
        </div>
    </section>

    <?php // ---------- 5. MARQUEE CIRCULOGIC ---------- ?>
    <div class="nos-marquee" aria-hidden="true">
        <p class="nos-marquee__track" data-marquee="right">
            <span>circulogic</span><span>circulogic</span><span>circulogic</span>
        </p>
    </div>

    <?php // ---------- 6. MÉTODO CIRCULOGIC ---------- ?>
    <section id="metodo" class="nos-metodo" data-nos-metodo data-nos-metodo-autoplay="6000">
        <header class="nos-metodo__header" data-reveal-header>
            <div>
                <p class="type-kicker text-secondary">/ <?php esc_html_e('Sobre nosotros', 'ese-latam'); ?></p>
                <h2 class="type-h2 uppercase">
                    <?php esc_html_e('Método', 'ese-latam'); ?><br>
                    <span class="hl"><?php esc_html_e('Circulogic', 'ese-latam'); ?></span>
                </h2>
            </div>
            <p class="nos-desc" data-reveal-desc>
                <?php esc_html_e('Nuestra producción está', 'ese-latam'); ?>
                <span class="hl-accent"><?php esc_html_e('estratégicamente diseñada', 'ese-latam'); ?></span>
                <?php esc_html_e('para cumplir con exigentes criterios', 'ese-latam'); ?>
                <span class="hl-accent"><?php esc_html_e('ESG', 'ese-latam'); ?></span>
                <?php esc_html_e('(Ambientales, Sociales y de Gobernanza) de forma real, transparente y medible.', 'ese-latam'); ?>
            </p>
        </header>

        <div class="nos-metodo__tabs" role="tablist" data-reveal-stagger>
            <?php foreach ($ese_esg as $ese_i => $ese_pilar): ?>
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
                <img src="<?php echo esc_url($ese_img($ese_esg[0]['img'])); ?>" alt="" width="1800" height="1200"
                     loading="lazy" decoding="async" data-nos-metodo-img data-parallax data-parallax-from="8" data-parallax-to="-8">
            </div>
            <span class="nos-metodo__leaf" aria-hidden="true" data-nos-metodo-leaf>
                <svg width="72" height="57" viewBox="0 0 72 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M59.2155 35.3469C56.3388 37.0774 53.0347 37.9876 49.6681 37.9768C46.8477 37.9547 44.0593 37.3838 41.4615 36.2968C39.4594 39.0939 38.3879 42.4389 38.396 45.8666V54.6201C38.3967 54.9457 38.3298 55.2679 38.1994 55.5667C38.069 55.8656 37.8779 56.1347 37.638 56.3574C37.398 56.5801 37.1144 56.7516 36.8046 56.8613C36.4949 56.9709 36.1657 57.0164 35.8374 56.9948C35.2207 56.9417 34.647 56.6597 34.2314 56.2056C33.8159 55.7514 33.5892 55.1586 33.5968 54.5459V50.8534L22.0128 39.3898C20.2907 40.0255 18.4696 40.358 16.6317 40.3723C14.1015 40.3784 11.6187 39.6933 9.45687 38.3924C2.92097 34.4624 -0.597444 25.4179 0.0834413 14.1888C0.117724 13.6079 0.366375 13.0596 0.782186 12.6481C1.198 12.2366 1.75202 11.9906 2.33906 11.9566C13.6862 11.2947 22.8256 14.7647 26.785 21.2326C28.3405 23.7678 29.0197 26.7353 28.7196 29.6864C28.701 29.915 28.6158 30.1334 28.4744 30.3152C28.333 30.4969 28.1414 30.6342 27.9228 30.7105C27.7042 30.7868 27.4679 30.7988 27.2426 30.7451C27.0172 30.6913 26.8125 30.5741 26.653 30.4076L20.894 24.4414C20.4403 24.0148 19.8362 23.7805 19.2105 23.7885C18.5847 23.7964 17.9869 24.0459 17.5444 24.4838C17.1019 24.9217 16.8498 25.5133 16.8418 26.1325C16.8338 26.7517 17.0705 27.3496 17.5015 27.7985L33.6628 44.1984C33.6808 43.9669 33.7018 43.7354 33.7258 43.5068C34.2505 39.104 36.2137 34.9913 39.3169 31.7939L54.4913 15.9253C54.9416 15.4801 55.1947 14.8761 55.195 14.2463C55.1953 13.6164 54.9427 13.0122 54.4928 12.5666C54.043 12.1211 53.4326 11.8706 52.7962 11.8703C52.1597 11.87 51.5491 12.12 51.0989 12.5652L36.4014 27.9469C36.2542 28.1011 36.0681 28.2135 35.8622 28.2724C35.6563 28.3313 35.4383 28.3346 35.2307 28.2819C35.0231 28.2292 34.8336 28.1224 34.6818 27.9727C34.5301 27.8229 34.4216 27.6357 34.3677 27.4304C32.9459 22.2418 33.5728 17.077 36.2874 12.6423C41.6445 3.89176 54.1104 -0.792232 69.6358 0.110134C70.2228 0.14406 70.7768 0.390125 71.1927 0.801613C71.6085 1.2131 71.8571 1.76136 71.8914 2.3423C72.7912 17.7092 68.058 30.0455 59.2155 35.3469Z" fill="currentColor"/>
                </svg>
            </span>
        </div>
    </section>

    <?php // ---------- 7. HDPE: EL FUTURO ES CIRCULAR ---------- ?>
    <section id="hdpe" class="nos-hdpe" data-nos-hdpe>
        <div class="nos-hdpe__top">
            <header class="nos-hdpe__header" data-reveal-header>
                <p class="type-kicker text-secondary">/ <?php esc_html_e('Tecnología y sostenibilidad', 'ese-latam'); ?></p>
                <h2 class="type-h2 uppercase">
                    <?php esc_html_e('hdpe: el futuro es', 'ese-latam'); ?><br>
                    <span class="hl"><?php esc_html_e('circular', 'ese-latam'); ?></span>
                </h2>
                <p class="nos-desc" data-reveal-desc>
                    <?php esc_html_e('Transformamos residuos en recursos de alta resistencia, garantizando el cumplimiento estricto de los', 'ese-latam'); ?>
                    <span class="hl-accent"><?php esc_html_e('estándares ESG', 'ese-latam'); ?></span>
                    <?php esc_html_e('y aportando valor real a cada etapa de su operación.', 'ese-latam'); ?>
                </p>
                <ul class="nos-hdpe__chips" data-nos-chips>
                    <?php foreach ($ese_chips as $ese_chip): ?>
                        <li class="nos-hdpe__chip">
                            <?php echo $ese_check_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                            <?php echo esc_html($ese_chip); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </header>

            <div class="nos-hdpe__scene" data-nos-panel="right">
                <canvas class="nos-hdpe__canvas" data-nos-pellets aria-hidden="true"></canvas>
                <div class="nos-hdpe__scene-label">
                    <p class="nos-hdpe__scene-kicker"><?php esc_html_e('Impacto positivo', 'ese-latam'); ?></p>
                    <p class="nos-hdpe__scene-sub"><?php esc_html_e('Estándar Global', 'ese-latam'); ?></p>
                </div>
                <p class="nos-hdpe__scene-hint" aria-hidden="true"><?php esc_html_e('Mueve el cursor', 'ese-latam'); ?></p>
            </div>
        </div>

        <ul class="nos-hdpe__cards" data-reveal-stagger>
            <?php foreach ($ese_hdpe_cards as $ese_card): ?>
                <li class="nos-hdpe__card">
                    <span class="nos-hdpe__card-icon<?php echo $ese_card['spin'] ? ' nos-hdpe__card-icon--spin' : ''; ?>" aria-hidden="true" data-nos-hdpe-icon>
                        <svg width="72" height="72" viewBox="0 0 72 72" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <?php echo $ese_card['icon']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                        </svg>
                    </span>
                    <p class="nos-hdpe__card-title"><?php echo esc_html($ese_card['title']); ?></p>
                    <p class="nos-hdpe__card-desc"><?php echo esc_html($ese_card['desc']); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <?php // ---------- 8. CONTACTEMOS ---------- ?>
    <?php
    get_template_part('template-parts/contacto', null, [
        'class'          => 'contacto--upper',
        'heading'        => __('¿Quieres ser parte del cambio hacia una gestión de residuos', 'ese-latam'),
        'heading_strong' => __('más responsable', 'ese-latam'),
        'desc'           => __('Conversemos sobre cómo tu organización puede sumarse a nuestra red de impacto.', 'ese-latam'),
        'bg'             => $ese_img('nosotros/cta-bg.webp'),
    ]);
    ?>

</div>

<?php
get_footer();
