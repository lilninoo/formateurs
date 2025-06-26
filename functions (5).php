<?php
/**
 * Fonctions utilitaires - Formateur Manager Pro
 * includes/functions.php
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Fonctions de formateurs
 */

/**
 * Obtenir les métadonnées d'un formateur
 */
function fmp_get_formateur_meta($formateur_id, $key = '', $single = true) {
    if (empty($key)) {
        return get_post_meta($formateur_id);
    }
    
    return get_post_meta($formateur_id, '_formateur_' . $key, $single);
}

/**
 * Mettre à jour les métadonnées d'un formateur
 */
function fmp_update_formateur_meta($formateur_id, $key, $value) {
    return update_post_meta($formateur_id, '_formateur_' . $key, $value);
}

/**
 * Supprimer les métadonnées d'un formateur
 */
function fmp_delete_formateur_meta($formateur_id, $key) {
    return delete_post_meta($formateur_id, '_formateur_' . $key);
}

/**
 * Vérifier si un post est un formateur
 */
function fmp_is_formateur($post_id) {
    return get_post_type($post_id) === 'formateur_pro';
}

/**
 * Obtenir le statut d'un formateur
 */
function fmp_get_formateur_status($formateur_id) {
    return fmp_get_formateur_meta($formateur_id, 'status') ?: 'pending';
}

/**
 * Mettre à jour le statut d'un formateur
 */
function fmp_update_formateur_status($formateur_id, $status) {
    $valid_statuses = ['pending', 'active', 'inactive', 'rejected'];
    
    if (!in_array($status, $valid_statuses)) {
        return false;
    }
    
    $updated = fmp_update_formateur_meta($formateur_id, 'status', $status);
    
    if ($updated) {
        // Déclencher des actions selon le statut
        do_action('fmp_formateur_status_changed', $formateur_id, $status);
        
        if ($status === 'active') {
            do_action('fmp_formateur_approved', $formateur_id);
        }
    }
    
    return $updated;
}

/**
 * Obtenir les formateurs avec filtres
 */
function fmp_get_formateurs($args = []) {
    $defaults = [
        'post_type' => 'formateur_pro',
        'post_status' => 'publish',
        'posts_per_page' => 12,
        'meta_query' => [
            [
                'key' => '_formateur_status',
                'value' => 'active',
                'compare' => '='
            ]
        ]
    ];
    
    $args = wp_parse_args($args, $defaults);
    
    // Cache pour les requêtes fréquentes
    $cache_key = 'fmp_formateurs_' . md5(serialize($args));
    $formateurs = get_transient($cache_key);
    
    if (false === $formateurs) {
        $formateurs = get_posts($args);
        set_transient($cache_key, $formateurs, 15 * MINUTE_IN_SECONDS);
    }
    
    return $formateurs;
}

/**
 * Obtenir les spécialités avec comptage
 */
function fmp_get_specialties_with_count() {
    $cache_key = 'fmp_specialties_count';
    $specialties = get_transient($cache_key);
    
    if (false === $specialties) {
        $terms = get_terms([
            'taxonomy' => 'formateur_specialty',
            'hide_empty' => true,
            'orderby' => 'count',
            'order' => 'DESC'
        ]);
        
        $specialties = [];
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $specialties[] = [
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'count' => $term->count,
                    'url' => get_term_link($term)
                ];
            }
        }
        
        set_transient($cache_key, $specialties, HOUR_IN_SECONDS);
    }
    
    return $specialties;
}

/**
 * Obtenir les statistiques globales
 */
function fmp_get_global_stats() {
    $cache_key = 'fmp_global_stats';
    $stats = get_transient($cache_key);
    
    if (false === $stats) {
        global $wpdb;
        
        $stats = [
            'total_formateurs' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'",
                'formateur_pro'
            )) ?: 0,
            'formateurs_actifs' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->posts} p 
                 INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
                 WHERE p.post_type = %s AND p.post_status = 'publish' 
                 AND pm.meta_key = '_formateur_status' AND pm.meta_value = 'active'",
                'formateur_pro'
            )) ?: 0,
            'total_specialites' => wp_count_terms('formateur_specialty', ['hide_empty' => false]) ?: 0,
            'total_competences' => wp_count_terms('formateur_skill', ['hide_empty' => false]) ?: 0
        ];
        
        set_transient($cache_key, $stats, HOUR_IN_SECONDS);
    }
    
    return $stats;
}

