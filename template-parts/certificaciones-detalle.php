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

$ese_img = static fn (string $file): string => ESE_LATAM_URI . '/assets/imgs/certificaciones/' . $file;

$ese_sellos = [
    [
        'name' => 'Blue Angel', 'img' => 'blue-angel.png',
        'sub'  => __('Sello de economía circular y control de toxicidad (PCR)', 'ese-latam'),
        'desc' => __('El estándar ecológico alemán más respetado del mundo. Certifica que nuestros contenedores de polietileno reciclado de alta densidad están <strong>libres de aditivos tóxicos</strong> y reducen drásticamente la huella ecológica de las ciudades.', 'ese-latam'),
        'criterios' => [
            __('100% de plástico reciclado post-consumo de grado PCR.', 'ese-latam'),
            __('Ausencia de plastificantes, metales pesados y compuestos nocivos.', 'ese-latam'),
            __('Consumo controlado y optimización de la huella de carbono industrial.', 'ese-latam'),
        ],
    ],
    [
        'name' => 'DIN', 'img' => 'din.png',
        'sub'  => __('Norma alemana de diseño y dimensiones (DIN EN 840)', 'ese-latam'),
        'desc' => __('El instituto alemán de normalización define las dimensiones, el peine de volteo y las cargas nominales que hacen que un contenedor sea <strong>compatible con cualquier camión recolector</strong> europeo o latinoamericano.', 'ese-latam'),
        'criterios' => [
            __('Dimensiones y peine DIN compatibles con volteo mecanizado.', 'ese-latam'),
            __('Carga nominal verificada para cada litraje.', 'ese-latam'),
            __('Marcado en relieve permanente sobre el cuerpo del contenedor.', 'ese-latam'),
        ],
    ],
    [
        'name' => 'TÜV SÜD', 'img' => 'tuv-sud.png',
        'sub'  => __('Inspección técnica independiente y ensayos de resistencia', 'ese-latam'),
        'desc' => __('Laboratorio independiente que somete cada modelo a <strong>ensayos de impacto, carga y envejecimiento</strong> acelerado antes de autorizar su comercialización.', 'ese-latam'),
        'criterios' => [
            __('Ensayos de caída e impacto a baja temperatura.', 'ese-latam'),
            __('Ciclos de volteo repetidos sin deformación permanente.', 'ese-latam'),
            __('Resistencia a rayos UV y agentes químicos.', 'ese-latam'),
        ],
    ],
    [
        'name' => 'ISO 9001', 'img' => 'iso-9001.png',
        'sub'  => __('Sistema de gestión de la calidad', 'ese-latam'),
        'desc' => __('Certifica que el proceso completo de diseño, fabricación y servicio está documentado y auditado para entregar una <strong>calidad consistente</strong> en cada lote.', 'ese-latam'),
        'criterios' => [
            __('Procesos de producción trazables y auditados.', 'ese-latam'),
            __('Control de calidad en cada etapa de moldeo.', 'ese-latam'),
            __('Mejora continua a partir de la retroalimentación de clientes.', 'ese-latam'),
        ],
    ],
    [
        'name' => 'ISO 14001', 'img' => 'iso-14001.png',
        'sub'  => __('Sistema de gestión ambiental', 'ese-latam'),
        'desc' => __('Acredita que la fabricación minimiza su impacto ambiental: <strong>consumo de energía, agua y residuos de proceso</strong> bajo control y en reducción constante.', 'ese-latam'),
        'criterios' => [
            __('Reducción medible de emisiones y residuos industriales.', 'ese-latam'),
            __('Uso responsable de recursos en planta.', 'ese-latam'),
            __('Cumplimiento de la legislación ambiental vigente.', 'ese-latam'),
        ],
    ],
    [
        'name' => 'ISO 50001', 'img' => 'iso-50001.png',
        'sub'  => __('Sistema de gestión de la energía', 'ese-latam'),
        'desc' => __('Garantiza un uso eficiente de la energía en toda la cadena de producción, con <strong>metas de ahorro verificadas</strong> año a año.', 'ese-latam'),
        'criterios' => [
            __('Monitoreo continuo del consumo energético.', 'ese-latam'),
            __('Objetivos de eficiencia auditados externamente.', 'ese-latam'),
            __('Tecnología de inyección de bajo consumo.', 'ese-latam'),
        ],
    ],
    [
        'name' => 'PKN', 'img' => 'pkn.png',
        'sub'  => __('Comité Polaco de Normalización', 'ese-latam'),
        'desc' => __('Certificación de conformidad con las normas europeas para contenedores de residuos, requerida en <strong>licitaciones públicas</strong> de la Unión Europea.', 'ese-latam'),
        'criterios' => [
            __('Conformidad con EN 840 partes 1 a 6.', 'ese-latam'),
            __('Ensayos en laboratorio acreditado de la UE.', 'ese-latam'),
            __('Documentación válida para compras públicas.', 'ese-latam'),
        ],
    ],
    [
        'name' => 'DEKRA', 'img' => 'dekra.png',
        'sub'  => __('Organismo de certificación y ensayos', 'ese-latam'),
        'desc' => __('Auditoría independiente de seguridad del producto que verifica que el contenedor <strong>no representa riesgos</strong> para operarios ni usuarios durante su manipulación.', 'ese-latam'),
        'criterios' => [
            __('Bordes, asas y tapas sin puntos de atrapamiento.', 'ese-latam'),
            __('Estabilidad verificada en pendientes y carga máxima.', 'ese-latam'),
            __('Inspección periódica de planta y producto.', 'ese-latam'),
        ],
    ],
    [
        'name' => 'Seconda Vita', 'img' => 'seconda-vita.png',
        'sub'  => __('Certificado de material plástico reciclado', 'ese-latam'),
        'desc' => __('Sello italiano que certifica el <strong>contenido real de material reciclado</strong> en cada producto, con trazabilidad desde el origen del plástico hasta el contenedor terminado.', 'ese-latam'),
        'criterios' => [
            __('Porcentaje de reciclado verificado por lote.', 'ese-latam'),
            __('Trazabilidad documental del plástico PCR.', 'ese-latam'),
            __('Auditorías anuales de la cadena de suministro.', 'ese-latam'),
        ],
    ],
];

