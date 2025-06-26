<?php
/**
 * Template de la page d'accueil moderne
 * templates/home-page.php
 */

if (!defined('ABSPATH')) exit;

// Récupérer les statistiques
$stats = fmp_get_global_stats();
?>

<div class="formateur-container formateur-container--wide formateur-modern-home">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero__badge">
                    <i class="fas fa-star"></i>
                    <span><?php _e('Plateforme #1', 'formateur-manager-pro'); ?></span>
                </div>
                
                <h1 class="hero__title"><?php echo esc_html($atts['title']); ?></h1>
                <p class="hero__subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                
                <div class="hero__actions">
                    <a href="<?php echo esc_url($atts['search_url']); ?>" class="btn btn--primary btn--xl">
                        <i class="fas fa-search"></i>
                        <span><?php _e('Trouver un formateur', 'formateur-manager-pro'); ?></span>
                    </a>
                    <a href="<?php echo esc_url($atts['register_url']); ?>" class="btn btn--secondary btn--xl">
                        <i class="fas fa-user-plus"></i>
                        <span><?php _e('Devenir formateur', 'formateur-manager-pro'); ?></span>
                    </a>
                </div>
                
                <div class="hero__stats">
                    <div class="stat">
                        <div class="stat__number"><?php echo number_format($stats['total_formateurs']); ?>+</div>
                        <div class="stat__label"><?php _e('Formateurs', 'formateur-manager-pro'); ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat__number"><?php echo number_format($stats['total_specialites']); ?>+</div>
                        <div class="stat__label"><?php _e('Spécialités', 'formateur-manager-pro'); ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat__number">98%</div>
                        <div class="stat__label"><?php _e('Satisfaction', 'formateur-manager-pro'); ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat__number">24h</div>
                        <div class="stat__label"><?php _e('Réponse moyenne', 'formateur-manager-pro'); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="hero-visual">
                <div class="formateur-showcase">
                    <?php if (!empty($atts['formateur_image'])): ?>
                        <img src="<?php echo esc_url($atts['formateur_image']); ?>" 
                             alt="<?php esc_attr_e('Formateur expert', 'formateur-manager-pro'); ?>" 
                             class="formateur-photo">
                    <?php else: ?>
                        <div class="formateur-placeholder">
                            <i class="fas fa-user-graduate"></i>
                            <span><?php _e('Votre expertise ici', 'formateur-manager-pro'); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="showcase-elements">
                        <div class="element element-1" data-tooltip="<?php esc_attr_e('Développement', 'formateur-manager-pro'); ?>">
                            <i class="fas fa-code"></i>
                            <span><?php _e('Développement', 'formateur-manager-pro'); ?></span>
                        </div>
                        <div class="element element-2" data-tooltip="<?php esc_attr_e('Marketing Digital', 'formateur-manager-pro'); ?>">
                            <i class="fas fa-bullhorn"></i>
                            <span><?php _e('Marketing', 'formateur-manager-pro'); ?></span>
                        </div>
                        <div class="element element-3" data-tooltip="<?php esc_attr_e('Design Créatif', 'formateur-manager-pro'); ?>">
                            <i class="fas fa-palette"></i>
                            <span><?php _e('Design', 'formateur-manager-pro'); ?></span>
                        </div>
                        <div class="element element-4" data-tooltip="<?php esc_attr_e('Management', 'formateur-manager-pro'); ?>">
                            <i class="fas fa-users"></i>
                            <span><?php _e('Management', 'formateur-manager-pro'); ?></span>
                        </div>
                        <div class="element element-5" data-tooltip="<?php esc_attr_e('Data Science', 'formateur-manager-pro'); ?>">
                            <i class="fas fa-chart-line"></i>
                            <span><?php _e('Data', 'formateur-manager-pro'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features-section">
        <div class="features-container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('Pourquoi choisir notre plateforme ?', 'formateur-manager-pro'); ?></h2>
                <p class="section-subtitle">
                    <?php _e('Une approche moderne pour connecter formateurs et apprenants', 'formateur-manager-pro'); ?>
                </p>
            </div>
            
            <div class="features-grid">
                <div class="feature animate-fadeInUp">
                    <div class="feature__icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature__title"><?php _e('Formateurs vérifiés', 'formateur-manager-pro'); ?></h3>
                    <p class="feature__description">
                        <?php _e('Expertise validée, références vérifiées et pédagogie confirmée par notre équipe.', 'formateur-manager-pro'); ?>
                    </p>
                </div>
                
                <div class="feature animate-fadeInUp">
                    <div class="feature__icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="feature__title"><?php _e('Contact immédiat', 'formateur-manager-pro'); ?></h3>
                    <p class="feature__description">
                        <?php _e('Échange direct avec le formateur sans intermédiaire, réponse garantie sous 24h.', 'formateur-manager-pro'); ?>
                    </p>
                </div>
                
                <div class="feature animate-fadeInUp">
                    <div class="feature__icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="feature__title"><?php _e('Qualité garantie', 'formateur-manager-pro'); ?></h3>
                    <p class="feature__description">
                        <?php _e('Avis clients authentiques et système d\'évaluation transparent pour votre tranquillité.', 'formateur-manager-pro'); ?>
                    </p>
                </div>
                
                <div class="feature animate-fadeInUp">
                    <div class="feature__icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <h3 class="feature__title"><?php _e('Sur-mesure', 'formateur-manager-pro'); ?></h3>
                    <p class="feature__description">
                        <?php _e('Formations adaptées à vos besoins spécifiques, en présentiel ou à distance.', 'formateur-manager-pro'); ?>
                    </p>
                </div>
                
                <div class="feature animate-fadeInUp">
                    <div class="feature__icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="feature__title"><?php _e('Flexibilité totale', 'formateur-manager-pro'); ?></h3>
                    <p class="feature__description">
                        <?php _e('Planification selon vos disponibilités avec des formateurs réactifs et professionnels.', 'formateur-manager-pro'); ?>
                    </p>
                </div>
                
                <div class="feature animate-fadeInUp">
                    <div class="feature__icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3 class="feature__title"><?php _e('100% sécurisé', 'formateur-manager-pro'); ?></h3>
                    <p class="feature__description">
                        <?php _e('Données protégées, paiements sécurisés et respect total de la confidentialité.', 'formateur-manager-pro'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Specialties Section -->
    <section class="specialties-section">
        <div class="specialties-container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('Nos domaines d\'expertise', 'formateur-manager-pro'); ?></h2>
                <p class="section-subtitle">
                    <?php _e('Des formateurs experts dans tous les secteurs', 'formateur-manager-pro'); ?>
                </p>
            </div>
            
            <div class="specialties-grid">
                <div class="specialty-card">
                    <div class="specialty-card__icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3><?php _e('Informatique & Tech', 'formateur-manager-pro'); ?></h3>
                    <p><?php _e('Développement, IA, cybersécurité, cloud...', 'formateur-manager-pro'); ?></p>
                    <div class="specialty-card__count"><?php echo rand(15, 45); ?> formateurs</div>
                </div>
                
                <div class="specialty-card">
                    <div class="specialty-card__icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3><?php _e('Marketing & Digital', 'formateur-manager-pro'); ?></h3>
                    <p><?php _e('SEO, réseaux sociaux, analytics, growth...', 'formateur-manager-pro'); ?></p>
                    <div class="specialty-card__count"><?php echo rand(10, 30); ?> formateurs</div>
                </div>
                
                <div class="specialty-card">
                    <div class="specialty-card__icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h3><?php _e('Design & Créativité', 'formateur-manager-pro'); ?></h3>
                    <p><?php _e('UI/UX, graphisme, motion design...', 'formateur-manager-pro'); ?></p>
                    <div class="specialty-card__count"><?php echo rand(8, 25); ?> formateurs</div>
                </div>
                
                <div class="specialty-card">
                    <div class="specialty-card__icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3><?php _e('Management & RH', 'formateur-manager-pro'); ?></h3>
                    <p><?php _e('Leadership, gestion d\'équipe, recrutement...', 'formateur-manager-pro'); ?></p>
                    <div class="specialty-card__count"><?php echo rand(12, 35); ?> formateurs</div>
                </div>
                
                <div class="specialty-card">
                    <div class="specialty-card__icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3><?php _e('Langues & Communication', 'formateur-manager-pro'); ?></h3>
                    <p><?php _e('Anglais, présentation, négociation...', 'formateur-manager-pro'); ?></p>
                    <div class="specialty-card__count"><?php echo rand(5, 20); ?> formateurs</div>
                </div>
                
                <div class="specialty-card">
                    <div class="specialty-card__icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3><?php _e('Finance & Comptabilité', 'formateur-manager-pro'); ?></h3>
                    <p><?php _e('Gestion financière, comptabilité, audit...', 'formateur-manager-pro'); ?></p>
                    <div class="specialty-card__count"><?php echo rand(7, 18); ?> formateurs</div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="testimonials-container">
            <div class="section-header">
                <h2 class="section-title"><?php _e('Ils nous font confiance', 'formateur-manager-pro'); ?></h2>
                <p class="section-subtitle">
                    <?php _e('Découvrez les retours de nos clients satisfaits', 'formateur-manager-pro'); ?>
                </p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial">
                    <div class="testimonial__rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <blockquote class="testimonial__quote">
                        "<?php _e('Formation exceptionnelle en développement React. Le formateur était à l\'écoute et très pédagogue.', 'formateur-manager-pro'); ?>"
                    </blockquote>
                    <div class="testimonial__author">
                        <strong>Marie D.</strong>
                        <span><?php _e('CTO, TechCorp', 'formateur-manager-pro'); ?></span>
                    </div>
                </div>
                
                <div class="testimonial">
                    <div class="testimonial__rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <blockquote class="testimonial__quote">
                        "<?php _e('Grâce à cette plateforme, j\'ai trouvé le formateur SEO parfait pour mon équipe. Résultats immédiats !', 'formateur-manager-pro'); ?>"
                    </blockquote>
                    <div class="testimonial__author">
                        <strong>Pierre L.</strong>
                        <span><?php _e('Directeur Marketing', 'formateur-manager-pro'); ?></span>
                    </div>
                </div>
                
                <div class="testimonial">
                    <div class="testimonial__rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <blockquote class="testimonial__quote">
                        "<?php _e('Interface intuitive et formateurs de qualité. La formation management m\'a beaucoup apporté.', 'formateur-manager-pro'); ?>"
                    </blockquote>
                    <div class="testimonial__author">
                        <strong>Sophie R.</strong>
                        <span><?php _e('Manager, StartupXYZ', 'formateur-manager-pro'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-container">
            <div class="cta-content">
                <h2 class="cta__title"><?php _e('Prêt à démarrer votre formation ?', 'formateur-manager-pro'); ?></h2>
                <p class="cta__subtitle">
                    <?php _e('Rejoignez des milliers de professionnels qui font confiance à notre plateforme pour se former', 'formateur-manager-pro'); ?>
                </p>
                <div class="cta-buttons">
                    <a href="<?php echo esc_url($atts['search_url']); ?>" class="btn btn--primary btn--xl">
                        <i class="fas fa-rocket"></i>
                        <span><?php _e('Trouver un formateur', 'formateur-manager-pro'); ?></span>
                    </a>
                    <a href="<?php echo esc_url($atts['register_url']); ?>" class="btn btn--secondary btn--xl">
                        <i class="fas fa-handshake"></i>
                        <span><?php _e('Devenir formateur', 'formateur-manager-pro'); ?></span>
                    </a>
                </div>
                
                <div class="cta-guarantee">
                    <i class="fas fa-shield-check"></i>
                    <span><?php _e('Satisfaction garantie ou remboursé', 'formateur-manager-pro'); ?></span>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Styles spécifiques à la page d'accueil */
.hero-section {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-purple) 100%);
    color: var(--white);
    padding: var(--space-4xl) 0;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>') repeat;
    opacity: 0.3;
}

.hero-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-4xl);
    align-items: center;
    position: relative;
    z-index: 1;
}

