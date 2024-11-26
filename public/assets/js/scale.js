// Drop box
const drag_zone = document.querySelector(".drag-zone");
const fileInput = document.getElementById("inputfile");
const root = document.querySelector("#image-root");

// lot de fichiers pour le redimensionnement
let batch_files = [];

// Gestion du drag & drop
drag_zone.addEventListener('dragenter', (e) => {
    e.preventDefault();
    drag_zone.classList.add('dragover');
});

drag_zone.addEventListener('dragover', (e) => {
    e.preventDefault();
    drag_zone.classList.add('dragover');
});

drag_zone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    drag_zone.classList.remove('dragover');
});

drag_zone.addEventListener('drop', (e) => {
    e.preventDefault();
    drag_zone.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        [...files].forEach(file => addImageScale(file));
    }
});

// Gestion de la sélection de fichier
fileInput.addEventListener("change", function() {
    const files = [...fileInput.files];
    files.forEach(file => addImageScale(file));
});

// Formatage de la taille du fichier
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Ajout d'une image à la liste avec prévisualisation des dimensions
function addImageScale(file) {
    // Vérifier le type de fichier
    if (!file.type.startsWith('image/')) {
        showCustomAlert(`Le fichier "${file.name}" n'est pas une image valide.`, 'error');
        return;
    }

    // Vérifier la taille du fichier (10MB max)
    const maxSize = 10 * 1024 * 1024; // 10MB
    if (file.size > maxSize) {
        showCustomAlert(`Le fichier "${file.name}" est trop volumineux. La taille maximale est de 10MB.`, 'error');
        return;
    }

    const imageURL = URL.createObjectURL(file);
    const fileSize = formatFileSize(file.size);
    
    const imagewrapper = document.createElement('div');
    imagewrapper.className = 'image-wrapper';
    imagewrapper.dataset.index = batch_files.length;
    
    // Tronquer le nom du fichier si nécessaire
    const displayName = file.name.length > 20 ? file.name.substring(0, 20) + '...' : file.name;
    
    imagewrapper.innerHTML = `
        <div class="image-content">
            <img src="${imageURL}" alt="${file.name}" onerror="this.src='assets/img/error.svg';">
            <button class="remove-btn" onclick="onRemove(this)" title="Supprimer">
                <i class='bx bx-trash'></i>
            </button>
        </div>
        <div class="file-info">
            <p class="file-name" title="${file.name}">${displayName}</p>
            <span class="file-size">${fileSize}</span>
            <div class="dimensions-info">
                <span class="dimensions">Chargement...</span>
                <i class='bx bx-right-arrow-alt'></i>
                <span class="new-dimensions">Calcul...</span>
            </div>
        </div>
        <div class="progress-container">
        <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: 0%" 
                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="progress-text">0%</div>
        </div>
    `;

    root.appendChild(imagewrapper);
    batch_files.push(file);

    // Créer un élément image temporaire pour obtenir les dimensions
    const img = new Image();
    img.onload = function() {
        const dimensions = imagewrapper.querySelector('.dimensions');
        if (dimensions) {
            dimensions.textContent = `${this.width} × ${this.height}px`;
            
            // Calculer et afficher les nouvelles dimensions
            const scaleFactor = parseFloat(document.getElementById('scale-factor').value);
            const newWidth = Math.round(this.width * scaleFactor);
            const newHeight = Math.round(this.height * scaleFactor);
            const newDimensions = imagewrapper.querySelector('.new-dimensions');
            if (newDimensions) {
                newDimensions.textContent = `${newWidth} × ${newHeight}px`;
            }
        }
        URL.revokeObjectURL(imageURL);
    };
    img.onerror = function() {
        showCustomAlert(`Impossible de charger l'image "${file.name}". Le fichier pourrait être corrompu.`, 'error');
        URL.revokeObjectURL(imageURL);
        onRemove(imagewrapper.querySelector('.remove-btn'));
    };
    img.src = imageURL;
}

// Suppression d'une image
function onRemove(button) {
    const wrapper = button.closest('.image-wrapper');
    const index = parseInt(wrapper.dataset.index);
    
    // Supprimer l'élément du DOM
    wrapper.remove();
    
    // Supprimer le fichier du tableau
    batch_files = batch_files.filter((_, i) => i !== index);
    
    // Mettre à jour les indices des éléments restants
    document.querySelectorAll('.image-wrapper').forEach((el, i) => {
        el.dataset.index = i;
    });
}

// Mise à jour de la progression
function updateProgress(index, progress) {
    const wrapper = document.querySelector(`.image-wrapper[data-index="${index}"]`);
    if (!wrapper) return;

    const progressBar = wrapper.querySelector('.progress-bar');
    const progressText = wrapper.querySelector('.progress-text');
    
    if (progressBar && progressText) {
        progressBar.style.width = `${progress}%`;
        progressBar.setAttribute('aria-valuenow', progress);
        progressText.textContent = `${progress}%`;
        
        // Ajouter/supprimer la classe 'complete' en fonction de la progression
        if (progress >= 100) {
            progressBar.classList.add('complete');
        } else {
            progressBar.classList.remove('complete');
        }
    }
}

