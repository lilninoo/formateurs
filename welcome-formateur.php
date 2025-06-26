<?php
/**
 * Template email de bienvenue formateur
 * templates/emails/welcome-formateur.php
 */

if (!defined('ABSPATH')) exit;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue chez <?php echo esc_html($site_name); ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); color: white; padding: 30px 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px 20px; }
        .card { background: white; padding: 25px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .button { display: inline-block; background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .footer { background: #374151; color: white; padding: 20px; text-align: center; font-size: 14px; }
        .icon { font-size: 24px; margin-right: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Bienvenue chez <?php echo esc_html($site_name); ?> !</h1>
            <p>Félicitations, votre inscription est confirmée</p>
        </div>
        
        <div class="content">
            <div class="card">
                <h2>Bonjour <?php echo esc_html($prenom); ?> !</h2>
                
                <p>Nous sommes ravis de vous accueillir parmi notre réseau de formateurs professionnels.</p>
                
                <p><strong>Votre profil a été approuvé</strong> et est maintenant visible sur notre plateforme. Les entreprises et particuliers peuvent désormais vous contacter pour des missions de formation.</p>
                
                <h3>📋 Récapitulatif de votre profil :</h3>
                <ul>
                    <li><strong>Nom :</strong> <?php echo esc_html($prenom . ' ' . ($nom ?? '')); ?></li>
                    <li><strong>Email :</strong> <?php echo esc_html($email); ?></li>
                    <li><strong>Ville :</strong> <?php echo esc_html($ville ?? 'Non renseignée'); ?></li>
                    <li><strong>Spécialité :</strong> <?php echo esc_html($specialite ?? 'Non renseignée'); ?></li>
                </ul>
                
                <h3>🚀 Prochaines étapes :</h3>
                <ol>
                    <li>Complétez votre profil si nécessaire</li>
                    <li>Vérifiez vos informations de contact</li>
                    <li>Préparez-vous à recevoir vos premières demandes</li>
                </ol>
                
                <p style="text-align: center; margin: 30px 0;">
                    <a href="<?php echo esc_url($site_url . '/formateurs/'); ?>" class="button">
                        Voir mon profil public
                    </a>
                </p>
            </div>
            
            <div class="card">
                <h3>💡 Conseils pour optimiser votre visibilité :</h3>
                <ul>
                    <li>Ajoutez une photo de profil professionnelle</li>
                    <li>Rédigez une biographie détaillée et engageante</li>
                    <li>Listez toutes vos compétences techniques</li>
                    <li>Décrivez vos expériences de formation précédentes</li>
                    <li>Répondez rapidement aux demandes de contact</li>
                </ul>
            </div>
            
            <div class="card">
                <h3>📞 Besoin d'aide ?</h3>
                <p>Notre équipe est là pour vous accompagner :</p>
                <ul>
                    <li>📧 Email : <a href="mailto:<?php echo esc_attr($admin_email); ?>"><?php echo esc_html($admin_email); ?></a></li>
                    <li>🌐 Centre d'aide : <a href="<?php echo esc_url($site_url . '/aide/'); ?>">Consulter la FAQ</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p>Merci de faire partie de la communauté <?php echo esc_html($site_name); ?></p>
            <p>Cet email a été envoyé à <?php echo esc_html($email); ?></p>
            <p>
                <a href="<?php echo esc_url($site_url); ?>" style="color: #93c5fd;"><?php echo esc_html($site_name); ?></a> |
                <a href="<?php echo esc_url($site_url . '/contact/'); ?>" style="color: #93c5fd;">Contact</a>
            </p>
        </div>
    </div>
</body>
</html>