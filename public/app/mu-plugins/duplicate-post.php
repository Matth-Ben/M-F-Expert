<?php
/**
 * Plugin Name: Duplicate Post
 * Description: Permet de dupliquer n'importe quel post, page ou custom post type, y compris les champs ACF.
 * Version: 1.0.0
 * Author: Matthias BENOIT
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajouter le lien "Dupliquer" dans les actions de ligne (row actions)
 *
 * @param array  $actions Actions existantes.
 * @param object $post    Objet Post courant.
 * @return array Actions modifiées.
 */
function duplicate_post_link($actions, $post)
{
    if (current_user_can('edit_posts')) {
        $actions['duplicate'] = sprintf(
            '<a href="%s" aria-label="%s">%s</a>',
            wp_nonce_url(
                admin_url('admin.php?action=duplicate_post_as_draft&post=' . $post->ID),
                'duplicate_post_' . $post->ID
            ),
            esc_attr__('Dupliquer ce post', 'wp-gutenberg'),
            esc_html__('Dupliquer', 'wp-gutenberg')
        );
    }
    return $actions;
}

add_filter('post_row_actions', 'duplicate_post_link', 10, 2);
add_filter('page_row_actions', 'duplicate_post_link', 10, 2);

/**
 * Logique de duplication du post
 */
function duplicate_post_as_draft()
{
    // 1. Vérifications de sécurité
    if (!isset($_GET['post']) || !isset($_GET['action'])) {
        wp_die('Paramètres manquants.');
    }

    $post_id = absint($_GET['post']);

    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'duplicate_post_' . $post_id)) {
        wp_die('Lien de sécurité expiré.');
    }

    if (!current_user_can('edit_posts')) {
        wp_die('Vous n\'avez pas les permissions nécessaires.');
    }

    // 2. Récupérer le post original
    $post = get_post($post_id);
    if (!$post) {
        wp_die('Post introuvable.');
    }

    // 3. Préparer le nouveau post
    $current_user = wp_get_current_user();
    $new_post_args = array(
        'post_author' => $current_user->ID,
        'post_content' => $post->post_content,
        'post_title' => $post->post_title . ' - Copie',
        'post_excerpt' => $post->post_excerpt,
        'post_status' => 'draft', // Toujours en brouillon
        'post_type' => $post->post_type,
        'post_parent' => $post->post_parent,
        'post_password' => $post->post_password,
        'comment_status' => $post->comment_status,
        'ping_status' => $post->ping_status,
        'menu_order' => $post->menu_order,
    );

    // 4. Insérer le nouveau post
    $new_post_id = wp_insert_post($new_post_args);

    if ($new_post_id) {
        // 5. Dupliquer les Taxonomies
        $taxonomies = get_object_taxonomies($post->post_type);
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        }

        // 6. Dupliquer les Métadonnées (inclus ACF)
        $post_meta = get_post_meta($post_id);
        if ($post_meta) {
            foreach ($post_meta as $meta_key => $meta_values) {
                // Ignorer les métadonnées internes de WordPress (sauf edit_lock, etc. si nécessaire, mais généralement on évite)
                // Ici on copie tout pour être sûr d'avoir ACF

                // On évite cependant de copier certaines clés qui pourraient causer des soucis
                if ($meta_key === '_wp_old_slug') {
                    continue;
                }

                foreach ($meta_values as $meta_value) {
                    add_post_meta($new_post_id, $meta_key, maybe_unserialize($meta_value));
                }
            }
        }

        // 7. Redirection vers la liste des posts avec message
        $redirect_url = admin_url('edit.php?post_type=' . $post->post_type);
        wp_redirect(add_query_arg('duplicate_msg', '1', $redirect_url));
        exit;
    } else {
        wp_die('Erreur lors de la création du post : ' . $new_post_id->get_error_message());
    }
}

add_action('admin_action_duplicate_post_as_draft', 'duplicate_post_as_draft');

/**
 * Afficher un message de succès
 */
function duplicate_admin_notice()
{
    if (isset($_GET['duplicate_msg']) && $_GET['duplicate_msg'] == '1') {
        echo '<div class="notice notice-success is-dismissible"><p>Post dupliqué avec succès (brouillon).</p></div>';
    }
}
add_action('admin_notices', 'duplicate_admin_notice');
