<?php
/*
Plugin Name: USM Notes
Plugin URI: http://localhost/wp_lab2/
Description: Учебный плагин для управления заметками с приоритетами и датой напоминания.
Version: 1.0
Author: Student
Author URI: http://localhost/wp_lab2/
License: GPL2
Text Domain: usm-notes
*/

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Регистрация Custom Post Type "Notes"
 */
function usm_register_notes_cpt() {
    $labels = array(
        'name'                  => 'Notes',
        'singular_name'         => 'Note',
        'menu_name'             => 'Notes',
        'name_admin_bar'        => 'Note',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Note',
        'new_item'              => 'New Note',
        'edit_item'             => 'Edit Note',
        'view_item'             => 'View Note',
        'all_items'             => 'All Notes',
        'search_items'          => 'Search Notes',
        'not_found'             => 'No notes found',
        'not_found_in_trash'    => 'No notes found in Trash'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-edit-page',
        'supports'           => array('title', 'editor', 'author', 'thumbnail'),
        'show_in_rest'       => true
    );

    register_post_type('usm_note', $args);
}

add_action('init', 'usm_register_notes_cpt');

/**
 * Регистрация таксономии "Priority"
 */
function usm_register_priority_taxonomy() {
    $labels = array(
        'name'              => 'Priorities',
        'singular_name'     => 'Priority',
        'search_items'      => 'Search Priorities',
        'all_items'         => 'All Priorities',
        'edit_item'         => 'Edit Priority',
        'update_item'       => 'Update Priority',
        'add_new_item'      => 'Add New Priority',
        'new_item_name'     => 'New Priority Name',
        'menu_name'         => 'Priority'
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'public'            => true,
        'show_in_rest'      => true
    );

    register_taxonomy('usm_priority', array('usm_note'), $args);
}

add_action('init', 'usm_register_priority_taxonomy');

/**
 * Добавление метабокса для даты напоминания
 */
function usm_add_due_date_meta_box() {
    add_meta_box(
        'usm_due_date_meta_box',
        'Due Date',
        'usm_render_due_date_meta_box',
        'usm_note',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'usm_add_due_date_meta_box');

/**
 * Отрисовка метабокса
 */
function usm_render_due_date_meta_box($post) {
    wp_nonce_field('usm_save_due_date', 'usm_due_date_nonce');

    $due_date = get_post_meta($post->ID, '_usm_due_date', true);

    echo '<label for="usm_due_date_field">Select reminder date:</label>';
    echo '<input type="date" id="usm_due_date_field" name="usm_due_date_field" value="' . esc_attr($due_date) . '" style="width:100%;" required />';
}

/**
 * Сохранение даты напоминания
 */
function usm_save_due_date_meta($post_id) {
    if (!isset($_POST['post_type']) || $_POST['post_type'] !== 'usm_note') {
        return;
    }

    if (!isset($_POST['usm_due_date_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['usm_due_date_nonce'], 'usm_save_due_date')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $due_date = isset($_POST['usm_due_date_field']) ? sanitize_text_field($_POST['usm_due_date_field']) : '';

    if (empty($due_date)) {
        delete_post_meta($post_id, '_usm_due_date');
        set_transient('usm_due_date_error_' . get_current_user_id(), 'empty', 30);
        return;
    }

    $today = date('Y-m-d');

    if ($due_date < $today) {
        delete_post_meta($post_id, '_usm_due_date');
        set_transient('usm_due_date_error_' . get_current_user_id(), 'past', 30);
        return;
    }

    update_post_meta($post_id, '_usm_due_date', $due_date);
}
add_action('save_post', 'usm_save_due_date_meta');

/**
 * Вывод сообщений об ошибках
 */
function usm_due_date_admin_notices() {
    $error = get_transient('usm_due_date_error_' . get_current_user_id());

    if (!$error) {
        return;
    }

    if ($error === 'empty') {
        echo '<div class="notice notice-error is-dismissible"><p>Due Date is required.</p></div>';
    }

    if ($error === 'past') {
        echo '<div class="notice notice-error is-dismissible"><p>Due Date cannot be in the past.</p></div>';
    }

    delete_transient('usm_due_date_error_' . get_current_user_id());
}
add_action('admin_notices', 'usm_due_date_admin_notices');

/**
 * Добавление колонки Due Date в список заметок
 */
function usm_add_due_date_column($columns) {
    $columns['usm_due_date'] = 'Due Date';
    return $columns;
}
add_filter('manage_usm_note_posts_columns', 'usm_add_due_date_column');

/**
 * Отображение значения Due Date в колонке
 */
function usm_show_due_date_column($column, $post_id) {
    if ($column === 'usm_due_date') {
        $due_date = get_post_meta($post_id, '_usm_due_date', true);
        echo $due_date ? esc_html($due_date) : '—';
    }
}
add_action('manage_usm_note_posts_custom_column', 'usm_show_due_date_column', 10, 2);

/**
 * Шорткод для вывода заметок
 */
function usm_notes_shortcode($atts) {
    $atts = shortcode_atts(array(
        'priority'    => '',
        'before_date' => ''
    ), $atts, 'usm_notes');

    $args = array(
        'post_type'      => 'usm_note',
        'post_status'    => 'publish',
        'posts_per_page' => -1
    );

    $meta_query = array();
    $tax_query = array();

    if (!empty($atts['before_date'])) {
        $meta_query[] = array(
            'key'     => '_usm_due_date',
            'value'   => sanitize_text_field($atts['before_date']),
            'compare' => '<=',
            'type'    => 'DATE'
        );
    }

    if (!empty($atts['priority'])) {
        $tax_query[] = array(
            'taxonomy' => 'usm_priority',
            'field'    => 'slug',
            'terms'    => sanitize_title($atts['priority'])
        );
    }

    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }

    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    $query = new WP_Query($args);

    ob_start();

    echo '<div class="usm-notes-list">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $due_date = get_post_meta(get_the_ID(), '_usm_due_date', true);
            $terms = get_the_terms(get_the_ID(), 'usm_priority');

            echo '<div class="usm-note-item">';
            echo '<h3>' . get_the_title() . '</h3>';
            echo '<div class="usm-note-content">' . get_the_excerpt() . '</div>';

            if ($terms && !is_wp_error($terms)) {
                echo '<p><strong>Priority:</strong> ' . esc_html($terms[0]->name) . '</p>';
            }

            if ($due_date) {
                echo '<p><strong>Due Date:</strong> ' . esc_html($due_date) . '</p>';
            }

            echo '</div>';
        }
    } else {
        echo '<p>Нет заметок с заданными параметрами</p>';
    }

    echo '</div>';

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('usm_notes', 'usm_notes_shortcode');

/**
 * Стили для списка заметок
 */
function usm_notes_shortcode_styles() {
    echo '
    <style>
        .usm-notes-list {
            display: grid;
            gap: 16px;
            margin: 20px 0;
        }

        .usm-note-item {
            border: 1px solid #dcdcdc;
            padding: 16px;
            border-radius: 8px;
            background: #f9f9f9;
        }

        .usm-note-item h3 {
            margin: 0 0 10px;
        }

        .usm-note-content {
            margin-bottom: 10px;
        }
    </style>
    ';
}
add_action('wp_head', 'usm_notes_shortcode_styles');