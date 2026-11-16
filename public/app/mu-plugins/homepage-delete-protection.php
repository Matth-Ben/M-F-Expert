<?php
/**
 * Plugin Name: Homepage Delete Protection
 * Description: Ajoute une confirmation de suppression pour la page d'accueil configurée
 * Version: 1.0.0
 */

// Sécurité : Empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principale pour protéger la page d'accueil
 */
class Homepage_Delete_Protection
{

    /**
     * Constructeur - Initialise les hooks
     */
    public function __construct()
    {
        // Ajoute un script JavaScript pour la confirmation
        add_action('admin_enqueue_scripts', [$this, 'enqueue_confirmation_script']);

        // Ajoute une vérification côté serveur avant la suppression
        add_action('wp_trash_post', [$this, 'prevent_homepage_deletion'], 10, 1);
        add_action('before_delete_post', [$this, 'prevent_homepage_deletion'], 10, 1);
    }

    /**
     * Charge le script JavaScript pour la confirmation
     */
    public function enqueue_confirmation_script($hook)
    {
        // Charge sur la page de liste des pages ET sur la page d'édition
        $is_page_list = ($hook === 'edit.php' && (!isset($_GET['post_type']) || $_GET['post_type'] === 'page'));
        $is_page_edit = ($hook === 'post.php' || $hook === 'post-new.php');

        if (!$is_page_list && !$is_page_edit) {
            return;
        }

        // Récupère l'ID de la page d'accueil
        $homepage_id = get_option('page_on_front');

        // Debug
        error_log('Homepage Protection Debug - Hook: ' . $hook);
        error_log('Homepage Protection Debug - Homepage ID: ' . $homepage_id);

        // Si aucune page d'accueil n'est configurée, on ne fait rien
        if (!$homepage_id || $homepage_id == 0) {
            error_log('Homepage Protection Debug - No homepage configured');
            return;
        }

        // Sur la page d'édition, vérifie si c'est bien la page d'accueil
        if ($is_page_edit) {
            $current_post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;
            error_log('Homepage Protection Debug - Current Post ID: ' . $current_post_id);
            error_log('Homepage Protection Debug - Comparison: ' . $current_post_id . ' === ' . $homepage_id . ' ? ' . ($current_post_id === intval($homepage_id) ? 'YES' : 'NO'));

            if ($current_post_id !== intval($homepage_id)) {
                error_log('Homepage Protection Debug - Not homepage, skipping');
                return;
            }

            error_log('Homepage Protection Debug - IS HOMEPAGE! Loading script...');
        }

        // Ajoute le script inline
        wp_add_inline_script('jquery', $this->get_confirmation_script($homepage_id, $is_page_edit));
        error_log('Homepage Protection Debug - Script enqueued');
    }

