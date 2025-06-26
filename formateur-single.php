<?php
/**
 * Template: Profil individuel formateur
 * Version: 2.0.0
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

// Récupération des données du formateur
$formateur_id = get_the_ID();
$formateur = get_post($formateur_id);
$meta = get_post_meta($formateur_id);

// Données personnelles
$prenom = $meta['_formateur_prenom'][0] ?? '';
$nom = $meta['_formateur_nom'][0] ?? '';
$email = $meta['_formateur_email'][0] ?? '';
$telephone = $meta['_formateur_telephone'][0] ?? '';
$ville = $meta['_formateur_ville'][0] ?? '';
$pays = $meta['_formateur_pays'][0] ?? '';
$site_web = $meta['_formateur_site_web'][0] ?? '';
$linkedin = $meta['_formateur_linkedin'][0] ?? '';

// Expertise
$experience = $meta['_formateur_experience'][0] ?? '';
$competences = $meta['_formateur_competences'][0] ?? [];
$modalites = $meta['_formateur_modalites'][0] ?? [];
$publics = $meta['_formateur_publics'][0] ?? [];
$langues = $meta['_formateur_langues'][0] ?? [];
$tarif = $meta['_formateur_tarif_jour'][0] ?? '';

// Contenus
$biographie = $formateur->post_content;
$formations_donnees = $meta['_formateur_formations_donnees'][0] ?? '';
$methodes_pedagogiques = $meta['_formateur_methodes_pedagogiques'][0] ?? '';
$diplomes = $meta['_formateur_diplomes'][0] ?? '';

// Fichiers
$cv_data = $meta['_formateur_cv'][0] ?? [];
$portfolio_data = $meta['_formateur_portfolio'][0] ?? [];

// Spécialité et compétences
$specialty_terms = wp_get_post_terms($formateur_id, 'formateur_specialty');
$skill_terms = wp_get_post_terms($formateur_id, 'formateur_skill');

// Initiales pour avatar
$initiales = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));

// Rating et stats (simulés)
$rating = 4.8;
$rating_count = rand(15, 80);
$formations_count = rand(25, 150);
$clients_count = rand(10, 45);

get_header(); ?>

<div class="formateur-container formateur-container--wide">
    <div class="formateur-single">
        
        <!-- Hero Section du Profil -->
        <div class="formateur-hero">
            <div class="formateur-hero__background">
                <div class="hero-pattern"></div>
            </div>
            
            <div class="formateur-hero__content">
                <div class="formateur-hero__main">
                    <div class="formateur-hero__avatar">
                        <?php if (has_post_thumbnail($formateur_id)): ?>
                            <?php echo get_the_post_thumbnail($formateur_id, 'large', ['class' => 'hero-avatar-image']); ?>
                        <?php else: ?>
                            <span class="hero-avatar-initials"><?php echo esc_html($initiales); ?></span>
                        <?php endif; ?>
                        <div class="avatar-badge">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="formateur-hero__info">
                        <h1 class="formateur-hero__name">
                            <?php echo esc_html($prenom . ' ' . $nom); ?>
                        </h1>
                        
                        <?php if (!empty($specialty_terms)): ?>
                        <div class="formateur-hero__specialty">
                            <?php echo esc_html($specialty_terms[0]->name); ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="formateur-hero__meta">
                            <?php if ($ville): ?>
                            <div class="meta-item">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                <span><?php echo esc_html($ville . ($pays ? ', ' . $pays : '')); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="meta-item">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                    <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"></path>
                                </svg>
                                <span><?php echo esc_html($experience); ?></span>
                            </div>
                            
                            <div class="meta-item">
                                <div class="rating-display">
                                    <div class="stars-inline">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <svg class="star <?php echo $i <= $rating ? 'filled' : ''; ?>" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="rating-score"><?php echo number_format($rating, 1); ?></span>
                                    <span class="rating-count">(<?php echo $rating_count; ?> avis)</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="formateur-hero__stats">
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $formations_count; ?>+</div>
                                <div class="stat-label"><?php _e('Formations', 'formateur-manager-pro'); ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $clients_count; ?>+</div>
                                <div class="stat-label"><?php _e('Clients', 'formateur-manager-pro'); ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo intval($rating * 20); ?>%</div>
                                <div class="stat-label"><?php _e('Satisfaction', 'formateur-manager-pro'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="formateur-hero__actions">
                    <button class="btn btn--primary btn--lg" 
                            onclick="contactFormateur('<?php echo esc_js($initiales); ?>', '<?php echo esc_js($specialty_terms[0]->name ?? ''); ?>', '<?php echo esc_js($email); ?>')">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        <?php _e('Prendre contact', 'formateur-manager-pro'); ?>
                    </button>
                    
                    <?php if ($cv_data && !empty($cv_data['url'])): ?>
                    <a href="<?php echo esc_url($cv_data['url']); ?>" 
                       class="btn btn--outline btn--lg" 
                       target="_blank" 
                       rel="noopener">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                        </svg>
                        <?php _e('Télécharger CV', 'formateur-manager-pro'); ?>
                    </a>
                    <?php endif; ?>
                    
                    <div class="social-links">
                        <?php if ($site_web): ?>
                        <a href="<?php echo esc_url($site_web); ?>" 
                           class="social-link" 
                           target="_blank" 
                           rel="noopener"
                           title="<?php esc_attr_e('Site web', 'formateur-manager-pro'); ?>">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16 8 8 0 000-16zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($linkedin): ?>
                        <a href="<?php echo esc_url($linkedin); ?>" 
                           class="social-link" 
                           target="_blank" 
                           rel="noopener"
                           title="<?php esc_attr_e('LinkedIn', 'formateur-manager-pro'); ?>">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu Principal -->
        <div class="formateur-content">
            <div class="content-grid">
                <!-- Colonne Principale -->
                <div class="content-main">
                    
                    <!-- À Propos -->
                    <section class="content-section">
                        <h2 class="section-title">
                            <svg class="section-icon" width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <?php _e('À propos', 'formateur-manager-pro'); ?>
                        </h2>
                        
                        <div class="bio-content">
                            <?php echo wp_kses_post(wpautop($biographie)); ?>
                        </div>
                    </section>

                    <!-- Compétences -->
                    <?php if (!empty($competences) && is_array($competences)): ?>
                    <section class="content-section">
                        <h2 class="section-title">
                            <svg class="section-icon" width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <?php _e('Compétences techniques', 'formateur-manager-pro'); ?>
                        </h2>
                        
                        <div class="skills-grid">
                            <?php foreach ($competences as $competence): ?>
                                <div class="skill-item">
                                    <span class="skill-name"><?php echo esc_html($competence); ?></span>
                                    <div class="skill-level">
                                        <div class="skill-bar">
                                            <div class="skill-progress" style="width: <?php echo rand(75, 95); ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                    <?php endif; ?>

                    <!-- Formations Dispensées -->
                    <?php if ($formations_donnees): ?>
                    <section class="content-section">
                        <h2 class="section-title">
                            <svg class="section-icon" width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                            </svg>
                            <?php _e('Formations dispensées', 'formateur-manager-pro'); ?>
                        </h2>
                        
                        <div class="formatted-content">
                            <?php echo wp_kses_post(wpautop($formations_donnees)); ?>
                        </div>
                    </section>
                    <?php endif; ?>

                    <!-- Méthodes Pédagogiques -->
                    <?php if ($methodes_pedagogiques): ?>
                    <section class="content-section">
                        <h2 class="section-title">
                            <svg class="section-icon" width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd"></path>
                            </svg>
                            <?php _e('Approche pédagogique', 'formateur-manager-pro'); ?>
                        </h2>
                        
                        <div class="formatted-content">
                            <?php echo wp_kses_post(wpautop($methodes_pedagogiques)); ?>
                        </div>
                    </section>
                    <?php endif; ?>

                    <!-- Diplômes et Certifications -->
                    <?php if ($diplomes): ?>
                    <section class="content-section">
                        <h2 class="section-title">
                            <svg class="section-icon" width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                            </svg>
                            <?php _e('Diplômes & Certifications', 'formateur-manager-pro'); ?>
                        </h2>
                        
                        <div class="formatted-content">
                            <?php echo wp_kses_post(wpautop($diplomes)); ?>
                        </div>
                    </section>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="content-sidebar">
                    
                    <!-- Informations Pratiques -->
                    <div class="sidebar-card">
                        <h3 class="sidebar-title">
                            <?php _e('Informations pratiques', 'formateur-manager-pro'); ?>
                        </h3>
                        
                        <div class="info-list">
                            <?php if (!empty($modalites) && is_array($modalites)): ?>
                            <div class="info-item">
                                <div class="info-label">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <?php _e('Modalités', 'formateur-manager-pro'); ?>
                                </div>
                                <div class="info-value">
                                    <?php echo esc_html(implode(', ', $modalites)); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($publics) && is_array($publics)): ?>
                            <div class="info-item">
                                <div class="info-label">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                    </svg>
                                    <?php _e('Public cible', 'formateur-manager-pro'); ?>
                                </div>
                                <div class="info-value">
                                    <?php echo esc_html(implode(', ', $publics)); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($langues) && is_array($langues)): ?>
                            <div class="info-item">
                                <div class="info-label">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7 2a1 1 0 011 1v1h3a1 1 0 110 2H9.578a18.87 18.87 0 01-1.724 4.78c.29.354.596.696.914 1.026a1 1 0 11-1.44 1.389c-.188-.196-.373-.396-.554-.6a19.098 19.098 0 01-3.107 3.567 1 1 0 01-1.334-1.49 17.087 17.087 0 003.13-3.733 18.992 18.992 0 01-1.487-2.494 1 1 0 111.79-.89c.234.47.489.928.764 1.372.417-.934.752-1.913.997-2.927H3a1 1 0 110-2h3V3a1 1 0 011-1zm6 6a1 1 0 01.894.553l2.991 5.982a.869.869 0 01.02.037l.99 1.98a1 1 0 11-1.79.895L15.383 16h-4.764l-.724 1.447a1 1 0 11-1.788-.894l.99-1.98.019-.038 2.99-5.982A1 1 0 0113 8zm-1.382 6h2.764L13 11.236 11.618 14z" clip-rule="evenodd"></path>
                                    </svg>
                                    <?php _e('Langues', 'formateur-manager-pro'); ?>
                                </div>
                                <div class="info-value">
                                    <?php echo esc_html(implode(', ', $langues)); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($tarif): ?>
                            <div class="info-item info-item--highlight">
                                <div class="info-label">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                    </svg>
                                    <?php _e('Tarif indicatif', 'formateur-manager-pro'); ?>
                                </div>
                                <div class="info-value info-value--price">
                                    <?php echo esc_html($tarif); ?>/jour
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Documents Disponibles -->
                    <?php if ($cv_data || $portfolio_data): ?>
                    <div class="sidebar-card">
                        <h3 class="sidebar-title">
                            <?php _e('Documents', 'formateur-manager-pro'); ?>
                        </h3>
                        
                        <div class="documents-list">
                            <?php if ($cv_data && !empty($cv_data['url'])): ?>
                            <a href="<?php echo esc_url($cv_data['url']); ?>" 
                               class="document-item" 
                               target="_blank" 
                               rel="noopener">
                                <div class="document-icon">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="document-info">
                                    <div class="document-name"><?php _e('Curriculum Vitae', 'formateur-manager-pro'); ?></div>
                                    <div class="document-type">PDF</div>
                                </div>
                                <div class="document-arrow">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($portfolio_data && !empty($portfolio_data['url'])): ?>
                            <a href="<?php echo esc_url($portfolio_data['url']); ?>" 
                               class="document-item" 
                               target="_blank" 
                               rel="noopener">
                                <div class="document-icon">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="document-info">
                                    <div class="document-name"><?php _e('Portfolio', 'formateur-manager-pro'); ?></div>
                                    <div class="document-type"><?php echo strtoupper(pathinfo($portfolio_data['name'] ?? '', PATHINFO_EXTENSION)); ?></div>
                                </div>
                                <div class="document-arrow">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Call to Action -->
                    <div class="sidebar-card sidebar-card--cta">
                        <h3 class="sidebar-title">
                            <?php _e('Intéressé par ce profil ?', 'formateur-manager-pro'); ?>
                        </h3>
                        
                        <p class="cta-text">
                            <?php _e('Prenez contact directement pour discuter de vos besoins de formation.', 'formateur-manager-pro'); ?>
                        </p>
                        
                        <button class="btn btn--primary btn--full" 
                                onclick="contactFormateur('<?php echo esc_js($initiales); ?>', '<?php echo esc_js($specialty_terms[0]->name ?? ''); ?>', '<?php echo esc_js($email); ?>')">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                            <?php _e('Contacter ce formateur', 'formateur-manager-pro'); ?>
                        </button>
                        
                        <div class="response-time">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php _e('Répond en moins de 24h en moyenne', 'formateur-manager-pro'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formateurs Similaires -->
        <section class="similar-formateurs">
            <div class="section-header">
                <h2 class="section-title">
                    <?php _e('Formateurs similaires', 'formateur-manager-pro'); ?>
                </h2>
                <p class="section-subtitle">
                    <?php _e('Découvrez d\'autres experts dans le même domaine', 'formateur-manager-pro'); ?>
                </p>
            </div>
            
            <div class="similar-grid">
                <?php
                // Requête pour formateurs similaires
                $similar_args = [
                    'post_type' => 'formateur_pro',
                    'post_status' => 'publish',
                    'posts_per_page' => 3,
                    'post__not_in' => [$formateur_id],
                    'meta_query' => [
                        [
                            'key' => '_formateur_status',
                            'value' => 'active',
                            'compare' => '='
                        ]
                    ]
                ];
                
                if (!empty($specialty_terms)) {
                    $similar_args['tax_query'] = [
                        [
                            'taxonomy' => 'formateur_specialty',
                            'field' => 'term_id',
                            'terms' => $specialty_terms[0]->term_id
                        ]
                    ];
                }
                
                $similar_query = new WP_Query($similar_args);
                
                if ($similar_query->have_posts()):
                    while ($similar_query->have_posts()): $similar_query->the_post();
                        // Réutiliser la fonction de rendu des cartes depuis la liste
                        echo renderFormateurCard(get_the_ID(), 'grid');
                    endwhile;
                    wp_reset_postdata();
                else:
                ?>
                    <div class="no-similar">
                        <p><?php _e('Aucun formateur similaire trouvé pour le moment.', 'formateur-manager-pro'); ?></p>
                        <a href="/formateurs/" class="btn btn--outline">
                            <?php _e('Voir tous les formateurs', 'formateur-manager-pro'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<style>
/* Styles spécifiques au profil individuel */
.formateur-hero {
    background: var(--gradient-primary);
    color: var(--white);
    padding: var(--space-4xl) 0;
    position: relative;
    overflow: hidden;
    margin-bottom: var(--space-3xl);
}

