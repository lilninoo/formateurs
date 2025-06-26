<?php
/**
 * Template de la liste des formateurs
 * templates/formateur-list.php
 */

if (!defined('ABSPATH')) exit;

// Récupérer les formateurs
$query_args = [
    'post_type' => 'formateur_pro',
    'post_status' => 'publish',
    'posts_per_page' => intval($atts['limit']),
    'meta_query' => [
        [
            'key' => '_formateur_status',
            'value' => 'active',
            'compare' => '='
        ]
    ]
];

// Filtres
if (!empty($atts['specialty'])) {
    $query_args['tax_query'] = [
        [
            'taxonomy' => 'formateur_specialty',
            'field' => 'slug',
            'terms' => $atts['specialty']
        ]
    ];
}

if (!empty($atts['city'])) {
    $query_args['meta_query'][] = [
        'key' => '_formateur_ville',
        'value' => $atts['city'],
        'compare' => 'LIKE'
    ];
}

$formateurs = get_posts($query_args);

// Récupérer les spécialités pour le filtre
$specialties = get_terms([
    'taxonomy' => 'formateur_specialty',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC'
]);

// Récupérer les villes pour le filtre
global $wpdb;
$cities = $wpdb->get_col("
    SELECT DISTINCT meta_value 
    FROM {$wpdb->postmeta} 
    WHERE meta_key = '_formateur_ville' 
    AND meta_value != '' 
    ORDER BY meta_value ASC
");
?>

<div class="formateur-container formateur-list-container">
    <?php if ($atts['show_search'] === 'true'): ?>
    <!-- Filtres et recherche -->
    <div class="search-filters">
        <div class="search-filters__grid">
            <div class="search-filters__main">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-input-icon"></i>
                    <input type="text" 
                           id="formateur-search" 
                           class="form-input search-input" 
                           placeholder="🔍 <?php esc_attr_e('Rechercher par nom, compétence ou mot-clé...', 'formateur-manager-pro'); ?>">
                    <button type="button" class="search-clear" id="search-clear" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="filter-item">
                <label class="filter-label"><?php _e('Spécialité', 'formateur-manager-pro'); ?></label>
                <select id="formateur-filter-specialite" class="form-select">
                    <option value=""><?php _e('Toutes les spécialités', 'formateur-manager-pro'); ?></option>
                    <?php foreach ($specialties as $specialty): ?>
                        <option value="<?php echo esc_attr($specialty->slug); ?>">
                            <?php echo esc_html($specialty->name); ?> (<?php echo $specialty->count; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-item">
                <label class="filter-label"><?php _e('Ville', 'formateur-manager-pro'); ?></label>
                <select id="formateur-filter-ville" class="form-select">
                    <option value=""><?php _e('Toutes les villes', 'formateur-manager-pro'); ?></option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo esc_attr($city); ?>">
                            <?php echo esc_html($city); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-item">
                <label class="filter-label"><?php _e('Modalité', 'formateur-manager-pro'); ?></label>
                <select id="formateur-filter-modalite" class="form-select">
                    <option value=""><?php _e('Toutes les modalités', 'formateur-manager-pro'); ?></option>
                    <option value="presentiel"><?php _e('Présentiel', 'formateur-manager-pro'); ?></option>
                    <option value="distanciel"><?php _e('Distanciel', 'formateur-manager-pro'); ?></option>
                    <option value="hybride"><?php _e('Hybride', 'formateur-manager-pro'); ?></option>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="button" id="reset-filters" class="btn btn--outline btn--sm">
                    <i class="fas fa-undo"></i>
                    <?php _e('Réinitialiser', 'formateur-manager-pro'); ?>
                </button>
            </div>
        </div>
        
        <div class="search-stats">
            <span id="results-count"><?php echo count($formateurs); ?></span> 
            <?php _e('formateur(s) trouvé(s)', 'formateur-manager-pro'); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Liste des formateurs -->
    <div class="formateurs-grid" id="formateurs-grid">
        <?php if ($formateurs): ?>
            <?php foreach ($formateurs as $formateur): ?>
                <?php echo fmp_render_formateur_card($formateur->ID); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-formateurs">
                <div class="empty-state">
                    <i class="fas fa-search empty-state__icon"></i>
                    <h3><?php _e('Aucun formateur trouvé', 'formateur-manager-pro'); ?></h3>
                    <p><?php _e('Essayez de modifier vos critères de recherche ou consultez tous nos formateurs.', 'formateur-manager-pro'); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Message "aucun résultat" (caché par défaut) -->
    <div class="no-results-message" style="display: none;">
        <div class="empty-state">
            <i class="fas fa-search empty-state__icon"></i>
            <h3><?php _e('Aucun résultat', 'formateur-manager-pro'); ?></h3>
            <p><?php _e('Aucun formateur ne correspond à vos critères de recherche.', 'formateur-manager-pro'); ?></p>
            <button type="button" class="btn btn--primary" onclick="resetAllFilters()">
                <?php _e('Voir tous les formateurs', 'formateur-manager-pro'); ?>
            </button>
        </div>
    </div>
    
    <!-- Pagination (si nécessaire) -->
    <?php if (count($formateurs) >= intval($atts['limit'])): ?>
    <div class="pagination-container">
        <button type="button" class="btn btn--outline" id="load-more-formateurs">
            <i class="fas fa-plus"></i>
            <?php _e('Charger plus de formateurs', 'formateur-manager-pro'); ?>
        </button>
    </div>
    <?php endif; ?>
</div>

<?php
/**
 * Fonction pour rendre une carte de formateur
 */
function fmp_render_formateur_card($post_id) {
    $prenom = get_post_meta($post_id, '_formateur_prenom', true);
    $nom = get_post_meta($post_id, '_formateur_nom', true);
    $email = get_post_meta($post_id, '_formateur_email', true);
    $ville = get_post_meta($post_id, '_formateur_ville', true);
    $biographie = get_post_meta($post_id, '_formateur_biographie', true);
    $experience = get_post_meta($post_id, '_formateur_experience', true);
    $tarif_jour = get_post_meta($post_id, '_formateur_tarif_jour', true);
    $modalites = get_post_meta($post_id, '_formateur_modalites', true) ?: [];
    
    // Récupérer les spécialités
    $specialties = get_the_terms($post_id, 'formateur_specialty');
    $specialty_names = [];
    if ($specialties && !is_wp_error($specialties)) {
        foreach ($specialties as $specialty) {
            $specialty_names[] = $specialty->name;
        }
    }
    
    // Récupérer les compétences
    $skills = get_the_terms($post_id, 'formateur_skill');
    $skill_names = [];
    if ($skills && !is_wp_error($skills)) {
        foreach (array_slice($skills, 0, 5) as $skill) {
            $skill_names[] = $skill->name;
        }
    }
    
    // Créer les initiales
    $initiales = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
    
    // Labels d'expérience
    $experience_labels = [
        'debutant' => __('Débutant', 'formateur-manager-pro'),
        'junior' => __('Junior', 'formateur-manager-pro'),
        'confirme' => __('Confirmé', 'formateur-manager-pro'),
        'senior' => __('Senior', 'formateur-manager-pro'),
        'expert' => __('Expert', 'formateur-manager-pro')
    ];
    
    ob_start();
    ?>
    <div class="formateur-card" 
         data-specialite="<?php echo esc_attr(implode(',', wp_list_pluck($specialties ?: [], 'slug'))); ?>" 
         data-ville="<?php echo esc_attr(strtolower($ville)); ?>"
         data-modalites="<?php echo esc_attr(implode(',', $modalites)); ?>"
         data-searchable="<?php echo esc_attr(strtolower($prenom . ' ' . $nom . ' ' . implode(' ', $specialty_names) . ' ' . implode(' ', $skill_names))); ?>">
        
        <div class="formateur-card__header">
            <div class="formateur-card__avatar">
                <?php if (has_post_thumbnail($post_id)): ?>
                    <?php echo get_the_post_thumbnail($post_id, 'thumbnail', ['class' => 'avatar-image']); ?>
                <?php else: ?>
                    <span class="avatar-initials"><?php echo esc_html($initiales); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="formateur-card__info">
                <h3 class="formateur-card__name">
                    <?php echo esc_html('Formateur ' . $initiales); ?>
                </h3>
                
                <?php if (!empty($specialty_names)): ?>
                    <div class="formateur-card__specialty">
                        <?php echo esc_html(implode(', ', $specialty_names)); ?>
                    </div>
                <?php endif; ?>
                
                <div class="formateur-card__location">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo esc_html($ville); ?>
                </div>
            </div>
            
            <?php if ($experience): ?>
                <div class="formateur-card__badge">
                    <span class="badge badge--<?php echo esc_attr($experience); ?>">
                        <?php echo esc_html($experience_labels[$experience] ?? $experience); ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($biographie): ?>
            <div class="formateur-card__bio">
                <?php echo esc_html(wp_trim_words($biographie, 25)); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($skill_names)): ?>
            <div class="formateur-card__skills">
                <?php foreach ($skill_names as $skill): ?>
                    <span class="skill-tag"><?php echo esc_html($skill); ?></span>
                <?php endforeach; ?>
                <?php if (count($skills) > 5): ?>
                    <span class="skill-tag skill-tag--more">
                        +<?php echo (count($skills) - 5); ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="formateur-card__footer">
            <div class="formateur-card__rating">
                <div class="formateur-card__stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star star <?php echo $i <= 4 ? 'filled' : ''; ?>"></i>
                    <?php endfor; ?>
                </div>
                <span class="rating-text">4.0 (<?php echo rand(5, 25); ?> avis)</span>
            </div>
            
            <?php if ($tarif_jour): ?>
                <div class="formateur-card__price">
                    <span class="price-range"><?php echo esc_html($tarif_jour); ?>€/jour</span>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="formateur-card__actions">
            <button type="button" 
                    class="btn btn--primary btn--sm" 
                    onclick="contactFormateur('<?php echo esc_js($initiales); ?>', '<?php echo esc_js($email); ?>', '<?php echo esc_js(implode(', ', $specialty_names)); ?>')">
                <i class="fas fa-envelope"></i>
                <?php _e('Contacter', 'formateur-manager-pro'); ?>
            </button>
            
            <button type="button" 
                    class="btn btn--outline btn--sm" 
                    onclick="viewFormateur(<?php echo $post_id; ?>)">
                <i class="fas fa-eye"></i>
                <?php _e('Voir profil', 'formateur-manager-pro'); ?>
            </button>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}
?>

<style>
/* Styles spécifiques à la liste */
.search-stats {
    margin-top: var(--space-lg);
    font-size: 0.875rem;
    color: var(--gray-600);
    text-align: center;
}

.formateurs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: var(--space-xl);
    margin-top: var(--space-2xl);
}