.hero-content {
    padding: var(--space-2xl) 0;
}

.hero-visual {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
}

.formateur-showcase {
    position: relative;
    width: 300px;
    height: 300px;
}

.formateur-photo,
.formateur-placeholder {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border: 4px solid rgba(255, 255, 255, 0.2);
    box-shadow: var(--shadow-2xl);
}

.formateur-placeholder {
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: rgba(255, 255, 255, 0.7);
}

.formateur-placeholder span {
    font-size: 0.875rem;
    margin-top: var(--space-sm);
}

.showcase-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.element {
    position: absolute;
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--white);
    cursor: pointer;
    transition: all var(--transition-normal);
    animation: float 6s ease-in-out infinite;
}

.element:hover {
    transform: scale(1.1);
    background: rgba(255, 255, 255, 0.2);
}

.element span {
    font-size: 0.625rem;
    margin-top: 4px;
    font-weight: 600;
}

.element-1 { top: 10%; left: 20%; animation-delay: 0s; }
.element-2 { top: 30%; right: 10%; animation-delay: 1s; }
.element-3 { bottom: 30%; left: 10%; animation-delay: 2s; }
.element-4 { bottom: 10%; right: 20%; animation-delay: 3s; }
.element-5 { top: 50%; left: 10%; animation-delay: 4s; }

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.features-section,
.specialties-section,
.testimonials-section {
    padding: var(--space-4xl) 0;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--space-2xl);
    margin-top: var(--space-3xl);
}

