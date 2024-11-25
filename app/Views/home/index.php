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
    <?= $this->include('partials/modal.php') ?>
    <h1>Convertissez vos images au format désiré</h1>
    <form class="d-flex w-100 justify-content-center text-center" action="<?= base_url('convert-action') ?>" id="upload-form" enctype="multipart/form-data" method="post">
        <div class="drop-box">
            <input type="file" id="inputfile" name="uploadfiles[]" class="btn" multiple accept="image/*" />
            <input type="hidden" name="action" value="convert">
            <label class="mb-3" for="inputfile"><i class="bx bx-upload"></i>Sélectionner des images</label>
            <div class="drag-zone">
                <i class='bx bx-plus-circle'></i>
                <span>Déposez vos images ici</span>
            </div>
            <div id="root" class="root-card"></div>            
            <div class="start_box">
                <button type="submit" class="btn"><?= $btn ?><i class='bx bx-right-arrow-alt'></i></button>
            </div>
        </div>
    </form>
</main>

<section class="content-box">
    <section class="w-70 container text-center">
        <h2>Facilité d'utilisation:</h2>
        <p>La plateforme se distingue par sa facilité d'utilisation. Vous n'avez qu'à
            télécharger votre fichier, choisir le format de sortie, et laisser l'outil convertir votre fichier
            rapidement et efficacement.
        </p>
        <h2>Confidentialité garantie:</h2>
        <p>Avec Express Convert IO, vos fichiers sont en sécurité. Ils sont supprimés des serveurs peu après la
            conversion, vous assurant que personne n'a accès à vos fichiers.
        </p>
        <h2>Accessibilité:</h2>
        <p>En tant que plateforme en ligne, vous avez accès à Express Convert IO sur n'importe quel appareil, pourvu
            qu'il ait une connexion internet. Peu importe si vous êtes sur votre ordinateur de bureau, votre tablette ou
            votre smartphone, vos outils de conversion de fichiers sont toujours à portée de main.
        </p>
    </section>
</section>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    var baseUrl = '<?= base_url() ?>';
</script>
<script defer src="<?= base_url('/assets/js/convert.js') ?>"></script>
<script>
    var modal = new bootstrap.Modal(document.getElementById('settingsModal'));

    function onSettings(elem) {
        modal.show();
    }

    function onConfirm() {
        modal.hide();
    }
</script>
<?= $this->endSection() ?>