/**
 * Fonctions de cache
 */

/**
 * Vider le cache des formateurs
 */
function fmp_clear_formateur_cache($formateur_id = 0) {
    global $wpdb;
    
    if ($formateur_id) {
        // Vider le cache spécifique à un formateur
        delete_transient("fmp_formateur_{$formateur_id}");
    }
    
    // Vider tous les caches liés aux formateurs
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_fmp_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_fmp_%'");
}

/**
 * Fonctions de validation
 */

/**
 * Valider les données d'un formateur
 */
function fmp_validate_formateur_data($data) {
    $errors = [];
    $required_fields = ['prenom', 'nom', 'email', 'telephone', 'ville', 'specialite'];
    
    // Vérifier les champs obligatoires
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            $errors[$field] = sprintf(__('Le champ %s est obligatoire', 'formateur-manager-pro'), $field);
        }
    }
    
    // Valider l'email
    if (!empty($data['email']) && !is_email($data['email'])) {
        $errors['email'] = __('Adresse email invalide', 'formateur-manager-pro');
    }
    
    // Valider le téléphone
    if (!empty($data['telephone']) && !preg_match('/^[\+]?[0-9\s\-\(\)]{10,}$/', $data['telephone'])) {
        $errors['telephone'] = __('Numéro de téléphone invalide', 'formateur-manager-pro');
    }
    
    // Valider l'URL du site web
    if (!empty($data['site_web']) && !filter_var($data['site_web'], FILTER_VALIDATE_URL)) {
        $errors['site_web'] = __('URL du site web invalide', 'formateur-manager-pro');
    }
    
    return $errors;
}

/**
 * Valider un fichier uploadé
 */
function fmp_validate_uploaded_file($file, $type) {
    $config = [
        'max_file_sizes' => [
            'photo' => 5 * 1024 * 1024,     // 5MB
            'cv' => 10 * 1024 * 1024,       // 10MB
            'portfolio' => 15 * 1024 * 1024 // 15MB
        ],
        'allowed_file_types' => [
            'photo' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
            'cv' => ['pdf', 'doc', 'docx'],
            'portfolio' => ['pdf', 'zip', 'rar']
        ]
    ];
    
    $errors = [];
    
    // Vérifier la taille
    $max_size = $config['max_file_sizes'][$type] ?? 5 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        $errors[] = sprintf(
            __('Le fichier est trop volumineux. Taille maximum: %s', 'formateur-manager-pro'),
            fmp_format_file_size($max_size)
        );
    }
    
    // Vérifier le type
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_types = $config['allowed_file_types'][$type] ?? [];
    
    if (!in_array($extension, $allowed_types)) {
        $errors[] = sprintf(
            __('Type de fichier non autorisé. Types acceptés: %s', 'formateur-manager-pro'),
            implode(', ', $allowed_types)
        );
    }
    
    // Vérifier les erreurs d'upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = __('Erreur lors de l\'upload du fichier', 'formateur-manager-pro');
    }
    
    return $errors;
}

/**
 * Fonctions de sécurité
 */

/**
 * Obtenir l'IP de l'utilisateur
 */
function fmp_get_user_ip() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '';
}

/**
 * Limiter le taux de requêtes
 */
function fmp_rate_limit($action, $limit = 5, $window = 300) {
    $ip = fmp_get_user_ip();
    $key = "fmp_rate_limit_{$action}_{$ip}";
    
    $count = get_transient($key) ?: 0;
    
    if ($count >= $limit) {
        return false;
    }
    
    set_transient($key, $count + 1, $window);
    return true;
}

/**
 * Vérifier les permissions utilisateur
 */
function fmp_check_user_permissions($action, $formateur_id = 0) {
    switch ($action) {
        case 'edit_formateur':
            return current_user_can('edit_posts') || current_user_can('edit_post', $formateur_id);
            
        case 'approve_formateur':
            return current_user_can('publish_posts');
            
        case 'delete_formateur':
            return current_user_can('delete_posts') || current_user_can('delete_post', $formateur_id);
            
        case 'manage_settings':
            return current_user_can('manage_options');
            
        default:
            return false;
    }
}

