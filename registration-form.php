<?php
/**
 * Template du formulaire d'inscription formateur
 * templates/registration-form.php
 */

if (!defined('ABSPATH')) exit;
?>

<div class="formateur-container formateur-register-container">
    <div class="formateur-register__header">
        <h2 class="formateur-register__title">📝 Inscription Formateur</h2>
        <p class="formateur-register__subtitle">Rejoignez notre réseau de formateurs professionnels</p>
    </div>
    
    <div class="form-messages" style="display: none;"></div>
    
    <form id="formateur-register-form" class="formateur-form" enctype="multipart/form-data">
        <?php wp_nonce_field('formateur_register_nonce', 'formateur_nonce'); ?>
        
        <!-- Informations personnelles -->
        <div class="form-section">
            <h3 class="form-section__title">
                <i class="fas fa-user form-section__icon"></i>
                <?php _e('Informations personnelles', 'formateur-manager-pro'); ?>
            </h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="fm_prenom" class="form-label form-label--required">
                        <?php _e('Prénom', 'formateur-manager-pro'); ?>
                    </label>
                    <input type="text" id="fm_prenom" name="prenom" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="fm_nom" class="form-label form-label--required">
                        <?php _e('Nom', 'formateur-manager-pro'); ?>
                    </label>
                    <input type="text" id="fm_nom" name="nom" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="fm_email" class="form-label form-label--required">
                        <?php _e('Adresse email', 'formateur-manager-pro'); ?>
                    </label>
                    <input type="email" id="fm_email" name="email" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="fm_telephone" class="form-label form-label--required">
                        <?php _e('Téléphone', 'formateur-manager-pro'); ?>
                    </label>
                    <input type="tel" id="fm_telephone" name="telephone" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="fm_ville" class="form-label form-label--required">
                        <?php _e('Ville', 'formateur-manager-pro'); ?>
                    </label>
                    <input type="text" id="fm_ville" name="ville" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="fm_pays" class="form-label">
                        <?php _e('Pays', 'formateur-manager-pro'); ?>
                    </label>
                    <select id="fm_pays" name="pays" class="form-select">
                        <option value=""><?php _e('Sélectionner un pays', 'formateur-manager-pro'); ?></option>
                        <option value="FR"><?php _e('France', 'formateur-manager-pro'); ?></option>
                        <option value="BE"><?php _e('Belgique', 'formateur-manager-pro'); ?></option>
                        <option value="CH"><?php _e('Suisse', 'formateur-manager-pro'); ?></option>
                        <option value="CA"><?php _e('Canada', 'formateur-manager-pro'); ?></option>
                        <option value="autre"><?php _e('Autre', 'formateur-manager-pro'); ?></option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Photo et CV -->
        <div class="form-section">
            <h3 class="form-section__title">
                <i class="fas fa-camera form-section__icon"></i>
                <?php _e('Documents', 'formateur-manager-pro'); ?>
            </h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <?php _e('Photo de profil', 'formateur-manager-pro'); ?>
                    </label>
                    <div class="file-upload">
                        <input type="file" id="fm_photo" name="photo" accept="image/*" class="file-upload__input">
                        <div class="file-upload__zone">
                            <i class="fas fa-cloud-upload-alt file-upload__icon"></i>
                            <div class="file-upload__text"><?php _e('Cliquez pour ajouter une photo', 'formateur-manager-pro'); ?></div>
                            <div class="file-upload__hint"><?php _e('JPG, PNG, GIF - Max 5MB', 'formateur-manager-pro'); ?></div>
                        </div>
                        <div class="file-upload__preview" id="photo-preview"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label form-label--required">
                        <?php _e('CV (PDF)', 'formateur-manager-pro'); ?>
                    </label>
                    <div class="file-upload">
                        <input type="file" id="fm_cv" name="cv" accept=".pdf,.doc,.docx" class="file-upload__input" required>
                        <div class="file-upload__zone">
                            <i class="fas fa-file-pdf file-upload__icon"></i>
                            <div class="file-upload__text"><?php _e('Cliquez pour ajouter votre CV', 'formateur-manager-pro'); ?></div>
                            <div class="file-upload__hint"><?php _e('PDF, DOC, DOCX - Max 10MB', 'formateur-manager-pro'); ?></div>
                        </div>
                        <div class="file-upload__preview" id="cv-preview"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expertise -->
        <div class="form-section">
            <h3 class="form-section__title">
                <i class="fas fa-graduation-cap form-section__icon"></i>
                <?php _e('Expertise et spécialités', 'formateur-manager-pro'); ?>
            </h3>
            
            <div class="form-group form-group--full">
                <label for="fm_specialite" class="form-label form-label--required">
                    <?php _e('Spécialité principale', 'formateur-manager-pro'); ?>
                </label>
                <select id="fm_specialite" name="specialite" class="form-select" required>
                    <option value=""><?php _e('Choisir une spécialité', 'formateur-manager-pro'); ?></option>
                    
                    <optgroup label="💻 Informatique & Tech">
                        <option value="dev-web"><?php _e('Développement Web', 'formateur-manager-pro'); ?></option>
                        <option value="dev-mobile"><?php _e('Développement Mobile', 'formateur-manager-pro'); ?></option>
                        <option value="cybersecurite"><?php _e('Cybersécurité', 'formateur-manager-pro'); ?></option>
                        <option value="data-science"><?php _e('Data Science', 'formateur-manager-pro'); ?></option>
                        <option value="intelligence-artificielle"><?php _e('Intelligence Artificielle', 'formateur-manager-pro'); ?></option>
                        <option value="cloud-computing"><?php _e('Cloud Computing', 'formateur-manager-pro'); ?></option>
                    </optgroup>
                    
                    <optgroup label="📊 Marketing & Communication">
                        <option value="marketing-digital"><?php _e('Marketing Digital', 'formateur-manager-pro'); ?></option>
                        <option value="seo-sem"><?php _e('SEO/SEM', 'formateur-manager-pro'); ?></option>
                        <option value="reseaux-sociaux"><?php _e('Réseaux Sociaux', 'formateur-manager-pro'); ?></option>
                        <option value="content-marketing"><?php _e('Content Marketing', 'formateur-manager-pro'); ?></option>
                    </optgroup>
                    
                    <optgroup label="🎨 Design & Créativité">
                        <option value="design-graphique"><?php _e('Design Graphique', 'formateur-manager-pro'); ?></option>
                        <option value="design-web"><?php _e('Design Web (UI/UX)', 'formateur-manager-pro'); ?></option>
                        <option value="motion-design"><?php _e('Motion Design', 'formateur-manager-pro'); ?></option>
                    </optgroup>
                    
                    <optgroup label="💼 Business & Management">
                        <option value="management"><?php _e('Management', 'formateur-manager-pro'); ?></option>
                        <option value="leadership"><?php _e('Leadership', 'formateur-manager-pro'); ?></option>
                        <option value="gestion-projet"><?php _e('Gestion de Projet', 'formateur-manager-pro'); ?></option>
                        <option value="entrepreneuriat"><?php _e('Entrepreneuriat', 'formateur-manager-pro'); ?></option>
                    </optgroup>
                    
                    <optgroup label="🗣️ Langues & Communication">
                        <option value="anglais"><?php _e('Anglais', 'formateur-manager-pro'); ?></option>
                        <option value="espagnol"><?php _e('Espagnol', 'formateur-manager-pro'); ?></option>
                        <option value="communication-orale"><?php _e('Communication Orale', 'formateur-manager-pro'); ?></option>
                    </optgroup>
                    
                    <option value="autre"><?php _e('Autre spécialité', 'formateur-manager-pro'); ?></option>
                </select>
            </div>

            <div class="form-group form-group--full">
                <label for="fm_experience" class="form-label form-label--required">
                    <?php _e('Années d\'expérience', 'formateur-manager-pro'); ?>
                </label>
                <select id="fm_experience" name="experience" class="form-select" required>
                    <option value=""><?php _e('Sélectionner', 'formateur-manager-pro'); ?></option>
                    <option value="debutant"><?php _e('Débutant (0-2 ans)', 'formateur-manager-pro'); ?></option>
                    <option value="junior"><?php _e('Junior (2-5 ans)', 'formateur-manager-pro'); ?></option>
                    <option value="confirme"><?php _e('Confirmé (5-8 ans)', 'formateur-manager-pro'); ?></option>
                    <option value="senior"><?php _e('Senior (8-15 ans)', 'formateur-manager-pro'); ?></option>
                    <option value="expert"><?php _e('Expert (15+ ans)', 'formateur-manager-pro'); ?></option>
                </select>
            </div>

            <div class="form-group form-group--full">
                <label class="form-label">
                    <?php _e('Compétences techniques', 'formateur-manager-pro'); ?>
                </label>
                <div class="competences-container">
                    <input type="text" id="competence-search" placeholder="🔍 Ajouter une compétence..." class="form-input">
                    <div id="competences-tags" class="competences-tags"></div>
                    <p class="form-description"><?php _e('Tapez une compétence et appuyez sur Entrée pour l\'ajouter.', 'formateur-manager-pro'); ?></p>
                </div>
            </div>

            <div class="form-group form-group--full">
                <label for="fm_biographie" class="form-label form-label--required">
                    <?php _e('Présentation professionnelle', 'formateur-manager-pro'); ?>
                </label>
                <textarea id="fm_biographie" name="biographie" rows="6" class="form-textarea form-textarea--large" 
                          placeholder="<?php esc_attr_e('Décrivez votre parcours, vos expériences, vos réalisations marquantes...', 'formateur-manager-pro'); ?>" required></textarea>
                <div class="char-counter">
                    <span class="current">0</span> / 2000 <?php _e('caractères', 'formateur-manager-pro'); ?>
                </div>
            </div>
        </div>

        <!-- Préférences -->
        <div class="form-section">
            <h3 class="form-section__title">
                <i class="fas fa-cog form-section__icon"></i>
                <?php _e('Préférences de formation', 'formateur-manager-pro'); ?>
            </h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label form-label--required">
                        <?php _e('Modalités de formation', 'formateur-manager-pro'); ?>
                    </label>
                    <div class="checkbox-group">
                        <label class="checkbox-item">
                            <input type="checkbox" name="modalites[]" value="presentiel" required>
                            <span class="checkbox-label"><?php _e('Présentiel', 'formateur-manager-pro'); ?></span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="modalites[]" value="distanciel">
                            <span class="checkbox-label"><?php _e('Distanciel', 'formateur-manager-pro'); ?></span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="modalites[]" value="hybride">
                            <span class="checkbox-label"><?php _e('Hybride', 'formateur-manager-pro'); ?></span>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label form-label--required">
                        <?php _e('Types de public', 'formateur-manager-pro'); ?>
                    </label>
                    <div class="checkbox-group">
                        <label class="checkbox-item">
                            <input type="checkbox" name="publics[]" value="entreprises" required>
                            <span class="checkbox-label"><?php _e('Entreprises', 'formateur-manager-pro'); ?></span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="publics[]" value="particuliers">
                            <span class="checkbox-label"><?php _e('Particuliers', 'formateur-manager-pro'); ?></span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="publics[]" value="etudiants">
                            <span class="checkbox-label"><?php _e('Étudiants', 'formateur-manager-pro'); ?></span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="publics[]" value="demandeurs-emploi">
                            <span class="checkbox-label"><?php _e('Demandeurs d\'emploi', 'formateur-manager-pro'); ?></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group form-group--full">
                <label for="fm_tarif_jour" class="form-label">
                    <?php _e('Tarif journalier indicatif (€)', 'formateur-manager-pro'); ?>
                </label>
                <select id="fm_tarif_jour" name="tarif_jour" class="form-select">
                    <option value=""><?php _e('Sélectionner une fourchette', 'formateur-manager-pro'); ?></option>
                    <option value="200-400">200€ - 400€</option>
                    <option value="400-600">400€ - 600€</option>
                    <option value="600-800">600€ - 800€</option>
                    <option value="800-1000">800€ - 1000€</option>
                    <option value="1000-1500">1000€ - 1500€</option>
                    <option value="1500+">1500€+</option>
                </select>
            </div>
        </div>

        <!-- Consentements -->
        <div class="form-section">
            <h3 class="form-section__title">
                <i class="fas fa-shield-alt form-section__icon"></i>
                <?php _e('Consentements', 'formateur-manager-pro'); ?>
            </h3>
            
            <div class="form-group form-group--full">
                <label class="checkbox-item checkbox-item--consent">
                    <input type="checkbox" name="rgpd_consent" required>
                    <span class="checkbox-label">
                        <?php _e('J\'accepte que mes données soient traitées selon la politique de confidentialité', 'formateur-manager-pro'); ?> *
                    </span>
                </label>
            </div>
            
            <div class="form-group form-group--full">
                <label class="checkbox-item checkbox-item--consent">
                    <input type="checkbox" name="newsletter_consent">
                    <span class="checkbox-label">
                        <?php _e('J\'accepte de recevoir des informations sur les opportunités de formation', 'formateur-manager-pro'); ?>
                    </span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary btn--lg btn--full">
                <i class="fas fa-rocket"></i>
                <span><?php _e('S\'inscrire comme formateur', 'formateur-manager-pro'); ?></span>
            </button>
        </div>
    </form>
    
    <div class="formateur-register__footer">
        <p class="text-center">
            <?php _e('Votre profil sera examiné sous 24-48h. Vous recevrez un email de confirmation.', 'formateur-manager-pro'); ?>
        </p>
    </div>
</div>

<style>
/* Styles spécifiques au formulaire */
.competences-container {
    position: relative;
}

.competences-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
    min-height: 40px;
    padding: 8px;
    border: 2px dashed var(--gray-300);
    border-radius: var(--radius-lg);
    background: var(--gray-50);
}

.competence-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--primary-blue);
    color: var(--white);
    padding: 4px 8px;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 500;
}

.competence-tag .remove {
    background: none;
    border: none;
    color: var(--white);
    cursor: pointer;
    font-weight: bold;
    padding: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.competence-tag .remove:hover {
    background: rgba(255, 255, 255, 0.2);
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 0;
    cursor: pointer;
}

.checkbox-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: var(--primary-blue);
}

.checkbox-item--consent {
    background: var(--gray-50);
    padding: 12px;
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
}

.char-counter {
    text-align: right;
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 4px;
}

.char-counter.warning {
    color: var(--warning);
}

.char-counter.error {
    color: var(--error);
}

.form-description {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-top: 4px;
}

.formateur-register__footer {
    margin-top: var(--space-2xl);
    padding-top: var(--space-xl);
    border-top: 1px solid var(--gray-200);
    color: var(--gray-600);
    font-size: 0.875rem;
}
</style>