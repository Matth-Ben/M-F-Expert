<?php
/**
 * Plugin Name: Client Menu Restrictions
 * Description: Gestionnaire de visibilité des menus d'administration basé sur une liste noire pour un rôle client spécifique
 * Version: 1.0.0
 */

// Sécurité : Empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configuration : Slug du rôle cible
 * Modifiez cette constante pour cibler un autre rôle
 */
define('CLIENT_ROLE_SLUG', 'client_role');

/**
 * Classe principale pour gérer les restrictions de menus
 */
class Client_Menu_Restrictions
{

    /**
     * Clé d'option pour stocker les menus masqués
     */
    private const OPTION_KEY = 'client_hidden_menus';

    /**
     * Nonce action pour la sécurité du formulaire
     */
    private const NONCE_ACTION = 'save_client_menu_restrictions';

    /**
     * Constructeur - Initialise les hooks
     */
    public function __construct()
    {
        // Crée le rôle client s'il n'existe pas
        add_action('init', [$this, 'create_client_role']);
        
        // Ajoute la page de réglages (uniquement pour les admins)
        add_action('admin_menu', [$this, 'add_settings_page']);

        // Masque les menus pour le rôle client (priorité très basse)
        add_action('admin_menu', [$this, 'hide_menus_for_client'], 9999);
        
        // Masque les sous-menus d'Apparence spécifiques pour le rôle client
        add_action('admin_menu', [$this, 'hide_appearance_submenus_for_client'], 9999);
        
        // Bloque l'accès aux fonctionnalités de thèmes pour le rôle client
        add_filter('user_has_cap', [$this, 'remove_theme_capabilities'], 10, 4);
        
        // Bloque l'accès direct aux pages interdites
        add_action('admin_init', [$this, 'block_restricted_pages']);

        // Masque l'onglet "Indispensables" pour le rôle client
        add_filter('show_advanced_plugins', [$this, 'hide_mu_plugins_tab'], 10, 2);

        // Traite la sauvegarde du formulaire
        add_action('admin_init', [$this, 'save_restrictions']);
    }

    /**
     * Crée le rôle client s'il n'existe pas déjà
     */
    public function create_client_role()
    {
        // Vérifie si le rôle existe déjà
        $existing_role = get_role(CLIENT_ROLE_SLUG);
        
        // Si le rôle existe, on le supprime pour le recréer avec les bonnes capacités
        // (utile si vous aviez déjà créé le rôle avec des capacités limitées)
        if ($existing_role) {
            // On vérifie si le rôle a déjà les capacités admin
            // Si non, on le supprime pour le recréer
            if (!isset($existing_role->capabilities['manage_options'])) {
                remove_role(CLIENT_ROLE_SLUG);
            } else {
                return; // Le rôle existe déjà avec les bonnes capacités
            }
        }

        // Récupère les capacités du rôle "Administrateur" comme base
        // Cela permet au client d'avoir accès à tous les menus par défaut
        $administrator = get_role('administrator');
        $capabilities = $administrator ? $administrator->capabilities : [
            'read' => true,
            'manage_options' => true,
        ];

        // Crée le rôle client avec les capacités d'administrateur
        add_role(
            CLIENT_ROLE_SLUG,
            'Client',  // Nom affiché
            $capabilities
        );
    }

    /**
     * Vérifie si l'utilisateur actuel a le rôle client
     * 
     * @return bool
     */
    private function is_client_role()
    {
        $user = wp_get_current_user();
        return in_array(CLIENT_ROLE_SLUG, (array) $user->roles, true);
    }

    /**
     * Ajoute la page de réglages dans le menu "Réglages"
     */
    public function add_settings_page()
    {
        // Visible uniquement pour les administrateurs (pas pour le rôle client)
        if ($this->is_client_role()) {
            return;
        }

        add_options_page(
            'Restrictions Client',           // Titre de la page
            'Restrictions Client',           // Titre du menu
            'manage_options',                // Capacité requise
            'client-menu-restrictions',      // Slug de la page
            [$this, 'render_settings_page']  // Callback pour afficher la page
        );
    }

    /**
     * Affiche la page de réglages
     */
    public function render_settings_page()
    {
        // Vérification de sécurité
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires pour accéder à cette page.'));
        }