/**
 * Fonctions de formatage
 */

/**
 * Formater la taille d'un fichier
 */
function fmp_format_file_size($bytes) {
    if ($bytes === 0) return '0 Bytes';
    
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

/**
 * Formater un numéro de téléphone
 */
function fmp_format_phone($phone) {
    // Supprimer tous les caractères non numériques sauf le +
    $phone = preg_replace('/[^\d\+]/', '', $phone);
    
    // Formater selon le format français si commence par 0
    if (strpos($phone, '0') === 0 && strlen($phone) === 10) {
        return preg_replace('/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', '$1 $2 $3 $4 $5', $phone);
    }
    
    return $phone;
}

/**
 * Générer des initiales
 */
function fmp_generate_initials($prenom, $nom) {
    $prenom_initial = !empty($prenom) ? strtoupper(substr($prenom, 0, 1)) : '';
    $nom_initial = !empty($nom) ? strtoupper(substr($nom, 0, 1)) : '';
    
    return $prenom_initial . $nom_initial;
}

/**
 * Formater une date relative
 */
function fmp_time_elapsed($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'à l\'instant';
    if ($time < 3600) return floor($time / 60) . ' minutes';
    if ($time < 86400) return floor($time / 3600) . ' heures';
    if ($time < 2592000) return floor($time / 86400) . ' jours';
    if ($time < 31536000) return floor($time / 2592000) . ' mois';
    
    return floor($time / 31536000) . ' années';
}

/**
 * Fonctions de slugs et URLs
 */

/**
 * Générer un slug unique
 */
function fmp_generate_unique_slug($title, $post_type = 'formateur_pro') {
    $slug = sanitize_title($title);
    $original_slug = $slug;
    $counter = 1;
    
    while (get_page_by_path($slug, OBJECT, $post_type)) {
        $slug = $original_slug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}

/**
 * Obtenir l'URL du profil d'un formateur
 */
function fmp_get_formateur_profile_url($formateur_id) {
    return get_permalink($formateur_id);
}

/**
 * Fonctions de logs
 */

/**
 * Logger une action
 */
function fmp_log($message, $level = 'info', $context = []) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $timestamp = current_time('Y-m-d H:i:s');
        $context_str = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $log_message = sprintf('[%s] FMP %s: %s%s', $timestamp, strtoupper($level), $message, $context_str);
        error_log($log_message);
    }
    
    // Optionnel : sauvegarder dans une table de logs personnalisée
    if (get_option('fmp_enable_database_logs', false)) {
        fmp_save_to_database_log($message, $level, $context);
    }
}

/**
 * Logger une erreur
 */
function fmp_log_error($message, $context = []) {
    fmp_log($message, 'error', $context);
}

/**
 * Logger un succès
 */
function fmp_log_success($message, $context = []) {
    fmp_log($message, 'success', $context);
}

/**
 * Sauvegarder un log dans la base de données (optionnel)
 */
function fmp_save_to_database_log($message, $level, $context) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'fmp_logs';
    
    // Créer la table si elle n'existe pas
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$table_name} (
        id int(11) NOT NULL AUTO_INCREMENT,
        timestamp datetime DEFAULT CURRENT_TIMESTAMP,
        level varchar(20) NOT NULL,
        message text NOT NULL,
        context longtext,
        user_id int(11),
        ip_address varchar(45),
        PRIMARY KEY (id),
        KEY level (level),
        KEY timestamp (timestamp)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    $wpdb->insert(
        $table_name,
        [
            'level' => $level,
            'message' => $message,
            'context' => json_encode($context),
            'user_id' => get_current_user_id(),
            'ip_address' => fmp_get_user_ip()
        ],
        ['%s', '%s', '%s', '%d', '%s']
    );
}

/**
 * Fonctions d'emails
 */

/**
 * Obtenir l'instance de gestion des emails
 */
function fmp_get_email_manager() {
    static $email_manager = null;
    
    if (null === $email_manager) {
        require_once FMP_PLUGIN_PATH . 'includes/class-email.php';
        $config = [
            'from_name' => get_bloginfo('name'),
            'from_email' => get_option('admin_email')
        ];
        $email_manager = new FMP_Email($config);
    }
    
    return $email_manager;
}