.empty-state {
    text-align: center;
    padding: var(--space-4xl) var(--space-xl);
    color: var(--gray-600);
}

.empty-state__icon {
    font-size: 4rem;
    color: var(--gray-400);
    margin-bottom: var(--space-lg);
}

.empty-state h3 {
    color: var(--gray-800);
    margin-bottom: var(--space-sm);
}

.no-formateurs {
    grid-column: 1 / -1;
}

.no-results-message {
    grid-column: 1 / -1;
    margin-top: var(--space-2xl);
}

.pagination-container {
    grid-column: 1 / -1;
    text-align: center;
    margin-top: var(--space-2xl);
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge--debutant { background: #e3f2fd; color: #1565c0; }
.badge--junior { background: #f3e5f5; color: #7b1fa2; }
.badge--confirme { background: #e8f5e8; color: #2e7d32; }
.badge--senior { background: #fff3e0; color: #f57c00; }
.badge--expert { background: #ffebee; color: #c62828; }

.skill-tag--more {
    background: var(--gray-300);
    color: var(--gray-700);
}

@media (max-width: 768px) {
    .formateurs-grid {
        grid-template-columns: 1fr;
        gap: var(--space-lg);
    }
    
    .search-filters__grid {
        grid-template-columns: 1fr;
        gap: var(--space-md);
    }
}
</style>