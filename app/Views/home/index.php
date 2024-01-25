<?= $this->extend('base') ?>

<?= $this->section('head') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="main-box">
    <?= $this->include('partials/modal.php') ?>
    <h1>Convertissez vos images au format désiré</h1>
    <form class="d-flex w-100 justify-content-center text-center" action="action" id="upload-form" enctype="multipart/form-data" method="post">
        <div class="drop-box">
            <input type="file" id="inputfile" name="uploadfiles[]" class="btn" />
            <input type="hidden" name="action" value="convert">
            <label class="mb-1" for="inputfile"><i class="bx bx-upload"></i>Sélectionner des images</label>
            <div class="drag-zone"><i class='bx bx-plus-circle'></i><br>déposez vos images ici
            </div>
            <div id="image-root"></div>
            <div class="start_box">
                <button class="btn" onclick="onConvert()"><?= $btn ?><i class='bx bx-right-arrow-alt'></i></button>
            </div>
    </form>
    </div>
</main>

<section class="content-box">
    <section class="w-70 container  text-center ">
        <h1>Facilité d'utilisation:</h1>
        <p>la plateforme se distingue par sa facilité d'utilisation. Vous n'avez qu'à
            télécharger votre fichier, choisir le format de sortie, et laisser l'outil convertir votre fichier
            rapidement et efficacement.
        </p>
        <h1>Confidentialité garantie:</h1>
        <p>avec Express Convert IO, vos fichiers sont en sécurité. Ils sont supprimés des serveurs peu après la
            conversion, vous assurant que personne n'a accès à vos fichiers.
        </p>
        <h1>Accessibilité:</h1>
        <p>En tant que plateforme en ligne, vous avez accès à Express Convert IO sur n'importe quel appareil, pourvu
            qu'il ait une connexion internet. Peu importe si vous êtes sur votre ordinateur de bureau, votre tablette ou
            votre smartphone, vos outils de conversion de fichiers sont toujours à portée de main.
        </p>
    </section>
</section>
<?= $this->endSection() ?>


<!-- SCRIPTS -->
<?= $this->section('js') ?>
<script defer src="<?= base_url('assets/js/convert.js') ?>"></script>
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
<!-- -->