.formateur-hero__background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.1;
}

.hero-pattern {
    width: 100%;
    height: 100%;
    background-image: radial-gradient(circle at 1px 1px, var(--white) 1px, transparent 0);
    background-size: 20px 20px;
}

.formateur-hero__content {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--space-3xl);
}

.formateur-hero__main {
    display: flex;
    align-items: center;
    gap: var(--space-2xl);
    flex: 1;
}

.formateur-hero__avatar {
    position: relative;
    width: 120px;
    height: 120px;
    border-radius: var(--radius-full);
    overflow: hidden;
    border: 4px solid rgba(255, 255, 255, 0.2);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
}

.hero-avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-avatar-initials {
    font-size: 2rem;
    font-weight: 800;
    color: var(--white);
}

.avatar-badge {
    position: absolute;
    bottom: 8px;
    right: 8px;
    width: 32px;
    height: 32px;
    background: var(--success);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid var(--white);
}

.formateur-hero__name {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: var(--space-sm);
    line-height: 1.2;
}

.formateur-hero__specialty {
    font-size: 1.25rem;
    opacity: 0.9;
    margin-bottom: var(--space-lg);
    font-weight: 600;
}

.formateur-hero__meta {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-lg);
    margin-bottom: var(--space-lg);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    font-size: 0.875rem;
    opacity: 0.9;
}

