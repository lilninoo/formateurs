<?php
/**
 * Plugin Name: TMM Formateur Manager
 * Plugin URI: https://votresite.com
 * Description: Plugin de gestion des formateurs avec design moderne
 * Version: 1.0.0
 * Author: Votre Nom
 * Text Domain: formateur-manager
 */

// Sécurité - Empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Constantes du plugin
define('FORMATEUR_MANAGER_VERSION', '1.0.0');
define('FORMATEUR_MANAGER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FORMATEUR_MANAGER_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Classe principale du plugin
 */
class FormateurManager {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
    }
    
    public function init() {
        // Créer le Custom Post Type
        $this->create_post_type();
        
        // Ajouter les shortcodes
        add_shortcode('formateur_register', array($this, 'shortcode_register_form'));
        add_shortcode('formateur_list', array($this, 'shortcode_formateur_list'));
        add_shortcode('formateur_home', array($this, 'shortcode_home_page'));
        
        // Actions AJAX
        add_action('wp_ajax_formateur_register', array($this, 'handle_ajax_register'));
        add_action('wp_ajax_nopriv_formateur_register', array($this, 'handle_ajax_register'));
        
        // Scripts et styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Menu admin
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        
        // Colonnes admin
        add_filter('manage_formateur_posts_columns', array($this, 'add_admin_columns'));
        add_action('manage_formateur_posts_custom_column', array($this, 'admin_column_content'), 10, 2);
    }
    
    /**
     * Shortcode de la page d'accueil ultra moderne - Style Stripe
     */
    public function shortcode_home_page($atts) {
        $atts = shortcode_atts(array(
            'search_url' => '/formateurs/',
            'register_url' => '/devenir-formateur/',
            'title' => 'Trouvez votre formateur expert',
            'subtitle' => 'La plateforme qui connecte professionnels et formateurs de qualité',
            'formateur_image' => '', // URL de l'image du formateur
        ), $atts);
        
        ob_start();
        ?>
        <div class="formateur-modern-home">
            <!-- Hero Section -->
            <section class="hero-section">
                <div class="hero-container">
                    <div class="hero-content">
                        <div class="hero-badge">
                            <i class="fas fa-star"></i>
                            <span>Plateforme #1</span>
                        </div>
                        
                        <h1 class="hero-title"><?php echo esc_html($atts['title']); ?></h1>
                        <p class="hero-description"><?php echo esc_html($atts['subtitle']); ?></p>
                        
                        <div class="hero-actions">
                            <a href="<?php echo esc_url($atts['search_url']); ?>" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                <span>Trouver un formateur</span>
                            </a>
                            <a href="<?php echo esc_url($atts['register_url']); ?>" class="btn btn-secondary">
                                <i class="fas fa-user-plus"></i>
                                <span>Devenir formateur</span>
                            </a>
                        </div>
                        
                        <div class="hero-stats">
                            <div class="stat">
                                <div class="stat-number"><?php echo $this->get_formateurs_count(); ?>+</div>
                                <div class="stat-label">Formateurs</div>
                            </div>
                            <div class="stat">
                                <div class="stat-number"><?php echo $this->get_specialites_count(); ?>+</div>
                                <div class="stat-label">Spécialités</div>
                            </div>
                            <div class="stat">
                                <div class="stat-number">98%</div>
                                <div class="stat-label">Satisfaction</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="hero-visual">
                        <div class="formateur-showcase">
                            <?php if (!empty($atts['formateur_image'])): ?>
                                <img src="<?php echo esc_url($atts['formateur_image']); ?>" alt="Formateur expert" class="formateur-photo">
                            <?php else: ?>
                                <div class="formateur-placeholder">
                                    <i class="fas fa-user-graduate"></i>
                                    <span>Votre photo ici</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="showcase-elements">
                                <div class="element element-1">
                                    <i class="fas fa-code"></i>
                                    <span>Développement</span>
                                </div>
                                <div class="element element-2">
                                    <i class="fas fa-bullhorn"></i>
                                    <span>Marketing</span>
                                </div>
                                <div class="element element-3">
                                    <i class="fas fa-palette"></i>
                                    <span>Design</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Features Section -->
            <section class="features-section">
                <div class="features-container">
                    <div class="features-grid">
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h3>Formateurs vérifiés</h3>
                            <p>Expertise validée et pédagogie confirmée</p>
                        </div>
                        
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <h3>Contact immédiat</h3>
                            <p>Échange direct sans intermédiaire</p>
                        </div>
                        
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <h3>100% sécurisé</h3>
                            <p>Données protégées et anonymisées</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- CTA Section -->
            <section class="cta-section">
                <div class="cta-container">
                    <div class="cta-content">
                        <h2>Prêt à démarrer ?</h2>
                        <p>Rejoignez des milliers de professionnels qui font confiance à notre plateforme</p>
                        <div class="cta-buttons">
                            <a href="<?php echo esc_url($atts['search_url']); ?>" class="btn btn-primary-large">
                                <i class="fas fa-rocket"></i>
                                <span>Commencer maintenant</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Compter le nombre de formateurs actifs
     */
    private function get_formateurs_count() {
        $count = wp_count_posts('formateur');
        return $count->publish ?: 0;
    }
    
    /**
     * Compter le nombre de spécialités uniques
     */
    private function get_specialites_count() {
        global $wpdb;
        $count = $wpdb->get_var("
            SELECT COUNT(DISTINCT meta_value) 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_formateur_specialite' 
            AND meta_value != ''
        ");
        return intval($count) ?: 5;
    }
    
    /**
     * Compter le nombre de villes uniques
     */
    private function get_cities_count() {
        global $wpdb;
        $count = $wpdb->get_var("
            SELECT COUNT(DISTINCT meta_value) 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_formateur_ville' 
            AND meta_value != ''
        ");
        return intval($count) ?: 12;
    }
    
    /**
     * Créer le Custom Post Type 'formateur'
     */
    public function create_post_type() {
        $labels = array(
            'name'               => 'Formateurs',
            'singular_name'      => 'Formateur',
            'menu_name'          => 'Formateurs',
            'add_new'            => 'Ajouter un formateur',
            'edit_item'          => 'Modifier le formateur',
        );
        
        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 30,
            'menu_icon'           => 'dashicons-businessman',
            'supports'            => array('title', 'editor'),
        );
        
        register_post_type('formateur', $args);
    }
    
    /**
     * Enqueue scripts et styles avec le design de la maquette
     */
    public function enqueue_scripts() {
        // Styles modernes de la maquette
        wp_enqueue_style('formateur-manager-css', FORMATEUR_MANAGER_PLUGIN_URL . 'assets/css/formateur-style.css', array(), FORMATEUR_MANAGER_VERSION);
        
        // JavaScript pour les interactions
        wp_enqueue_script('formateur-manager-js', FORMATEUR_MANAGER_PLUGIN_URL . 'assets/js/formateur-script.js', array('jquery'), FORMATEUR_MANAGER_VERSION, true);
        
        // Localisation AJAX
        wp_localize_script('formateur-manager-js', 'formateur_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('formateur_nonce'),
            'contact_email' => get_option('admin_email'), // Email de contact
        ));
    }
    
    /**
     * Shortcode du formulaire d'inscription - Version complète avec uploads
     */
    public function shortcode_register_form($atts) {
        ob_start();
        ?>
        <div class="formateur-register-container">
            <h2>📝 Inscription formateur</h2>
            <p style="margin-bottom: 30px; color: #666;">Rejoignez notre réseau de formateurs professionnels</p>
            
            <form id="formateur-register-form" class="formateur-form" enctype="multipart/form-data">
                <?php wp_nonce_field('formateur_register_nonce', 'formateur_nonce'); ?>
                
                <!-- Informations personnelles -->
                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Informations personnelles</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fm_prenom">Prénom *</label>
                            <input type="text" id="fm_prenom" name="prenom" required>
                        </div>
                        <div class="form-group">
                            <label for="fm_nom">Nom *</label>
                            <input type="text" id="fm_nom" name="nom" required>
                        </div>
                        <div class="form-group">
                            <label for="fm_email">E-mail *</label>
                            <input type="email" id="fm_email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="fm_telephone">Téléphone *</label>
                            <input type="tel" id="fm_telephone" name="telephone" required>
                        </div>
                        <div class="form-group">
                            <label for="fm_ville">Ville *</label>
                            <input type="text" id="fm_ville" name="ville" required>
                        </div>
                        <div class="form-group">
                            <label for="fm_pays">Pays</label>
                            <select id="fm_pays" name="pays">
                                <option value="FR">France</option>
                                <option value="BE">Belgique</option>
                                <option value="CH">Suisse</option>
                                <option value="CA">Canada</option>
                                <option value="LU">Luxembourg</option>
                                <option value="MC">Monaco</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Photo et CV -->
                <div class="form-section">
                    <h3><i class="fas fa-camera"></i> Photo et CV</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fm_photo">Photo de profil</label>
                            <div class="file-upload-container">
                                <input type="file" id="fm_photo" name="photo" accept="image/*" class="file-input">
                                <div class="file-upload-area" onclick="document.getElementById('fm_photo').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Cliquez pour ajouter votre photo</span>
                                    <small>JPG, PNG, GIF - Max 5MB</small>
                                </div>
                                <div class="file-preview" id="photo-preview"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="fm_cv">CV (PDF) *</label>
                            <div class="file-upload-container">
                                <input type="file" id="fm_cv" name="cv" accept=".pdf,.doc,.docx" class="file-input" required>
                                <div class="file-upload-area" onclick="document.getElementById('fm_cv').click()">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Cliquez pour ajouter votre CV</span>
                                    <small>PDF, DOC, DOCX - Max 10MB</small>
                                </div>
                                <div class="file-preview" id="cv-preview"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expertise et spécialités -->
                <div class="form-section">
                    <h3><i class="fas fa-graduation-cap"></i> Expertise et spécialités</h3>
                    
                    <div class="form-group full-width">
                        <label for="fm_specialite">Spécialité principale *</label>
                        <select id="fm_specialite" name="specialite" required>
                            <option value="">Choisir une spécialité</option>
                            
                            <optgroup label="🖥️ Informatique & Tech">
                                <option value="dev-web">Développement Web</option>
                                <option value="dev-mobile">Développement Mobile</option>
                                <option value="dev-logiciel">Développement Logiciel</option>
                                <option value="dev-jeux">Développement de Jeux</option>
                                <option value="cybersecurite">Cybersécurité</option>
                                <option value="securite-systemes">Sécurité des Systèmes</option>
                                <option value="data-science">Data Science</option>
                                <option value="data-analyst">Data Analyst</option>
                                <option value="big-data">Big Data</option>
                                <option value="intelligence-artificielle">Intelligence Artificielle</option>
                                <option value="machine-learning">Machine Learning</option>
                                <option value="deep-learning">Deep Learning</option>
                                <option value="blockchain">Blockchain</option>
                                <option value="devops">DevOps</option>
                                <option value="cloud-computing">Cloud Computing</option>
                                <option value="systemes-reseaux">Systèmes & Réseaux</option>
                                <option value="bases-donnees">Bases de Données</option>
                                <option value="architecture-logicielle">Architecture Logicielle</option>
                                <option value="iot">Internet des Objets (IoT)</option>
                                <option value="realite-virtuelle">Réalité Virtuelle/Augmentée</option>
                            </optgroup>
                            
                            <optgroup label="📊 Marketing & Communication">
                                <option value="marketing-digital">Marketing Digital</option>
                                <option value="seo-sem">SEO/SEM</option>
                                <option value="reseaux-sociaux">Réseaux Sociaux</option>
                                <option value="content-marketing">Content Marketing</option>
                                <option value="email-marketing">Email Marketing</option>
                                <option value="marketing-automation">Marketing Automation</option>
                                <option value="growth-hacking">Growth Hacking</option>
                                <option value="analytics">Analytics & Mesure</option>
                                <option value="publicite-digitale">Publicité Digitale</option>
                                <option value="communication">Communication</option>
                                <option value="relations-publiques">Relations Publiques</option>
                                <option value="brand-management">Brand Management</option>
                            </optgroup>
                            
                            <optgroup label="🎨 Design & Créativité">
                                <option value="design-graphique">Design Graphique</option>
                                <option value="design-web">Design Web (UI/UX)</option>
                                <option value="design-mobile">Design Mobile</option>
                                <option value="motion-design">Motion Design</option>
                                <option value="design-produit">Design Produit</option>
                                <option value="architecture-interieure">Architecture d'Intérieur</option>
                                <option value="photographie">Photographie</option>
                                <option value="videographie">Vidéographie</option>
                                <option value="illustration">Illustration</option>
                                <option value="design-industriel">Design Industriel</option>
                                <option value="packaging">Packaging</option>
                            </optgroup>
                            
                            <optgroup label="💼 Business & Management">
                                <option value="management">Management</option>
                                <option value="leadership">Leadership</option>
                                <option value="gestion-projet">Gestion de Projet</option>
                                <option value="agile-scrum">Agile & Scrum</option>
                                <option value="entrepreneuriat">Entrepreneuriat</option>
                                <option value="strategie-entreprise">Stratégie d'Entreprise</option>
                                <option value="finance">Finance</option>
                                <option value="comptabilite">Comptabilité</option>
                                <option value="ressources-humaines">Ressources Humaines</option>
                                <option value="vente">Vente</option>
                                <option value="negociation">Négociation</option>
                                <option value="customer-success">Customer Success</option>
                                <option value="lean-management">Lean Management</option>
                            </optgroup>
                            
                            <optgroup label="🗣️ Langues & Soft Skills">
                                <option value="anglais">Anglais</option>
                                <option value="espagnol">Espagnol</option>
                                <option value="allemand">Allemand</option>
                                <option value="italien">Italien</option>
                                <option value="chinois">Chinois</option>
                                <option value="japonais">Japonais</option>
                                <option value="arabe">Arabe</option>
                                <option value="communication-orale">Communication Orale</option>
                                <option value="prise-parole">Prise de Parole en Public</option>
                                <option value="redaction">Rédaction</option>
                                <option value="storytelling">Storytelling</option>
                            </optgroup>
                            
                            <optgroup label="📚 Éducation & Formation">
                                <option value="pedagogie">Pédagogie</option>
                                <option value="formation-formateurs">Formation de Formateurs</option>
                                <option value="e-learning">E-learning</option>
                                <option value="conception-pedagogique">Conception Pédagogique</option>
                                <option value="evaluation">Évaluation</option>
                            </optgroup>
                            
                            <optgroup label="🔬 Sciences & Recherche">
                                <option value="mathematiques">Mathématiques</option>
                                <option value="statistiques">Statistiques</option>
                                <option value="physique">Physique</option>
                                <option value="chimie">Chimie</option>
                                <option value="biologie">Biologie</option>
                                <option value="recherche">Recherche & Développement</option>
                            </optgroup>
                            
                            <optgroup label="⚖️ Juridique & Conformité">
                                <option value="droit">Droit</option>
                                <option value="rgpd">RGPD</option>
                                <option value="conformite">Conformité</option>
                                <option value="propriete-intellectuelle">Propriété Intellectuelle</option>
                            </optgroup>
                            
                            <optgroup label="🏥 Santé & Bien-être">
                                <option value="sante">Santé</option>
                                <option value="bien-etre">Bien-être</option>
                                <option value="nutrition">Nutrition</option>
                                <option value="sport">Sport & Fitness</option>
                                <option value="yoga">Yoga & Méditation</option>
                            </optgroup>
                            
                            <optgroup label="🎵 Arts & Culture">
                                <option value="musique">Musique</option>
                                <option value="theatre">Théâtre</option>
                                <option value="danse">Danse</option>
                                <option value="ecriture">Écriture</option>
                                <option value="culture">Culture</option>
                            </optgroup>
                            
                            <optgroup label="🔧 Technique & Industrie">
                                <option value="electricite">Électricité</option>
                                <option value="mecanique">Mécanique</option>
                                <option value="electronique">Électronique</option>
                                <option value="automation">Automation</option>
                                <option value="maintenance">Maintenance</option>
                            </optgroup>
                            
                            <optgroup label="🌍 Environnement & Durabilité">
                                <option value="environnement">Environnement</option>
                                <option value="developpement-durable">Développement Durable</option>
                                <option value="energie-renouvelable">Énergies Renouvelables</option>
                                <option value="rse">RSE</option>
                            </optgroup>
                            
                            <option value="autre">Autre spécialité</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label>Compétences techniques (sélectionnez toutes celles qui s'appliquent)</label>
                        <div class="competences-container">
                            
                            <!-- Langages de programmation -->
                            <div class="competence-category">
                                <h4><i class="fas fa-code"></i> Langages de programmation</h4>
                                <div class="checkbox-group">
                                    <div class="checkbox-item"><input type="checkbox" id="comp_javascript" name="competences[]" value="JavaScript"><label for="comp_javascript">JavaScript</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_python" name="competences[]" value="Python"><label for="comp_python">Python</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_java" name="competences[]" value="Java"><label for="comp_java">Java</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_csharp" name="competences[]" value="C#"><label for="comp_csharp">C#</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_cpp" name="competences[]" value="C++"><label for="comp_cpp">C++</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_c" name="competences[]" value="C"><label for="comp_c">C</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_php" name="competences[]" value="PHP"><label for="comp_php">PHP</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_ruby" name="competences[]" value="Ruby"><label for="comp_ruby">Ruby</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_go" name="competences[]" value="Go"><label for="comp_go">Go</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_rust" name="competences[]" value="Rust"><label for="comp_rust">Rust</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_swift" name="competences[]" value="Swift"><label for="comp_swift">Swift</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_kotlin" name="competences[]" value="Kotlin"><label for="comp_kotlin">Kotlin</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_typescript" name="competences[]" value="TypeScript"><label for="comp_typescript">TypeScript</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_dart" name="competences[]" value="Dart"><label for="comp_dart">Dart</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_scala" name="competences[]" value="Scala"><label for="comp_scala">Scala</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_r" name="competences[]" value="R"><label for="comp_r">R</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_matlab" name="competences[]" value="MATLAB"><label for="comp_matlab">MATLAB</label></div>
                                </div>
                            </div>

                            <!-- Frameworks & Librairies -->
                            <div class="competence-category">
                                <h4><i class="fas fa-layer-group"></i> Frameworks & Librairies</h4>
                                <div class="checkbox-group">
                                    <div class="checkbox-item"><input type="checkbox" id="comp_react" name="competences[]" value="React"><label for="comp_react">React</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_vue" name="competences[]" value="Vue.js"><label for="comp_vue">Vue.js</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_angular" name="competences[]" value="Angular"><label for="comp_angular">Angular</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_nodejs" name="competences[]" value="Node.js"><label for="comp_nodejs">Node.js</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_express" name="competences[]" value="Express.js"><label for="comp_express">Express.js</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_django" name="competences[]" value="Django"><label for="comp_django">Django</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_flask" name="competences[]" value="Flask"><label for="comp_flask">Flask</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_laravel" name="competences[]" value="Laravel"><label for="comp_laravel">Laravel</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_symfony" name="competences[]" value="Symfony"><label for="comp_symfony">Symfony</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_spring" name="competences[]" value="Spring"><label for="comp_spring">Spring</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_dotnet" name="competences[]" value=".NET"><label for="comp_dotnet">.NET</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_rails" name="competences[]" value="Ruby on Rails"><label for="comp_rails">Ruby on Rails</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_nextjs" name="competences[]" value="Next.js"><label for="comp_nextjs">Next.js</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_nuxtjs" name="competences[]" value="Nuxt.js"><label for="comp_nuxtjs">Nuxt.js</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_flutter" name="competences[]" value="Flutter"><label for="comp_flutter">Flutter</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_reactnative" name="competences[]" value="React Native"><label for="comp_reactnative">React Native</label></div>
                                </div>
                            </div>

                            <!-- Technologies Web -->
                            <div class="competence-category">
                                <h4><i class="fas fa-globe"></i> Technologies Web</h4>
                                <div class="checkbox-group">
                                    <div class="checkbox-item"><input type="checkbox" id="comp_html" name="competences[]" value="HTML/CSS"><label for="comp_html">HTML/CSS</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_sass" name="competences[]" value="SASS/SCSS"><label for="comp_sass">SASS/SCSS</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_tailwind" name="competences[]" value="Tailwind CSS"><label for="comp_tailwind">Tailwind CSS</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_bootstrap" name="competences[]" value="Bootstrap"><label for="comp_bootstrap">Bootstrap</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_webpack" name="competences[]" value="Webpack"><label for="comp_webpack">Webpack</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_vite" name="competences[]" value="Vite"><label for="comp_vite">Vite</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_graphql" name="competences[]" value="GraphQL"><label for="comp_graphql">GraphQL</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_rest" name="competences[]" value="REST API"><label for="comp_rest">REST API</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_websocket" name="competences[]" value="WebSocket"><label for="comp_websocket">WebSocket</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_pwa" name="competences[]" value="PWA"><label for="comp_pwa">PWA</label></div>
                                </div>
                            </div>

                            <!-- Bases de données -->
                            <div class="competence-category">
                                <h4><i class="fas fa-database"></i> Bases de données</h4>
                                <div class="checkbox-group">
                                    <div class="checkbox-item"><input type="checkbox" id="comp_mysql" name="competences[]" value="MySQL"><label for="comp_mysql">MySQL</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_postgresql" name="competences[]" value="PostgreSQL"><label for="comp_postgresql">PostgreSQL</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_mongodb" name="competences[]" value="MongoDB"><label for="comp_mongodb">MongoDB</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_redis" name="competences[]" value="Redis"><label for="comp_redis">Redis</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_elasticsearch" name="competences[]" value="Elasticsearch"><label for="comp_elasticsearch">Elasticsearch</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_oracle" name="competences[]" value="Oracle"><label for="comp_oracle">Oracle</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_sqlserver" name="competences[]" value="SQL Server"><label for="comp_sqlserver">SQL Server</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_sqlite" name="competences[]" value="SQLite"><label for="comp_sqlite">SQLite</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_cassandra" name="competences[]" value="Cassandra"><label for="comp_cassandra">Cassandra</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_dynamodb" name="competences[]" value="DynamoDB"><label for="comp_dynamodb">DynamoDB</label></div>
                                </div>
                            </div>

                            <!-- Cloud & DevOps -->
                            <div class="competence-category">
                                <h4><i class="fas fa-cloud"></i> Cloud & DevOps</h4>
                                <div class="checkbox-group">
                                    <div class="checkbox-item"><input type="checkbox" id="comp_aws" name="competences[]" value="AWS"><label for="comp_aws">AWS</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_azure" name="competences[]" value="Azure"><label for="comp_azure">Azure</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_gcp" name="competences[]" value="Google Cloud"><label for="comp_gcp">Google Cloud</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_docker" name="competences[]" value="Docker"><label for="comp_docker">Docker</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_kubernetes" name="competences[]" value="Kubernetes"><label for="comp_kubernetes">Kubernetes</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_jenkins" name="competences[]" value="Jenkins"><label for="comp_jenkins">Jenkins</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_gitlab" name="competences[]" value="GitLab CI"><label for="comp_gitlab">GitLab CI</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_github" name="competences[]" value="GitHub Actions"><label for="comp_github">GitHub Actions</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_terraform" name="competences[]" value="Terraform"><label for="comp_terraform">Terraform</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_ansible" name="competences[]" value="Ansible"><label for="comp_ansible">Ansible</label></div>
                                </div>
                            </div>

                            <!-- Cybersécurité -->
                            <div class="competence-category">
                                <h4><i class="fas fa-shield-alt"></i> Cybersécurité</h4>
                                <div class="checkbox-group">
                                    <div class="checkbox-item"><input type="checkbox" id="comp_penetration" name="competences[]" value="Tests de pénétration"><label for="comp_penetration">Tests de pénétration</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_ethical_hacking" name="competences[]" value="Ethical Hacking"><label for="comp_ethical_hacking">Ethical Hacking</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_iso27001" name="competences[]" value="ISO 27001"><label for="comp_iso27001">ISO 27001</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_cissp" name="competences[]" value="CISSP"><label for="comp_cissp">CISSP</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_ceh" name="competences[]" value="CEH"><label for="comp_ceh">CEH</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_cryptographie" name="competences[]" value="Cryptographie"><label for="comp_cryptographie">Cryptographie</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_firewall" name="competences[]" value="Firewall"><label for="comp_firewall">Firewall</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_ids" name="competences[]" value="IDS/IPS"><label for="comp_ids">IDS/IPS</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_siem" name="competences[]" value="SIEM"><label for="comp_siem">SIEM</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_incident" name="competences[]" value="Réponse aux incidents"><label for="comp_incident">Réponse aux incidents</label></div>
                                </div>
                            </div>

                            <!-- Data Science & IA -->
                            <div class="competence-category">
                                <h4><i class="fas fa-brain"></i> Data Science & IA</h4>
                                <div class="checkbox-group">
                                    <div class="checkbox-item"><input type="checkbox" id="comp_pandas" name="competences[]" value="Pandas"><label for="comp_pandas">Pandas</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_numpy" name="competences[]" value="NumPy"><label for="comp_numpy">NumPy</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_sklearn" name="competences[]" value="Scikit-learn"><label for="comp_sklearn">Scikit-learn</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_tensorflow" name="competences[]" value="TensorFlow"><label for="comp_tensorflow">TensorFlow</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_pytorch" name="competences[]" value="PyTorch"><label for="comp_pytorch">PyTorch</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_keras" name="competences[]" value="Keras"><label for="comp_keras">Keras</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_jupyter" name="competences[]" value="Jupyter"><label for="comp_jupyter">Jupyter</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_tableau" name="competences[]" value="Tableau"><label for="comp_tableau">Tableau</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_powerbi" name="competences[]" value="Power BI"><label for="comp_powerbi">Power BI</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_spark" name="competences[]" value="Apache Spark"><label for="comp_spark">Apache Spark</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_hadoop" name="competences[]" value="Hadoop"><label for="comp_hadoop">Hadoop</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_nlp" name="competences[]" value="NLP"><label for="comp_nlp">NLP</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_computer_vision" name="competences[]" value="Computer Vision"><label for="comp_computer_vision">Computer Vision</label></div>
                                </div>
                            </div>

                            <!-- Outils Design -->
                            <div class="competence-category">
                                <h4><i class="fas fa-palette"></i> Outils Design</h4>
                                <div class="checkbox-group">
                                    <div class="checkbox-item"><input type="checkbox" id="comp_photoshop" name="competences[]" value="Photoshop"><label for="comp_photoshop">Photoshop</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_illustrator" name="competences[]" value="Illustrator"><label for="comp_illustrator">Illustrator</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_indesign" name="competences[]" value="InDesign"><label for="comp_indesign">InDesign</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_figma" name="competences[]" value="Figma"><label for="comp_figma">Figma</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_sketch" name="competences[]" value="Sketch"><label for="comp_sketch">Sketch</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_xd" name="competences[]" value="Adobe XD"><label for="comp_xd">Adobe XD</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_canva" name="competences[]" value="Canva"><label for="comp_canva">Canva</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_aftereffects" name="competences[]" value="After Effects"><label for="comp_aftereffects">After Effects</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_premiere" name="competences[]" value="Premiere Pro"><label for="comp_premiere">Premiere Pro</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_blender" name="competences[]" value="Blender"><label for="comp_blender">Blender</label></div>
                                </div>
                            </div>

                            <!-- Marketing Digital -->
                            <div class="competence-category">
                                <h4><i class="fas fa-bullhorn"></i> Marketing Digital</h4>
                                <div class="checkbox-group">
                                    <div class="checkbox-item"><input type="checkbox" id="comp_seo" name="competences[]" value="SEO"><label for="comp_seo">SEO</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_sem" name="competences[]" value="SEM"><label for="comp_sem">SEM</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_googleads" name="competences[]" value="Google Ads"><label for="comp_googleads">Google Ads</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_facebookads" name="competences[]" value="Facebook Ads"><label for="comp_facebookads">Facebook Ads</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_linkedinads" name="competences[]" value="LinkedIn Ads"><label for="comp_linkedinads">LinkedIn Ads</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_analytics" name="competences[]" value="Google Analytics"><label for="comp_analytics">Google Analytics</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_tagmanager" name="competences[]" value="Google Tag Manager"><label for="comp_tagmanager">Google Tag Manager</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_mailchimp" name="competences[]" value="MailChimp"><label for="comp_mailchimp">MailChimp</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_hubspot" name="competences[]" value="HubSpot"><label for="comp_hubspot">HubSpot</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_salesforce" name="competences[]" value="Salesforce"><label for="comp_salesforce">Salesforce</label></div>
                                </div>
                            </div>

                            <!-- Soft Skills -->
                            <div class="competence-category">
                                <h4><i class="fas fa-users"></i> Soft Skills</h4>
                                <div class="checkbox-group">
                                    <div class="checkbox-item"><input type="checkbox" id="comp_leadership2" name="competences[]" value="Leadership"><label for="comp_leadership2">Leadership</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_communication2" name="competences[]" value="Communication"><label for="comp_communication2">Communication</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_teamwork" name="competences[]" value="Travail d'équipe"><label for="comp_teamwork">Travail d'équipe</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_problem_solving" name="competences[]" value="Résolution de problèmes"><label for="comp_problem_solving">Résolution de problèmes</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_creativity" name="competences[]" value="Créativité"><label for="comp_creativity">Créativité</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_adaptability" name="competences[]" value="Adaptabilité"><label for="comp_adaptability">Adaptabilité</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_time_management" name="competences[]" value="Gestion du temps"><label for="comp_time_management">Gestion du temps</label></div>
                                    <div class="checkbox-item"><input type="checkbox" id="comp_emotional_intelligence" name="competences[]" value="Intelligence émotionnelle"><label for="comp_emotional_intelligence">Intelligence émotionnelle</label></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expérience -->
                <div class="form-section">
                    <h3><i class="fas fa-briefcase"></i> Expérience et formation</h3>
                    
                    <div class="form-group full-width">
                        <label for="fm_experience">Années d'expérience *</label>
                        <select id="fm_experience" name="experience" required>
                            <option value="">Sélectionner</option>
                            <option value="debutant">Débutant (0-1 an)</option>
                            <option value="junior">Junior (1-3 ans)</option>
                            <option value="confirme">Confirmé (3-5 ans)</option>
                            <option value="senior">Senior (5-10 ans)</option>
                            <option value="expert">Expert (10+ ans)</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="fm_biographie">Présentation professionnelle *</label>
                        <textarea id="fm_biographie" name="biographie" rows="6" placeholder="Décrivez votre parcours, vos expériences, vos réalisations marquantes..." required></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label for="fm_formations_donnees">Formations déjà dispensées</label>
                        <textarea id="fm_formations_donnees" name="formations_donnees" rows="4" placeholder="Listez les formations que vous avez déjà données (entreprises, organismes, nombre de participants, etc.)"></textarea>
                    </div>
                </div>

                <!-- Disponibilités -->
                <div class="form-section">
                    <h3><i class="fas fa-calendar-alt"></i> Disponibilités et préférences</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Modalités de formation *</label>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="modalite_presentiel" name="modalites[]" value="Présentiel" required>
                                    <label for="modalite_presentiel">Présentiel</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="modalite_distanciel" name="modalites[]" value="Distanciel">
                                    <label for="modalite_distanciel">Distanciel</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="modalite_hybride" name="modalites[]" value="Hybride">
                                    <label for="modalite_hybride">Hybride</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Types de public *</label>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="public_entreprise" name="publics[]" value="Entreprises" required>
                                    <label for="public_entreprise">Entreprises</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="public_particuliers" name="publics[]" value="Particuliers">
                                    <label for="public_particuliers">Particuliers</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="public_etudiants" name="publics[]" value="Étudiants">
                                    <label for="public_etudiants">Étudiants</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="public_demandeurs" name="publics[]" value="Demandeurs d'emploi">
                                    <label for="public_demandeurs">Demandeurs d'emploi</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="fm_tarif_jour">Tarif journalier indicatif (€) *</label>
                        <select id="fm_tarif_jour" name="tarif_jour" required>
                            <option value="">Sélectionner une fourchette</option>
                            <option value="200-400">200€ - 400€</option>
                            <option value="400-600">400€ - 600€</option>
                            <option value="600-800">600€ - 800€</option>
                            <option value="800-1000">800€ - 1000€</option>
                            <option value="1000-1500">1000€ - 1500€</option>
                            <option value="1500+">1500€+</option>
                        </select>
                    </div>
                </div>

                <!-- RGPD -->
                <div class="form-section">
                    <h3><i class="fas fa-shield-alt"></i> Consentements</h3>
                    
                    <div class="form-group full-width checkbox-consent">
                        <input type="checkbox" id="fm_rgpd" name="rgpd_consent" required>
                        <label for="fm_rgpd">J'accepte que mes données soient traitées selon la politique de confidentialité *</label>
                    </div>
                    
                    <div class="form-group full-width checkbox-consent">
                        <input type="checkbox" id="fm_newsletter" name="newsletter_consent">
                        <label for="fm_newsletter">J'accepte de recevoir des informations sur les opportunités de formation</label>
                    </div>
                </div>

                <button type="submit" class="btn">🚀 S'inscrire comme formateur</button>
                <div class="form-messages"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Shortcode de la liste des formateurs - Design de la maquette
     */
    public function shortcode_formateur_list($atts) {
        $atts = shortcode_atts(array(
            'limit' => 12,
            'show_search' => 'true'
        ), $atts);
        
        ob_start();
        
        // Récupérer les formateurs actifs
        $formateurs = get_posts(array(
            'post_type' => 'formateur',
            'post_status' => 'publish',
            'posts_per_page' => intval($atts['limit']),
            'meta_query' => array(
                array(
                    'key' => '_formateur_status',
                    'value' => 'active',
                    'compare' => '='
                )
            )
        ));
        ?>
        
        <div class="formateur-list-container">
            <?php if ($atts['show_search'] === 'true'): ?>
            <div class="search-bar">
                <input type="text" id="formateur-search" placeholder="🔍 Rechercher par nom ou mot-clé...">
                <select id="formateur-filter-specialite">
                    <option value="">Toutes les spécialités</option>
                    <option value="web">Développement Web</option>
                    <option value="marketing">Marketing Digital</option>
                    <option value="design">Design Graphique</option>
                    <option value="management">Management</option>
                    <option value="langues">Langues</option>
                </select>
                <select id="formateur-filter-ville">
                    <option value="">Toutes les villes</option>
                    <?php
                    $villes = $this->get_available_cities();
                    foreach ($villes as $ville) {
                        echo '<option value="' . esc_attr($ville) . '">' . esc_html($ville) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="formateurs-grid">
                <?php if ($formateurs): ?>
                    <?php foreach ($formateurs as $formateur): ?>
                        <?php echo $this->render_formateur_card($formateur->ID); ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-formateurs">Aucun formateur trouvé.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Rendu d'une carte formateur identique à la maquette
     */
    private function render_formateur_card($post_id) {
        $prenom = get_post_meta($post_id, '_formateur_prenom', true);
        $nom = get_post_meta($post_id, '_formateur_nom', true);
        $specialite = get_post_meta($post_id, '_formateur_specialite', true);
        $ville = get_post_meta($post_id, '_formateur_ville', true);
        $competences = get_post_meta($post_id, '_formateur_competences', true);
        $biographie = get_post_meta($post_id, '_formateur_biographie', true);
        
        // Créer les initiales
        $initiales = strtoupper(substr($prenom, 0, 1) . '.' . substr($nom, 0, 1));
        
        // Spécialités traduites
        $specialites_fr = array(
            'web' => 'Développement Web',
            'marketing' => 'Marketing Digital',
            'design' => 'Design Graphique',
            'management' => 'Management',
            'langues' => 'Langues'
        );
        
        $specialite_label = isset($specialites_fr[$specialite]) ? $specialites_fr[$specialite] : $specialite;
        
        ob_start();
        ?>
        <div class="formateur-card" data-specialite="<?php echo esc_attr($specialite); ?>" data-ville="<?php echo esc_attr($ville); ?>">
            <div class="formateur-header">
                <div class="formateur-avatar"><?php echo esc_html($initiales); ?></div>
                <div class="formateur-info">
                    <h3>Formateur <?php echo esc_html($initiales); ?></h3>
                    <div class="formateur-specialite"><?php echo esc_html($specialite_label); ?></div>
                </div>
            </div>
            
            <?php if ($biographie): ?>
                <p style="color: #666; margin-bottom: 15px;"><?php echo esc_html(wp_trim_words($biographie, 20)); ?></p>
            <?php endif; ?>
            
            <?php if ($competences): ?>
                <div class="formateur-skills">
                    <?php 
                    $comp_array = is_array($competences) ? $competences : explode(',', $competences);
                    foreach ($comp_array as $comp): 
                    ?>
                        <span class="skill-tag"><?php echo esc_html(trim($comp)); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                <?php if ($ville): ?>
                    <small style="color: #666;">📍 <?php echo esc_html($ville); ?></small>
                <?php else: ?>
                    <small></small>
                <?php endif; ?>
                <div class="formateur-contact">
                    <button class="btn-contact" onclick="contactFormateur('<?php echo esc_js($initiales); ?>', '<?php echo esc_js($specialite_label); ?>')">📞 Demander contact</button>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Gestion AJAX de l'inscription complète avec uploads
     */
    public function handle_ajax_register() {
        if (!wp_verify_nonce($_POST['formateur_nonce'], 'formateur_register_nonce')) {
            wp_send_json_error('Erreur de sécurité');
        }
        
        // Récupération et validation des données de base
        $prenom = sanitize_text_field($_POST['prenom']);
        $nom = sanitize_text_field($_POST['nom']);
        $email = sanitize_email($_POST['email']);
        $telephone = sanitize_text_field($_POST['telephone']);
        $ville = sanitize_text_field($_POST['ville']);
        $pays = sanitize_text_field($_POST['pays']);
        $specialite = sanitize_text_field($_POST['specialite']);
        $experience = sanitize_text_field($_POST['experience']);
        $competences = isset($_POST['competences']) ? array_map('sanitize_text_field', $_POST['competences']) : array();
        $biographie = sanitize_textarea_field($_POST['biographie']);
        $formations_donnees = sanitize_textarea_field($_POST['formations_donnees']);
        $modalites = isset($_POST['modalites']) ? array_map('sanitize_text_field', $_POST['modalites']) : array();
        $publics = isset($_POST['publics']) ? array_map('sanitize_text_field', $_POST['publics']) : array();
        $tarif_jour = sanitize_text_field($_POST['tarif_jour']);
        $rgpd_consent = isset($_POST['rgpd_consent']) ? true : false;
        $newsletter_consent = isset($_POST['newsletter_consent']) ? true : false;
        
        // Validation des champs obligatoires
        if (empty($prenom) || empty($nom) || empty($email) || empty($telephone) || 
            empty($ville) || empty($specialite) || empty($experience) || 
            empty($biographie) || empty($tarif_jour) || !$rgpd_consent ||
            empty($modalites) || empty($publics)) {
            wp_send_json_error('Veuillez remplir tous les champs obligatoires et accepter la politique de confidentialité.');
        }
        
        // Vérifier si l'email existe déjà
        $existing = get_posts(array(
            'post_type' => 'formateur',
            'meta_query' => array(
                array(
                    'key' => '_formateur_email',
                    'value' => $email,
                    'compare' => '='
                )
            )
        ));
        
        if (!empty($existing)) {
            wp_send_json_error('Un formateur avec cet e-mail est déjà inscrit.');
        }
        
        // Gestion des uploads de fichiers
        $uploaded_files = array();
        
        // Upload du CV (obligatoire)
        if (!empty($_FILES['cv']['name'])) {
            $cv_upload = $this->handle_file_upload('cv', array('pdf', 'doc', 'docx'), 10 * 1024 * 1024); // 10MB max
            if (is_wp_error($cv_upload)) {
                wp_send_json_error('Erreur lors de l\'upload du CV : ' . $cv_upload->get_error_message());
            }
            $uploaded_files['cv'] = $cv_upload;
        } else {
            wp_send_json_error('Le CV est obligatoire.');
        }
        
        // Upload de la photo (optionnel)
        if (!empty($_FILES['photo']['name'])) {
            $photo_upload = $this->handle_file_upload('photo', array('jpg', 'jpeg', 'png', 'gif'), 5 * 1024 * 1024); // 5MB max
            if (is_wp_error($photo_upload)) {
                wp_send_json_error('Erreur lors de l\'upload de la photo : ' . $photo_upload->get_error_message());
            }
            $uploaded_files['photo'] = $photo_upload;
        }
        
        // Créer le post
        $post_data = array(
            'post_title' => $prenom . ' ' . $nom,
            'post_type' => 'formateur',
            'post_status' => 'publish',
            'post_content' => $biographie
        );
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id) {
            // Sauvegarder toutes les métadonnées
            update_post_meta($post_id, '_formateur_prenom', $prenom);
            update_post_meta($post_id, '_formateur_nom', $nom);
            update_post_meta($post_id, '_formateur_email', $email);
            update_post_meta($post_id, '_formateur_telephone', $telephone);
            update_post_meta($post_id, '_formateur_ville', $ville);
            update_post_meta($post_id, '_formateur_pays', $pays);
            update_post_meta($post_id, '_formateur_specialite', $specialite);
            update_post_meta($post_id, '_formateur_experience', $experience);
            update_post_meta($post_id, '_formateur_competences', $competences);
            update_post_meta($post_id, '_formateur_biographie', $biographie);
            update_post_meta($post_id, '_formateur_formations_donnees', $formations_donnees);
            update_post_meta($post_id, '_formateur_modalites', $modalites);
            update_post_meta($post_id, '_formateur_publics', $publics);
            update_post_meta($post_id, '_formateur_tarif_jour', $tarif_jour);
            update_post_meta($post_id, '_formateur_status', 'active');
            update_post_meta($post_id, '_formateur_date_inscription', current_time('mysql'));
            update_post_meta($post_id, '_formateur_newsletter_consent', $newsletter_consent);
            
            // Sauvegarder les fichiers uploadés
            if (isset($uploaded_files['cv'])) {
                update_post_meta($post_id, '_formateur_cv', $uploaded_files['cv']);
            }
            if (isset($uploaded_files['photo'])) {
                update_post_meta($post_id, '_formateur_photo', $uploaded_files['photo']);
                set_post_thumbnail($post_id, $uploaded_files['photo']['attachment_id']);
            }
            
            // Envoyer email de confirmation avec CV en pièce jointe
            $this->send_confirmation_email_with_attachments($email, $prenom, $nom, $uploaded_files);
            
            wp_send_json_success('Inscription réussie ! Votre profil complet est maintenant visible avec votre CV et photo.');
        } else {
            wp_send_json_error('Erreur lors de l\'inscription. Veuillez réessayer.');
        }
    }
    
    /**
     * Gérer l'upload de fichiers
     */
    private function handle_file_upload($field_name, $allowed_extensions, $max_size) {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        
        $file = $_FILES[$field_name];
        
        // Vérifier la taille
        if ($file['size'] > $max_size) {
            return new WP_Error('file_too_large', 'Le fichier est trop volumineux.');
        }
        
        // Vérifier l'extension
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_extensions)) {
            return new WP_Error('invalid_extension', 'Type de fichier non autorisé.');
        }
        
        // Configurer l'upload
        $upload_overrides = array(
            'test_form' => false,
            'mimes' => array(
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'jpg|jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif'
            )
        );
        
        // Effectuer l'upload
        $uploaded_file = wp_handle_upload($file, $upload_overrides);
        
        if (isset($uploaded_file['error'])) {
            return new WP_Error('upload_error', $uploaded_file['error']);
        }
        
        // Créer l'attachement dans la médiathèque
        $attachment = array(
            'guid' => $uploaded_file['url'],
            'post_mime_type' => $uploaded_file['type'],
            'post_title' => sanitize_file_name(pathinfo($file['name'], PATHINFO_FILENAME)),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);
        
        if (!is_wp_error($attachment_id)) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
            wp_update_attachment_metadata($attachment_id, $attachment_data);
            
            return array(
                'attachment_id' => $attachment_id,
                'url' => $uploaded_file['url'],
                'file' => $uploaded_file['file'],
                'type' => $uploaded_file['type']
            );
        }
        
        return new WP_Error('attachment_error', 'Erreur lors de la création de l\'attachement.');
    }
    
    /**
     * Envoyer email de confirmation avec pièces jointes
     */
    private function send_confirmation_email_with_attachments($email, $prenom, $nom, $files) {
        $subject = 'Inscription confirmée - Formateur Manager';
        $message = "Bonjour $prenom $nom,\n\n";
        $message .= "Votre inscription en tant que formateur a été confirmée avec succès.\n\n";
        $message .= "Nous avons bien reçu :\n";
        if (isset($files['cv'])) {
            $message .= "- Votre CV\n";
        }
        if (isset($files['photo'])) {
            $message .= "- Votre photo de profil\n";
        }
        $message .= "\nVotre profil sera bientôt visible sur notre plateforme.\n\n";
        $message .= "Cordialement,\nL'équipe " . get_bloginfo('name');
        
        // Préparer les pièces jointes
        $attachments = array();
        if (isset($files['cv']['file'])) {
            $attachments[] = $files['cv']['file'];
        }
        
        // Envoyer l'email
        wp_mail($email, $subject, $message, '', $attachments);
        
        // Envoyer une copie à l'admin
        $admin_email = get_option('admin_email');
        $admin_subject = 'Nouvelle inscription formateur - ' . $prenom . ' ' . $nom;
        $admin_message = "Une nouvelle inscription formateur a été reçue :\n\n";
        $admin_message .= "Nom : $prenom $nom\n";
        $admin_message .= "Email : $email\n";
        $admin_message .= "Spécialité : " . $_POST['specialite'] . "\n\n";
        $admin_message .= "Consultez le profil complet dans l'administration WordPress.";
        
        wp_mail($admin_email, $admin_subject, $admin_message, '', $attachments);
    }
    
    /**
     * Récupérer les villes disponibles
     */
    private function get_available_cities() {
        global $wpdb;
        $villes = $wpdb->get_col("
            SELECT DISTINCT meta_value 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_formateur_ville' 
            AND meta_value != '' 
            ORDER BY meta_value ASC
        ");
        return $villes ?: array();
    }
    
    // ... Reste du code admin (métaboxes, colonnes, etc.)
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=formateur',
            'Tableau de bord',
            'Tableau de bord', 
            'manage_options',
            'formateur-dashboard',
            array($this, 'admin_dashboard_page')
        );
    }
    
    public function admin_dashboard_page() {
        $total = wp_count_posts('formateur')->publish;
        echo '<div class="wrap"><h1>📊 Formateurs - Tableau de bord</h1>';
        echo '<p>Total: <strong>' . $total . '</strong> formateurs inscrits</p></div>';
    }
    
    public function add_meta_boxes() {
        add_meta_box('formateur_details', 'Informations', array($this, 'formateur_metabox'), 'formateur');
    }
    
    public function formateur_metabox($post) {
        $prenom = get_post_meta($post->ID, '_formateur_prenom', true);
        $email = get_post_meta($post->ID, '_formateur_email', true);
        echo '<p><strong>Prénom:</strong> ' . esc_html($prenom) . '</p>';
        echo '<p><strong>Email:</strong> ' . esc_html($email) . '</p>';
    }
    
    public function save_meta_boxes($post_id) {
        // Sauvegarde automatique par AJAX
    }
    
    public function add_admin_columns($columns) {
        $columns['formateur_info'] = 'Formateur';
        $columns['specialite'] = 'Spécialité';
        return $columns;
    }
    
    public function admin_column_content($column, $post_id) {
        if ($column === 'formateur_info') {
            $prenom = get_post_meta($post_id, '_formateur_prenom', true);
            $nom = get_post_meta($post_id, '_formateur_nom', true);
            echo '<strong>' . $prenom . ' ' . $nom . '</strong>';
        }
    }
    
    public function activate() {
        $this->create_post_type();
        flush_rewrite_rules();
    }
}

// Initialiser le plugin
FormateurManager::get_instance();
?>