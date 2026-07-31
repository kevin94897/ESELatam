<?php
/**
 * Footer
 *
 * @package EseLatam
 */
declare(strict_types=1);
?>
</main>

<footer class="site-footer bg-primary text-white mt-24">
    <div class="mx-auto max-w-7xl px-6 py-16 grid gap-10 md:grid-cols-3">
        <div>
            <p class="type-nav text-white"><?php bloginfo('name'); ?></p>
            <p class="type-body mt-4 text-white/70"><?php bloginfo('description'); ?></p>
        </div>
        <?php if (is_active_sidebar('footer-1')) : ?>
            <div class="md:col-span-2">
                <?php dynamic_sidebar('footer-1'); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="border-t border-white/10">
        <div class="mx-auto max-w-7xl px-6 py-6 type-caption text-white/60">
            © <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>.
            <?php esc_html_e('Todos los derechos reservados.', 'ese-latam'); ?>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