.rating-display {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

.stars-inline {
    display: flex;
    gap: 2px;
}

.rating-score {
    font-weight: 700;
    font-size: 1rem;
}

.rating-count {
    opacity: 0.7;
}

.formateur-hero__stats {
    display: flex;
    gap: var(--space-xl);
    margin-top: var(--space-lg);
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: var(--space-xs);
}

.stat-label {
    font-size: 0.75rem;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.formateur-hero__actions {
    display: flex;
    flex-direction: column;
    gap: var(--space-lg);
    align-items: end;
}

.social-links {
    display: flex;
    gap: var(--space-md);
}

.social-link {
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    text-decoration: none;
    transition: all var(--transition-fast);
    backdrop-filter: blur(10px);
}

.social-link:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

/* Contenu Principal */
.content-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: var(--space-3xl);
    align-items: start;
}

.content-section {
    background: var(--white);
    padding: var(--space-2xl);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--gray-200);
    margin-bottom: var(--space-2xl);
}

.section-title {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: var(--space-xl);
}

.section-icon {
    color: var(--primary-blue);
}

.bio-content,
.formatted-content {
    line-height: 1.7;
    color: var(--gray-700);
}

.skills-grid {
    display: grid;
    gap: var(--space-lg);
}

.skill-item {
    display: flex;
    align-items: center;
    gap: var(--space-lg);
}

