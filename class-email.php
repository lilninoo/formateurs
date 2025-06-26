<?php
/**
 * Classe de gestion des emails - Formateur Manager Pro
 * includes/class-email.php
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe de gestion des emails
 */
class FMP_Email {
    
    private $config;
    private $template_path;
    
    public function __construct($config = []) {
        $this->config = wp_parse_args($config, [
            'from_name' => get_bloginfo('name'),
            'from_email' => get_option('admin_email'),
            'reply_to' => get_option('admin_email')
        ]);
        
        $this->template_path = FMP_PLUGIN_PATH . 'templates/emails/';
        
        add_action('init', [$this, 'init']);
    }
    
    public function init() {
        // Hook pour personnaliser les emails WordPress
        add_filter('wp_mail_content_type', [$this, 'setEmailContentType']);
        add_filter('wp_mail_from_name', [$this, 'setEmailFromName']);
        add_filter('wp_mail_from', [$this, 'setEmailFrom']);
        
        // Actions AJAX pour les emails
        add_action('wp_ajax_fmp_send_test_email', [$this, 'sendTestEmail']);
        add_action('wp_ajax_fmp_resend_registration_email', [$this, 'resendRegistrationEmail']);
    }
    
    /**
     * Définir le type de contenu HTML pour les emails
     */
    public function setEmailContentType() {
        return 'text/html';
    }
    
    /**
     * Définir le nom de l'expéditeur
     */
    public function setEmailFromName($name) {
        return $this->config['from_name'];
    }
    
    /**
     * Définir l'email de l'expéditeur
     */
    public function setEmailFrom($email) {
        return $this->config['from_email'];
    }
    
    /**
     * Envoyer un email avec template
     */
    public function sendEmail($to, $subject, $template, $data = [], $attachments = []) {
        $message = $this->renderTemplate($template, $data);
        
        if (!$message) {
            fmp_log_error("Template email non trouvé: {$template}");
            return false;
        }
        
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->config['from_name'] . ' <' . $this->config['from_email'] . '>'
        ];
        
        // Ajouter des headers supplémentaires si nécessaire
        if (!empty($data['reply_to'])) {
            $headers[] = 'Reply-To: ' . $data['reply_to'];
        }
        
        if (!empty($this->config['reply_to'])) {
            $headers[] = 'Reply-To: ' . $this->config['reply_to'];
        }
        
        $sent = wp_mail($to, $subject, $message, $headers, $attachments);
        
        // Logger l'envoi
        $this->logEmail($to, $subject, $template, $sent, $data);
        