.feature {
    text-align: center;
    padding: var(--space-2xl);
    border-radius: var(--radius-2xl);
    background: var(--white);
    border: 1px solid var(--gray-200);
    transition: all var(--transition-normal);
}

.feature:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-blue);
}

.feature__icon {
    width: 80px;
    height: 80px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto var(--space-lg);
    font-size: 2rem;
    color: var(--white);
}

.feature__title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--space-md);
}

.feature__description {
    color: var(--gray-600);
    line-height: 1.6;
}

.specialties-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--space-xl);
    margin-top: var(--space-3xl);
}

.specialty-card {
    background: var(--gradient-primary);
    color: var(--white);
    padding: var(--space-2xl);
    border-radius: var(--radius-2xl);
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: all var(--transition-normal);
}

.specialty-card:hover {
    transform: scale(1.05);
}

.specialty-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    transform: rotate(45deg);
}

.specialty-card__icon {
    font-size: 3rem;
    margin-bottom: var(--space-lg);
}

.specialty-card h3 {
    font-size: 1.25rem;
    margin-bottom: var(--space-md);
}

.specialty-card p {
    opacity: 0.9;
    margin-bottom: var(--space-lg);
}

.specialty-card__count {
    font-weight: 600;
    font-size: 0.875rem;
    background: rgba(255, 255, 255, 0.2);
    padding: var(--space-sm) var(--space-lg);
    border-radius: var(--radius-full);
    display: inline-block;
}

