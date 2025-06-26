<?php
/**
 * Page de paramètres - Formateur Manager Pro
 * admin/settings.php
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

// Traitement du formulaire
if ($_POST && wp_verify_nonce($_POST['fmp_settings_nonce'], 'fmp_settings_save')) {
    $settings = [
        'fmp_email_notifications' => sanitize_text_field($_POST['email_notifications'] ?? 'yes'),
        'fmp_auto_approve' => sanitize_text_field($_POST['auto_approve'] ?? 'no'),
        'fmp_require_photo' => sanitize_text_field($_POST['require_photo'] ?? 'no'),
        'fmp_require_cv' => sanitize_text_field($_POST['require_cv'] ?? 'yes'),
        'fmp_items_per_page' => intval($_POST['items_per_page'] ?? 12),
        'fmp_cache_duration' => intval($_POST['cache_duration'] ?? 60),
        'fmp_contact_method' => sanitize_text_field($_POST['contact_method'] ?? 'email'),
        'fmp_admin_email' => sanitize_email($_POST['admin_email'] ?? get_option('admin_email')),
        'fmp_enable_database_logs' => sanitize_text_field($_POST['enable_logs'] ?? 'no')
    ];
    
    foreach ($settings as $key => $value) {
        update_option($key, $value);
    }
    
    fmp_clear_formateur_cache();
    echo '<div class="notice notice-success"><p>Paramètres sauvegardés avec succès !</p></div>';
}

// Récupérer les paramètres actuels
$current_settings = [
    'email_notifications' => get_option('fmp_email_notifications', 'yes'),
    'auto_approve' => get_option('fmp_auto_approve', 'no'),
    'require_photo' => get_option('fmp_require_photo', 'no'),
    'require_cv' => get_option('fmp_require_cv', 'yes'),
    'items_per_page' => get_option('fmp_items_per_page', 12),
    'cache_duration' => get_option('fmp_cache_duration', 60),
    'contact_method' => get_option('fmp_contact_method', 'email'),
    'admin_email' => get_option('fmp_admin_email', get_option('admin_email')),
    'enable_logs' => get_option('fmp_enable_database_logs', 'no')
];
?>

<div class="wrap">
    <h1>⚙️ Paramètres Formateur Manager Pro</h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('fmp_settings_save', 'fmp_settings_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">Notifications par email</th>
                <td>
                    <fieldset>
                        <label>
                            <input type="radio" name="email_notifications" value="yes" <?php checked($current_settings['email_notifications'], 'yes'); ?>>
                            Activées
                        </label><br>
                        <label>
                            <input type="radio" name="email_notifications" value="no" <?php checked($current_settings['email_notifications'], 'no'); ?>>
                            Désactivées
                        </label>
                        <p class="description">Envoyer des notifications par email lors des inscriptions.</p>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <th scope="row">Approbation automatique</th>
                <td>
                    <fieldset>
                        <label>
                            <input type="radio" name="auto_approve" value="yes" <?php checked($current_settings['auto_approve'], 'yes'); ?>>
                            Activée
                        </label><br>
                        <label>
                            <input type="radio" name="auto_approve" value="no" <?php checked($current_settings['auto_approve'], 'no'); ?>>
                            Désactivée (recommandé)
                        </label>
                        <p class="description">Approuver automatiquement les nouveaux formateurs.</p>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <th scope="row">Photo obligatoire</th>
                <td>
                    <fieldset>
                        <label>
                            <input type="radio" name="require_photo" value="yes" <?php checked($current_settings['require_photo'], 'yes'); ?>>
                            Oui
                        </label><br>
                        <label>
                            <input type="radio" name="require_photo" value="no" <?php checked($current_settings['require_photo'], 'no'); ?>>
                            Non
                        </label>
                        <p class="description">Rendre la photo de profil obligatoire lors de l'inscription.</p>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <th scope="row">CV obligatoire</th>
                <td>
                    <fieldset>
                        <label>
                            <input type="radio" name="require_cv" value="yes" <?php checked($current_settings['require_cv'], 'yes'); ?>>
                            Oui (recommandé)
                        </label><br>
                        <label>
                            <input type="radio" name="require_cv" value="no" <?php checked($current_settings['require_cv'], 'no'); ?>>
                            Non
                        </label>
                        <p class="description">Rendre le CV obligatoire lors de l'inscription.</p>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <th scope="row">Formateurs par page</th>
                <td>
                    <input type="number" name="items_per_page" value="<?php echo esc_attr($current_settings['items_per_page']); ?>" min="1" max="50" class="small-text">
                    <p class="description">Nombre de formateurs affichés par page dans la liste publique.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">Durée du cache (minutes)</th>
                <td>
                    <input type="number" name="cache_duration" value="<?php echo esc_attr($current_settings['cache_duration']); ?>" min="0" max="1440" class="small-text">
                    <p class="description">Durée de mise en cache des données (0 = pas de cache).</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">Méthode de contact</th>
                <td>
                    <select name="contact_method">
                        <option value="email" <?php selected($current_settings['contact_method'], 'email'); ?>>Email direct</option>
                        <option value="form" <?php selected($current_settings['contact_method'], 'form'); ?>>Formulaire de contact</option>
                        <option value="both" <?php selected($current_settings['contact_method'], 'both'); ?>>Les deux</option>
                    </select>
                    <p class="description">Comment les visiteurs peuvent contacter les formateurs.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">Email administrateur</th>
                <td>
                    <input type="email" name="admin_email" value="<?php echo esc_attr($current_settings['admin_email']); ?>" class="regular-text">
                    <p class="description">Email pour recevoir les notifications d'administration.</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">Logs en base de données</th>
                <td>
                    <fieldset>
                        <label>
                            <input type="radio" name="enable_logs" value="yes" <?php checked($current_settings['enable_logs'], 'yes'); ?>>
                            Activés
                        </label><br>
                        <label>
                            <input type="radio" name="enable_logs" value="no" <?php checked($current_settings['enable_logs'], 'no'); ?>>
                            Désactivés
                        </label>
                        <p class="description">Enregistrer les logs d'activité en base de données.</p>
                    </fieldset>
                </td>
            </tr>
        </table>
        
        <?php submit_button('Sauvegarder les paramètres'); ?>
    </form>
    
    <hr>
    
    <h2>🔧 Actions de maintenance</h2>
    <table class="form-table">
        <tr>
            <th scope="row">Vider le cache</th>
            <td>
                <button type="button" class="button" onclick="clearCache()">Vider maintenant</button>
                <p class="description">Supprime toutes les données mises en cache.</p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">Test email</th>
            <td>
                <input type="email" id="test-email" placeholder="email@exemple.com" class="regular-text">
                <button type="button" class="button" onclick="sendTestEmail()">Envoyer un test</button>
                <p class="description">Tester la configuration email.</p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">Exporter les données</th>
            <td>
                <button type="button" class="button" onclick="exportData()">Exporter CSV</button>
                <p class="description">Exporter tous les formateurs au format CSV.</p>
            </td>
        </tr>
    </table>
    
    <h2>📊 Informations système</h2>
    <table class="form-table">
        <tr>
            <th scope="row">Version du plugin</th>
            <td><?php echo FMP_VERSION; ?></td>
        </tr>
        <tr>
            <th scope="row">Version WordPress</th>
            <td><?php echo get_bloginfo('version'); ?></td>
        </tr>
        <tr>
            <th scope="row">Version PHP</th>
            <td><?php echo PHP_VERSION; ?></td>
        </tr>
        <tr>
            <th scope="row">Limite de mémoire</th>
            <td><?php echo ini_get('memory_limit'); ?></td>
        </tr>
        <tr>
            <th scope="row">Taille max upload</th>
            <td><?php echo ini_get('upload_max_filesize'); ?></td>
        </tr>
    </table>
</div>

<script>
function clearCache() {
    if (confirm('Êtes-vous sûr de vouloir vider le cache ?')) {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=fmp_clear_cache&nonce=<?php echo wp_create_nonce('fmp_admin_nonce'); ?>'
        })
        .then(response => response.json())
        .then(data => {
            alert(data.success ? 'Cache vidé avec succès' : 'Erreur lors du vidage du cache');
        });
    }
}

function sendTestEmail() {
    const email = document.getElementById('test-email').value;
    if (!email) {
        alert('Veuillez saisir une adresse email');
        return;
    }
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=fmp_send_test_email&email=${email}&nonce=<?php echo wp_create_nonce('fmp_admin_nonce'); ?>`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.success ? 'Email de test envoyé' : 'Erreur: ' + data.data);
    });
}

function exportData() {
    window.location.href = '<?php echo admin_url('admin-ajax.php?action=fmp_export_formateurs&nonce=' . wp_create_nonce('fmp_admin_nonce')); ?>';
}
</script>