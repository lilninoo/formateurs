<?php
/**
 * Template email d'approbation formateur
 * templates/emails/formateur-approved.php
 */

if (!defined('ABSPATH')) exit;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil approuvé - <?php echo esc_html($site_name); ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 30px 20px; text-align: center; position: relative; overflow: hidden; }
        .header::before { content: '🎉'; font-size: 100px; position: absolute; top: -20px; right: -30px; opacity: 0.3; }
        .content { background: #f9fafb; padding: 20px; }
        .card { background: white; padding: 25px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success-banner { background: #ecfdf5; border: 2px solid #10b981; padding: 20px; border-radius: 8px; text-align: center; }
        .button { display: inline-block; background: #10b981; color: white; padding: 15px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; }
        .button-secondary { background: #2563eb; }
        .feature-list { background: #f0f9ff; padding: 20px; border-radius: 6px; }
        .checklist { list-style: none; padding: 0; }
        .checklist li { padding: 8px 0; }
        .checklist li::before { content: '✅'; margin-right: 10px; }
        .footer { background: #374151; color: white; padding: 20px; text-align: center; font-size: 14px; }
        .celebration { animation: bounce 1s infinite; display: inline-block; }
        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span class="celebration">🎉</span> Félicitations !</h1>
            <h2>Votre profil a été approuvé</h2>
            <p>Vous êtes maintenant officiellement formateur sur <?php echo esc_html($site_name); ?></p>
        </div>
        
        <div class="content">
            <div class="success-banner">
                <h2>✅ Profil activé avec succès !</h2>
                <p><strong>Votre expertise est maintenant visible par des milliers de clients potentiels.</strong></p>
            </div>
            
            <div class="card">
                <h3>👋 Bonjour <?php echo esc_html($prenom); ?> !</h3>
                
                <p>Excellente nouvelle ! Après examen de votre candidature, nous avons le plaisir de vous informer que <strong>votre profil formateur est maintenant approuvé et actif</strong>.</p>
                
                <p>Cela signifie que :</p>
                <ul class="checklist">
                    <li>Votre profil est visible sur notre plateforme</li>
                    <li>Les clients peuvent vous contacter directement</li>
                    <li>Vous pouvez recevoir des demandes de formation</li>
                    <li>Votre expertise est mise en avant dans les résultats de recherche</li>
                </ul>
            </div>
            
            <div class="card">
                <h3>🚀 Vos prochaines étapes</h3>
                
                <div class="feature-list">
                    <h4>1. Consultez votre profil public</h4>
                    <p>Vérifiez que toutes vos informations sont correctes et attractives.</p>
                    
                    <p style="text-align: center; margin: 20px 0;">
                        <a href="<?php echo esc_url($profile_url ?? $site_url); ?>" class="button">
                            👁️ Voir mon profil public
                        </a>
                    </p>
                </div>
                
                <div class="feature-list" style="margin-top: 20px;">
                    <h4>2. Optimisez votre visibilité</h4>
                    <ul>
                        <li>Ajoutez des mots-clés pertinents à votre description</li>
                        <li>Mentionnez vos certifications et réalisations</li>
                        <li>Précisez vos méthodes pédagogiques</li>
                        <li>Indiquez vos disponibilités</li>
                    </ul>
                </div>
                
                <div class="feature-list" style="margin-top: 20px;">
                    <h4>3. Préparez-vous aux demandes</h4>
                    <ul>
                        <li>Définissez vos tarifs selon le type de formation</li>
                        <li>Préparez vos supports de présentation</li>
                        <li>Réfléchissez à vos créneaux de disponibilité</li>
                    </ul>
                </div>
            </div>
            
            <div class="card">
                <h3>💡 Conseils pour maximiser vos opportunités</h3>
                
                <ol>
                    <li><strong>Répondez rapidement</strong> - Les clients préfèrent les formateurs réactifs</li>
                    <li><strong>Personnalisez vos réponses</strong> - Montrez que vous avez lu leur demande</li>
                    <li><strong>Proposez un échange</strong> - Un appel permet de mieux cerner les besoins</li>
                    <li><strong>Mettez en avant votre expertise</strong> - Partagez des exemples concrets</li>
                    <li><strong>Soyez professionnel</strong> - Soignez vos communications</li>
                </ol>
            </div>
            
            <div class="card">
                <h3>📊 Suivez vos performances</h3>
                
                <p>Nous mettons à votre disposition des outils pour suivre :</p>
                <ul>
                    <li>📈 Le nombre de vues de votre profil</li>
                    <li>📞 Les demandes de contact reçues</li>
                    <li>⭐ Les avis et évaluations clients</li>
                    <li>💰 Vos revenus prévisionnels</li>
                </ul>
                
                <p style="text-align: center; margin: 20px 0;">
                    <a href="<?php echo esc_url($site_url . '/mon-compte/'); ?>" class="button button-secondary">
                        📊 Accéder à mon tableau de bord
                    </a>
                </p>
            </div>
            
            <div class="card" style="background: #fffbeb; border: 1px solid #fbbf24;">
                <h3>🎯 Objectif premier mois</h3>
                <p>Fixez-vous comme objectif de <strong>recevoir votre première demande dans les 7 jours</strong> et de <strong>décrocher votre première mission dans le mois</strong>.</p>
                <p>Notre équipe est là pour vous accompagner dans cette démarche !</p>
            </div>
            
            <div class="card">
                <h3>🆘 Besoin d'aide ?</h3>
                <p>Notre équipe support est disponible pour vous accompagner :</p>
                <ul>
                    <li>📧 <a href="mailto:<?php echo esc_attr($admin_email); ?>">Support par email</a></li>
                    <li>📚 <a href="<?php echo esc_url($site_url . '/guide-formateur/'); ?>">Guide du formateur</a></li>
                    <li>❓ <a href="<?php echo esc_url($site_url . '/faq/'); ?>">Questions fréquentes</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p>🚀 Bienvenue dans l'aventure <?php echo esc_html($site_name); ?> !</p>
            <p>Nous vous souhaitons un grand succès en tant que formateur.</p>
            <p>
                <a href="<?php echo esc_url($site_url); ?>" style="color: #93c5fd;"><?php echo esc_html($site_name); ?></a> |
                <a href="<?php echo esc_url($site_url . '/contact/'); ?>" style="color: #93c5fd;">Contact</a> |
                <a href="<?php echo esc_url($site_url . '/mon-compte/'); ?>" style="color: #93c5fd;">Mon compte</a>
            </p>
        </div>
    </div>
</body>
</html>