<?php
/**
 * Template Name: Encuentra un distribuidor
 *
 * Página "Encuentra un distribuidor" (Figma node 3952-9273, "14 – Encuentra
 * un Distribuidor"). WordPress la aplica sola a la página con slug
 * `distribuidores` (que crea inc/paginas.php) y, por el header de arriba,
 * también se puede asignar a mano desde el editor.
 *
 * Fondo azul degradado de punta a punta. Dos columnas: a la izquierda el
 * buscador (texto + país), el contador y las tarjetas de distribuidor; a la
 * derecha el mapa (embed de Google Maps, sin API key — mismo recurso que la
 * sede en contacto-sede.php) pegado con sticky, que sigue a la tarjeta
 * activa. Los datos salen de inc/distribuidores.php, los mismos del globo
 * de la home.
 *
 * Filtros por query string (?q=, ?pais=) para que funcione sin JS; con JS
 * (src/ts/modules/distribuidores-page.ts) el filtrado es en vivo y el mapa
 * cambia al hacer clic en una tarjeta. Sin sección Contactemos: el Figma
 * cierra directo con el footer.
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();

$ese_dst_base   = ese_latam_distribuidores_url();
$ese_dst_q      = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';
$ese_dst_pais   = isset($_GET['pais']) ? sanitize_title(wp_unslash($_GET['pais'])) : '';
$ese_dst_paises = ese_latam_distribuidores();
$ese_dst_todos  = ese_latam_distribuidores_planos();

// Filtro del lado del servidor (sin JS). Con JS, el módulo vuelve a filtrar
// sobre TODAS las tarjetas del DOM, así que acá solo se marca `hidden`.
$ese_dst_q_norm = mb_strtolower($ese_dst_q);
$ese_dst_visibles = 0;
foreach ($ese_dst_todos as $ese_i => $ese_d) {
    $ok = ('' === $ese_dst_pais || $ese_d['pais_slug'] === $ese_dst_pais)
        && ('' === $ese_dst_q_norm || false !== mb_strpos($ese_d['buscar'], $ese_dst_q_norm));
    $ese_dst_todos[$ese_i]['visible'] = $ok;
    if ($ok) {
        $ese_dst_visibles++;
    }
}

// El mapa arranca en el primer distribuidor visible.
$ese_dst_activo = null;
foreach ($ese_dst_todos as $ese_d) {
    if ($ese_d['visible']) {
        $ese_dst_activo = $ese_d;
        break;
    }
}
$ese_dst_mapa_q = $ese_dst_activo['mapa'] ?? 'Latinoamérica';

// Íconos de la ficha (Figma: cuadrado 32px blanco al 12% + glifo blanco).
$ese_dst_icons = [
    'rep'     => 'M22.9206 22.209C21.8965 20.4993 20.3183 19.2734 18.4766 18.6923C19.3876 18.1686 20.0954 17.3706 20.4913 16.4208C20.8872 15.4711 20.9493 14.4221 20.668 13.435C20.3868 12.4478 19.7778 11.5771 18.9345 10.9566C18.0912 10.3361 17.0602 10 16 10C14.9398 10 13.9088 10.3361 13.0655 10.9566C12.2222 11.5771 11.6132 12.4478 11.332 13.435C11.0507 14.4221 11.1128 15.4711 11.5087 16.4208C11.9046 17.3706 12.6124 18.1686 13.5234 18.6923C11.6817 19.2728 10.1035 20.4987 9.07937 22.209C9.04182 22.2681 9.0169 22.3339 9.00611 22.4025C8.99531 22.4711 8.99885 22.541 9.01652 22.6083C9.03418 22.6755 9.06561 22.7386 9.10895 22.794C9.1523 22.8493 9.20667 22.8957 9.26888 22.9304C9.33108 22.9651 9.39985 22.9874 9.47113 22.996C9.54241 23.0046 9.61476 22.9993 9.6839 22.9805C9.75304 22.9617 9.81758 22.9297 9.8737 22.8864C9.92983 22.8431 9.9764 22.7894 10.0107 22.7284C11.2775 20.6142 13.5167 19.352 16 19.352C18.4833 19.352 20.7225 20.6142 21.9893 22.7284C22.0236 22.7894 22.0702 22.8431 22.1263 22.8864C22.1824 22.9297 22.247 22.9617 22.3161 22.9805C22.3852 22.9993 22.4576 23.0046 22.5289 22.996C22.6001 22.9874 22.6689 22.9651 22.7311 22.9304C22.7933 22.8957 22.8477 22.8493 22.891 22.794C22.9344 22.7386 22.9658 22.6755 22.9835 22.6083C23.0011 22.541 23.0047 22.4711 22.9939 22.4025C22.9831 22.3339 22.9582 22.2681 22.9206 22.209ZM12.2344 14.6769C12.2344 13.9577 12.4553 13.2547 12.869 12.6567C13.2828 12.0587 13.8709 11.5927 14.559 11.3175C15.247 11.0423 16.0042 10.9702 16.7346 11.1105C17.4651 11.2508 18.1361 11.5972 18.6627 12.1057C19.1893 12.6142 19.5479 13.2621 19.6932 13.9675C19.8385 14.6728 19.764 15.4039 19.479 16.0684C19.1939 16.7328 18.7113 17.3007 18.0921 17.7002C17.4728 18.0998 16.7448 18.3131 16 18.3131C15.0016 18.312 14.0445 17.9286 13.3385 17.2469C12.6325 16.5652 12.2355 15.6409 12.2344 14.6769Z',
    'phone'   => 'M19.1667 7.5H11.8333C11.3471 7.5 10.8808 7.6919 10.537 8.03348C10.1932 8.37507 10 8.83836 10 9.32143V22.6786C10 23.1616 10.1932 23.6249 10.537 23.9665C10.8808 24.3081 11.3471 24.5 11.8333 24.5H19.1667C19.6529 24.5 20.1192 24.3081 20.463 23.9665C20.8068 23.6249 21 23.1616 21 22.6786V9.32143C21 8.83836 20.8068 8.37507 20.463 8.03348C20.1192 7.6919 19.6529 7.5 19.1667 7.5ZM19.7778 22.6786C19.7778 22.8396 19.7134 22.994 19.5988 23.1079C19.4842 23.2217 19.3287 23.2857 19.1667 23.2857H11.8333C11.6713 23.2857 11.5158 23.2217 11.4012 23.1079C11.2866 22.994 11.2222 22.8396 11.2222 22.6786V9.32143C11.2222 9.1604 11.2866 9.00598 11.4012 8.89211C11.5158 8.77825 11.6713 8.71429 11.8333 8.71429H19.1667C19.3287 8.71429 19.4842 8.77825 19.5988 8.89211C19.7134 9.00598 19.7778 9.1604 19.7778 9.32143V22.6786ZM16.4167 10.8393C16.4167 11.0194 16.3629 11.1955 16.2622 11.3453C16.1615 11.495 16.0183 11.6117 15.8508 11.6807C15.6833 11.7496 15.499 11.7676 15.3212 11.7325C15.1434 11.6974 14.98 11.6106 14.8518 11.4833C14.7236 11.3559 14.6363 11.1936 14.6009 11.017C14.5656 10.8403 14.5837 10.6572 14.6531 10.4908C14.7225 10.3244 14.84 10.1821 14.9907 10.0821C15.1415 9.98198 15.3187 9.92857 15.5 9.92857C15.7431 9.92857 15.9763 10.0245 16.1482 10.1953C16.3201 10.3661 16.4167 10.5977 16.4167 10.8393Z',
    'email'   => 'M22.4615 10.5H9.53846C9.39565 10.5 9.25869 10.5579 9.15771 10.6611C9.05673 10.7642 9 10.9041 9 11.05V20.4C9 20.6917 9.11346 20.9715 9.31542 21.1778C9.51739 21.3841 9.79131 21.5 10.0769 21.5H21.9231C22.2087 21.5 22.4826 21.3841 22.6846 21.1778C22.8865 20.9715 23 20.6917 23 20.4V11.05C23 10.9041 22.9433 10.7642 22.8423 10.6611C22.7413 10.5579 22.6043 10.5 22.4615 10.5ZM16 16.3541L10.923 11.6H21.077L16 16.3541ZM14.0286 16L10.0769 19.6994V12.3006L14.0286 16ZM14.8255 16.7459L15.6332 17.5056C15.7325 17.5988 15.8625 17.6505 15.9973 17.6505C16.1322 17.6505 16.2621 17.5988 16.3614 17.5056L17.1691 16.7459L21.073 20.4H10.923L14.8255 16.7459ZM17.9714 16L21.9231 12.2999V19.7001L17.9714 16Z',
    'address' => 'M15.4286 12.6481C15.4286 12.443 15.4914 12.2425 15.6091 12.072C15.7269 11.9015 15.8942 11.7685 16.09 11.6901C16.2858 11.6116 16.5012 11.591 16.709 11.631C16.9169 11.6711 17.1078 11.7698 17.2576 11.9149C17.4075 12.0599 17.5095 12.2447 17.5508 12.4458C17.5922 12.647 17.571 12.8555 17.4899 13.045C17.4088 13.2345 17.2714 13.3965 17.0953 13.5104C16.9191 13.6244 16.7119 13.6852 16.5 13.6852C16.2158 13.6852 15.9433 13.5759 15.7424 13.3814C15.5415 13.187 15.4286 12.9232 15.4286 12.6481ZM12.2143 12.6481C12.2143 11.548 12.6658 10.4929 13.4695 9.71496C14.2733 8.93704 15.3634 8.5 16.5 8.5C17.6366 8.5 18.7267 8.93704 19.5305 9.71496C20.3342 10.4929 20.7857 11.548 20.7857 12.6481C20.7857 16.5338 16.9299 18.7109 16.7679 18.8023C16.6869 18.8471 16.5953 18.8707 16.502 18.8707C16.4088 18.8707 16.3171 18.8471 16.2362 18.8023C16.0701 18.7109 12.2143 16.537 12.2143 12.6481ZM13.2857 12.6481C13.2857 15.3833 15.6857 17.1988 16.5 17.7361C17.3136 17.1994 19.7143 15.3833 19.7143 12.6481C19.7143 11.823 19.3756 11.0317 18.7728 10.4483C18.17 9.86481 17.3525 9.53704 16.5 9.53704C15.6475 9.53704 14.83 9.86481 14.2272 10.4483C13.6244 11.0317 13.2857 11.823 13.2857 12.6481ZM21.5069 17.0316C21.3751 16.9894 21.2315 16.9985 21.1064 17.0568C20.9813 17.1151 20.8844 17.2182 20.8363 17.3443C20.7882 17.4704 20.7926 17.6096 20.8485 17.7326C20.9044 17.8557 21.0074 17.9529 21.1359 18.0038C22.2415 18.3998 22.9286 18.93 22.9286 19.3889C22.9286 20.2548 20.483 21.463 16.5 21.463C12.517 21.463 10.0714 20.2548 10.0714 19.3889C10.0714 18.93 10.7585 18.3998 11.8641 18.0044C11.9926 17.9535 12.0956 17.8563 12.1515 17.7333C12.2074 17.6102 12.2118 17.471 12.1637 17.3449C12.1156 17.2188 12.0187 17.1158 11.8936 17.0574C11.7685 16.9991 11.6249 16.9901 11.4931 17.0322C9.88527 17.6058 9 18.4432 9 19.3889C9 21.4098 12.8645 22.5 16.5 22.5C20.1355 22.5 24 21.4098 24 19.3889C24 18.4432 23.1147 17.6058 21.5069 17.0316Z',
];
$ese_dst_campos = [
    'rep'     => __('Representante', 'ese-latam'),
    'phone'   => __('Contacto', 'ese-latam'),
    'email'   => __('E-mail', 'ese-latam'),
    'address' => __('Ubicación', 'ese-latam'),
];
?>

<div class="dst" data-distribuidores-page>
    <section class="dst-hero">
        <?php
        get_template_part('template-parts/breadcrumbs', null, [
            'current' => __('Distribuidores', 'ese-latam'),
            'attrs'   => 'data-reveal="fade"',
        ]);
        ?>

        <h1 class="dst-hero__title" data-reveal="up">
            <?php echo ese_latam_titulo(__('Encuentra un', 'ese-latam') .' '. __('|distribuidor|', 'ese-latam')); ?>
        </h1>
    </section>

    <div class="dst-layout">
        <form class="dst-filters" method="get" action="<?php echo esc_url($ese_dst_base); ?>" data-dst-filters data-reveal="up" data-reveal-delay="0.1">
            <div class="dst-filters__field">
                <label class="dst-filters__label" for="dst-q"><?php esc_html_e('Búsqueda geográfica', 'ese-latam'); ?></label>
                <div class="dst-filters__control">
                    <svg width="20" height="20" viewBox="0 0 19.5211 19.5211" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48111 15.0187 4.76534 14.3069 3.49991 13.0415C2.23448 11.7761 1.52268 10.0603 1.52072 8.27072Z" fill="currentColor"/>
                    </svg>
                    <input type="search" id="dst-q" name="q" value="<?php echo esc_attr($ese_dst_q); ?>" autocomplete="off"
                        placeholder="<?php esc_attr_e('Busca por ciudad, empresa, representante', 'ese-latam'); ?>">
                </div>
            </div>

            <div class="dst-filters__field">
                <label class="dst-filters__label" for="dst-pais"><?php esc_html_e('País', 'ese-latam'); ?></label>
                <div class="dst-filters__control dst-filters__control--select">
                    <?php // Sin JS el select envía el formulario solo; el módulo quita el onchange y filtra en vivo. ?>
                    <select id="dst-pais" name="pais" onchange="this.form.submit()">
                        <option value=""><?php esc_html_e('Todos los países', 'ese-latam'); ?></option>
                        <?php foreach ($ese_dst_paises as $ese_pais) : ?>
                            <option value="<?php echo esc_attr($ese_pais['slug']); ?>" <?php selected($ese_dst_pais, $ese_pais['slug']); ?>>
                                <?php echo esc_html($ese_pais['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <svg width="10" height="5" viewBox="0 0 10 5" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M5 5L0 0H10L5 5Z" fill="currentColor"/>
                    </svg>
                </div>
            </div>

            <button type="submit" class="sr-only"><?php esc_html_e('Buscar', 'ese-latam'); ?></button>
        </form>

        <div class="dst-main">
            <div class="dst-results" data-reveal="fade" data-reveal-delay="0.2">
                <p class="dst-results__count">
                    <?php esc_html_e('Distribuidores encontrados:', 'ese-latam'); ?>
                    <strong data-dst-count><?php echo esc_html((string) $ese_dst_visibles); ?></strong>
                </p>
                <a class="dst-results__reset" href="<?php echo esc_url($ese_dst_base); ?>" data-dst-reset>
                    <?php esc_html_e('Restablecer filtros', 'ese-latam'); ?>
                </a>
            </div>

            <ul class="dst-list" data-dst-list data-reveal-stagger>
                <?php foreach ($ese_dst_todos as $ese_i => $ese_d) : ?>
                    <li class="dst-list__item" <?php echo $ese_d['visible'] ? '' : 'hidden'; ?>
                        data-dst-item data-dst-pais="<?php echo esc_attr($ese_d['pais_slug']); ?>"
                        data-dst-buscar="<?php echo esc_attr($ese_d['buscar']); ?>">
                        <article class="dst-card<?php echo $ese_dst_activo === $ese_d ? ' is-active' : ''; ?><?php echo $ese_d['pendiente'] ? ' dst-card--pendiente' : ''; ?>"
                            data-dst-card data-dst-mapa="<?php echo esc_attr($ese_d['mapa']); ?>" tabindex="0"
                            aria-label="<?php echo esc_attr(sprintf(__('Ver %s en el mapa', 'ese-latam'), $ese_d['name'])); ?>">
                            <div class="dst-card__body">
                                <header class="dst-card__head">
                                    <span class="dst-card__logo" aria-hidden="true">
                                        <?php if (! empty($ese_d['logo'])) : ?>
                                            <img src="<?php echo esc_url($ese_d['logo']); ?>" alt="" loading="lazy" decoding="async">
                                        <?php else : ?>
                                            <span class="dst-card__initial"><?php echo esc_html(mb_substr($ese_d['name'], 0, 1)); ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <div class="dst-card__titles">
                                        <p class="dst-card__place"><?php echo esc_html($ese_d['lugar']); ?></p>
                                        <h2 class="dst-card__name"><?php echo esc_html($ese_d['name']); ?></h2>
                                        <?php if (! empty($ese_d['desc'])) : ?>
                                            <p class="dst-card__desc"><?php echo esc_html($ese_d['desc']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </header>

                                <hr class="dst-card__rule">

                                <?php if ($ese_d['pendiente']) : ?>
                                    <p class="dst-card__pending"><?php echo esc_html($ese_d['address']); ?></p>
                                <?php else : ?>
                                    <dl class="dst-card__info">
                                        <?php foreach ($ese_dst_campos as $ese_key => $ese_label) : ?>
                                            <?php if (empty($ese_d[$ese_key])) { continue; } ?>
                                            <div class="dst-card__row">
                                                <span class="dst-card__icon" aria-hidden="true">
                                                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="<?php echo esc_attr($ese_dst_icons[$ese_key]); ?>" fill="currentColor"/>
                                                    </svg>
                                                </span>
                                                <div class="dst-card__field">
                                                    <dt><?php echo esc_html($ese_label); ?></dt>
                                                    <dd>
                                                        <?php if ('phone' === $ese_key) : ?>
                                                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $ese_d['phone'])); ?>"><?php echo esc_html($ese_d['phone']); ?></a>
                                                        <?php elseif ('email' === $ese_key) : ?>
                                                            <a href="mailto:<?php echo esc_attr($ese_d['email']); ?>"><?php echo esc_html($ese_d['email']); ?></a>
                                                        <?php else : ?>
                                                            <?php echo esc_html($ese_d[$ese_key]); ?>
                                                        <?php endif; ?>
                                                    </dd>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </dl>
                                <?php endif; ?>
                            </div>

                            <?php if (! empty($ese_d['web']) || ! empty($ese_d['whatsapp'])) : ?>
                                <div class="dst-card__actions">
                                    <?php if (! empty($ese_d['web'])) : ?>
                                        <a class="dst-btn dst-btn--web" href="<?php echo esc_url($ese_d['web']); ?>" target="_blank" rel="noopener">
                                            <?php esc_html_e('Visitar web', 'ese-latam'); ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (! empty($ese_d['whatsapp'])) : ?>
                                        <a class="dst-btn dst-btn--wa" href="<?php echo esc_url('https://wa.me/' . preg_replace('/\D/', '', $ese_d['whatsapp'])); ?>" target="_blank" rel="noopener">
                                            <?php esc_html_e('WhatsApp', 'ese-latam'); ?>
                                            <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M13.0011 2.16667C18.9843 2.16667 23.8344 7.01675 23.8344 13C23.8344 18.9833 18.9843 23.8333 13.0011 23.8333C11.0866 23.8366 9.20571 23.33 7.55192 22.3654L2.17208 23.8333L3.63675 18.4513C2.67146 16.797 2.1644 14.9154 2.16775 13C2.16775 7.01675 7.01784 2.16667 13.0011 2.16667ZM9.30908 7.90833L9.09242 7.917C8.95233 7.92665 8.81546 7.96345 8.68942 8.02533C8.57196 8.09197 8.46469 8.17516 8.37092 8.27233C8.24092 8.39475 8.16725 8.50092 8.08817 8.60383C7.68747 9.12481 7.47172 9.76442 7.475 10.4217C7.47717 10.9525 7.61583 11.4693 7.8325 11.9524C8.27558 12.9296 9.00467 13.9642 9.96667 14.9229C10.1985 15.1537 10.426 15.3855 10.6708 15.6011C11.8662 16.6534 13.2906 17.4124 14.8308 17.8176L15.4462 17.9118C15.6466 17.9227 15.847 17.9075 16.0485 17.8978C16.364 17.8811 16.672 17.7957 16.9509 17.6475C17.0927 17.5742 17.2311 17.4947 17.3658 17.4092C17.3658 17.4092 17.4117 17.3781 17.5013 17.3117C17.6475 17.2033 17.7374 17.1264 17.8588 16.9997C17.9498 16.9058 18.0256 16.7967 18.0863 16.6725C18.1708 16.4959 18.2553 16.159 18.2899 15.8784C18.3159 15.6639 18.3083 15.5469 18.3051 15.4743C18.3008 15.3584 18.2043 15.2382 18.0993 15.1873L17.4688 14.9045C17.4688 14.9045 16.5263 14.4939 15.9499 14.2318C15.8896 14.2055 15.825 14.1904 15.7593 14.1873C15.6851 14.1796 15.6102 14.1879 15.5395 14.2116C15.4689 14.2353 15.4042 14.274 15.3498 14.3249C15.3443 14.3228 15.2718 14.3845 14.4885 15.3335C14.4435 15.3939 14.3816 15.4396 14.3106 15.4646C14.2396 15.4897 14.1628 15.4931 14.0898 15.4743C14.0192 15.4555 13.9501 15.4316 13.8829 15.4028C13.7486 15.3465 13.702 15.3248 13.6099 15.2858C12.9879 15.0149 12.4122 14.6483 11.9037 14.1993C11.7672 14.0801 11.6404 13.9501 11.5104 13.8244C11.0842 13.4162 10.7128 12.9545 10.4054 12.4508L10.3415 12.3478C10.2963 12.2783 10.2592 12.2038 10.231 12.1258C10.1898 11.9665 10.2971 11.8387 10.2971 11.8387C10.2971 11.8387 10.5603 11.5505 10.6828 11.3945C10.8019 11.2428 10.9027 11.0955 10.9677 10.9904C11.0955 10.7846 11.1356 10.5733 11.0684 10.4098C10.7651 9.66875 10.4516 8.93172 10.1281 8.19867C10.0642 8.0535 9.87458 7.9495 9.70233 7.92892C9.64383 7.92169 9.58533 7.91592 9.52683 7.91158C9.38137 7.90324 9.23552 7.90469 9.09025 7.91592L9.30908 7.90833Z" fill="currentColor"/>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>

            <p class="dst-empty" data-dst-empty <?php echo 0 === $ese_dst_visibles ? '' : 'hidden'; ?>>
                <?php esc_html_e('No encontramos distribuidores con esos filtros. Prueba con otra búsqueda o restablece los filtros.', 'ese-latam'); ?>
            </p>
        </div>

        <aside class="dst-map" data-reveal="fade" data-reveal-delay="0.25">
            <div class="dst-map__sticky">
                <div class="dst-map__frame">
                    <iframe data-dst-map-frame
                        src="https://www.google.com/maps?q=<?php echo rawurlencode($ese_dst_mapa_q); ?>&amp;z=14&amp;hl=es&amp;output=embed"
                        title="<?php esc_attr_e('Mapa del distribuidor seleccionado', 'ese-latam'); ?>"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>
            </div>
        </aside>
    </div>
</div>

<?php
get_footer();
