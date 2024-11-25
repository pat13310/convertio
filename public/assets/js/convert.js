// Drop box
const dragZone = document.querySelector(".drag-zone");
const fileInput = document.getElementById("inputfile");
const root = document.querySelector("#root");

// lot de fichiers pour la conversion
let batch_files = [];
// Stocker les URLs des objets pour le nettoyage
let objectUrls = [];

// Gestion du drag & drop
dragZone.addEventListener('dragenter', (e) => {
    e.preventDefault();
    dragZone.classList.add('dragover');
});

dragZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dragZone.classList.add('dragover');
});

dragZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dragZone.classList.remove('dragover');
});

dragZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dragZone.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        Array.from(files).forEach(file => addImageCard(file));
    }
});

// Gestion de la sélection de fichier
fileInput.addEventListener("change", function() {
    const files = [...fileInput.files];
    files.forEach(file => addImageCard(file));
});

// Formatage de la taille du fichier
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Nettoyage des ressources
function cleanupResources() {
    // Révoquer toutes les URLs d'objets
    objectUrls.forEach(url => URL.revokeObjectURL(url));
    objectUrls = [];
    // Vider la liste des fichiers
    batch_files = [];
    // Réinitialiser l'input file
    fileInput.value = '';
}

// Ajout d'une image à la liste
function addImageCard(file) {
    const imageURL = URL.createObjectURL(file);
    objectUrls.push(imageURL); // Stocker l'URL pour le nettoyage ultérieur
    const fileSize = formatFileSize(file.size);
    
    const imagewrapper = document.createElement('div');
    imagewrapper.className = 'image-wrapper';
    imagewrapper.dataset.index = batch_files.length;
    
    // Tronquer le nom du fichier si nécessaire
    const displayName = file.name.length > 20 ? file.name.substring(0, 20) + '...' : file.name;
    
    // Déterminer le format par défaut basé sur l'extension du fichier
    const extension = file.name.split('.').pop().toLowerCase();
    const defaultFormat = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(extension) ? extension : 'jpg';
    
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
        </div>
        <div class="format-select">
            <select class="form-select" name="format" onchange="onFormatChange(this, ${batch_files.length})">
                <option value="jpg" ${defaultFormat === 'jpg' || defaultFormat === 'jpeg' ? 'selected' : ''}>JPG</option>
                <option value="png" ${defaultFormat === 'png' ? 'selected' : ''}>PNG</option>
                <option value="gif" ${defaultFormat === 'gif' ? 'selected' : ''}>GIF</option>
                <option value="webp" ${defaultFormat === 'webp' ? 'selected' : ''}>WEBP</option>
                <option value="bmp" ${defaultFormat === 'bmp' ? 'selected' : ''}>BMP</option>
            </select>
        </div>
        <div class="progress-container">
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 0%" 
                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="progress-text">0%</div>
        </div>
        <div class="download-link">
            <a href="#" class="btn btn-success btn-sm disabled" download title="Télécharger">
                <i class='bx bx-download'></i>
            </a>
        </div>
    `;

    root.appendChild(imagewrapper);
    batch_files.push(file);
}

// Suppression d'un fichier
function onRemove(button) {
    const wrapper = button.closest('.image-wrapper');
    const index = parseInt(wrapper.dataset.index);
    
    // Supprimer l'URL de l'objet correspondant
    if (objectUrls[index]) {
        URL.revokeObjectURL(objectUrls[index]);
        objectUrls.splice(index, 1);
    }
    
    batch_files.splice(index, 1);
    wrapper.remove();
    
    // Mettre à jour les index des wrappers restants
    document.querySelectorAll('.image-wrapper').forEach((wrap, idx) => {
        wrap.dataset.index = idx;
    });
}

// Conversion d'un seul fichier
async function convertSingleFile(file, format, index) {
    const formData = new FormData();
    formData.append('uploadfile', file);
    formData.append('format', format);
    formData.append('index', index);

    let progressInterval = null;

    try {
        // Démarrer le suivi de la progression
        progressInterval = setInterval(async () => {
            try {
                const progressResponse = await fetch(`${baseUrl}/convert/progress/${index}`);
                const data = await progressResponse.json();
                if (data && typeof data.progress !== 'undefined') {
                    updateProgressBar(index, Math.min(99, data.progress)); // Max 99% jusqu'à la fin
                }
            } catch (error) {
                console.error('Error fetching progress:', error);
            }
        }, 500);

        // Démarrer la conversion
        const response = await fetch(`${baseUrl}/convert-action`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        // Nettoyer l'intervalle une fois la conversion terminée
        if (progressInterval) {
            clearInterval(progressInterval);
        }

        if (result.success) {
            updateProgressBar(index, 100); // 100% une fois terminé
            if (result.download_url) {
                updateDownloadLink(index, result.download_url, result.filename);
            }
            return true;
        } else {
            showError(index, result.message || 'Erreur de conversion');
            return false;
        }
    } catch (error) {
        // Nettoyer l'intervalle en cas d'erreur
        if (progressInterval) {
            clearInterval(progressInterval);
        }
        showError(index, 'Erreur lors de la conversion');
        console.error('Error:', error);
        return false;
    }
}

function updateProgressBar(index, progress) {
    const wrapper = document.querySelector(`.image-wrapper[data-index="${index}"]`);
    if (wrapper) {
        const progressContainer = wrapper.querySelector('.progress-container');
        const progressBar = progressContainer.querySelector('.progress-bar');
        const progressText = progressContainer.querySelector('.progress-text');
        
        // S'assurer que la progression est un nombre entre 0 et 100
        progress = Math.max(0, Math.min(100, parseInt(progress)));
        
        if (progressBar && progressText) {
            progressBar.style.width = progress + '%';
            progressBar.setAttribute('aria-valuenow', progress);
            progressText.textContent = progress + '%';
            
            // Si la conversion est terminée (100%), garder ces valeurs
            if (progress === 100) {
                progressBar.style.width = '100%';
                progressBar.setAttribute('aria-valuenow', '100');
                progressText.textContent = '100%';
            }
        }
    }
}

// Mise à jour de la progression
function updateProgress(index, progress) {
    const wrapper = document.querySelector(`.image-wrapper[data-index="${index}"]`);
    if (wrapper) {
        const progressBar = wrapper.querySelector('.progress-bar');
        const progressText = wrapper.querySelector('.progress-text');
        
        // S'assurer que la progression est un nombre entre 0 et 100
        progress = Math.max(0, Math.min(100, parseInt(progress)));
        
        // Mettre à jour la barre de progression
        progressBar.style.width = `${progress}%`;
        progressBar.setAttribute('aria-valuenow', progress);
        
        // Mettre à jour le texte de progression
        progressText.textContent = `${progress}%`;
        
        // Ajouter une classe pour l'animation si en cours
        if (progress > 0 && progress < 100) {
            progressBar.classList.add('progressing');
        } else {
            progressBar.classList.remove('progressing');
        }
    }
}

// Afficher une erreur sur une carte
function showError(index, message) {
    const wrapper = document.querySelector(`.image-wrapper[data-index="${index}"]`);
    if (wrapper) {
        const progressContainer = wrapper.querySelector('.progress-container');
        const progressBar = progressContainer.querySelector('.progress-bar');
        const progressText = progressContainer.querySelector('.progress-text');
        progressBar.style.backgroundColor = '#dc3545';
        progressText.textContent = message;
        progressText.style.color = '#dc3545';
    }
}

// Mise à jour du lien de téléchargement
function updateDownloadLink(index, downloadUrl, filename) {
    const wrapper = document.querySelector(`.image-wrapper[data-index="${index}"]`);
    if (wrapper) {
        const downloadLinkDiv = wrapper.querySelector('.download-link');
        const downloadLink = downloadLinkDiv.querySelector('a');
        if (downloadLink) {
            downloadLink.href = downloadUrl;
            downloadLink.setAttribute('download', filename);
            downloadLink.classList.remove('disabled');
        }
    }
}

// Conversion des fichiers
async function convertFiles(event) {
    event.preventDefault();
    
    if (batch_files.length === 0) {
        showCustomAlert('Veuillez sélectionner au moins un fichier', 'error');
        return;
    }

    const submitBtn = document.querySelector('#upload-form button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
    }

    try {
        const results = await Promise.all(batch_files.map(async (file, index) => {
            const wrapper = document.querySelector(`.image-wrapper[data-index="${index}"]`);
            const formatSelect = wrapper.querySelector('select[name="format"]');
            const format = formatSelect ? formatSelect.value : 'jpg';
            
            return await convertSingleFile(file, format, index);
        }));

        const successCount = results.filter(result => result === true).length;
        const errorCount = results.filter(result => result === false).length;

        let message = '';
        if (successCount > 0) {
            message += `${successCount} fichier(s) converti(s) avec succès. `;
        }
        if (errorCount > 0) {
            message += `${errorCount} erreur(s) de conversion.`;
        }

        showCustomAlert(message, errorCount === 0 ? 'success' : 'warning');
    } catch (error) {
        console.error('Error:', error);
        showCustomAlert('Erreur lors de la conversion des fichiers', 'error');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
        }
    }
}

// Événement de changement de format
function onFormatChange(select, index) {
    const wrapper = select.closest('.image-wrapper');
    const downloadLink = wrapper.querySelector('.download-link a');
    const progressContainer = wrapper.querySelector('.progress-container');
    const progressBar = progressContainer.querySelector('.progress-bar');
    const progressText = progressContainer.querySelector('.progress-text');
    
    // Réinitialiser l'interface
    downloadLink.classList.add('disabled');
    downloadLink.href = '#';
    progressBar.style.width = '0%';
    progressBar.style.backgroundColor = '#0dcaf0';
    progressText.textContent = '0%';
    progressText.style.color = '';
}

// Événement de conversion
document.getElementById('upload-form').addEventListener('submit', convertFiles);

// Fonction pour afficher l'alerte personnalisée
function showCustomAlert(message, type = 'success') {
    const alertBox = document.getElementById('custom-alert');
    const alertMessage = alertBox.querySelector('.alert-message');
    const icon = alertBox.querySelector('i');
    
    alertBox.className = `custom-alert ${type}`;
    alertMessage.textContent = message;
    icon.className = type === 'success' ? 'bx bx-check-circle' : 'bx bx-x-circle';
    
    alertBox.classList.add('show');
    
    setTimeout(() => {
        alertBox.classList.remove('show');
    }, 3000);
}

function checkProgress(index) {
    fetch(`${baseUrl}/convert/progress/${index}`)
        .then(response => response.json())
        .then(data => {
            updateProgressBar(index, data.progress);
            if (data.progress < 100) {
                setTimeout(() => checkProgress(index), 1000);
            }
        })
        .catch(error => console.error('Error checking progress:', error));
}
