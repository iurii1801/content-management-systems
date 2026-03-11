<?php get_header(); ?>

<h2>Страница</h2>

<?php
if (have_posts()) :
    while (have_posts()) : the_post();
?>

    <h3><?php the_title(); ?></h3>
    <p><?php the_content(); ?></p>

    <?php get_sidebar(); ?>

    <?php comments_template(); ?>

<?php
    endwhile;
endif;
?>

<?php get_footer(); ?>