.testimonials-section {
    background: var(--gray-50);
}

.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: var(--space-2xl);
    margin-top: var(--space-3xl);
}

.testimonial {
    background: var(--white);
    padding: var(--space-2xl);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    border-left: 4px solid var(--primary-blue);
}

.testimonial__rating {
    color: #fbbf24;
    margin-bottom: var(--space-lg);
}

.testimonial__quote {
    font-style: italic;
    color: var(--gray-700);
    line-height: 1.6;
    margin: 0 0 var(--space-lg);
}

.testimonial__author strong {
    color: var(--gray-900);
}

.testimonial__author span {
    color: var(--gray-600);
    font-size: 0.875rem;
}

.cta-section {
    background: var(--gradient-primary);
    color: var(--white);
    padding: var(--space-4xl) 0;
    text-align: center;
}

.cta__title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: var(--space-lg);
}

.cta__subtitle {
    font-size: 1.125rem;
    opacity: 0.9;
    margin-bottom: var(--space-3xl);
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.cta-buttons {
    display: flex;
    gap: var(--space-lg);
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: var(--space-2xl);
}

.cta-guarantee {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-sm);
    font-size: 0.875rem;
    opacity: 0.9;
}

@media (max-width: 1024px) {
    .hero-container {
        grid-template-columns: 1fr;
        text-align: center;
        gap: var(--space-2xl);
    }
    
    .formateur-showcase {
        width: 250px;
        height: 250px;
    }
    
    .formateur-photo,
    .formateur-placeholder {
        width: 150px;
        height: 150px;
    }
    
    .element {
        width: 60px;
        height: 60px;
        font-size: 1.25rem;
    }
}

@media (max-width: 768px) {
    .hero__title {
        font-size: 2.5rem;
    }
    
    .hero__actions {
        flex-direction: column;
        align-items: center;
    }
    
    .hero__stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .features-grid,
    .specialties-grid,
    .testimonials-grid {
        grid-template-columns: 1fr;
    }
}
</style>