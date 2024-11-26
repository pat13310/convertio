<?= $this->extend('base') ?>

<?= $this->section('head') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="custom-alert" class="custom-alert">
    <div class="alert-content">
        <i class='bx bx-check-circle'></i>
        <span class="alert-message"></span>
    </div>
</div>
<main class="main-box">
    <h1>Redimensionnez vos images à la taille souhaitée</h1>

    <form class="d-flex w-100 justify-content-center text-center" action="<?= base_url('scale-action') ?>" id="upload-form" enctype="multipart/form-data" method="post">
        <div class="drop-box">
            <input type="file" id="inputfile" name="files[]" class="btn" multiple accept="image/*" />
            <input type="hidden" name="action" value="scale">
            <label class="mb-3" for="inputfile"><i class="bx bx-upload"></i>Sélectionner des images</label>
            <div class="drag-zone">
                <i class='bx bx-plus-circle'></i>
                <span>Déposez vos images ici</span>
            </div>
            <div id="image-root"></div>
            <div class="start_box">
                <button type="submit" class="btn"><?= $btn ?><i class='bx bx-right-arrow-alt'></i></button>
            </div>
            <div class="format-box">
                <select id="scale-factor" name="scale_factor" class="scale-select">
                    <option value="0.25">0.25x</option>
                    <option value="0.5">0.5x</option>
                    <option value="0.75">0.75x</option>
                    <option value="1" selected>1x</option>
                    <option value="1.5">1.5x</option>
                    <option value="2">2x</option>
                    <option value="3">3x</option>
                    <option value="4">4x</option>
                </select>
            </div>
            
        </div>
    </form>
</main>

<section class="content-box">
    <section class="w-70 container text-center">
        <h1>Redimensionnement précis:</h1>
        <p>Notre outil vous permet de redimensionner vos images avec précision en spécifiant le facteur de multiplication souhaité.
           Le redimensionnement conserve automatiquement les proportions de vos images pour un résultat optimal.
        </p>
        <h1>Confidentialité garantie:</h1>
        <p>Avec Express Convert IO, vos fichiers sont en sécurité. Ils sont supprimés des serveurs peu après le
            redimensionnement, vous assurant que personne n'a accès à vos fichiers.
        </p>
        <h1>Accessibilité:</h1>
        <p>En tant que plateforme en ligne, vous avez accès à Express Convert IO sur n'importe quel appareil, pourvu
            qu'il ait une connexion internet. Redimensionnez vos images facilement, où que vous soyez.
        </p>
    </section>
</section>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script defer src="<?= base_url('/assets/js/scale.js') ?>"></script>
<?= $this->endSection() ?>
