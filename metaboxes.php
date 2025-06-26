<?php
/**
 * Métaboxes pour l'administration - Formateur Manager Pro
 * admin/metaboxes.php
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajouter les métaboxes pour le post type formateur
 */
function fmp_add_formateur_metaboxes() {
    add_meta_box(
        'formateur_details',
        '👤 Informations personnelles',
        'fmp_formateur_details_metabox',
        'formateur_pro',
        'normal',
        'high'
    );
    
    add_meta_box(
        'formateur_expertise',
        '🎓 Expertise et compétences',
        'fmp_formateur_expertise_metabox',
        'formateur_pro',
        'normal',
        'high'
    );
    
    add_meta_box(
        'formateur_files',
        '📁 Documents',
        'fmp_formateur_files_metabox',
        'formateur_pro',
        'side',
        'default'
    );
    
    add_meta_box(
        'formateur_preferences',
        '⚙️ Préférences',
        'fmp_formateur_preferences_metabox',
        'formateur_pro',
        'side',
        'default'
    );
    
    add_meta_box(
        'formateur_status',
        '📊 Statut et actions',
        'fmp_formateur_status_metabox',
        'formateur_pro',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'fmp_add_formateur_metaboxes');

/**
 * Métabox des informations personnelles
 */
function fmp_formateur_details_metabox($post) {
    wp_nonce_field('fmp_save_formateur', 'fmp_formateur_nonce');
    
    $prenom = fmp_get_formateur_meta($post->ID, 'prenom');
    $nom = fmp_get_formateur_meta($post->ID, 'nom');
    $email = fmp_get_formateur_meta($post->ID, 'email');
    $telephone = fmp_get_formateur_meta($post->ID, 'telephone');
    $ville = fmp_get_formateur_meta($post->ID, 'ville');
    $pays = fmp_get_formateur_meta($post->ID, 'pays');
    $site_web = fmp_get_formateur_meta($post->ID, 'site_web');
    $linkedin = fmp_get_formateur_meta($post->ID, 'linkedin');
    ?>
    
    <table class="form-table">
        <tr>
            <th><label for="formateur_prenom">Prénom *</label></th>
            <td><input type="text" id="formateur_prenom" name="formateur_prenom" value="<?php echo esc_attr($prenom); ?>" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="formateur_nom">Nom *</label></th>
            <td><input type="text" id="formateur_nom" name="formateur_nom" value="<?php echo esc_attr($nom); ?>" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="formateur_email">Email *</label></th>
            <td><input type="email" id="formateur_email" name="formateur_email" value="<?php echo esc_attr($email); ?>" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="formateur_telephone">Téléphone *</label></th>
            <td><input type="tel" id="formateur_telephone" name="formateur_telephone" value="<?php echo esc_attr($telephone); ?>" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="formateur_ville">Ville *</label></th>
            <td><input type="text" id="formateur_ville" name="formateur_ville" value="<?php echo esc_attr($ville); ?>" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="formateur_pays">Pays</label></th>
            <td>
                <select id="formateur_pays" name="formateur_pays">
                    <option value="">Sélectionner</option>
                    <option value="FR" <?php selected($pays, 'FR'); ?>>France</option>
                    <option value="BE" <?php selected($pays, 'BE'); ?>>Belgique</option>
                    <option value="CH" <?php selected($pays, 'CH'); ?>>Suisse</option>
                    <option value="CA" <?php selected($pays, 'CA'); ?>>Canada</option>
                    <option value="autre" <?php selected($pays, 'autre'); ?>>Autre</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="formateur_site_web">Site web</label></th>
            <td><input type="url" id="formateur_site_web" name="formateur_site_web" value="<?php echo esc_attr($site_web); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="formateur_linkedin">LinkedIn</label></th>
            <td><input type="url" id="formateur_linkedin" name="formateur_linkedin" value="<?php echo esc_attr($linkedin); ?>" class="regular-text"></td>
        </tr>
    </table>
    <?php
}

/**
 * Métabox de l'expertise
 */
function fmp_formateur_expertise_metabox($post) {
    $experience = fmp_get_formateur_meta($post->ID, 'experience');
    $specialite = fmp_get_formateur_meta($post->ID, 'specialite');
    $competences = fmp_get_formateur_meta($post->ID, 'competences');
    $formations_donnees = fmp_get_formateur_meta($post->ID, 'formations_donnees');
    $diplomes = fmp_get_formateur_meta($post->ID, 'diplomes');
    $methodes_pedagogiques = fmp_get_formateur_meta($post->ID, 'methodes_pedagogiques');
    ?>
    
    <table class="form-table">
        <tr>
            <th><label for="formateur_experience">Expérience *</label></th>
            <td>
                <select id="formateur_experience" name="formateur_experience" required>
                    <option value="">Sélectionner</option>
                    <option value="debutant" <?php selected($experience, 'debutant'); ?>>Débutant (0-2 ans)</option>
                    <option value="junior" <?php selected($experience, 'junior'); ?>>Junior (2-5 ans)</option>
                    <option value="confirme" <?php selected($experience, 'confirme'); ?>>Confirmé (5-8 ans)</option>
                    <option value="senior" <?php selected($experience, 'senior'); ?>>Senior (8-15 ans)</option>
                    <option value="expert" <?php selected($experience, 'expert'); ?>>Expert (15+ ans)</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="formateur_specialite">Spécialité principale</label></th>
            <td><input type="text" id="formateur_specialite" name="formateur_specialite" value="<?php echo esc_attr($specialite); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="formateur_competences">Compétences</label></th>
            <td>
                <textarea id="formateur_competences" name="formateur_competences" rows="3" class="large-text"><?php echo esc_textarea(is_array($competences) ? implode(', ', $competences) : $competences); ?></textarea>
                <p class="description">Séparez les compétences par des virgules</p>
            </td>
        </tr>
        <tr>
            <th><label for="formateur_formations_donnees">Formations dispensées</label></th>
            <td><textarea id="formateur_formations_donnees" name="formateur_formations_donnees" rows="4" class="large-text"><?php echo esc_textarea($formations_donnees); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="formateur_diplomes">Diplômes et certifications</label></th>
            <td><textarea id="formateur_diplomes" name="formateur_diplomes" rows="3" class="large-text"><?php echo esc_textarea($diplomes); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="formateur_methodes_pedagogiques">Méthodes pédagogiques</label></th>
            <td><textarea id="formateur_methodes_pedagogiques" name="formateur_methodes_pedagogiques" rows="3" class="large-text"><?php echo esc_textarea($methodes_pedagogiques); ?></textarea></td>
        </tr>
    </table>
    <?php
}

/**
 * Métabox des documents
 */
function fmp_formateur_files_metabox($post) {
    $cv_data = fmp_get_formateur_meta($post->ID, 'cv');
    $photo_data = fmp_get_formateur_meta($post->ID, 'photo');
    $portfolio_data = fmp_get_formateur_meta($post->ID, 'portfolio');
    ?>
    
    <div class="fmp-files-container">
        <h4>📄 CV</h4>
        <?php if ($cv_data && !empty($cv_data['url'])): ?>
            <p>
                <a href="<?php echo esc_url($cv_data['url']); ?>" target="_blank" class="button">
                    📎 Télécharger CV
                </a>
                <br><small>Uploadé le <?php echo date('d/m/Y', strtotime($post->post_date)); ?></small>
            </p>
        <?php else: ?>
            <p><em>Aucun CV uploadé</em></p>
        <?php endif; ?>
        
        <h4>📷 Photo de profil</h4>
        <?php if (has_post_thumbnail($post->ID)): ?>
            <div style="margin-bottom: 10px;">
                <?php echo get_the_post_thumbnail($post->ID, 'thumbnail'); ?>
            </div>
            <p>
                <button type="button" class="button" onclick="wp.media.editor.open(); return false;">
                    Changer la photo
                </button>
            </p>
        <?php else: ?>
            <p>
                <button type="button" class="button" onclick="wp.media.editor.open(); return false;">
                    📎 Ajouter une photo
                </button>
            </p>
        <?php endif; ?>
        
        <h4>💼 Portfolio</h4>
        <?php if ($portfolio_data && !empty($portfolio_data['url'])): ?>
            <p>
                <a href="<?php echo esc_url($portfolio_data['url']); ?>" target="_blank" class="button">
                    📎 Voir portfolio
                </a>
            </p>
        <?php else: ?>
            <p><em>Aucun portfolio uploadé</em></p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Métabox des préférences
 */
function fmp_formateur_preferences_metabox($post) {
    $modalites = fmp_get_formateur_meta($post->ID, 'modalites');
    $publics = fmp_get_formateur_meta($post->ID, 'publics');
    $langues = fmp_get_formateur_meta($post->ID, 'langues');
    $tarif_jour = fmp_get_formateur_meta($post->ID, 'tarif_jour');
    $newsletter_consent = fmp_get_formateur_meta($post->ID, 'newsletter_consent');
    
    if (!is_array($modalites)) $modalites = [];
    if (!is_array($publics)) $publics = [];
    if (!is_array($langues)) $langues = [];
    ?>
    
    <table class="form-table">
        <tr>
            <th>Modalités</th>
            <td>
                <label><input type="checkbox" name="formateur_modalites[]" value="presentiel" <?php checked(in_array('presentiel', $modalites)); ?>> Présentiel</label><br>
                <label><input type="checkbox" name="formateur_modalites[]" value="distanciel" <?php checked(in_array('distanciel', $modalites)); ?>> Distanciel</label><br>
                <label><input type="checkbox" name="formateur_modalites[]" value="hybride" <?php checked(in_array('hybride', $modalites)); ?>> Hybride</label>
            </td>
        </tr>
        <tr>
            <th>Public cible</th>
            <td>
                <label><input type="checkbox" name="formateur_publics[]" value="entreprises" <?php checked(in_array('entreprises', $publics)); ?>> Entreprises</label><br>
                <label><input type="checkbox" name="formateur_publics[]" value="particuliers" <?php checked(in_array('particuliers', $publics)); ?>> Particuliers</label><br>
                <label><input type="checkbox" name="formateur_publics[]" value="etudiants" <?php checked(in_array('etudiants', $publics)); ?>> Étudiants</label><br>
                <label><input type="checkbox" name="formateur_publics[]" value="demandeurs-emploi" <?php checked(in_array('demandeurs-emploi', $publics)); ?>> Demandeurs d'emploi</label>
            </td>
        </tr>
        <tr>
            <th><label for="formateur_tarif_jour">Tarif/jour</label></th>
            <td>
                <select id="formateur_tarif_jour" name="formateur_tarif_jour">
                    <option value="">Non renseigné</option>
                    <option value="200-400" <?php selected($tarif_jour, '200-400'); ?>>200€ - 400€</option>
                    <option value="400-600" <?php selected($tarif_jour, '400-600'); ?>>400€ - 600€</option>
                    <option value="600-800" <?php selected($tarif_jour, '600-800'); ?>>600€ - 800€</option>
                    <option value="800-1000" <?php selected($tarif_jour, '800-1000'); ?>>800€ - 1000€</option>
                    <option value="1000-1500" <?php selected($tarif_jour, '1000-1500'); ?>>1000€ - 1500€</option>
                    <option value="1500+" <?php selected($tarif_jour, '1500+'); ?>>1500€+</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Newsletter</th>
            <td>
                <label>
                    <input type="checkbox" name="formateur_newsletter_consent" value="1" <?php checked($newsletter_consent, '1'); ?>>
                    Accepte de recevoir la newsletter
                </label>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Métabox du statut
 */
function fmp_formateur_status_metabox($post) {
    $status = fmp_get_formateur_meta($post->ID, 'status');
    $date_inscription = fmp_get_formateur_meta($post->ID, 'date_inscription');
    $views = fmp_get_formateur_meta($post->ID, 'views');
    $contacts = fmp_get_formateur_meta($post->ID, 'contacts');
    ?>
    
    <div class="fmp-status-container">
        <h4>📊 Statut actuel</h4>
        <p>
            <select name="formateur_status" style="width: 100%;">
                <option value="pending" <?php selected($status, 'pending'); ?>>⏳ En attente</option>
                <option value="active" <?php selected($status, 'active'); ?>>✅ Actif</option>
                <option value="inactive" <?php selected($status, 'inactive'); ?>>❌ Inactif</option>
                <option value="rejected" <?php selected($status, 'rejected'); ?>>🚫 Rejeté</option>
            </select>
        </p>
        
        <?php if ($date_inscription): ?>
        <h4>📅 Inscription</h4>
        <p><?php echo date('d/m/Y à H:i', strtotime($date_inscription)); ?></p>
        <?php endif; ?>
        
        <h4>📈 Statistiques</h4>
        <p>
            👁️ Vues : <strong><?php echo intval($views); ?></strong><br>
            📞 Contacts : <strong><?php echo intval($contacts); ?></strong>
        </p>
        
        <h4>🔧 Actions rapides</h4>
        <p>
            <button type="button" class="button button-small" onclick="sendEmailToFormateur(<?php echo $post->ID; ?>)">
                📧 Envoyer email
            </button>
        </p>
        <p>
            <button type="button" class="button button-small" onclick="resetStatsFormateur(<?php echo $post->ID; ?>)">
                🔄 Reset stats
            </button>
        </p>
        
        <?php if ($status === 'pending'): ?>
        <p>
            <button type="button" class="button button-primary" onclick="approveFormateur(<?php echo $post->ID; ?>)">
                ✅ Approuver
            </button>
        </p>
        <?php endif; ?>
    </div>
    
    <script>
    function sendEmailToFormateur(formateurId) {
        if (confirm('Envoyer un email de rappel à ce formateur ?')) {
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=fmp_resend_registration_email&formateur_id=${formateurId}&nonce=<?php echo wp_create_nonce('fmp_admin_nonce'); ?>`
            })
            .then(response => response.json())
            .then(data => alert(data.success ? 'Email envoyé' : 'Erreur: ' + data.data));
        }
    }
    
    function approveFormateur(formateurId) {
        if (confirm('Approuver ce formateur ?')) {
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=fmp_approve_formateur&formateur_id=${formateurId}&nonce=<?php echo wp_create_nonce('fmp_admin_nonce'); ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('select[name="formateur_status"]').value = 'active';
                    alert('Formateur approuvé');
                } else {
                    alert('Erreur: ' + data.data);
                }
            });
        }
    }
    
    function resetStatsFormateur(formateurId) {
        if (confirm('Remettre à zéro les statistiques de ce formateur ?')) {
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=fmp_reset_formateur_stats&formateur_id=${formateurId}&nonce=<?php echo wp_create_nonce('fmp_admin_nonce'); ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur: ' + data.data);
                }
            });
        }
    }
    </script>
    <?php
}

/**
 * Sauvegarder les données des métaboxes
 */
function fmp_save_formateur_metaboxes($post_id) {
    // Vérifications de sécurité
    if (!isset($_POST['fmp_formateur_nonce']) || !wp_verify_nonce($_POST['fmp_formateur_nonce'], 'fmp_save_formateur')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Sauvegarder les données
    $fields = [
        'prenom', 'nom', 'email', 'telephone', 'ville', 'pays', 'site_web', 'linkedin',
        'experience', 'specialite', 'competences', 'formations_donnees', 'diplomes', 'methodes_pedagogiques',
        'tarif_jour', 'status'
    ];
    
    foreach ($fields as $field) {
        if (isset($_POST['formateur_' . $field])) {
            $value = $_POST['formateur_' . $field];
            
            if ($field === 'competences' && is_string($value)) {
                $value = array_map('trim', explode(',', $value));
            }
            
            fmp_update_formateur_meta($post_id, $field, sanitize_text_field($value));
        }
    }
    
    // Sauvegarder les tableaux
    $array_fields = ['modalites', 'publics', 'langues'];
    foreach ($array_fields as $field) {
        $value = isset($_POST['formateur_' . $field]) ? $_POST['formateur_' . $field] : [];
        fmp_update_formateur_meta($post_id, $field, array_map('sanitize_text_field', $value));
    }
    
    // Newsletter
    $newsletter = isset($_POST['formateur_newsletter_consent']) ? '1' : '0';
    fmp_update_formateur_meta($post_id, 'newsletter_consent', $newsletter);
    
    // Vider le cache
    fmp_clear_formateur_cache($post_id);
    
    // Log de la modification
    fmp_log("Formateur {$post_id} modifié", 'info', [
        'user_id' => get_current_user_id(),
        'fields_updated' => array_keys($_POST)
    ]);
}
add_action('save_post_formateur_pro', 'fmp_save_formateur_metaboxes');

/**
 * Actions AJAX supplémentaires pour les métaboxes
 */

// Reset des statistiques d'un formateur
add_action('wp_ajax_fmp_reset_formateur_stats', function() {
    if (!current_user_can('edit_posts') || !wp_verify_nonce($_POST['nonce'] ?? '', 'fmp_admin_nonce')) {
        wp_send_json_error('Permission refusée');
    }
    
    $formateur_id = intval($_POST['formateur_id'] ?? 0);
    if (!$formateur_id) {
        wp_send_json_error('ID formateur invalide');
    }
    
    fmp_update_formateur_meta($formateur_id, 'views', 0);
    fmp_update_formateur_meta($formateur_id, 'contacts', 0);
    
    wp_send_json_success('Statistiques remises à zéro');
});

// Vider le cache via AJAX
add_action('wp_ajax_fmp_clear_cache', function() {
    if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['nonce'] ?? '', 'fmp_admin_nonce')) {
        wp_send_json_error('Permission refusée');
    }
    
    fmp_clear_formateur_cache();
    wp_send_json_success('Cache vidé');
});

// Export des formateurs
add_action('wp_ajax_fmp_export_formateurs', function() {
    if (!current_user_can('export') || !wp_verify_nonce($_GET['nonce'] ?? '', 'fmp_admin_nonce')) {
        wp_die('Permission refusée');
    }
    
    $csv_data = fmp_export_formateurs_csv();
    
    if (!$csv_data) {
        wp_die('Aucune donnée à exporter');
    }
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="formateurs-' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    foreach ($csv_data as $row) {
        fputcsv($output, $row, ';');
    }
    
    fclose($output);
    exit;
});