    /**
     * Génère le script JavaScript de confirmation
     * 
     * @param int $homepage_id ID de la page d'accueil
     * @param bool $is_edit_page Si on est sur la page d'édition
     * @return string Code JavaScript
     */
    private function get_confirmation_script($homepage_id, $is_edit_page = false)
    {
        // Script pour la page d'édition
        if ($is_edit_page) {
            return <<<JS
jQuery(document).ready(function($) {
	// Fonction de confirmation
	function confirmHomepageDeletion(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		
		var confirmed = confirm(
			'⚠️ ATTENTION ⚠️\\n\\n' +
			'Vous êtes sur le point de supprimer la PAGE D\\'ACCUEIL de votre site.\\n\\n' +
			'Cette action peut avoir des conséquences importantes sur votre site.\\n\\n' +
			'Êtes-vous absolument certain de vouloir continuer ?'
		);
		
		if (confirmed) {
			// Retire temporairement l'event listener pour permettre la suppression
			$(this).off('click', confirmHomepageDeletion);
			// Déclenche le clic à nouveau
			this.click();
		}
		
		return false;
	}
	
	// Attache l'event handler avec le sélecteur exact basé sur le HTML fourni
	// Méthode 1: Sélecteur très spécifique
	$('#delete-action a.submitdelete.deletion').on('click', confirmHomepageDeletion);
	
	// Méthode 2: Sélecteur plus large au cas où
	$('#delete-action a.submitdelete').on('click', confirmHomepageDeletion);
	
	// Méthode 3: Délégation sur le document (la plus robuste)
	$(document).on('click', '#delete-action a.submitdelete', confirmHomepageDeletion);
	
	// Méthode 4: Attendre un peu et réessayer (au cas où le DOM n'est pas prêt)
	setTimeout(function() {
		$('#delete-action a.submitdelete').off('click', confirmHomepageDeletion).on('click', confirmHomepageDeletion);
	}, 500);
	
	// Ajoute un avertissement visuel dans le titre (éditeur classique)
	$('#titlewrap').before(
		'<div id="homepage-warning" style="background: #fff3cd; color: #856404; padding: 12px; margin-bottom: 15px; border-radius: 3px; border-left: 4px solid #ffc107; font-size: 14px; font-weight: 600;">' +
		'🏠 Vous éditez actuellement la PAGE D\\'ACCUEIL de votre site' +
		'</div>'
	);
	
	// Ajoute un avertissement dans la zone de publication
	$('#delete-action').prepend(
		'<div style="background: #fff3cd; color: #856404; padding: 8px; margin-bottom: 10px; border-radius: 3px; border: 1px solid #ffc107; font-size: 12px; font-weight: 600;">' +
		'⚠️ Page d\\'accueil du site' +
		'</div>'
	);
});
JS;
        }

        // Script pour la liste des pages
        return <<<JS
jQuery(document).ready(function($) {
	var homepageId = {$homepage_id};
	
	// Intercepte les clics sur les liens "Mettre à la corbeille"
	$('body').on('click', 'a.submitdelete', function(e) {
		var postId = $(this).closest('tr').attr('id');
		
		if (!postId) {
			return;
		}
		
		// Extrait l'ID du post depuis l'attribut id (format: post-123)
		postId = parseInt(postId.replace('post-', ''));
		
		// Si c'est la page d'accueil
		if (postId === homepageId) {
			var confirmed = confirm(
				'⚠️ ATTENTION ⚠️\\n\\n' +
				'Vous êtes sur le point de supprimer la PAGE D\'ACCUEIL de votre site.\\n\\n' +
				'Cette action peut avoir des conséquences importantes sur votre site.\\n\\n' +
				'Êtes-vous absolument certain de vouloir continuer ?'
			);
			
			if (!confirmed) {
				e.preventDefault();
				return false;
			}
		}
	});
	
	// Intercepte les actions groupées
	$('#doaction, #doaction2').on('click', function(e) {
		var action = $(this).siblings('select').val();
		
		// Si l'action est "trash" (mettre à la corbeille)
		if (action === 'trash') {
			var selectedPosts = [];
			$('input[name="post[]"]:checked').each(function() {
				selectedPosts.push(parseInt($(this).val()));
			});
			
			// Vérifie si la page d'accueil est dans la sélection
			if (selectedPosts.indexOf(homepageId) !== -1) {
				var confirmed = confirm(
					'⚠️ ATTENTION ⚠️\\n\\n' +
					'Votre sélection inclut la PAGE D\'ACCUEIL de votre site.\\n\\n' +
					'Cette action peut avoir des conséquences importantes sur votre site.\\n\\n' +
					'Êtes-vous absolument certain de vouloir continuer ?'
				);
				
				if (!confirmed) {
					e.preventDefault();
					return false;
				}
			}
		}
	});
	
	// Ajoute un indicateur visuel sur la ligne de la page d'accueil
	$('#post-' + homepageId).css({
		'background-color': '#fff3cd',
		'border-left': '4px solid #ffc107'
	}).find('.row-title').prepend('🏠 ');
});
JS;
    }

    /**
     * Empêche la suppression de la page d'accueil sans confirmation
     * 
     * @param int $post_id ID du post à supprimer
     */
    public function prevent_homepage_deletion($post_id)
    {
        // Récupère l'ID de la page d'accueil
        $homepage_id = get_option('page_on_front');

        // Si ce n'est pas la page d'accueil, on laisse passer
        if (!$homepage_id || $homepage_id != $post_id) {
            return;
        }

        // Vérifie si c'est une requête AJAX (action groupée)
        // Dans ce cas, le JavaScript a déjà demandé confirmation
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        // Pour les autres cas, on ajoute un avertissement dans l'admin
        add_action('admin_notices', function () {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <strong>⚠️ Attention :</strong>
                    Vous avez supprimé la page d'accueil de votre site.
                    N'oubliez pas de configurer une nouvelle page d'accueil dans
                    <a href="<?php echo admin_url('options-reading.php'); ?>">Réglages → Lecture</a>.
                </p>
            </div>
            <?php
        });
    }
}

// Initialise le plugin
new Homepage_Delete_Protection();