$ese_check = '<svg width="18" height="18" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M7.23015 12.306L13.2455 6.29067L12.3026 5.34784L7.23015 10.4203L4.68015 7.87033L3.73732 8.81316L7.23015 12.306ZM8.50157 17C7.32588 17 6.22081 16.7769 5.18634 16.3307C4.15188 15.8846 3.25207 15.279 2.48692 14.5142C1.72177 13.7493 1.11596 12.8499 0.669487 11.8159C0.223162 10.7819 0 9.6771 0 8.50157C0 7.32588 0.223088 6.22081 0.669263 5.18634C1.11544 4.15188 1.72095 3.25207 2.4858 2.48692C3.25065 1.72177 4.15009 1.11596 5.18411 0.669487C6.21812 0.223162 7.3229 0 8.49843 0C9.67412 0 10.7792 0.223087 11.8137 0.669263C12.8481 1.11544 13.7479 1.72095 14.5131 2.4858C15.2782 3.25065 15.884 4.15009 16.3305 5.18411C16.7768 6.21812 17 7.3229 17 8.49843C17 9.67412 16.7769 10.7792 16.3307 11.8137C15.8846 12.8481 15.279 13.7479 14.5142 14.5131C13.7493 15.2782 12.8499 15.884 11.8159 16.3305C10.7819 16.7768 9.6771 17 8.50157 17ZM8.5 15.6579C10.4982 15.6579 12.1908 14.9645 13.5776 13.5776C14.9645 12.1908 15.6579 10.4982 15.6579 8.5C15.6579 6.50175 14.9645 4.80921 13.5776 3.42237C12.1908 2.03553 10.4982 1.34211 8.5 1.34211C6.50175 1.34211 4.80921 2.03553 3.42237 3.42237C2.03553 4.80921 1.34211 6.50175 1.34211 8.5C1.34211 10.4982 2.03553 12.1908 3.42237 13.5776C4.80921 14.9645 6.50175 15.6579 8.5 15.6579Z" fill="currentColor"/></svg>';
?>

<section class="certdet" id="nuestras-certificaciones" data-tabs>
    <header class="certdet__header" data-reveal-header>
        <div>
            <p class="type-kicker text-secondary">/ <?php esc_html_e('Respaldo comprobado', 'ese-latam'); ?></p>
            <h2 class="type-h2 uppercase">
                <?php esc_html_e('Nuestras', 'ese-latam'); ?><br>
                <span class="hl"><?php esc_html_e('certificaciones', 'ese-latam'); ?></span>
            </h2>
        </div>
        <p class="nos-desc" data-reveal-desc>
            <?php esc_html_e('Cumplimos de forma', 'ese-latam'); ?>
            <span class="hl-accent"><?php esc_html_e('auditable', 'ese-latam'); ?></span>
            <?php esc_html_e('y', 'ese-latam'); ?>
            <span class="hl-accent"><?php esc_html_e('transparente', 'ese-latam'); ?></span>
            <?php esc_html_e('con cada normativa internacional de seguridad, ecología y diseño molecular, certificando compras públicas y corporativas de primer nivel.', 'ese-latam'); ?>
        </p>
    </header>

    <div class="certdet__layout">
        <ul class="certdet__tiles" role="tablist" data-reveal-stagger>
            <?php foreach ($ese_sellos as $ese_i => $ese_sello) : ?>
                <li>
                    <button type="button" role="tab"
                        class="certdet__tile<?php echo 0 === $ese_i ? ' is-active' : ''; ?>"
                        aria-selected="<?php echo 0 === $ese_i ? 'true' : 'false'; ?>" data-tab>
                        <img src="<?php echo esc_url($ese_img($ese_sello['img'])); ?>" alt="" loading="lazy" decoding="async">
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
                            <img src="<?php echo esc_url($ese_img($ese_sello['img'])); ?>" alt="" loading="lazy" decoding="async">
                        </div>
                        <div>
                            <h3 class="certdet__detail-name"><?php echo esc_html($ese_sello['name']); ?></h3>
                            <p class="certdet__detail-sub"><?php echo esc_html($ese_sello['sub']); ?></p>
                        </div>
                    </div>

                    <p class="certdet__label"><?php esc_html_e('Descripción', 'ese-latam'); ?></p>
                    <p class="certdet__detail-desc"><?php echo wp_kses($ese_sello['desc'], ['strong' => []]); ?></p>

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
