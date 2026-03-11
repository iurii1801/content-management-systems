<?php
if (have_comments()) :
?>

    <h3>Комментарии</h3>

    <?php
    wp_list_comments();
endif;

if (comments_open()) :
    comment_form();
endif;
?>