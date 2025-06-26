<?php
/**
 * Template email pour notification d'inscription admin
 * templates/emails/admin-registration.php
 */

if (!defined('ABSPATH')) exit;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle inscription formateur - <?php echo esc_html($site_name); ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1f2937; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 20px; }
        .card { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .urgent { border-left: 4px solid #f59e0b; background: #fffbeb; }
        .button { display: inline-block; background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; margin: 5px; }
        .button-success { background: #10b981; }
        .button-danger { background: #ef4444; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .info-item { background: #f3f4f6; padding: 10px; border-radius: 4px; }
        .footer { background: #374151; color: white; padding: 15px; text-align: center; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚨 Nouvelle inscription formateur</h1>
            <p><?php echo esc_html($site_name); ?> - Administration</p>
        </div>
        
        <div class="content">
            <div class="card urgent">
                <h2>⚠️ Action requise</h2>
                <p><strong>Un nouveau formateur s'est inscrit et attend votre validation.</strong></p>
                <p>Date d'inscription : <strong><?php echo date('d/m/Y à H:i'); ?></strong></p>
            </div>
            
            <div class="card">
                <h3>👤 Informations du formateur</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Nom complet :</strong><br>
                        <?php echo esc_html($prenom . ' ' . $nom); ?>
                    </div>
                    <div class="info-item">
                        <strong>Email :</strong><br>
                        <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                    </div>
                    <div class="info-item">
                        <strong>Téléphone :</strong><br>
                        <?php echo esc_html($telephone ?? 'Non renseigné'); ?>
                    </div>
                    <div class="info-item">
                        <strong>Ville :</strong><br>
                        <?php echo esc_html($ville ?? 'Non renseignée'); ?>
                    </div>
                    <div class="info-item">
                        <strong>Spécialité :</strong><br>
                        <?php echo esc_html($specialite ?? 'Non renseignée'); ?>
                    </div>
                    <div class="info-item">
                        <strong>Expérience :</strong><br>
                        <?php echo esc_html($experience ?? 'Non renseignée'); ?>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($competences) && is_array($competences)): ?>
            <div class="card">
                <h3>🎯 Compétences déclarées</h3>
                <p><?php echo esc_html(implode(', ', $competences)); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($biographie)): ?>
            <div class="card">
                <h3>📝 Présentation</h3>
                <p style="font-style: italic;"><?php echo esc_html(wp_trim_words($biographie, 50)); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($uploaded_files)): ?>
            <div class="card">
                <h3>📎 Documents joints</h3>
                <ul>
                    <?php if (isset($uploaded_files['cv'])): ?>
                    <li>📄 CV : <a href="<?php echo esc_url($uploaded_files['cv']['url']); ?>" target="_blank">Télécharger</a></li>
                    <?php endif; ?>
                    <?php if (isset($uploaded_files['photo'])): ?>
                    <li>📷 Photo : <a href="<?php echo esc_url($uploaded_files['photo']['url']); ?>" target="_blank">Voir</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <h3>🔧 Actions rapides</h3>
                <p style="text-align: center;">
                    <a href="<?php echo esc_url($edit_url); ?>" class="button">
                        ✏️ Modifier le profil
                    </a>
                    <a href="<?php echo esc_url($approve_url); ?>" class="button button-success">
                        ✅ Voir les demandes
                    </a>
                </p>
                
                <p style="text-align: center; margin-top: 20px;">
                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=formateur_pro')); ?>" class="button">
                        📋 Tous les formateurs
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=formateur-dashboard')); ?>" class="button">
                        📊 Tableau de bord
                    </a>
                </p>
            </div>
            
            <div class="card">
                <h3>📊 Statistiques actuelles</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Total formateurs :</strong><br>
                        <?php 
                        $total = wp_count_posts('formateur_pro');
                        echo ($total->publish ?? 0) + ($total->pending ?? 0);
                        ?>
                    </div>
                    <div class="info-item">
                        <strong>En attente :</strong><br>
                        <?php echo $total->pending ?? 0; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Notification automatique - <?php echo esc_html($site_name); ?></p>
            <p>Reçu le <?php echo date('d/m/Y à H:i:s'); ?></p>
            <p>
                <a href="<?php echo esc_url(admin_url()); ?>" style="color: #93c5fd;">Administration</a> |
                <a href="<?php echo esc_url(admin_url('admin.php?page=formateur-settings')); ?>" style="color: #93c5fd;">Paramètres</a>
            </p>
        </div>
    </div>
</body>
</html>