        global $menu;

        // Récupère les menus actuellement masqués
        $hidden_menus = get_option(self::OPTION_KEY, []);

        ?>
        <div class="wrap">
            <h1>
                <?php echo esc_html(get_admin_page_title()); ?>
            </h1>

            <p class="description">
                Cochez les menus que vous souhaitez <strong>MASQUER</strong> pour le rôle "
                <?php echo esc_html(CLIENT_ROLE_SLUG); ?>".
                <br>Les menus non cochés resteront visibles (comportement par défaut).
            </p>

            <form method="post" action="">
                <?php
                // Nonce de sécurité
                wp_nonce_field(self::NONCE_ACTION, 'client_menu_nonce');
                ?>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" id="select-all" />
                            </th>
                            <th>Icône</th>
                            <th>Nom du menu</th>
                            <th>Slug</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($menu)) {
                            foreach ($menu as $menu_item) {
                                // Ignore les séparateurs
                                if (empty($menu_item[0]) || strpos($menu_item[4], 'wp-menu-separator') !== false) {
                                    continue;
                                }

                                $menu_slug = $menu_item[2];
                                $menu_title = wp_strip_all_tags($menu_item[0]);
                                $menu_icon = $menu_item[6];

                                // Vérifie si ce menu est actuellement masqué
                                $is_hidden = in_array($menu_slug, $hidden_menus, true);
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="hidden_menus[]" value="<?php echo esc_attr($menu_slug); ?>" <?php checked($is_hidden); ?>
                                        class="menu-checkbox"
                                        />
                                    </td>
                                    <td>
                                        <?php
                                        if (strpos($menu_icon, 'dashicons-') !== false) {
                                            echo '<span class="dashicons ' . esc_attr($menu_icon) . '"></span>';
                                        } elseif (strpos($menu_icon, 'data:image') !== false || strpos($menu_icon, 'http') !== false) {
                                            echo '<img src="' . esc_url($menu_icon) . '" style="width: 20px; height: 20px;" />';
                                        } else {
                                            echo '<span class="dashicons dashicons-admin-generic"></span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <strong>
                                            <?php echo esc_html($menu_title); ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <code><?php echo esc_html($menu_slug); ?></code>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <p class="submit">
                    <?php submit_button('Enregistrer les restrictions', 'primary', 'submit', false); ?>
                </p>
            </form>
        </div>

        <script>
            jQuery(document).ready(function ($) {
                // Sélectionner/Désélectionner tout
                $('#select-all').on('change', function () {
                    $('.menu-checkbox').prop('checked', $(this).prop('checked'));
                });

                // Mettre à jour "Tout sélectionner" si toutes les cases sont cochées
                $('.menu-checkbox').on('change', function () {
                    var allChecked = $('.menu-checkbox:checked').length === $('.menu-checkbox').length;
                    $('#select-all').prop('checked', allChecked);
                });
            });
        </script>

        <style>
            .wp-list-table th,
            .wp-list-table td {
                vertical-align: middle;
            }

            .wp-list-table .dashicons {
                font-size: 20px;
                width: 20px;
                height: 20px;
            }
        </style>
        <?php
    }

    /**
     * Traite et sauvegarde les restrictions de menus
     */
    public function save_restrictions()
    {
        // Vérifie si le formulaire a été soumis
        if (!isset($_POST['submit']) || !isset($_POST['client_menu_nonce'])) {
            return;
        }

        // Vérification de sécurité : Nonce
        if (!wp_verify_nonce($_POST['client_menu_nonce'], self::NONCE_ACTION)) {
            wp_die(__('Erreur de sécurité. Veuillez réessayer.'));
        }

        // Vérification de sécurité : Capacité
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.'));
        }

        // Récupère et sanitise les menus cochés (= à masquer)
        $hidden_menus = isset($_POST['hidden_menus']) ? array_map('sanitize_text_field', $_POST['hidden_menus']) : [];

        // Sauvegarde dans la base de données
        update_option(self::OPTION_KEY, $hidden_menus);

        // Message de confirmation
        add_settings_error(
            'client_menu_restrictions',
            'settings_updated',
            'Restrictions enregistrées avec succès.',
            'updated'
        );

        // Redirige pour éviter la resoumission du formulaire
        wp_safe_redirect(add_query_arg('settings-updated', 'true', wp_get_referer()));
        exit;
    }

    /**
     * Masque les menus pour le rôle client
     */
    public function hide_menus_for_client()
    {
        // Ne s'applique que si l'utilisateur a le rôle client
        if (!$this->is_client_role()) {
            return;
        }

        // Récupère la liste des menus à masquer
        $hidden_menus = get_option(self::OPTION_KEY, []);

        // Masque chaque menu de la liste noire
        foreach ($hidden_menus as $menu_slug) {
            remove_menu_page($menu_slug);
        }
    }

    /**
     * Masque les sous-menus d'Apparence spécifiques pour le rôle client
     * Garde l'accès au menu "Menus" (nav-menus.php)
     */
    public function hide_appearance_submenus_for_client()
    {
        // Ne s'applique que si l'utilisateur a le rôle client
        if (!$this->is_client_role()) {
            return;
        }

        // Liste des sous-menus d'Apparence à masquer
        // Note: nav-menus.php (Menus) n'est PAS dans cette liste
        $submenus_to_hide = [
            'themes.php',           // Thèmes
            'customize.php',        // Personnaliser
            'theme-editor.php',     // Éditeur de fichiers des thèmes
            'widgets.php',          // Widgets (ancien)
            'site-editor.php',      // Éditeur de site (FSE)
        ];

        // Masque chaque sous-menu
        foreach ($submenus_to_hide as $submenu) {
            remove_submenu_page('themes.php', $submenu);
        }
    }

    /**
     * Retire les capacités liées aux thèmes pour le rôle client
     * 
     * @param array $allcaps Toutes les capacités de l'utilisateur
     * @param array $caps Capacités demandées
     * @param array $args Arguments supplémentaires
     * @param WP_User $user Utilisateur actuel
     * @return array Capacités modifiées
     */
    public function remove_theme_capabilities($allcaps, $caps, $args, $user)
    {
        // Ne s'applique que si l'utilisateur a le rôle client
        if (!in_array(CLIENT_ROLE_SLUG, (array) $user->roles, true)) {
            return $allcaps;
        }

        // Liste des capacités à retirer pour bloquer l'accès aux thèmes
        // Note: edit_theme_options n'est PAS retiré car il est nécessaire pour les menus
        $caps_to_remove = [
            'switch_themes',        // Accès à la page Thèmes
            'edit_themes',          // Accès à l'éditeur de thèmes
            'install_themes',       // Installation de thèmes
            'update_themes',        // Mise à jour de thèmes
            'delete_themes',        // Suppression de thèmes
        ];

        // Retire chaque capacité
        foreach ($caps_to_remove as $cap) {
            if (isset($allcaps[$cap])) {
                $allcaps[$cap] = false;
            }
        }

        return $allcaps;
    }

    /**
     * Bloque l'accès direct aux pages interdites pour le rôle client
     */
    public function block_restricted_pages()
    {
        // Ne s'applique que si l'utilisateur a le rôle client
        if (!$this->is_client_role()) {
            return;
        }

        // Récupère la page actuelle
        global $pagenow;

        // Liste des pages à bloquer
        $blocked_pages = [
            'themes.php',           // Thèmes
            'customize.php',        // Personnaliser
            'theme-editor.php',     // Éditeur de thèmes
            'widgets.php',          // Widgets
            'site-editor.php',      // Éditeur de site (FSE)
        ];

        // Si la page actuelle est dans la liste des pages bloquées
        if (in_array($pagenow, $blocked_pages, true)) {
            wp_die(
                __('Vous n\'avez pas les permissions nécessaires pour accéder à cette page.'),
                __('Accès refusé'),
                ['response' => 403]
            );
        }
    }

    /**
     * Masque l'onglet "Indispensables" (Must-Use) pour le rôle client
     * 
     * @param bool   $show    Si l'onglet doit être affiché
     * @param string $type    Type de plugin (mustuse, dropins)
     * @return bool
     */
    public function hide_mu_plugins_tab($show, $type)
    {
        // Masque uniquement l'onglet "mustuse" pour le rôle client
        if ($type === 'mustuse' && $this->is_client_role()) {
            return false;
        }

        return $show;
    }
}

// Initialise le plugin
new Client_Menu_Restrictions();