// Fonction pour afficher l'alerte personnalisée
function showCustomAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `custom-alert ${type}`;
    alertDiv.innerHTML = `
        <div class="alert-content">
            <i class='bx ${type === 'success' ? 'bx-check' : 'bx-x'}'></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Animation d'entrée
    setTimeout(() => alertDiv.classList.add('show'), 10);
    
    // Suppression après 5 secondes
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

// Redimensionnement des fichiers
function scaleFiles(event) {
    event.preventDefault();
    
    if (batch_files.length === 0) {
        showCustomAlert('Veuillez sélectionner au moins une image', 'error');
        return;
    }

    // Désactiver le formulaire pendant le traitement
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    
    const formData = new FormData();
    batch_files.forEach((file, index) => {
        formData.append('files[]', file);
    });
    
    const scaleFactor = document.getElementById('scale-factor').value;
    formData.append('scale_factor', scaleFactor);
    
    // Réinitialiser les barres de progression
    document.querySelectorAll('.progress-bar').forEach(bar => {
        bar.style.width = '0%';
        bar.classList.remove('complete');
    });

    fetch('scale-action', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showCustomAlert(data.message);
            
            // Démarrer la vérification de la progression
            checkScaleProgress();
            
            // Mettre à jour le bouton si nécessaire
            if (data.btn) {
                submitBtn.innerHTML = data.btn + '<i class="bx bx-right-arrow-alt"></i>';
            }
        } else {
            throw new Error(data.error || 'Une erreur est survenue');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showCustomAlert(error.message || 'Une erreur est survenue lors du traitement', 'error');
    })
    .finally(() => {
        // Réactiver le formulaire
        submitBtn.disabled = false;
    });
}

let progressCheckInterval = null;

function checkScaleProgress() {
    if (progressCheckInterval) {
        clearInterval(progressCheckInterval);
    }

    let lastProgress = null;
    let stagnantCount = 0;
    const MAX_STAGNANT_CHECKS = 10;

    progressCheckInterval = setInterval(() => {
        fetch('/index.php/home/getScaleProgress', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data || typeof data.percentage === 'undefined') {
                throw new Error('Données de progression invalides');
            }

            const { current, total, percentage } = data;
            
            // Vérifier si la progression est stagnante
            const currentProgress = JSON.stringify({ current, total, percentage });
            if (currentProgress === lastProgress) {
                stagnantCount++;
                if (stagnantCount >= MAX_STAGNANT_CHECKS) {
                    clearInterval(progressCheckInterval);
                    throw new Error('Le traitement semble bloqué');
                }
            } else {
                stagnantCount = 0;
                lastProgress = currentProgress;
            }

            // Mettre à jour la progression
            if (current > 0 && current <= total) {
                // Mettre à jour tous les fichiers terminés
                for (let i = 0; i < current - 1; i++) {
                    updateProgress(i, 100);
                }

                // Mettre à jour le fichier en cours
                const currentIndex = current - 1;
                if (current === total && percentage === 100) {
                    updateProgress(currentIndex, 100);
                    clearInterval(progressCheckInterval);
                    showCustomAlert('Traitement terminé avec succès !');
                } else {
                    updateProgress(currentIndex, percentage);
                }

                // Réinitialiser les fichiers non traités
                for (let i = current; i < total; i++) {
                    updateProgress(i, 0);
                }
            }
        })
        .catch(error => {
            console.error('Erreur de progression:', error);
            clearInterval(progressCheckInterval);
            showCustomAlert(error.message, 'error');
        });
    }, 1000);

    // Arrêter la vérification après 5 minutes pour éviter les boucles infinies
    setTimeout(() => {
        if (progressCheckInterval) {
            clearInterval(progressCheckInterval);
            showCustomAlert('Le traitement a pris trop de temps', 'error');
        }
    }, 5 * 60 * 1000);
}

// Fonction pour ouvrir le dossier des images redimensionnées
function openImprovedFolder() {
    window.location.href = 'improved';
}

// Événement de redimensionnement
document.getElementById('upload-form').addEventListener('submit', scaleFiles);

// Mise à jour des dimensions lors du changement du facteur d'échelle
document.getElementById('scale-factor').addEventListener('change', function() {
    const scaleFactor = parseFloat(this.value);
    document.querySelectorAll('.image-wrapper').forEach(wrapper => {
        const dimensions = wrapper.querySelector('.dimensions');
        const newDimensions = wrapper.querySelector('.new-dimensions');
        if (dimensions && newDimensions) {
            const [width, height] = dimensions.textContent.split('×')[0].trim().split(' ');
            const newWidth = Math.round(parseInt(width) * scaleFactor);
            const newHeight = Math.round(parseInt(height) * scaleFactor);
            newDimensions.textContent = `${newWidth} × ${newHeight}px`;
        }
    });
});
