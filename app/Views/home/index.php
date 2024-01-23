<?= $this->extend('base') ?>

<?= $this->section('head') ?>

<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="main-box">
    <?= $this->include('partials/modal.php') ?>
    <h1>Convertissez vos images au format désiré</h1>
    <div class="drop-box">
        <input type="file" id="inputfile" class="btn" id="upload" />
        <label class="mb-1" for="inputfile"><i class="bx bx-upload"></i>Sélectionner des images</label>
        <div class="drag-zone"><i class='bx bx-plus-circle'></i><br>déposez vos images ici
        </div>
        <div id="image-root"></div>
        <div class="start_box">
            <button class="btn">Convertir<i class='bx bx-right-arrow-alt' ></i></button>
        </div>
    </div>
</section>

<div class="content-box">

    <section class="w-70 container  text-center ">

        <h4>Facilité d'utilisation:</h4>
        <p>la plateforme se distingue par sa facilité d'utilisation. Vous n'avez qu'à
            télécharger votre fichier, choisir le format de sortie, et laisser l'outil convertir votre fichier
            rapidement et efficacement.
        </p>
        <h4>Confidentialité garantie:</h4>
        <p>avec Express Convert IO, vos fichiers sont en sécurité. Ils sont supprimés des serveurs peu après la
            conversion, vous assurant que personne n'a accès à vos fichiers.
        </p>
        <h4>Accessibilité:</h4>
        <p>En tant que plateforme en ligne, vous avez accès à Express Convert IO sur n'importe quel appareil, pourvu
            qu'il ait une connexion internet. Peu importe si vous êtes sur votre ordinateur de bureau, votre tablette ou
            votre smartphone, vos outils de conversion de fichiers sont toujours à portée de main.
        </p>

    </section>

</div>
<?= $this->endSection() ?>


<!-- SCRIPTS -->
<?= $this->section('js') ?>
<script src="<?= base_url('assets/js/convert.js') ?>">
    /* function toggleMenu() {
      var menuItems = document.getElementsByClassName('menu-item');
      for (var i = 0; i < menuItems.length; i++) {
        var menuItem = menuItems[i];
        menuItem.classList.toggle("hidden");
      }
    } */
</script>
<script>
     var modal = new bootstrap.Modal(document.getElementById('settingsModal'));
       
    function onSettings(elem) {
        modal.show();
    }

    function onConfirm(){
        modal.hide();
    }
    /*  document.getElementById('openModalBtn').addEventListener('click', function () {
      var modal = new bootstrap.Modal(document.getElementById('exampleModal'));
      modal.show();
    }); */
</script>
<?= $this->endSection() ?>
<!-- -->