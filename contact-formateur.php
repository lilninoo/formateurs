<?php
/**
 * Template email de contact formateur
 * templates/emails/contact-formateur.php
 */

if (!defined('ABSPATH')) exit;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle demande de formation - <?php echo esc_html($site_name); ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 25px 20px; text-align: center; }
        .content { background: #f9fafb; padding: 20px; }
        .card { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .highlight { background: #ecfdf5; border-left: 4px solid #10b981; padding: 15px; }
        .client-info { background: #f0f9ff; border: 1px solid #bfdbfe; padding: 15px; border-radius: 6px; }
        .message-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 6px; font-style: italic; }
        .button { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .footer { background: #374151; color: white; padding: 20px; text-align: center; font-size: 14px; }
        .urgent { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.7; } 100% { opacity: 1; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Nouvelle demande de formation</h1>
            <p class="urgent">🔔 Vous avez reçu une nouvelle demande !</p>
        </div>
        
        <div class="content">
            <div class="card">
                <div class="highlight">
                    <h2>👋 Bonjour <?php echo esc_html($formateur_name); ?> !</h2>
                    <p><strong>Un client potentiel souhaite entrer en contact avec vous pour une formation.</strong></p>
                    <p>📅 Demande reçue le <strong><?php echo date('d/m/Y à H:i'); ?></strong></p>
                </div>
            </div>
            
            <div class="card">
                <h3>👤 Informations du client</h3>
                <div class="client-info">
                    <p><strong>📝 Nom :</strong> <?php echo esc_html($client_name); ?></p>
                    <p><strong>📧 Email :</strong> <a href="mailto:<?php echo esc_attr($client_email); ?>"><?php echo esc_html($client_email); ?></a></p>
                    <p><strong>📅 Date de la demande :</strong> <?php echo date('d/m/Y à H:i'); ?></p>
                </div>
            </div>
            
            <div class="card">
                <h3>💬 Message du client</h3>
                <div class="message-box">
                    <?php echo nl2br(esc_html($message)); ?>
                </div>
            </div>
            
            <div class="card">
                <h3>🚀 Comment répondre ?</h3>
                <p>Pour donner suite à cette demande, vous pouvez :</p>
                <ol>
                    <li><strong>Répondre directement par email</strong> en cliquant sur le bouton ci-dessous</li>
                    <li>Prendre contact par téléphone si le client a fourni son numéro</li>
                    <li>Proposer un rendez-vous pour discuter des besoins en détail</li>
                </ol>
                
                <p style="text-align: center; margin: 25px 0;">
                    <a href="mailto:<?php echo esc_attr($client_email); ?>?subject=<?php echo esc_attr('Re: Demande de formation - ' . $site_name); ?>&body=<?php echo esc_attr("Bonjour " . $client_name . ",\n\nMerci pour votre intérêt concernant mes formations.\n\nJe vous recontacte rapidement pour discuter de vos besoins.\n\nCordialement,\n" . $formateur_name); ?>" class="button">
                        📧 Répondre au client
                    </a>
                </p>
            </div>
            
            <div class="card">
                <h3>💡 Conseils pour une réponse efficace</h3>
                <ul>
                    <li>✅ Répondez dans les <strong>24 heures</strong> pour maximiser vos chances</li>
                    <li>✅ Personnalisez votre réponse en vous référant au message du client</li>
                    <li>✅ Proposez un appel ou un rendez-vous pour mieux comprendre les besoins</li>
                    <li>✅ Présentez brièvement votre expertise et vos méthodes</li>
                    <li>✅ Indiquez vos disponibilités et votre processus de formation</li>
                </ul>
            </div>
            
            <div class="card" style="background: #fffbeb; border: 1px solid #fbbf24;">
                <h3>⏰ Temps de réponse important</h3>
                <p>Les clients qui reçoivent une réponse rapide sont <strong>3 fois plus susceptibles</strong> de choisir votre formation. Ne tardez pas !</p>
            </div>
            
            <div class="card">
                <h3>📊 Vos statistiques</h3>
                <p>Cette demande augmente votre visibilité. Consultez vos statistiques complètes dans votre espace formateur.</p>
                <p style="text-align: center;">
                    <a href="<?php echo esc_url($site_url . '/mon-compte/'); ?>" style="color: #2563eb; text-decoration: none;">
                        📈 Voir mes statistiques
                    </a>
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>💼 Bonne formation avec <?php echo esc_html($site_name); ?> !</p>
            <p>Cet email a été envoyé à <?php echo esc_html($formateur_email); ?></p>
            <p>
                <a href="<?php echo esc_url($site_url); ?>" style="color: #93c5fd;"><?php echo esc_html($site_name); ?></a> |
                <a href="<?php echo esc_url($site_url . '/contact/'); ?>" style="color: #93c5fd;">Support</a>
            </p>
        </div>
    </div>
</body>
</html>