        return $sent;
    }
    
    /**
     * Envoyer un email simple sans template
     */
    public function sendSimpleEmail($to, $subject, $message, $headers = []) {
        $default_headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->config['from_name'] . ' <' . $this->config['from_email'] . '>'
        ];
        
        $headers = array_merge($default_headers, $headers);
        
        $sent = wp_mail($to, $subject, $message, $headers);
        
        $this->logEmail($to, $subject, 'simple', $sent);
        
        return $sent;
    }
    
    /**
     * Rendre un template d'email
     */
    private function renderTemplate($template, $data = []) {
        $template_file = $this->template_path . $template . '.php';
        
        if (!file_exists($template_file)) {
            // Essayer avec un template par défaut
            $template_file = $this->template_path . 'default.php';
            if (!file_exists($template_file)) {
                return $this->getDefaultTemplate($data);
            }
        }
        
        // Extraire les données pour les rendre disponibles dans le template
        extract(array_merge([
            'site_name' => get_bloginfo('name'),
            'site_url' => home_url(),
            'admin_email' => get_option('admin_email')
        ], $data));
        
        ob_start();
        include $template_file;
        return ob_get_clean();
    }
    
    /**
     * Template par défaut si aucun template n'est trouvé
     */
    private function getDefaultTemplate($data) {
        $site_name = get_bloginfo('name');
        $site_url = home_url();
        $message = $data['message'] ?? 'Message par défaut';
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>{$site_name}</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h1 style='color: #2563eb;'>{$site_name}</h1>
                <div style='background: #f9f9f9; padding: 20px; border-radius: 5px;'>
                    {$message}
                </div>
                <hr style='margin: 30px 0;'>
                <p style='font-size: 12px; color: #666;'>
                    Cet email a été envoyé par <a href='{$site_url}'>{$site_name}</a>
                </p>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Logger les envois d'emails
     */
    private function logEmail($to, $subject, $template, $sent, $data = []) {
        $log_data = [
            'to' => $to,
            'template' => $template,
            'subject' => $subject,
            'data_keys' => array_keys($data)
        ];
        
        if ($sent) {
            fmp_log("Email envoyé avec succès", 'success', $log_data);
        } else {
            fmp_log_error("Échec envoi email", $log_data);
        }
    }
    
    /**
     * Envoyer email de test (AJAX)
     */
    public function sendTestEmail() {
        if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['nonce'] ?? '', 'fmp_admin_nonce')) {
            wp_send_json_error(__('Permission refusée', 'formateur-manager-pro'));
        }
        
        $email = sanitize_email($_POST['email'] ?? '');
        if (!is_email($email)) {
            wp_send_json_error(__('Email invalide', 'formateur-manager-pro'));
        }
        
        $test_data = [
            'message' => __('Ceci est un email de test du plugin Formateur Manager Pro. Si vous recevez cet email, la configuration fonctionne correctement !', 'formateur-manager-pro'),
            'test_date' => current_time('d/m/Y à H:i'),
            'wp_version' => get_bloginfo('version'),
            'plugin_version' => FMP_VERSION
        ];
        
        $sent = $this->sendEmail(
            $email,
            __('Test Email - Formateur Manager Pro', 'formateur-manager-pro'),
            'test-email',
            $test_data
        );
        
        if ($sent) {
            wp_send_json_success(__('Email de test envoyé avec succès', 'formateur-manager-pro'));
        } else {
            wp_send_json_error(__('Erreur lors de l\'envoi de l\'email de test', 'formateur-manager-pro'));
        }
    }
    
    /**
     * Renvoyer email d'inscription (AJAX)
     */
    public function resendRegistrationEmail() {
        if (!current_user_can('edit_posts') || !wp_verify_nonce($_POST['nonce'] ?? '', 'fmp_admin_nonce')) {
            wp_send_json_error(__('Permission refusée', 'formateur-manager-pro'));
        }
        
        $formateur_id = intval($_POST['formateur_id'] ?? 0);
        if (!$formateur_id) {
            wp_send_json_error(__('ID formateur invalide', 'formateur-manager-pro'));
        }
        
        $formateur = get_post($formateur_id);
        if (!$formateur || $formateur->post_type !== 'formateur_pro') {
            wp_send_json_error(__('Formateur non trouvé', 'formateur-manager-pro'));
        }
        
        // Récupérer les données du formateur
        $formateur_data = [
            'prenom' => fmp_get_formateur_meta($formateur_id, 'prenom'),
            'nom' => fmp_get_formateur_meta($formateur_id, 'nom'),
            'email' => fmp_get_formateur_meta($formateur_id, 'email'),
            'specialite' => fmp_get_formateur_meta($formateur_id, 'specialite'),
            'ville' => fmp_get_formateur_meta($formateur_id, 'ville'),
            'status' => fmp_get_formateur_meta($formateur_id, 'status')
        ];
        
        if (!is_email($formateur_data['email'])) {
            wp_send_json_error(__('Email du formateur invalide', 'formateur-manager-pro'));
        }
        
        $template = $formateur_data['status'] === 'active' ? 'formateur-approved' : 'registration-reminder';
        
        $sent = $this->sendEmail(
            $formateur_data['email'],
            sprintf(__('Rappel - Inscription %s', 'formateur-manager-pro'), get_bloginfo('name')),
            $template,
            $formateur_data
        );
        
        if ($sent) {
            wp_send_json_success(__('Email de rappel envoyé', 'formateur-manager-pro'));
        } else {
            wp_send_json_error(__('Erreur lors de l\'envoi', 'formateur-manager-pro'));
        }
    }
    
    /**
     * Envoyer notification de nouveau contact
     */
    public function sendContactNotification($contact_data) {
        $subject = sprintf(
            __('Nouvelle demande de formation - %s', 'formateur-manager-pro'),
            $contact_data['subject'] ?? 'Demande de contact'
        );
        
        return $this->sendEmail(
            $contact_data['formateur_email'],
            $subject,
            'contact-formateur',
            $contact_data
        );
    }
    
    /**
     * Envoyer notification d'approbation
     */
    public function sendApprovalNotification($formateur_id) {
        $formateur_data = [
            'ID' => $formateur_id,
            'prenom' => fmp_get_formateur_meta($formateur_id, 'prenom'),
            'nom' => fmp_get_formateur_meta($formateur_id, 'nom'),
            'email' => fmp_get_formateur_meta($formateur_id, 'email'),
            'profile_url' => get_permalink($formateur_id)
        ];
        
        return $this->sendEmail(
            $formateur_data['email'],
            sprintf(__('🎉 Profil approuvé - %s', 'formateur-manager-pro'), get_bloginfo('name')),
            'formateur-approved',
            $formateur_data
        );
    }
    
    /**
     * Envoyer notification d'inscription à l'admin
     */
    public function sendAdminRegistrationNotification($formateur_data, $formateur_id, $uploaded_files = []) {
        $admin_email = get_option('fmp_admin_email', get_option('admin_email'));
        
        $subject = sprintf(
            __('Nouvelle inscription formateur - %s %s', 'formateur-manager-pro'),
            $formateur_data['prenom'],
            $formateur_data['nom']
        );
        
        $data = array_merge($formateur_data, [
            'formateur_id' => $formateur_id,
            'uploaded_files' => $uploaded_files,
            'edit_url' => admin_url("post.php?post={$formateur_id}&action=edit"),
            'approve_url' => admin_url("edit.php?post_type=formateur_pro&post_status=pending")
        ]);
        
        return $this->sendEmail(
            $admin_email,
            $subject,
            'admin-registration',
            $data
        );
    }
    
    /**
     * Envoyer email de bienvenue
     */
    public function sendWelcomeEmail($formateur_data) {
        $subject = sprintf(__('Bienvenue chez %s !', 'formateur-manager-pro'), get_bloginfo('name'));
        
        return $this->sendEmail(
            $formateur_data['email'],
            $subject,
            'welcome-formateur',
            $formateur_data
        );
    }
    
    /**
     * Envoyer email de rejet
     */
    public function sendRejectionEmail($formateur_data, $reason = '') {
        $data = array_merge($formateur_data, [
            'reason' => $reason,
            'reapply_url' => home_url('/devenir-formateur/')
        ]);
        
        $subject = sprintf(__('Inscription non retenue - %s', 'formateur-manager-pro'), get_bloginfo('name'));
        
        return $this->sendEmail(
            $formateur_data['email'],
            $subject,
            'formateur-rejected',
            $data
        );
    }
    
    /**
     * Envoyer email de confirmation de contact
     */
    public function sendContactConfirmation($contact_data) {
        $subject = sprintf(__('Confirmation de votre demande - %s', 'formateur-manager-pro'), get_bloginfo('name'));
        
        return $this->sendEmail(
            $contact_data['client_email'],
            $subject,
            'contact-confirmation',
            $contact_data
        );
    }
    
    /**
     * Envoyer newsletter aux formateurs
     */
    public function sendNewsletterToFormateurs($subject, $content, $formateur_ids = []) {
        if (empty($formateur_ids)) {
            // Récupérer tous les formateurs qui ont accepté la newsletter
            global $wpdb;
            $formateur_ids = $wpdb->get_col($wpdb->prepare("
                SELECT p.ID 
                FROM {$wpdb->posts} p 
                INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id 
                INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id 
                WHERE p.post_type = %s 
                AND p.post_status = 'publish'
                AND pm1.meta_key = '_formateur_status' 
                AND pm1.meta_value = 'active'
                AND pm2.meta_key = '_formateur_newsletter_consent'
                AND pm2.meta_value = '1'
            ", 'formateur_pro'));
        }
        
        $sent_count = 0;
        $errors = [];
        
        foreach ($formateur_ids as $formateur_id) {
            $formateur_data = [
                'prenom' => fmp_get_formateur_meta($formateur_id, 'prenom'),
                'nom' => fmp_get_formateur_meta($formateur_id, 'nom'),
                'email' => fmp_get_formateur_meta($formateur_id, 'email'),
                'newsletter_content' => $content,
                'unsubscribe_url' => add_query_arg([
                    'action' => 'fmp_unsubscribe',
                    'formateur_id' => $formateur_id,
                    'token' => wp_create_nonce('fmp_unsubscribe_' . $formateur_id)
                ], home_url())
            ];
            
            $sent = $this->sendEmail(
                $formateur_data['email'],
                $subject,
                'newsletter',
                $formateur_data
            );
            
            if ($sent) {
                $sent_count++;
            } else {
                $errors[] = $formateur_id;
            }
            
            // Petite pause pour éviter de surcharger le serveur
            usleep(100000); // 0.1 seconde
        }
        
        fmp_log("Newsletter envoyée à {$sent_count} formateurs", 'success', [
            'subject' => $subject,
            'total_recipients' => count($formateur_ids),
            'errors' => $errors
        ]);
        
        return [
            'sent' => $sent_count,
            'total' => count($formateur_ids),
            'errors' => $errors
        ];
    }
    
    /**
     * Envoyer rappel de profil incomplet
     */
    public function sendProfileIncompleteReminder($formateur_id) {
        $formateur_data = [
            'prenom' => fmp_get_formateur_meta($formateur_id, 'prenom'),
            'nom' => fmp_get_formateur_meta($formateur_id, 'nom'),
            'email' => fmp_get_formateur_meta($formateur_id, 'email'),
            'profile_edit_url' => admin_url("post.php?post={$formateur_id}&action=edit"),
            'missing_fields' => $this->getMissingProfileFields($formateur_id)
        ];
        
        $subject = sprintf(__('Complétez votre profil - %s', 'formateur-manager-pro'), get_bloginfo('name'));
        
        return $this->sendEmail(
            $formateur_data['email'],
            $subject,
            'profile-incomplete',
            $formateur_data
        );
    }
    
    /**
     * Obtenir les champs manquants du profil
     */
    private function getMissingProfileFields($formateur_id) {
        $required_fields = [
            'prenom' => __('Prénom', 'formateur-manager-pro'),
            'nom' => __('Nom', 'formateur-manager-pro'),
            'email' => __('Email', 'formateur-manager-pro'),
            'telephone' => __('Téléphone', 'formateur-manager-pro'),
            'ville' => __('Ville', 'formateur-manager-pro'),
            'specialite' => __('Spécialité', 'formateur-manager-pro'),
            'biographie' => __('Biographie', 'formateur-manager-pro')
        ];
        
        $missing = [];
        
        foreach ($required_fields as $field => $label) {
            $value = fmp_get_formateur_meta($formateur_id, $field);
            if (empty($value)) {
                $missing[] = $label;
            }
        }
        
        // Vérifier le CV
        $cv = fmp_get_formateur_meta($formateur_id, 'cv');
        if (empty($cv) || empty($cv['url'])) {
            $missing[] = __('CV', 'formateur-manager-pro');
        }
        
        return $missing;
    }
    
    /**
     * Envoyer rapport mensuel aux administrateurs
     */
    public function sendMonthlyReport() {
        $admin_email = get_option('fmp_admin_email', get_option('admin_email'));
        
        // Statistiques du mois
        global $wpdb;
        
        $current_month = date('Y-m');
        
        $stats = [
            'new_registrations' => $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) FROM {$wpdb->posts} 
                WHERE post_type = 'formateur_pro' 
                AND DATE_FORMAT(post_date, '%%Y-%%m') = %s
            ", $current_month)),
            
            'approved_this_month' => $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                WHERE p.post_type = 'formateur_pro'
                AND pm.meta_key = '_formateur_status'
                AND pm.meta_value = 'active'
                AND DATE_FORMAT(p.post_modified, '%%Y-%%m') = %s
            ", $current_month)),
            
            'total_active' => $wpdb->get_var("
                SELECT COUNT(*) FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                WHERE p.post_type = 'formateur_pro'
                AND p.post_status = 'publish'
                AND pm.meta_key = '_formateur_status'
                AND pm.meta_value = 'active'
            "),
            
            'pending_approval' => $wpdb->get_var("
                SELECT COUNT(*) FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                WHERE p.post_type = 'formateur_pro'
                AND pm.meta_key = '_formateur_status'
                AND pm.meta_value = 'pending'
            ")
        ];
        
        $report_data = [
            'month_name' => date_i18n('F Y'),
            'stats' => $stats,
            'dashboard_url' => admin_url('edit.php?post_type=formateur_pro&page=formateur-dashboard')
        ];
        
        $subject = sprintf(__('Rapport mensuel - %s', 'formateur-manager-pro'), get_bloginfo('name'));
        
        return $this->sendEmail(
            $admin_email,
            $subject,
            'monthly-report',
            $report_data
        );
    }
    
    /**
     * Planifier l'envoi d'emails en masse
     */
    public function scheduleEmailBatch($emails, $delay_between_emails = 1) {
        foreach ($emails as $index => $email_data) {
            wp_schedule_single_event(
                time() + ($index * $delay_between_emails),
                'fmp_send_scheduled_email',
                [$email_data]
            );
        }
        
        fmp_log("Planification de {count($emails)} emails", 'info');
    }
    
    /**
     * Valider les paramètres d'email
     */
    public function validateEmailSettings() {
        $errors = [];
        
        if (!is_email($this->config['from_email'])) {
            $errors[] = __('L\'adresse email d\'expédition n\'est pas valide', 'formateur-manager-pro');
        }
        
        if (empty($this->config['from_name'])) {
            $errors[] = __('Le nom d\'expédition est requis', 'formateur-manager-pro');
        }
        
        // Test de connexion SMTP si configuré
        if (defined('SMTP_HOST') && !empty(SMTP_HOST)) {
            // Tester la connexion SMTP
            // Implementation dépendante de la configuration SMTP
        }
        
        return $errors;
    }
    
    /**
     * Obtenir les statistiques d'envoi
     */
    public function getEmailStats($period = '30 days') {
        global $wpdb;
        
        if (!get_option('fmp_enable_database_logs', false)) {
            return ['error' => __('Les logs de base de données ne sont pas activés', 'formateur-manager-pro')];
        }
        
        $table_name = $wpdb->prefix . 'fmp_logs';
        
        $stats = $wpdb->get_row($wpdb->prepare("
            SELECT 
                COUNT(*) as total_emails,
                SUM(CASE WHEN level = 'success' THEN 1 ELSE 0 END) as successful,
                SUM(CASE WHEN level = 'error' THEN 1 ELSE 0 END) as failed,
                COUNT(DISTINCT DATE(timestamp)) as active_days
            FROM {$table_name} 
            WHERE message LIKE '%%Email%%'
            AND timestamp >= DATE_SUB(NOW(), INTERVAL %s)
        ", $period), ARRAY_A);
        
        $stats['success_rate'] = $stats['total_emails'] > 0 
            ? round(($stats['successful'] / $stats['total_emails']) * 100, 2) 
            : 0;
        
        return $stats;
    }
}

// Action pour envoyer les emails planifiés
add_action('fmp_send_scheduled_email', function($email_data) {
    $email_manager = fmp_get_email_manager();
    $email_manager->sendEmail(
        $email_data['to'],
        $email_data['subject'],
        $email_data['template'],
        $email_data['data'] ?? [],
        $email_data['attachments'] ?? []
    );
});

// Programmer l'envoi du rapport mensuel
add_action('fmp_monthly_report', function() {
    $email_manager = fmp_get_email_manager();
    $email_manager->sendMonthlyReport();
});

// Activer le cron pour le rapport mensuel si pas déjà fait
if (!wp_next_scheduled('fmp_monthly_report')) {
    wp_schedule_event(time(), 'monthly', 'fmp_monthly_report');
}