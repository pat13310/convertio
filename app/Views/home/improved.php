<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<main>
    <div class="container py-4">
        <h1 class="mb-4"><?= $title ?></h1>
        
        <?php if (empty($files)): ?>
            <div class="alert alert-info">
                Aucune image améliorée trouvée.
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($files as $file): ?>
                    <div class="col">
                        <div class="card h-100">
                            <img src="<?= base_url($file['url']) ?>" class="card-img-top" alt="<?= $file['name'] ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= $file['name'] ?></h5>
                                <p class="card-text">
                                    Taille: <?= number_format($file['size'] / 1024, 2) ?> Ko<br>
                                    Date: <?= $file['date'] ?>
                                </p>
                                <a href="<?= base_url($file['url']) ?>" class="btn btn-primary" download>
                                    <i class="bx bx-download"></i> Télécharger
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="<?= base_url('scale') ?>" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Retour au redimensionnement
            </a>
        </div>
    </div>
</main>
<?= $this->endSection() ?>
