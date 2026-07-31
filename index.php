<?php
/**
 * Fallback template
 *
 * @package EseLatam
 */
declare(strict_types=1);

get_header();
?>

<section class="mx-auto max-w-7xl px-6 py-16">
    <?php if (have_posts()) : ?>
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-3">
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class('group'); ?> data-reveal="up">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" class="block overflow-hidden rounded-lg">
                            <?php the_post_thumbnail('large', [
                                'class' => 'w-full h-56 object-cover transition duration-500 group-hover:scale-105',
                            ]); ?>
                        </a>
                    <?php endif; ?>
                    <h2 class="mt-4 text-xl font-medium text-secondary-500">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    <div class="mt-2 text-sm text-neutral-700">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="mt-12">
            <?php the_posts_pagination(); ?>
        </div>
    <?php else : ?>
        <p class="text-neutral-700"><?php esc_html_e('No hay contenido para mostrar.', 'ese-latam'); ?></p>
    <?php endif; ?>
</section>

<?php get_footer(); ?>
