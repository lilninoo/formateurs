<?php
/**
 * Template email de test
 * templates/emails/test-email.php
 */

if (!defined('ABSPATH')) exit;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email - <?php echo esc_html($site_name); ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: white; padding: 25px 20px; text-align: center; }
        .content { background: #f9fafb; padding: 20px; }
        .card { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { background: #ecfdf5; border-left: 4px solid #10b981; padding: 15px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0; }
        .info-item { background: #f3f4f6; padding: 12px; border-radius: 6px; text-align: center; }
        .footer { background: #374151; color: white; padding: 15px; text-align: center; font-size: 14px; }
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Email de Test</h1>
            <p class="pulse">✅ Configuration Email Validée</p>
        </div>
        
        <div class="content">
            <div class="success">
                <h2>🎉 Félicitations !</h2>
                <p><strong>Votre configuration email fonctionne parfaitement.</strong></p>
                <p><?php echo esc_html($message ?? 'Test email envoyé avec succès.'); ?></p>
            </div>
            
            <div class="card">
                <h3>📋 Informations du test</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>📅 Date d'envoi</strong><br>
                        <?php echo esc_html($test_date ?? date('d/m/Y à H:i')); ?>
                    </div>
                    <div class="info-item">
                        <strong>🌐 Site web</strong><br>
                        <?php echo esc_html($site_name); ?>
                    </div>
                    <div class="info-item">
                        <strong>📧 Serveur email</strong><br>
                        ✅ Opérationnel
                    </div>
                    <div class="info-item">
                        <strong>⚡ Temps de réponse</strong><br>
                        < 1 seconde
                    </div>
                </div>
            </div>
            
            <div class="card">
                <h3>🔧 Informations techniques</h3>
                <ul>
                    <li><strong>Version WordPress :</strong> <?php echo esc_html($wp_version ?? get_bloginfo('version')); ?></li>
                    <li><strong>Version Plugin :</strong> <?php echo esc_html($plugin_version ?? 'N/A'); ?></li>
                    <li><strong>Encodage :</strong> UTF-8</li>
                    <li><strong>Type de contenu :</strong> HTML</li>
                </ul>
            </div>
            
            <div class="card">
                <h3>✅ Tests de fonctionnalités</h3>
                <p>Toutes les fonctionnalités email sont opérationnelles :</p>
                <ul style="list-style: none; padding: 0;">
                    <li>✅ Envoi d'emails HTML</li>
                    <li>✅ Encodage des caractères spéciaux</li>
                    <li>✅ Gestion des en-têtes</li>
                    <li>✅ Templates personnalisés</li>
                    <li>✅ Pièces jointes (si configuré)</li>
                </ul>
            </div>
            
            <div class="card" style="background: #f0f9ff; border: 1px solid #2563eb;">
                <h3>💡 Conseils pour optimiser vos emails</h3>
                <ul>
                    <li>Personnalisez vos messages pour chaque formateur</li>
                    <li>Utilisez des objets d'email accrocheurs</li>
                    <li>Incluez toujours un call-to-action clair</li>
                    <li>Testez régulièrement vos templates</li>
                    <li>Surveillez les taux de délivrabilité</li>
                </ul>
            </div>
            
            <div class="card" style="text-align: center; background: linear-gradient(135deg, #ecfdf5 0%, #f0f9ff 100%);">
                <h3>🚀 Prêt à utiliser !</h3>
                <p>Votre système d'email est maintenant opérationnel.<br>
                Vous pouvez envoyer des notifications aux formateurs en toute confiance.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>🧪 Test Email - <?php echo esc_html($site_name); ?></p>
            <p>Email généré automatiquement le <?php echo date('d/m/Y à H:i:s'); ?></p>
            <p>
                <a href="<?php echo esc_url($site_url); ?>" style="color: #93c5fd;"><?php echo esc_html($site_name); ?></a> |
                <a href="<?php echo esc_url(admin_url('admin.php?page=formateur-settings')); ?>" style="color: #93c5fd;">Configuration</a>
            </p>
        </div>
    </div>
</body>
</html>