/**
 * Envoyer un email simple
 */
function fmp_send_email($to, $subject, $message, $headers = []) {
    $email_manager = fmp_get_email_manager();
    return $email_manager->sendSimpleEmail($to, $subject, $message, $headers);
}

/**
 * Fonctions de templates
 */

/**
 * Charger un template avec variables
 */
function fmp_get_template($template_name, $variables = []) {
    $template_path = FMP_PLUGIN_PATH . 'templates/' . $template_name . '.php';
    
    if (!file_exists($template_path)) {
        fmp_log_error("Template non trouvé: {$template_name}", ['path' => $template_path]);
        return '';
    }
    
    // Extraire les variables pour les rendre disponibles dans le template
    extract($variables);
    
    ob_start();
    include $template_path;
    return ob_get_clean();
}

/**
 * Fonctions d'images et médias
 */

/**
 * Obtenir l'avatar d'un formateur
 */
function fmp_get_formateur_avatar($formateur_id, $size = 64) {
    $photo = fmp_get_formateur_meta($formateur_id, 'photo');
    
    if ($photo && !empty($photo['url'])) {
        return sprintf(
            '<img src="%s" alt="%s" width="%d" height="%d" class="formateur-avatar-img">',
            esc_url($photo['url']),
            esc_attr(fmp_get_formateur_meta($formateur_id, 'prenom') . ' ' . fmp_get_formateur_meta($formateur_id, 'nom')),
            $size,
            $size
        );
    }
    
    // Avatar par défaut avec initiales
    $prenom = fmp_get_formateur_meta($formateur_id, 'prenom');
    $nom = fmp_get_formateur_meta($formateur_id, 'nom');
    $initiales = fmp_generate_initials($prenom, $nom);
    
    return sprintf(
        '<div class="formateur-avatar-default" style="width:%dpx;height:%dpx;line-height:%dpx;">%s</div>',
        $size,
        $size,
        $size,
        $initiales
    );
}

/**
 * Redimensionner une image
 */
function fmp_resize_image($attachment_id, $width, $height, $crop = true) {
    $image = wp_get_attachment_image_src($attachment_id, 'full');
    
    if (!$image) {
        return false;
    }
    
    $image_path = get_attached_file($attachment_id);
    $wp_upload_dir = wp_upload_dir();
    
    $image_editor = wp_get_image_editor($image_path);
    
    if (is_wp_error($image_editor)) {
        return false;
    }
    
    $image_editor->resize($width, $height, $crop);
    
    $resized_filename = $image_editor->generate_filename($width . 'x' . $height);
    $saved = $image_editor->save($resized_filename);
    
    if (is_wp_error($saved)) {
        return false;
    }
    
    return str_replace($wp_upload_dir['basedir'], $wp_upload_dir['baseurl'], $saved['path']);
}

/**
 * Fonctions d'export/import
 */

/**
 * Exporter les formateurs en CSV
 */
function fmp_export_formateurs_csv($formateur_ids = []) {
    $formateurs = empty($formateur_ids) ? fmp_get_formateurs(['posts_per_page' => -1]) : get_posts([
        'post_type' => 'formateur_pro',
        'include' => $formateur_ids,
        'posts_per_page' => -1
    ]);
    
    if (empty($formateurs)) {
        return false;
    }
    
    $csv_data = [];
    $csv_data[] = [
        'ID', 'Prénom', 'Nom', 'Email', 'Téléphone', 'Ville', 'Pays',
        'Spécialité', 'Expérience', 'Statut', 'Date d\'inscription'
    ];
    
    foreach ($formateurs as $formateur) {
        $meta = get_post_meta($formateur->ID);
        
        $csv_data[] = [
            $formateur->ID,
            $meta['_formateur_prenom'][0] ?? '',
            $meta['_formateur_nom'][0] ?? '',
            $meta['_formateur_email'][0] ?? '',
            $meta['_formateur_telephone'][0] ?? '',
            $meta['_formateur_ville'][0] ?? '',
            $meta['_formateur_pays'][0] ?? '',
            $meta['_formateur_specialite'][0] ?? '',
            $meta['_formateur_experience'][0] ?? '',
            $meta['_formateur_status'][0] ?? '',
            $formateur->post_date
        ];
    }
    
    return $csv_data;
}

