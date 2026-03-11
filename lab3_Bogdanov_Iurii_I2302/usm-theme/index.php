<?php get_header(); ?>

<h2>Последние записи</h2>

<?php
$count = 0;

if (have_posts()) :
    while (have_posts()) : the_post();

    if ($count == 5) break;
?>

<h3><?php the_title(); ?></h3>
<p><?php the_excerpt(); ?></p>

<?php
$count++;
endwhile;
endif;
?>

<?php get_footer(); ?>