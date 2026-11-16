<?php
/**
 * Plugin Name: Disable Features
 * Description: Désactive les commentaires de manière globale. (Gutenberg reste actif : le thème est construit dessus.)
 * Version: 2.0.0
 * Author: Matthias BENOIT
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Désactiver les commentaires
 */

// Fermer les commentaires sur le front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Cacher les commentaires existants
add_filter('comments_array', '__return_empty_array', 10, 2);

// Supprimer le support des commentaires dans l'admin pour tous les types de posts
add_action('admin_init', function () {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

// Supprimer la page "Commentaires" du menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

// Rediriger si on essaie d'accéder à edit-comments.php
add_action('admin_init', function () {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
});

// Supprimer l'icône "Commentaires" de la barre d'admin
add_action('wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
});