.skill-name {
    min-width: 150px;
    font-weight: 600;
    color: var(--gray-800);
}

.skill-level {
    flex: 1;
}

.skill-bar {
    height: 8px;
    background: var(--gray-200);
    border-radius: var(--radius-full);
    overflow: hidden;
}

.skill-progress {
    height: 100%;
    background: var(--gradient-primary);
    border-radius: var(--radius-full);
    transition: width 1s ease;
}

/* Sidebar */
.sidebar-card {
    background: var(--white);
    padding: var(--space-xl);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--gray-200);
    margin-bottom: var(--space-xl);
}

.sidebar-card--cta {
    background: var(--gradient-primary);
    color: var(--white);
    border-color: transparent;
}

.sidebar-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: var(--space-lg);
}

.sidebar-card--cta .sidebar-title {
    color: var(--white);
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: var(--space-lg);
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: var(--space-sm);
}

.info-item--highlight {
    padding: var(--space-lg);
    background: var(--gray-50);
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
}

.info-label {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-600);
}

.info-value {
    color: var(--gray-800);
    font-weight: 500;
}

.info-value--price {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--success);
}

.documents-list {
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
}

.document-item {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    padding: var(--space-md);
    background: var(--gray-50);
    border-radius: var(--radius-lg);
    text-decoration: none;
    color: var(--gray-800);
    transition: all var(--transition-fast);
    border: 1px solid var(--gray-200);
}