/**
 * Fonctions de statistiques
 */

/**
 * Obtenir les statistiques d'un formateur
 */
function fmp_get_formateur_stats($formateur_id) {
    return [
        'vues' => get_post_meta($formateur_id, '_formateur_views', true) ?: 0,
        'contacts' => get_post_meta($formateur_id, '_formateur_contacts', true) ?: 0,
        'formations' => get_post_meta($formateur_id, '_formateur_formations_count', true) ?: 0,
        'note_moyenne' => get_post_meta($formateur_id, '_formateur_rating_average', true) ?: 0,
        'nb_avis' => get_post_meta($formateur_id, '_formateur_rating_count', true) ?: 0
    ];
}

/**
 * Incrémenter une statistique
 */
function fmp_increment_stat($formateur_id, $stat_key) {
    $current_value = get_post_meta($formateur_id, $stat_key, true) ?: 0;
    update_post_meta($formateur_id, $stat_key, $current_value + 1);
    
    fmp_log("Statistique incrémentée: {$stat_key} pour formateur {$formateur_id}");
}

/**
 * Hooks et actions
 */

// Nettoyer le cache quand un formateur est modifié
add_action('save_post_formateur_pro', function($post_id) {
    fmp_clear_formateur_cache($post_id);
    fmp_log("Cache vidé pour formateur {$post_id}");
});

// Nettoyer le cache quand une taxonomie est modifiée
add_action('edited_formateur_specialty', function() {
    fmp_clear_formateur_cache();
});

add_action('edited_formateur_skill', function() {
    fmp_clear_formateur_cache();
});

// Hook pour l'approbation d'un formateur
add_action('fmp_formateur_approved', function($formateur_id) {
    $email_manager = fmp_get_email_manager();
    $email_manager->sendApprovalNotification($formateur_id);
    
    fmp_log_success("Formateur approuvé: ID {$formateur_id}");
});

// Hook pour les changements de statut
add_action('fmp_formateur_status_changed', function($formateur_id, $status) {
    fmp_log("Statut formateur changé: ID {$formateur_id} -> {$status}");
});

// Incrémenter les vues quand un profil est consulté
add_action('wp_head', function() {
    if (is_singular('formateur_pro')) {
        $formateur_id = get_the_ID();
        fmp_increment_stat($formateur_id, '_formateur_views');
    }
});

/**
 * Actions AJAX supplémentaires
 */

// Approuver un formateur via AJAX
add_action('wp_ajax_fmp_approve_formateur', function() {
    if (!fmp_check_user_permissions('approve_formateur') || !wp_verify_nonce($_POST['nonce'] ?? '', 'fmp_admin_nonce')) {
        wp_send_json_error(__('Permission refusée', 'formateur-manager-pro'));
    }
    
    $formateur_id = intval($_POST['formateur_id'] ?? 0);
    if (!$formateur_id) {
        wp_send_json_error(__('ID formateur invalide', 'formateur-manager-pro'));
    }
    
    $updated = fmp_update_formateur_status($formateur_id, 'active');
    
    if ($updated) {
        // Publier le post aussi
        wp_update_post([
            'ID' => $formateur_id,
            'post_status' => 'publish'
        ]);
        
        wp_send_json_success(__('Formateur approuvé avec succès', 'formateur-manager-pro'));
    } else {
        wp_send_json_error(__('Erreur lors de l\'approbation', 'formateur-manager-pro'));
    }
});

// Actualiser le dashboard via AJAX
add_action('wp_ajax_fmp_refresh_dashboard', function() {
    if (!fmp_check_user_permissions('manage_settings') || !wp_verify_nonce($_POST['nonce'] ?? '', 'fmp_admin_nonce')) {
        wp_send_json_error(__('Permission refusée', 'formateur-manager-pro'));
    }
    
    // Supprimer le cache du dashboard
    delete_transient('fmp_dashboard_stats');
    fmp_clear_formateur_cache();
    
    wp_send_json_success(__('Dashboard actualisé', 'formateur-manager-pro'));
});

// Contact formateur via AJAX
add_action('wp_ajax_fmp_contact_formateur', 'fmp_handle_contact_formateur');
add_action('wp_ajax_nopriv_fmp_contact_formateur', 'fmp_handle_contact_formateur');

