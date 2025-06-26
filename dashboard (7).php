<?php
/**
 * Dashboard Administrateur - Formateur Manager Pro
 * admin/dashboard.php
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

// Récupération des statistiques pour le dashboard
$stats_cache_key = 'fmp_dashboard_stats';
$dashboard_stats = get_transient($stats_cache_key);

if (false === $dashboard_stats) {
    global $wpdb;
    
    // Statistiques générales
    $total_formateurs = wp_count_posts('formateur_pro');
    $formateurs_actifs = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->posts} p 
         INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
         WHERE p.post_type = %s 
         AND p.post_status = 'publish' 
         AND pm.meta_key = '_formateur_status' 
         AND pm.meta_value = 'active'",
        'formateur_pro'
    ));
    
    // Inscriptions ce mois
    $inscriptions_mois = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->posts} 
         WHERE post_type = %s 
         AND MONTH(post_date) = MONTH(CURRENT_DATE()) 
         AND YEAR(post_date) = YEAR(CURRENT_DATE())",
        'formateur_pro'
    ));
    
    // Spécialités les plus populaires
    $top_specialties = $wpdb->get_results($wpdb->prepare(
        "SELECT t.name, COUNT(*) as count 
         FROM {$wpdb->terms} t 
         INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id 
         INNER JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id 
         INNER JOIN {$wpdb->posts} p ON tr.object_id = p.ID 
         WHERE tt.taxonomy = %s 
         AND p.post_status = 'publish' 
         GROUP BY t.term_id 
         ORDER BY count DESC 
         LIMIT 5",
        'formateur_specialty'
    ));
    
    // Évolution mensuelle (12 derniers mois)
    $evolution_mensuelle = $wpdb->get_results($wpdb->prepare(
        "SELECT 
            MONTH(post_date) as mois,
            YEAR(post_date) as annee,
            COUNT(*) as inscriptions
         FROM {$wpdb->posts} 
         WHERE post_type = %s 
         AND post_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)
         GROUP BY YEAR(post_date), MONTH(post_date)
         ORDER BY annee ASC, mois ASC",
        'formateur_pro'
    ));
    
    // Statistiques des villes
    $top_cities = $wpdb->get_results("
        SELECT meta_value as ville, COUNT(*) as count 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = '_formateur_ville' 
        AND pm.meta_value != '' 
        AND p.post_type = 'formateur_pro'
        AND p.post_status = 'publish'
        GROUP BY meta_value 
        ORDER BY count DESC 
        LIMIT 10
    ");
    
    $dashboard_stats = [
        'total' => $total_formateurs->publish ?? 0,
        'pending' => $total_formateurs->pending ?? 0,
        'actifs' => $formateurs_actifs ?? 0,
        'inactive' => ($total_formateurs->publish ?? 0) - ($formateurs_actifs ?? 0),
        'inscriptions_mois' => $inscriptions_mois ?? 0,
        'top_specialties' => $top_specialties ?? [],
        'evolution' => $evolution_mensuelle ?? [],
        'top_cities' => $top_cities ?? []
    ];
    
    set_transient($stats_cache_key, $dashboard_stats, HOUR_IN_SECONDS);
}

// Récupération des formateurs récents
$recent_formateurs = get_posts([
    'post_type' => 'formateur_pro',
    'posts_per_page' => 10,
    'post_status' => ['publish', 'pending'],
    'orderby' => 'date',
    'order' => 'DESC'
]);

// Récupération des activités récentes
$recent_activities = [];
if (get_option('fmp_enable_database_logs', false)) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'fmp_logs';
    
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
        $recent_activities = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table_name} 
             WHERE level IN ('success', 'info', 'error')
             ORDER BY timestamp DESC 
             LIMIT %d",
            20
        ));
    }
}

?>

<div class="wrap">
    <div class="fmp-admin-header">
        <h1 class="fmp-admin-title">
            <span class="fmp-logo">👨‍🏫</span>
            <?php _e('Tableau de bord Formateurs', 'formateur-manager-pro'); ?>
        </h1>
        <div class="fmp-admin-actions">
            <a href="<?php echo admin_url('post-new.php?post_type=formateur_pro'); ?>" class="button button-primary">
                <span class="dashicons dashicons-plus"></span>
                <?php _e('Ajouter un formateur', 'formateur-manager-pro'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=formateur-settings'); ?>" class="button">
                <span class="dashicons dashicons-admin-settings"></span>
                <?php _e('Paramètres', 'formateur-manager-pro'); ?>
            </a>
            <button type="button" class="button" onclick="exportFormateurs()">
                <span class="dashicons dashicons-download"></span>
                <?php _e('Exporter', 'formateur-manager-pro'); ?>
            </button>
        </div>
    </div>

    <!-- Alertes importantes -->
    <?php if ($dashboard_stats['pending'] > 0): ?>
    <div class="notice notice-warning is-dismissible">
        <p>
            <strong><?php _e('Action requise:', 'formateur-manager-pro'); ?></strong>
            <?php echo sprintf(
                _n('%d formateur en attente de validation', '%d formateurs en attente de validation', $dashboard_stats['pending'], 'formateur-manager-pro'),
                $dashboard_stats['pending']
            ); ?>
            <a href="<?php echo admin_url('edit.php?post_type=formateur_pro&post_status=pending'); ?>" class="button button-small">
                <?php _e('Valider maintenant', 'formateur-manager-pro'); ?>
            </a>
        </p>
    </div>
    <?php endif; ?>

    <!-- Statistiques principales -->
    <div class="fmp-stats-grid">
        <div class="fmp-stat-card fmp-stat-card--primary">
            <div class="fmp-stat-icon">
                <span class="dashicons dashicons-businessman"></span>
            </div>
            <div class="fmp-stat-content">
                <div class="fmp-stat-number"><?php echo number_format($dashboard_stats['total']); ?></div>
                <div class="fmp-stat-label"><?php _e('Formateurs au total', 'formateur-manager-pro'); ?></div>
            </div>
            <div class="fmp-stat-trend fmp-stat-trend--up">
                <span class="dashicons dashicons-arrow-up-alt"></span>
                <span>+<?php echo $dashboard_stats['inscriptions_mois']; ?> ce mois</span>
            </div>
            <div class="fmp-stat-action">
                <a href="<?php echo admin_url('edit.php?post_type=formateur_pro'); ?>" class="fmp-stat-link">
                    <?php _e('Voir tous', 'formateur-manager-pro'); ?>
                </a>
            </div>
        </div>

        <div class="fmp-stat-card fmp-stat-card--success">
            <div class="fmp-stat-icon">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <div class="fmp-stat-content">
                <div class="fmp-stat-number"><?php echo number_format($dashboard_stats['actifs']); ?></div>
                <div class="fmp-stat-label"><?php _e('Formateurs actifs', 'formateur-manager-pro'); ?></div>
            </div>
            <div class="fmp-stat-progress">
                <div class="fmp-progress-bar">
                    <div class="fmp-progress-fill" style="width: <?php echo $dashboard_stats['total'] > 0 ? round(($dashboard_stats['actifs'] / $dashboard_stats['total']) * 100) : 0; ?>%;"></div>
                </div>
                <span class="fmp-progress-text">
                    <?php echo $dashboard_stats['total'] > 0 ? round(($dashboard_stats['actifs'] / $dashboard_stats['total']) * 100) : 0; ?>% du total
                </span>
            </div>
        </div>

        <div class="fmp-stat-card fmp-stat-card--warning">
            <div class="fmp-stat-icon">
                <span class="dashicons dashicons-clock"></span>
            </div>
            <div class="fmp-stat-content">
                <div class="fmp-stat-number"><?php echo number_format($dashboard_stats['pending']); ?></div>
                <div class="fmp-stat-label"><?php _e('En attente de validation', 'formateur-manager-pro'); ?></div>
            </div>
            <?php if ($dashboard_stats['pending'] > 0): ?>
            <div class="fmp-stat-action">
                <a href="<?php echo admin_url('edit.php?post_type=formateur_pro&post_status=pending'); ?>" class="button button-small">
                    <?php _e('Valider maintenant', 'formateur-manager-pro'); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <div class="fmp-stat-card fmp-stat-card--info">
            <div class="fmp-stat-icon">
                <span class="dashicons dashicons-category"></span>
            </div>
            <div class="fmp-stat-content">
                <div class="fmp-stat-number"><?php echo count($dashboard_stats['top_specialties']); ?></div>
                <div class="fmp-stat-label"><?php _e('Spécialités actives', 'formateur-manager-pro'); ?></div>
            </div>
            <div class="fmp-stat-detail">
                <?php if (!empty($dashboard_stats['top_specialties'])): ?>
                    <small><?php echo esc_html($dashboard_stats['top_specialties'][0]->name); ?> (<?php echo $dashboard_stats['top_specialties'][0]->count; ?>)</small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="fmp-dashboard-content">
        <!-- Graphique d'évolution -->
        <div class="fmp-dashboard-section fmp-chart-section">
            <div class="fmp-section-header">
                <h2><?php _e('Évolution des inscriptions', 'formateur-manager-pro'); ?></h2>
                <div class="fmp-section-actions">
                    <select id="fmp-chart-period" class="fmp-select">
                        <option value="12"><?php _e('12 derniers mois', 'formateur-manager-pro'); ?></option>
                        <option value="6"><?php _e('6 derniers mois', 'formateur-manager-pro'); ?></option>
                        <option value="3"><?php _e('3 derniers mois', 'formateur-manager-pro'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="fmp-chart-container">
                <canvas id="fmp-inscriptions-chart" width="800" height="300"></canvas>
                <div class="fmp-chart-legend">
                    <div class="fmp-legend-item">
                        <span class="fmp-legend-color fmp-legend-color--primary"></span>
                        <span><?php _e('Inscriptions', 'formateur-manager-pro'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="fmp-dashboard-grid">
            <!-- Formateurs récents -->
            <div class="fmp-dashboard-widget">
                <div class="fmp-widget-header">
                    <h3><?php _e('Formateurs récents', 'formateur-manager-pro'); ?></h3>
                    <div class="fmp-widget-actions">
                        <a href="<?php echo admin_url('edit.php?post_type=formateur_pro'); ?>" class="fmp-widget-link">
                            <?php _e('Voir tout', 'formateur-manager-pro'); ?>
                        </a>
                        <button type="button" class="fmp-widget-refresh" onclick="refreshWidget('recent-formateurs')">
                            <span class="dashicons dashicons-update"></span>
                        </button>
                    </div>
                </div>
                
                <div class="fmp-widget-content" id="recent-formateurs-content">
                    <?php if (!empty($recent_formateurs)): ?>
                        <div class="fmp-formateurs-list">
                            <?php foreach ($recent_formateurs as $formateur): 
                                $meta = get_post_meta($formateur->ID);
                                $prenom = $meta['_formateur_prenom'][0] ?? '';
                                $nom = $meta['_formateur_nom'][0] ?? '';
                                $specialite = $meta['_formateur_specialite'][0] ?? '';
                                $status = $meta['_formateur_status'][0] ?? 'pending';
                                $ville = $meta['_formateur_ville'][0] ?? '';
                                $email = $meta['_formateur_email'][0] ?? '';
                                $initiales = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
                            ?>
                            <div class="fmp-formateur-item" data-formateur-id="<?php echo $formateur->ID; ?>">
                                <div class="fmp-formateur-avatar">
                                    <?php if (has_post_thumbnail($formateur->ID)): ?>
                                        <?php echo get_the_post_thumbnail($formateur->ID, 'thumbnail', ['class' => 'fmp-avatar-img']); ?>
                                    <?php else: ?>
                                        <span class="fmp-avatar-initials"><?php echo $initiales; ?></span>
                                    <?php endif; ?>
                                    <span class="fmp-status-badge fmp-status-<?php echo $status; ?>" 
                                          title="<?php echo ucfirst($status); ?>"></span>
                                </div>
                                
                                <div class="fmp-formateur-info">
                                    <div class="fmp-formateur-name">
                                        <a href="<?php echo get_edit_post_link($formateur->ID); ?>">
                                            <?php echo esc_html($prenom . ' ' . $nom); ?>
                                        </a>
                                    </div>
                                    <div class="fmp-formateur-meta">
                                        <?php if ($specialite): ?>
                                            <span class="fmp-specialty"><?php echo esc_html($specialite); ?></span>
                                        <?php endif; ?>
                                        <?php if ($ville): ?>
                                            <span class="fmp-location"><?php echo esc_html($ville); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="fmp-formateur-contact">
                                        <?php if ($email): ?>
                                            <a href="mailto:<?php echo esc_attr($email); ?>" class="fmp-contact-link">
                                                <span class="dashicons dashicons-email"></span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="fmp-formateur-date">
                                        <?php echo sprintf(__('Inscrit le %s', 'formateur-manager-pro'), 
                                                  date_i18n('d/m/Y', strtotime($formateur->post_date))); ?>
                                        <span class="fmp-time-ago">(<?php echo fmp_time_elapsed($formateur->post_date); ?>)</span>
                                    </div>
                                </div>
                                
                                <div class="fmp-formateur-actions">
                                    <?php if ($status === 'pending'): ?>
                                        <button class="fmp-btn fmp-btn-sm fmp-btn-success" 
                                                onclick="approveFormateur(<?php echo $formateur->ID; ?>)"
                                                title="<?php esc_attr_e('Approuver ce formateur', 'formateur-manager-pro'); ?>">
                                            <span class="dashicons dashicons-yes"></span>
                                            <?php _e('Approuver', 'formateur-manager-pro'); ?>
                                        </button>
                                    <?php endif; ?>
                                    <a href="<?php echo get_edit_post_link($formateur->ID); ?>" 
                                       class="fmp-btn fmp-btn-sm fmp-btn-outline"
                                       title="<?php esc_attr_e('Modifier ce formateur', 'formateur-manager-pro'); ?>">
                                        <span class="dashicons dashicons-edit"></span>
                                        <?php _e('Modifier', 'formateur-manager-pro'); ?>
                                    </a>
                                    <button class="fmp-btn fmp-btn-sm fmp-btn-secondary" 
                                            onclick="sendEmailToFormateur(<?php echo $formateur->ID; ?>)"
                                            title="<?php esc_attr_e('Envoyer un email', 'formateur-manager-pro'); ?>">
                                        <span class="dashicons dashicons-email-alt"></span>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="fmp-empty-state">
                            <span class="dashicons dashicons-businessman"></span>
                            <p><?php _e('Aucun formateur inscrit pour le moment.', 'formateur-manager-pro'); ?></p>
                            <a href="<?php echo admin_url('post-new.php?post_type=formateur_pro'); ?>" class="button button-primary">
                                <?php _e('Ajouter le premier formateur', 'formateur-manager-pro'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Top spécialités -->
            <div class="fmp-dashboard-widget">
                <div class="fmp-widget-header">
                    <h3><?php _e('Spécialités populaires', 'formateur-manager-pro'); ?></h3>
                    <div class="fmp-widget-actions">
                        <a href="<?php echo admin_url('edit-tags.php?taxonomy=formateur_specialty&post_type=formateur_pro'); ?>" class="fmp-widget-link">
                            <?php _e('Gérer', 'formateur-manager-pro'); ?>
                        </a>
                    </div>
                </div>
                
                <div class="fmp-widget-content">
                    <?php if (!empty($dashboard_stats['top_specialties'])): ?>
                        <div class="fmp-specialties-chart">
                            <?php 
                            $max_count = max(array_column($dashboard_stats['top_specialties'], 'count'));
                            foreach ($dashboard_stats['top_specialties'] as $specialty): 
                                $percentage = $max_count > 0 ? ($specialty->count / $max_count) * 100 : 0;
                            ?>
                            <div class="fmp-specialty-item">
                                <div class="fmp-specialty-info">
                                    <span class="fmp-specialty-name"><?php echo esc_html($specialty->name); ?></span>
                                    <span class="fmp-specialty-count"><?php echo $specialty->count; ?></span>
                                </div>
                                <div class="fmp-specialty-bar">
                                    <div class="fmp-specialty-fill" style="width: <?php echo $percentage; ?>%;"></div>
                                </div>
                                <div class="fmp-specialty-percentage"><?php echo round($percentage); ?>%</div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="fmp-empty-state">
                            <span class="dashicons dashicons-category"></span>
                            <p><?php _e('Aucune spécialité définie.', 'formateur-manager-pro'); ?></p>
                            <a href="<?php echo admin_url('edit-tags.php?taxonomy=formateur_specialty&post_type=formateur_pro'); ?>" class="button">
                                <?php _e('Créer une spécialité', 'formateur-manager-pro'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Top villes -->
            <div class="fmp-dashboard-widget">
                <div class="fmp-widget-header">
                    <h3><?php _e('Répartition géographique', 'formateur-manager-pro'); ?></h3>
                </div>
                
                <div class="fmp-widget-content">
                    <?php if (!empty($dashboard_stats['top_cities'])): ?>
                        <div class="fmp-cities-list">
                            <?php foreach (array_slice($dashboard_stats['top_cities'], 0, 8) as $index => $city): ?>
                            <div class="fmp-city-item">
                                <span class="fmp-city-rank">#<?php echo $index + 1; ?></span>
                                <span class="fmp-city-name"><?php echo esc_html($city->ville); ?></span>
                                <span class="fmp-city-count"><?php echo $city->count; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="fmp-empty-state">
                            <span class="dashicons dashicons-location"></span>
                            <p><?php _e('Aucune donnée géographique disponible.', 'formateur-manager-pro'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="fmp-dashboard-widget">
                <div class="fmp-widget-header">
                    <h3><?php _e('Actions rapides', 'formateur-manager-pro'); ?></h3>
                </div>
                
                <div class="fmp-widget-content">
                    <div class="fmp-quick-actions">
                        <a href="<?php echo admin_url('post-new.php?post_type=formateur_pro'); ?>" class="fmp-quick-action">
                            <span class="dashicons dashicons-plus-alt"></span>
                            <span><?php _e('Ajouter un formateur', 'formateur-manager-pro'); ?></span>
                        </a>
                        
                        <a href="<?php echo admin_url('edit.php?post_type=formateur_pro&post_status=pending'); ?>" class="fmp-quick-action">
                            <span class="dashicons dashicons-clock"></span>
                            <span><?php _e('Valider les demandes', 'formateur-manager-pro'); ?></span>
                            <?php if ($dashboard_stats['pending'] > 0): ?>
                                <span class="fmp-badge"><?php echo $dashboard_stats['pending']; ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <a href="<?php echo admin_url('edit-tags.php?taxonomy=formateur_specialty&post_type=formateur_pro'); ?>" class="fmp-quick-action">
                            <span class="dashicons dashicons-category"></span>
                            <span><?php _e('Gérer les spécialités', 'formateur-manager-pro'); ?></span>
                        </a>
                        
                        <button onclick="bulkApproveFormateurs()" class="fmp-quick-action fmp-action-button">
                            <span class="dashicons dashicons-yes"></span>
                            <span><?php _e('Approuver en masse', 'formateur-manager-pro'); ?></span>
                        </button>
                        
                        <button onclick="sendNewsletterModal()" class="fmp-quick-action fmp-action-button">
                            <span class="dashicons dashicons-email-alt"></span>
                            <span><?php _e('Newsletter formateurs', 'formateur-manager-pro'); ?></span>
                        </button>
                        
                        <a href="<?php echo home_url('/formateurs/'); ?>" class="fmp-quick-action" target="_blank">
                            <span class="dashicons dashicons-external"></span>
                            <span><?php _e('Voir le site public', 'formateur-manager-pro'); ?></span>
                        </a>
                        
                        <button onclick="refreshDashboard()" class="fmp-quick-action fmp-action-button">
                            <span class="dashicons dashicons-update"></span>
                            <span><?php _e('Actualiser les données', 'formateur-manager-pro'); ?></span>
                        </button>
                        
                        <button onclick="exportFormateurs()" class="fmp-quick-action fmp-action-button">
                            <span class="dashicons dashicons-download"></span>
                            <span><?php _e('Exporter les données', 'formateur-manager-pro'); ?></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Activité récente -->
            <?php if (!empty($recent_activities)): ?>
            <div class="fmp-dashboard-widget">
                <div class="fmp-widget-header">
                    <h3><?php _e('Activité récente', 'formateur-manager-pro'); ?></h3>
                    <div class="fmp-widget-actions">
                        <button type="button" class="fmp-widget-refresh" onclick="refreshWidget('activity-log')">
                            <span class="dashicons dashicons-update"></span>
                        </button>
                    </div>
                </div>
                
                <div class="fmp-widget-content" id="activity-log-content">
                    <div class="fmp-activity-list">
                        <?php foreach (array_slice($recent_activities, 0, 10) as $activity): ?>
                        <div class="fmp-activity-item fmp-activity-<?php echo esc_attr($activity->level); ?>">
                            <div class="fmp-activity-icon">
                                <?php 
                                switch($activity->level) {
                                    case 'success': echo '<span class="dashicons dashicons-yes"></span>'; break;
                                    case 'error': echo '<span class="dashicons dashicons-no"></span>'; break;
                                    case 'info': echo '<span class="dashicons dashicons-info"></span>'; break;
                                    default: echo '<span class="dashicons dashicons-marker"></span>';
                                }
                                ?>
                            </div>
                            <div class="fmp-activity-content">
                                <div class="fmp-activity-message"><?php echo esc_html($activity->message); ?></div>
                                <div class="fmp-activity-time"><?php echo fmp_time_elapsed($activity->timestamp); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Notifications système -->
            <div class="fmp-dashboard-widget">
                <div class="fmp-widget-header">
                    <h3><?php _e('Notifications système', 'formateur-manager-pro'); ?></h3>
                </div>
                
                <div class="fmp-widget-content">
                    <div class="fmp-notifications">
                        <?php if ($dashboard_stats['pending'] > 0): ?>
                        <div class="fmp-notification fmp-notification--warning">
                            <span class="dashicons dashicons-warning"></span>
                            <div class="fmp-notification-content">
                                <strong><?php echo sprintf(_n('%d formateur en attente', '%d formateurs en attente', $dashboard_stats['pending'], 'formateur-manager-pro'), $dashboard_stats['pending']); ?></strong>
                                <p><?php _e('Des candidatures nécessitent votre validation.', 'formateur-manager-pro'); ?></p>
                            </div>
                            <a href="<?php echo admin_url('edit.php?post_type=formateur_pro&post_status=pending'); ?>" class="fmp-notification-action">
                                <?php _e('Valider', 'formateur-manager-pro'); ?>
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <?php 
                        // Vérifier les mises à jour disponibles
                        $current_version = get_option('fmp_version', '1.0.0');
                        if (version_compare($current_version, FMP_VERSION, '<')): ?>
                        <div class="fmp-notification fmp-notification--info">
                            <span class="dashicons dashicons-info"></span>
                            <div class="fmp-notification-content">
                                <strong><?php _e('Mise à jour disponible', 'formateur-manager-pro'); ?></strong>
                                <p><?php echo sprintf(__('Version %s disponible (actuel: %s)', 'formateur-manager-pro'), FMP_VERSION, $current_version); ?></p>
                            </div>
                            <button onclick="updatePlugin()" class="fmp-notification-action">
                                <?php _e('Mettre à jour', 'formateur-manager-pro'); ?>
                            </button>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!get_option('fmp_email_notifications', 'yes')): ?>
                        <div class="fmp-notification fmp-notification--warning">
                            <span class="dashicons dashicons-email"></span>
                            <div class="fmp-notification-content">
                                <strong><?php _e('Notifications désactivées', 'formateur-manager-pro'); ?></strong>
                                <p><?php _e('Les notifications par email sont désactivées.', 'formateur-manager-pro'); ?></p>
                            </div>
                            <a href="<?php echo admin_url('admin.php?page=formateur-settings'); ?>" class="fmp-notification-action">
                                <?php _e('Activer', 'formateur-manager-pro'); ?>
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <div class="fmp-notification fmp-notification--success">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <div class="fmp-notification-content">
                                <strong><?php _e('Plugin actif', 'formateur-manager-pro'); ?></strong>
                                <p><?php echo sprintf(__('Version %s installée et fonctionnelle.', 'formateur-manager-pro'), FMP_VERSION); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour l'envoi de newsletter -->
<div id="newsletter-modal" class="fmp-modal" style="display: none;">
    <div class="fmp-modal-content">
        <div class="fmp-modal-header">
            <h3><?php _e('Envoyer une newsletter', 'formateur-manager-pro'); ?></h3>
            <button type="button" class="fmp-modal-close" onclick="closeNewsletterModal()">&times;</button>
        </div>
        <div class="fmp-modal-body">
            <form id="newsletter-form">
                <div class="fmp-form-group">
                    <label for="newsletter-subject"><?php _e('Sujet', 'formateur-manager-pro'); ?></label>
                    <input type="text" id="newsletter-subject" class="regular-text" required>
                </div>
                <div class="fmp-form-group">
                    <label for="newsletter-content"><?php _e('Contenu', 'formateur-manager-pro'); ?></label>
                    <textarea id="newsletter-content" rows="10" class="large-text" required></textarea>
                </div>
                <div class="fmp-form-group">
                    <label>
                        <input type="checkbox" id="newsletter-test" value="1">
                        <?php _e('Envoyer d\'abord un test à mon adresse', 'formateur-manager-pro'); ?>
                    </label>
                </div>
            </form>
        </div>
        <div class="fmp-modal-footer">
            <button type="button" class="button" onclick="closeNewsletterModal()"><?php _e('Annuler', 'formateur-manager-pro'); ?></button>
            <button type="button" class="button button-primary" onclick="sendNewsletter()"><?php _e('Envoyer', 'formateur-manager-pro'); ?></button>
        </div>
    </div>
</div>

<!-- Inclusion des styles CSS -->
<link rel="stylesheet" href="<?php echo FMP_PLUGIN_URL; ?>assets/css/admin-dashboard.css">

<script>
// JavaScript pour le dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le graphique d'évolution
    initEvolutionChart();
    
    // Gestion du changement de période
    document.getElementById('fmp-chart-period')?.addEventListener('change', function() {
        updateChart(this.value);
    });
    
    // Auto-refresh des widgets toutes les 5 minutes
    setInterval(function() {
        refreshWidget('recent-formateurs');
        refreshWidget('activity-log');
    }, 300000); // 5 minutes
});

function initEvolutionChart() {
    const canvas = document.getElementById('fmp-inscriptions-chart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const evolutionData = <?php echo json_encode($dashboard_stats['evolution']); ?>;
    
    // Préparer les données pour le graphique
    const labels = evolutionData.map(item => {
        const mois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 
                     'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
        return mois[item.mois - 1] + ' ' + item.annee;
    });
    
    const data = evolutionData.map(item => item.inscriptions);
    
    // Créer le graphique simple (sans Chart.js pour éviter les dépendances)
    drawLineChart(ctx, labels, data);
}

function drawLineChart(ctx, labels, data) {
    const canvas = ctx.canvas;
    const width = canvas.width;
    const height = canvas.height;
    const padding = 50;
    
    // Nettoyer le canvas
    ctx.clearRect(0, 0, width, height);
    
    // Configuration
    const chartWidth = width - 2 * padding;
    const chartHeight = height - 2 * padding;
    const maxValue = Math.max(...data) || 1;
    
    // Dessiner les axes
    ctx.strokeStyle = '#c3c4c7';
    ctx.lineWidth = 1;
    
    // Axe Y
    ctx.beginPath();
    ctx.moveTo(padding, padding);
    ctx.lineTo(padding, height - padding);
    ctx.stroke();
    
    // Axe X
    ctx.beginPath();
    ctx.moveTo(padding, height - padding);
    ctx.lineTo(width - padding, height - padding);
    ctx.stroke();
    
    // Dessiner la ligne de données
    if (data.length > 1) {
        ctx.strokeStyle = '#2271b1';
        ctx.lineWidth = 3;
        ctx.beginPath();
        
        data.forEach((value, index) => {
            const x = padding + (index / (data.length - 1)) * chartWidth;
            const y = height - padding - (value / maxValue) * chartHeight;
            
            if (index === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        });
        
        ctx.stroke();
        
        // Dessiner les points
        ctx.fillStyle = '#2271b1';
        data.forEach((value, index) => {
            const x = padding + (index / (data.length - 1)) * chartWidth;
            const y = height - padding - (value / maxValue) * chartHeight;
            
            ctx.beginPath();
            ctx.arc(x, y, 4, 0, 2 * Math.PI);
            ctx.fill();
        });
    }
    
    // Ajouter les labels
    ctx.fillStyle = '#646970';
    ctx.font = '12px sans-serif';
    ctx.textAlign = 'center';
    
    labels.forEach((label, index) => {
        if (index % 2 === 0 || labels.length <= 6) { // Afficher un label sur deux si trop nombreux
            const x = padding + (index / (labels.length - 1)) * chartWidth;
            ctx.fillText(label, x, height - padding + 20);
        }
    });
}

function approveFormateur(formateurId) {
    if (!confirm('<?php echo esc_js(__('Êtes-vous sûr de vouloir approuver ce formateur ?', 'formateur-manager-pro')); ?>')) {
        return;
    }
    
    const data = new FormData();
    data.append('action', 'fmp_approve_formateur');
    data.append('formateur_id', formateurId);
    data.append('nonce', '<?php echo wp_create_nonce('fmp_admin_nonce'); ?>');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Mettre à jour l'affichage
            const item = document.querySelector(`[data-formateur-id="${formateurId}"]`);
            if (item) {
                const badge = item.querySelector('.fmp-status-badge');
                badge.className = 'fmp-status-badge fmp-status-active';
                badge.title = 'Actif';
                
                const approveBtn = item.querySelector('.fmp-btn-success');
                if (approveBtn) {
                    approveBtn.remove();
                }
            }
            
            showAdminNotice('success', result.data);
            refreshDashboard();
        } else {
            showAdminNotice('error', result.data);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAdminNotice('error', '<?php echo esc_js(__('Erreur de connexion', 'formateur-manager-pro')); ?>');
    });
}

function bulkApproveFormateurs() {
    if (!confirm('<?php echo esc_js(__('Êtes-vous sûr de vouloir approuver tous les formateurs en attente ?', 'formateur-manager-pro')); ?>')) {
        return;
    }
    
    const data = new FormData();
    data.append('action', 'fmp_bulk_approve_formateurs');
    data.append('nonce', '<?php echo wp_create_nonce('fmp_admin_nonce'); ?>');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showAdminNotice('success', result.data);
            refreshDashboard();
        } else {
            showAdminNotice('error', result.data);
        }
    });
}

function sendNewsletterModal() {
    document.getElementById('newsletter-modal').style.display = 'block';
}

function closeNewsletterModal() {
    document.getElementById('newsletter-modal').style.display = 'none';
    document.getElementById('newsletter-form').reset();
}

function sendNewsletter() {
    const subject = document.getElementById('newsletter-subject').value;
    const content = document.getElementById('newsletter-content').value;
    const isTest = document.getElementById('newsletter-test').checked;
    
    if (!subject || !content) {
        alert('<?php echo esc_js(__('Veuillez remplir tous les champs', 'formateur-manager-pro')); ?>');
        return;
    }
    
    const data = new FormData();
    data.append('action', 'fmp_send_newsletter');
    data.append('subject', subject);
    data.append('content', content);
    data.append('is_test', isTest ? '1' : '0');
    data.append('nonce', '<?php echo wp_create_nonce('fmp_admin_nonce'); ?>');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showAdminNotice('success', result.data);
            closeNewsletterModal();
        } else {
            showAdminNotice('error', result.data);
        }
    });
}

function sendEmailToFormateur(formateurId) {
    const data = new FormData();
    data.append('action', 'fmp_resend_registration_email');
    data.append('formateur_id', formateurId);
    data.append('nonce', '<?php echo wp_create_nonce('fmp_admin_nonce'); ?>');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showAdminNotice('success', result.data);
        } else {
            showAdminNotice('error', result.data);
        }
    });
}

function exportFormateurs() {
    window.location.href = '<?php echo admin_url('admin-ajax.php?action=fmp_export_formateurs&nonce=' . wp_create_nonce('fmp_admin_nonce')); ?>';
}

function refreshDashboard() {
    const data = new FormData();
    data.append('action', 'fmp_refresh_dashboard');
    data.append('nonce', '<?php echo wp_create_nonce('fmp_admin_nonce'); ?>');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        }
    });
}

function refreshWidget(widgetId) {
    const content = document.getElementById(widgetId + '-content');
    if (!content) return;
    
    content.style.opacity = '0.5';
    
    const data = new FormData();
    data.append('action', 'fmp_refresh_widget');
    data.append('widget', widgetId);
    data.append('nonce', '<?php echo wp_create_nonce('fmp_admin_nonce'); ?>');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            content.innerHTML = result.data;
        }
        content.style.opacity = '1';
    });
}

function showAdminNotice(type, message) {
    const notice = document.createElement('div');
    notice.className = `notice notice-${type} is-dismissible`;
    notice.innerHTML = `<p>${message}</p>`;
    
    const header = document.querySelector('.fmp-admin-header');
    header.parentNode.insertBefore(notice, header.nextSibling);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notice.parentNode) {
            notice.parentNode.removeChild(notice);
        }
    }, 5000);
}

function updatePlugin() {
    if (!confirm('<?php echo esc_js(__('Êtes-vous sûr de vouloir mettre à jour le plugin ?', 'formateur-manager-pro')); ?>')) {
        return;
    }
    
    showAdminNotice('info', '<?php echo esc_js(__('Fonctionnalité de mise à jour à implémenter', 'formateur-manager-pro')); ?>');
}

// Fermer la modal en cliquant à l'extérieur
window.onclick = function(event) {
    const modal = document.getElementById('newsletter-modal');
    if (event.target === modal) {
        closeNewsletterModal();
    }
}
</script>