.document-item:hover {
    background: var(--gray-100);
    transform: translateY(-1px);
}

.document-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-blue);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    flex-shrink: 0;
}

.document-info {
    flex: 1;
    min-width: 0;
}

.document-name {
    font-weight: 600;
    margin-bottom: var(--space-xs);
}

.document-type {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
    font-weight: 600;
}

.document-arrow {
    color: var(--gray-400);
    transition: color var(--transition-fast);
}

.document-item:hover .document-arrow {
    color: var(--gray-600);
}

.cta-text {
    margin-bottom: var(--space-xl);
    opacity: 0.9;
    line-height: 1.5;
}

.response-time {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    font-size: 0.875rem;
    opacity: 0.8;
    margin-top: var(--space-lg);
    text-align: center;
    justify-content: center;
}

/* Formateurs Similaires */
.similar-formateurs {
    margin-top: var(--space-4xl);
    padding-top: var(--space-3xl);
    border-top: 1px solid var(--gray-200);
}

.similar-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--space-2xl);
}

.no-similar {
    grid-column: 1 / -1;
    text-align: center;
    padding: var(--space-3xl);
}

/* Responsive */
@media (max-width: 1024px) {
    .content-grid {
        grid-template-columns: 1fr;
        gap: var(--space-2xl);
    }
    
    .formateur-hero__content {
        flex-direction: column;
        text-align: center;
    }
    
    .formateur-hero__main {
        flex-direction: column;
        text-align: center;
    }
    
    .formateur-hero__actions {
        align-items: center;
    }
}

@media (max-width: 768px) {
    .formateur-hero {
        padding: var(--space-2xl) 0;
    }
    
    .formateur-hero__name {
        font-size: 2rem;
    }
    
    .formateur-hero__avatar {
        width: 100px;
        height: 100px;
    }
    
    .hero-avatar-initials {
        font-size: 1.5rem;
    }
    
    .formateur-hero__meta {
        flex-direction: column;
        gap: var(--space-md);
    }
    
    .formateur-hero__stats {
        flex-direction: column;
        gap: var(--space-lg);
    }
    
    .skill-item {
        flex-direction: column;
        align-items: stretch;
        gap: var(--space-sm);
    }
    
    .skill-name {
        min-width: auto;
    }
    
    .similar-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php get_footer(); ?>