function fmp_handle_contact_formateur() {
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'fmp_contact_nonce')) {
        wp_send_json_error(__('Erreur de sécurité', 'formateur-manager-pro'));
    }
    
    // Rate limiting
    if (!fmp_rate_limit('contact_formateur', 3, 300)) {
        wp_send_json_error(__('Trop de demandes. Veuillez réessayer dans 5 minutes.', 'formateur-manager-pro'));
    }
    
    $formateur_id = intval($_POST['formateur_id'] ?? 0);
    $client_name = sanitize_text_field($_POST['client_name'] ?? '');
    $client_email = sanitize_email($_POST['client_email'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');
    
    if (!$formateur_id || !$client_name || !$client_email || !$message) {
        wp_send_json_error(__('Tous les champs sont obligatoires', 'formateur-manager-pro'));
    }
    
    // Incrémenter le compteur de contacts
    fmp_increment_stat($formateur_id, '_formateur_contacts');
    
    // Envoyer l'email
    $email_manager = fmp_get_email_manager();
    $formateur_email = fmp_get_formateur_meta($formateur_id, 'email');
    
    $contact_data = [
        'formateur_email' => $formateur_email,
        'client_name' => $client_name,
        'client_email' => $client_email,
        'message' => $message,
        'formateur_name' => fmp_get_formateur_meta($formateur_id, 'prenom') . ' ' . fmp_get_formateur_meta($formateur_id, 'nom')
    ];
    
    $sent = $email_manager->sendContactNotification($contact_data);
    
    if ($sent) {
        wp_send_json_success(__('Message envoyé avec succès', 'formateur-manager-pro'));
    } else {
        wp_send_json_error(__('Erreur lors de l\'envoi du message', 'formateur-manager-pro'));
    }
}

/**
 * Fonction de désinstallation
 */
function fmp_uninstall_cleanup() {
    // Supprimer les options
    $options_to_delete = [
        'fmp_version',
        'fmp_email_notifications',
        'fmp_auto_approve',
        'fmp_require_photo',
        'fmp_require_cv',
        'fmp_items_per_page',
        'fmp_cache_duration',
        'fmp_contact_method',
        'fmp_admin_email',
        'fmp_enable_database_logs'
    ];
    
    foreach ($options_to_delete as $option) {
        delete_option($option);
    }
    
    // Supprimer tous les transients
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_fmp_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_fmp_%'");
    
    // Supprimer la table des logs si elle existe
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}fmp_logs");
    
    fmp_log('Plugin désinstallé et nettoyé');
}

// Hook de désinstallation
register_uninstall_hook(FMP_PLUGIN_PATH . 'formateur-manager-pro.php', 'fmp_uninstall_cleanup');

/**
 * Fonctions de compatibilité
 */

/**
 * Vérifier la compatibilité WordPress
 */
function fmp_check_wp_compatibility() {
    global $wp_version;
    
    $required_wp_version = '5.0';
    
    if (version_compare($wp_version, $required_wp_version, '<')) {
        add_action('admin_notices', function() use ($required_wp_version) {
            echo '<div class="notice notice-error"><p>';
            printf(
                __('Formateur Manager Pro nécessite WordPress %s ou plus récent. Votre version : %s', 'formateur-manager-pro'),
                $required_wp_version,
                $GLOBALS['wp_version']
            );
            echo '</p></div>';
        });
        return false;
    }
    
    return true;
}

/**
 * Vérifier la compatibilité PHP
 */
function fmp_check_php_compatibility() {
    $required_php_version = '7.4';
    
    if (version_compare(PHP_VERSION, $required_php_version, '<')) {
        add_action('admin_notices', function() use ($required_php_version) {
            echo '<div class="notice notice-error"><p>';
            printf(
                __('Formateur Manager Pro nécessite PHP %s ou plus récent. Votre version : %s', 'formateur-manager-pro'),
                $required_php_version,
                PHP_VERSION
            );
            echo '</p></div>';
        });
        return false;
    }
    
    return true;
}

// Vérifications de compatibilité au chargement
add_action('plugins_loaded', function() {
    fmp_check_wp_compatibility();
    fmp_check_php_compatibility();
});