/**
 * Formateur Manager Pro - JavaScript Principal
 * Version: 2.0.0
 */

(function($) {
    'use strict';
    
    // Variables globales
    let competences = [];
    let isSubmitting = false;
    
    // Initialisation quand le DOM est prêt
    $(document).ready(function() {
        initFormateurManager();
    });
    
    /**
     * Initialisation principale
     */
    function initFormateurManager() {
        console.log('🚀 Formateur Manager Pro initialisé');
        
        // Initialiser les différents composants
        initRegistrationForm();
        initFormateurList();
        initHomePage();
        initFileUploads();
        initFormValidation();
        initCompetencesManager();
        initCharacterCounters();
        initAnimations();
        
        // Initialiser les événements globaux
        initGlobalEvents();
    }
    
    /**
     * Gestion du formulaire d'inscription
     */
    function initRegistrationForm() {
        const $form = $('#formateur-register-form');
        if (!$form.length) return;
        
        console.log('📝 Initialisation du formulaire d\'inscription');
        
        $form.on('submit', function(e) {
            e.preventDefault();
            
            if (isSubmitting) return;
            
            handleFormSubmission($(this));
        });
        
        // Sauvegarde automatique (brouillon)
        initAutoSave();
        
        // Charger un éventuel brouillon
        loadDraft();
    }
    
    /**
     * Gestion de la soumission du formulaire
     */
    function handleFormSubmission($form) {
        console.log('📤 Soumission du formulaire');
        
        // Valider le formulaire
        if (!validateCompleteForm($form)) {
            showMessage('error', '❌ Veuillez corriger les erreurs dans le formulaire.');
            return;
        }
        
        isSubmitting = true;
        const $submitBtn = $form.find('button[type="submit"]');
        const originalText = $submitBtn.html();
        
        // État de chargement
        $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> <span>Inscription en cours...</span>')
                  .prop('disabled', true);
        
        hideMessage();
        
        // Préparer les données du formulaire
        const formData = new FormData($form[0]);
        
        // Ajouter l'action et le nonce
        formData.append('action', 'formateur_register');
        formData.append('formateur_nonce', formateurAjax.nonce);
        
        // Ajouter les compétences
        competences.forEach(function(competence) {
            formData.append('competences[]', competence);
        });
        
        // Requête AJAX
        $.ajax({
            url: formateurAjax.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            timeout: 60000, // 60 secondes
            success: function(response) {
                console.log('✅ Réponse reçue:', response);
                
                if (response.success) {
                    showMessage('success', '✅ ' + response.data);
                    $form[0].reset();
                    resetFormState();
                    clearDraft();
                    
                    // Scroll vers le message
                    $('html, body').animate({
                        scrollTop: $('.form-messages').offset().top - 100
                    }, 500);
                    
                    // Animation de succès
                    createSuccessAnimation();
                    
                } else {
                    showMessage('error', '❌ ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Erreur AJAX:', status, error);
                
                let errorMessage = '❌ Une erreur est survenue. Veuillez réessayer.';
                
                if (status === 'timeout') {
                    errorMessage = '⏱️ Délai d\'attente dépassé. Vos fichiers sont peut-être trop volumineux.';
                } else if (xhr.responseJSON && xhr.responseJSON.data) {
                    errorMessage = '❌ ' + xhr.responseJSON.data;
                }
                
                showMessage('error', errorMessage);
            },
            complete: function() {
                // Restaurer le bouton
                $submitBtn.html(originalText).prop('disabled', false);
                isSubmitting = false;
            }
        });
    }
    
    /**
     * Gestion des uploads de fichiers
     */
    function initFileUploads() {
        console.log('📁 Initialisation des uploads de fichiers');
        
        // Upload de photo
        $('#fm_photo').on('change', function() {
            handleFileUpload(this, 'photo', ['jpg', 'jpeg', 'png', 'gif', 'webp'], 5 * 1024 * 1024);
        });
        
        // Upload de CV
        $('#fm_cv').on('change', function() {
            handleFileUpload(this, 'cv', ['pdf', 'doc', 'docx'], 10 * 1024 * 1024);
        });
        
        // Drag & Drop
        initDragAndDrop();
    }
    
    /**
     * Gérer l'upload d'un fichier
     */
    function handleFileUpload(input, type, allowedExtensions, maxSize) {
        const file = input.files[0];
        const $preview = $('#' + type + '-preview');
        
        if (!file) {
            $preview.removeClass('show').empty();
            return;
        }
        
        console.log('📄 Upload fichier:', file.name, 'Type:', type);
        
        // Validation de l'extension
        const extension = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(extension)) {
            showMessage('error', `❌ Type de fichier non autorisé pour ${type}. Extensions acceptées : ${allowedExtensions.join(', ')}`);
            input.value = '';
            $preview.removeClass('show');
            return;
        }
        
        // Validation de la taille
        if (file.size > maxSize) {
            const maxSizeMB = Math.round(maxSize / (1024 * 1024));
            showMessage('error', `❌ Fichier trop volumineux pour ${type}. Taille maximum : ${maxSizeMB}MB`);
            input.value = '';
            $preview.removeClass('show');
            return;
        }
        
        // Afficher la prévisualisation
        displayFilePreview(file, type, $preview);
        
        // Marquer le champ comme valide
        $(input).closest('.form-group').addClass('valid').removeClass('invalid');
    }
    
    /**
     * Afficher la prévisualisation d'un fichier
     */
    function displayFilePreview(file, type, $preview) {
        const fileSize = formatFileSize(file.size);
        
        let preview = `
            <div class="file-preview">
                <div class="file-preview__info">
                    <div class="file-preview__name">${file.name}</div>
                    <div class="file-preview__size">${fileSize}</div>
                </div>
                <button type="button" class="file-preview__remove" onclick="removeFile('${type}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        // Si c'est une image, ajouter un aperçu
        if (type === 'photo' && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imagePreview = `
                    <div class="file-preview">
                        <img src="${e.target.result}" alt="Aperçu" class="file-preview__image">
                        <div class="file-preview__info">
                            <div class="file-preview__name">${file.name}</div>
                            <div class="file-preview__size">${fileSize}</div>
                        </div>
                        <button type="button" class="file-preview__remove" onclick="removeFile('${type}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                $preview.html(imagePreview).addClass('show');
            };
            reader.readAsDataURL(file);
        } else {
            // Icône selon le type de fichier
            const iconClass = type === 'cv' ? 'fa-file-pdf' : 'fa-file';
            preview = `
                <div class="file-preview">
                    <i class="fas ${iconClass} file-preview__icon"></i>
                    <div class="file-preview__info">
                        <div class="file-preview__name">${file.name}</div>
                        <div class="file-preview__size">${fileSize}</div>
                    </div>
                    <button type="button" class="file-preview__remove" onclick="removeFile('${type}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            $preview.html(preview).addClass('show');
        }
    }
    
    /**
     * Drag & Drop pour les fichiers
     */
    function initDragAndDrop() {
        $('.file-upload__zone').on({
            'dragover': function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            },
            'dragleave': function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            },
            'drop': function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
                
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    const $input = $(this).siblings('.file-upload__input');
                    $input[0].files = files;
                    $input.trigger('change');
                }
            },
            'click': function() {
                $(this).siblings('.file-upload__input').click();
            }
        });
    }
    
    /**
     * Gestion des compétences
     */
    function initCompetencesManager() {
        const $competenceInput = $('#competence-search');
        if (!$competenceInput.length) return;
        
        console.log('🎯 Initialisation du gestionnaire de compétences');
        
        $competenceInput.on('keypress', function(e) {
            if (e.which === 13) { // Entrée
                e.preventDefault();
                addCompetence($(this).val().trim());
                $(this).val('');
            }
        });
        
        // Suggestions de compétences (optionnel)
        initCompetenceSuggestions();
    }
    
    /**
     * Ajouter une compétence
     */
    function addCompetence(competence) {
        if (!competence || competences.includes(competence)) return;
        
        competences.push(competence);
        
        const $container = $('#competences-tags');
        const $tag = $(`
            <span class="competence-tag">
                ${competence}
                <button type="button" class="remove" onclick="removeCompetence('${competence}')">
                    <i class="fas fa-times"></i>
                </button>
            </span>
        `);
        
        $container.append($tag);
        updateCompetenceCounter();
        
        console.log('➕ Compétence ajoutée:', competence);
    }
    
    /**
     * Supprimer une compétence
     */
    window.removeCompetence = function(competence) {
        const index = competences.indexOf(competence);
        if (index > -1) {
            competences.splice(index, 1);
            $(`#competences-tags .competence-tag:contains('${competence}')`).remove();
            updateCompetenceCounter();
            
            console.log('➖ Compétence supprimée:', competence);
        }
    };
    
    /**
     * Mettre à jour le compteur de compétences
     */
    function updateCompetenceCounter() {
        const count = competences.length;
        let $counter = $('#competence-counter');
        
        if (!$counter.length) {
            $counter = $('<div id="competence-counter" class="competence-counter"></div>');
            $('#competences-tags').after($counter);
        }
        
        const text = count > 0 ? `${count} compétence(s) sélectionnée(s)` : 'Aucune compétence sélectionnée';
        $counter.text(text).toggleClass('has-competences', count > 0);
    }
    
    /**
     * Suggestions de compétences
     */
    function initCompetenceSuggestions() {
        const suggestions = [
            'JavaScript', 'Python', 'React', 'Vue.js', 'Angular', 'Node.js',
            'PHP', 'Laravel', 'WordPress', 'Symfony',
            'HTML/CSS', 'SASS', 'Bootstrap', 'Tailwind CSS',
            'SQL', 'MySQL', 'PostgreSQL', 'MongoDB',
            'Git', 'Docker', 'AWS', 'Azure',
            'SEO', 'Google Ads', 'Facebook Ads', 'Analytics',
            'Photoshop', 'Illustrator', 'Figma', 'Sketch',
            'Management', 'Leadership', 'Scrum', 'Agile'
        ];
        
        const $input = $('#competence-search');
        
        $input.on('input', function() {
            const value = $(this).val().toLowerCase();
            if (value.length < 2) return;
            
            const matches = suggestions.filter(s => 
                s.toLowerCase().includes(value) && !competences.includes(s)
            ).slice(0, 5);
            
            showCompetenceSuggestions(matches);
        });
    }
    
    /**
     * Afficher les suggestions de compétences
     */
    function showCompetenceSuggestions(suggestions) {
        const $input = $('#competence-search');
        let $suggestions = $('#competence-suggestions');
        
        if (!$suggestions.length) {
            $suggestions = $('<div id="competence-suggestions" class="competence-suggestions"></div>');
            $input.after($suggestions);
        }
        
        if (suggestions.length === 0) {
            $suggestions.hide();
            return;
        }
        
        const suggestionsList = suggestions.map(s => 
            `<div class="competence-suggestion" onclick="addCompetence('${s}')">${s}</div>`
        ).join('');
        
        $suggestions.html(suggestionsList).show();
    }
    
    /**
     * Validation du formulaire
     */
    function initFormValidation() {
        console.log('✅ Initialisation de la validation');
        
        // Validation en temps réel
        $('input[required], select[required], textarea[required]').on('blur', function() {
            validateField($(this));
        });
        
        // Validation email
        $('input[type="email"]').on('blur', function() {
            validateEmail($(this));
        });
        
        // Validation téléphone
        $('input[type="tel"]').on('input', function() {
            formatPhoneNumber($(this));
        });
        
        // Validation des groupes de checkboxes
        $('input[type="checkbox"][required]').on('change', function() {
            validateCheckboxGroup($(this));
        });
    }
    
    /**
     * Valider un champ
     */
    function validateField($field) {
        const value = $field.val().trim();
        const $group = $field.closest('.form-group');
        const isRequired = $field.prop('required');
        
        if (isRequired && !value) {
            $group.addClass('invalid').removeClass('valid');
            return false;
        } else if (value) {
            $group.addClass('valid').removeClass('invalid');
            return true;
        } else {
            $group.removeClass('valid invalid');
            return true;
        }
    }
    
    /**
     * Valider un email
     */
    function validateEmail($field) {
        const email = $field.val().trim();
        const $group = $field.closest('.form-group');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            $group.addClass('invalid').removeClass('valid');
            return false;
        } else if (email) {
            $group.addClass('valid').removeClass('invalid');
            return true;
        }
        
        return validateField($field);
    }
    
    /**
     * Formater le numéro de téléphone
     */
    function formatPhoneNumber($field) {
        let value = $field.val().replace(/\D/g, '');
        
        if (value.startsWith('33')) {
            value = '+' + value;
        } else if (value.startsWith('0') && value.length === 10) {
            value = value.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5');
        }
        
        $field.val(value);
        validateField($field);
    }
    
    /**
     * Valider un groupe de checkboxes
     */
    function validateCheckboxGroup($checkbox) {
        const name = $checkbox.attr('name');
        const $group = $checkbox.closest('.form-group');
        const checked = $(`input[name="${name}"]:checked`).length > 0;
        
        if ($checkbox.prop('required') && !checked) {
            $group.addClass('invalid').removeClass('valid');
            return false;
        } else {
            $group.addClass('valid').removeClass('invalid');
            return true;
        }
    }
    
    /**
     * Validation complète du formulaire
     */
    function validateCompleteForm($form) {
        let isValid = true;
        
        // Valider tous les champs obligatoires
        $form.find('input[required], select[required], textarea[required]').each(function() {
            if (!validateField($(this))) {
                isValid = false;
            }
        });
        
        // Valider les emails
        $form.find('input[type="email"]').each(function() {
            if (!validateEmail($(this))) {
                isValid = false;
            }
        });
        
        // Valider les groupes de checkboxes obligatoires
        const requiredCheckboxGroups = ['modalites[]', 'publics[]'];
        requiredCheckboxGroups.forEach(function(name) {
            const checked = $form.find(`input[name="${name}"]:checked`).length > 0;
            if (!checked) {
                showMessage('error', `❌ Veuillez sélectionner au moins une option pour ${name.replace('[]', '')}.`);
                isValid = false;
            }
        });
        
        // Vérifier le CV obligatoire
        if (!$('#fm_cv').val()) {
            showMessage('error', '❌ Le CV est obligatoire.');
            isValid = false;
        }
        
        // Vérifier le consentement RGPD
        if (!$('input[name="rgpd_consent"]:checked').length) {
            showMessage('error', '❌ Vous devez accepter la politique de confidentialité.');
            isValid = false;
        }
        
        return isValid;
    }
    
    /**
     * Compteurs de caractères
     */
    function initCharacterCounters() {
        $('textarea[maxlength]').each(function() {
            const $textarea = $(this);
            const maxLength = parseInt($textarea.attr('maxlength')) || 2000;
            
            let $counter = $textarea.siblings('.char-counter');
            if (!$counter.length) {
                $counter = $(`
                    <div class="char-counter">
                        <span class="current">0</span> / ${maxLength} caractères
                    </div>
                `);
                $textarea.after($counter);
            }
            
            $textarea.on('input', function() {
                const length = $(this).val().length;
                $counter.find('.current').text(length);
                
                $counter.toggleClass('warning', length > maxLength * 0.8);
                $counter.toggleClass('error', length > maxLength);
            });
            
            // Initialiser le compteur
            $textarea.trigger('input');
        });
    }
    
    /**
     * Auto-resize des textareas
     */
    function initTextareaResize() {
        $('textarea').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
    
    /**
     * Gestion de la liste des formateurs
     */
    function initFormateurList() {
        if (!$('#formateurs-grid').length) return;
        
        console.log('📋 Initialisation de la liste des formateurs');
        
        // Filtres de recherche
        initFormateurFilters();
        
        // Animation des cartes au chargement
        animateFormateurCards();
    }
    
    /**
     * Filtres des formateurs
     */
    function initFormateurFilters() {
        const $search = $('#formateur-search');
        const $specialiteFilter = $('#formateur-filter-specialite');
        const $villeFilter = $('#formateur-filter-ville');
        const $modaliteFilter = $('#formateur-filter-modalite');
        const $resetBtn = $('#reset-filters');
        const $clearBtn = $('#search-clear');
        
        // Événements de filtrage
        $search.on('input', debounce(filterFormateurs, 300));
        $specialiteFilter.on('change', filterFormateurs);
        $villeFilter.on('change', filterFormateurs);
        $modaliteFilter.on('change', filterFormateurs);
        
        // Bouton de réinitialisation
        $resetBtn.on('click', resetAllFilters);
        
        // Bouton d'effacement de la recherche
        $clearBtn.on('click', function() {
            $search.val('').trigger('input');
        });
        
        // Afficher/masquer le bouton d'effacement
        $search.on('input', function() {
            $clearBtn.toggle($(this).val().length > 0);
        });
    }
    
    /**
     * Filtrer les formateurs
     */
    function filterFormateurs() {
        const searchTerm = $('#formateur-search').val().toLowerCase();
        const specialiteFilter = $('#formateur-filter-specialite').val();
        const villeFilter = $('#formateur-filter-ville').val().toLowerCase();
        const modaliteFilter = $('#formateur-filter-modalite').val();
        
        let visibleCount = 0;
        
        $('.formateur-card').each(function() {
            const $card = $(this);
            const searchable = $card.data('searchable') || '';
            const specialites = ($card.data('specialite') || '').split(',');
            const ville = ($card.data('ville') || '').toLowerCase();
            const modalites = ($card.data('modalites') || '').split(',');
            
            let show = true;
            
            // Filtre par terme de recherche
            if (searchTerm && !searchable.includes(searchTerm)) {
                show = false;
            }
            
            // Filtre par spécialité
            if (specialiteFilter && !specialites.includes(specialiteFilter)) {
                show = false;
            }
            
            // Filtre par ville
            if (villeFilter && !ville.includes(villeFilter)) {
                show = false;
            }
            
            // Filtre par modalité
            if (modaliteFilter && !modalites.includes(modaliteFilter)) {
                show = false;
            }
            
            if (show) {
                $card.fadeIn(300);
                visibleCount++;
            } else {
                $card.fadeOut(300);
            }
        });
        
        // Mettre à jour le compteur
        $('#results-count').text(visibleCount);
        
        // Afficher message "aucun résultat"
        $('.no-results-message').toggle(visibleCount === 0);
        $('#formateurs-grid').toggle(visibleCount > 0);
    }
    
    /**
     * Réinitialiser tous les filtres
     */
    window.resetAllFilters = function() {
        $('#formateur-search').val('');
        $('#formateur-filter-specialite').val('');
        $('#formateur-filter-ville').val('');
        $('#formateur-filter-modalite').val('');
        $('#search-clear').hide();
        
        filterFormateurs();
    };
    
    /**
     * Animation des cartes de formateurs
     */
    function animateFormateurCards() {
        $('.formateur-card').each(function(index) {
            $(this).css({
                opacity: 0,
                transform: 'translateY(30px)'
            }).delay(index * 50).animate({
                opacity: 1
            }, {
                duration: 600,
                step: function(now) {
                    const translateY = 30 - (30 * now);
                    $(this).css('transform', `translateY(${translateY}px)`);
                }
            });
        });
    }
    
    /**
     * Page d'accueil
     */
    function initHomePage() {
        if (!$('.formateur-modern-home').length) return;
        
        console.log('🏠 Initialisation de la page d\'accueil');
        
        // Animation des statistiques
        initStatsAnimation();
        
        // Animation des éléments flottants
        initFloatingElements();
        
        // Parallax subtil
        initParallax();
        
        // Animations au scroll
        initScrollAnimations();
    }
    
    /**
     * Animation des statistiques
     */
    function initStatsAnimation() {
        const $stats = $('.stat__number');
        
        if (!$stats.length) return;
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateNumber($(entry.target));
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        $stats.each(function() {
            observer.observe(this);
        });
    }
    
    /**
     * Animer un nombre
     */
    function animateNumber($element) {
        const text = $element.text();
        const hasPlus = text.includes('+');
        const hasPercent = text.includes('%');
        const target = parseInt(text.replace(/[^\d]/g, ''));
        
        if (isNaN(target)) return;
        
        let current = 0;
        const increment = target / 50;
        const suffix = hasPlus ? '+' : (hasPercent ? '%' : '');
        
        const timer = setInterval(function() {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            if (text.includes('h')) {
                $element.text(Math.floor(current) + 'h');
            } else {
                $element.text(Math.floor(current) + suffix);
            }
        }, 30);
    }
    
    /**
     * Éléments flottants
     */
    function initFloatingElements() {
        $('.element').each(function(index) {
            const $element = $(this);
            const delay = index * 2000;
            
            setInterval(function() {
                $element.addClass('pulse-animation');
                setTimeout(function() {
                    $element.removeClass('pulse-animation');
                }, 1000);
            }, 6000 + delay);
        });
    }
    
    /**
     * Parallax subtil
     */
    function initParallax() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return; // Respecter les préférences d'accessibilité
        }
        
        $(window).on('scroll', throttle(function() {
            const scrolled = $(this).scrollTop();
            
            $('.formateur-photo, .formateur-placeholder').css({
                transform: `translateY(${scrolled * -0.1}px)`
            });
            
            $('.element').each(function(index) {
                const rate = scrolled * (0.05 + index * 0.02);
                $(this).css({
                    transform: `translateY(${rate}px)`
                });
            });
        }, 16)); // 60fps
    }
    
    /**
     * Animations au scroll
     */
    function initScrollAnimations() {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    $(entry.target).addClass('animate-fadeInUp');
                }
            });
        }, { threshold: 0.1 });
        
        $('.feature, .specialty-card, .testimonial').each(function() {
            observer.observe(this);
        });
    }
    
    /**
     * Contact formateur
     */
    window.contactFormateur = function(initiales, email, specialite) {
        console.log('📞 Contact formateur:', initiales);
        
        const subject = `Demande de formation - ${specialite}`;
        const body = `Bonjour,\n\nJe souhaiterais obtenir plus d'informations concernant vos formations en ${specialite}.\n\nPouvez-vous me contacter pour discuter de mes besoins ?\n\nCordialement`;
        
        const mailtoLink = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        
        // Ouvrir le client email
        window.location.href = mailtoLink;
        
        // Feedback visuel
        showMessage('info', '📧 Client email ouvert');
        
        // Analytics (si disponible)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'contact_formateur', {
                'event_category': 'engagement',
                'event_label': initiales
            });
        }
    };
    
    /**
     * Voir le profil d'un formateur
     */
    window.viewFormateur = function(formateurId) {
        console.log('👁️ Voir formateur:', formateurId);
        
        // Ici vous pouvez implémenter la logique pour afficher le profil
        // Par exemple, ouvrir une modal ou rediriger vers une page dédiée
        
        showMessage('info', '🔍 Fonctionnalité à implémenter : affichage du profil complet');
    };
    
    /**
     * Supprimer un fichier uploadé
     */
    window.removeFile = function(type) {
        console.log('🗑️ Suppression fichier:', type);
        
        $(`#fm_${type}`).val('');
        $(`#${type}-preview`).removeClass('show').empty();
        $(`#fm_${type}`).closest('.form-group').removeClass('valid invalid');
    };
    
    /**
     * Sauvegarde automatique (brouillon)
     */
    function initAutoSave() {
        if (!window.localStorage) return;
        
        setInterval(function() {
            if ($('#formateur-register-form').length) {
                saveDraft();
            }
        }, 30000); // Toutes les 30 secondes
    }
    
    /**
     * Sauvegarder le brouillon
     */
    function saveDraft() {
        const formData = {};
        
        $('#formateur-register-form').find('input, select, textarea').each(function() {
            const $field = $(this);
            const name = $field.attr('name');
            
            if (name && $field.attr('type') !== 'file') {
                if ($field.attr('type') === 'checkbox') {
                    if (!formData[name]) formData[name] = [];
                    if ($field.is(':checked')) {
                        formData[name].push($field.val());
                    }
                } else {
                    formData[name] = $field.val();
                }
            }
        });
        
        // Ajouter les compétences
        formData.competences = competences;
        
        try {
            localStorage.setItem('fmp_form_draft', JSON.stringify(formData));
            console.log('💾 Brouillon sauvegardé');
        } catch (e) {
            console.warn('❌ Impossible de sauvegarder le brouillon:', e);
        }
    }
    
    /**
     * Charger le brouillon
     */
    function loadDraft() {
        if (!window.localStorage) return;
        
        try {
            const savedData = localStorage.getItem('fmp_form_draft');
            if (!savedData) return;
            
            const formData = JSON.parse(savedData);
            
            if (confirm('Un brouillon de formulaire a été trouvé. Voulez-vous le restaurer ?')) {
                // Restaurer les champs
                Object.keys(formData).forEach(function(name) {
                    if (name === 'competences') {
                        competences = formData[name] || [];
                        competences.forEach(addCompetence);
                        return;
                    }
                    
                    const $field = $(`[name="${name}"]`);
                    
                    if ($field.attr('type') === 'checkbox') {
                        $field.prop('checked', false);
                        if (Array.isArray(formData[name])) {
                            formData[name].forEach(function(value) {
                                $(`[name="${name}"][value="${value}"]`).prop('checked', true);
                            });
                        }
                    } else {
                        $field.val(formData[name]);
                    }
                });
                
                console.log('📁 Brouillon restauré');
            }
        } catch (e) {
            console.warn('❌ Impossible de charger le brouillon:', e);
        }
    }
    
    /**
     * Supprimer le brouillon
     */
    function clearDraft() {
        if (window.localStorage) {
            localStorage.removeItem('fmp_form_draft');
            console.log('🗑️ Brouillon supprimé');
        }
    }
    
    /**
     * Réinitialiser l'état du formulaire
     */
    function resetFormState() {
        competences = [];
        $('.file-preview').removeClass('show').empty();
        $('.form-group').removeClass('valid invalid');
        $('#competences-tags').empty();
        updateCompetenceCounter();
    }
    
    /**
     * Animation de succès
     */
    function createSuccessAnimation() {
        // Confetti
        for (let i = 0; i < 50; i++) {
            const confetti = $('<div class="confetti"></div>');
            confetti.css({
                position: 'fixed',
                width: '10px',
                height: '10px',
                background: ['#ffd700', '#00a32a', '#ff6b6b', '#4ecdc4'][Math.floor(Math.random() * 4)],
                left: Math.random() * 100 + '%',
                top: '-10px',
                zIndex: 9999,
                animation: `confetti-fall ${Math.random() * 3 + 2}s linear forwards`
            });
            
            $('body').append(confetti);
            
            setTimeout(function() {
                confetti.remove();
            }, 5000);
        }
        
        // Ajouter l'animation CSS
        if (!$('#confetti-animation').length) {
            $('head').append(`
                <style id="confetti-animation">
                    @keyframes confetti-fall {
                        0% { transform: translateY(0) rotateZ(0deg); opacity: 1; }
                        100% { transform: translateY(100vh) rotateZ(360deg); opacity: 0; }
                    }
                </style>
            `);
        }
    }
    
    /**
     * Animations générales
     */
    function initAnimations() {
        // Auto-resize des textareas
        initTextareaResize();
        
        // Effets hover sur les boutons
        initButtonEffects();
        
        // Smooth scroll pour les ancres
        initSmoothScroll();
    }
    
    /**
     * Effets sur les boutons
     */
    function initButtonEffects() {
        $('.btn').on('mouseenter', function() {
            $(this).addClass('btn--hover');
        }).on('mouseleave', function() {
            $(this).removeClass('btn--hover');
        });
        
        // Effet ripple
        $('.btn').on('click', function(e) {
            const $btn = $(this);
            const ripple = $('<span class="btn-ripple"></span>');
            
            const size = Math.max($btn.outerWidth(), $btn.outerHeight());
            const x = e.pageX - $btn.offset().left - size / 2;
            const y = e.pageY - $btn.offset().top - size / 2;
            
            ripple.css({
                width: size,
                height: size,
                left: x,
                top: y
            });
            
            $btn.append(ripple);
            
            setTimeout(function() {
                ripple.remove();
            }, 600);
        });
    }
    
    /**
     * Smooth scroll
     */
    function initSmoothScroll() {
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            
            const target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });
    }
    
    /**
     * Événements globaux
     */
    function initGlobalEvents() {
        // Raccourcis clavier
        $(document).on('keydown', function(e) {
            // Ctrl/Cmd + Enter pour soumettre le formulaire
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 13) {
                const $form = $('#formateur-register-form');
                if ($form.length) {
                    $form.trigger('submit');
                }
            }
            
            // Escape pour fermer les modales/suggestions
            if (e.keyCode === 27) {
                $('#competence-suggestions').hide();
                hideMessage();
            }
        });
        
        // Gestion responsive
        $(window).on('resize', debounce(function() {
            adjustForMobile();
        }, 250));
        
        // Initialiser l'ajustement mobile
        adjustForMobile();
    }
    
    /**
     * Ajustements pour mobile
     */
    function adjustForMobile() {
        const isMobile = $(window).width() < 768;
        
        $('body').toggleClass('is-mobile', isMobile);
        
        if (isMobile) {
            // Désactiver les animations coûteuses sur mobile
            $('.element').css('animation', 'none');
            $(window).off('scroll.parallax');
        }
    }
    
    /**
     * Afficher un message
     */
    function showMessage(type, message) {
        let $messageDiv = $('.form-messages');
        
        if (!$messageDiv.length) {
            $messageDiv = $('<div class="form-messages"></div>');
            $('#formateur-register-form').before($messageDiv);
        }
        
        $messageDiv.removeClass('success error info warning')
                   .addClass(type)
                   .html(message)
                   .fadeIn();
        
        // Auto-masquer les messages d'info
        if (type === 'info') {
            setTimeout(function() {
                $messageDiv.fadeOut();
            }, 5000);
        }
        
        console.log(`💬 Message ${type}:`, message);
    }
    
    /**
     * Masquer le message
     */
    function hideMessage() {
        $('.form-messages').fadeOut();
    }
    
    /**
     * Utilitaires
     */
    
    /**
     * Formater la taille d'un fichier
     */
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    /**
     * Debounce
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = function() {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    /**
     * Throttle
     */
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
    
})(jQuery);