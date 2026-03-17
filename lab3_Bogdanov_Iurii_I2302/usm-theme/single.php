<?php get_header(); ?>

<section class="content-page">
    <div class="content-main">
        <h2 class="section-title">Запись</h2>

        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
        ?>

        <article class="content-card">
            <h3><?php the_title(); ?></h3>
            <div class="content-text">
                <?php the_content(); ?>
            </div>
        </article>

        <?php comments_template(); ?>

        <?php
            endwhile;
        endif;
        ?>
    </div>

    <div class="content-side">
        <?php get_sidebar(); ?>
    </div>
</section>

<?php get_footer(); ?>