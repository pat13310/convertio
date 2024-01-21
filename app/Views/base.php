<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Express Convert IO</title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <link href="<?= base_url('node_modules/bootstrap/dist/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/style/common.min.css') ?>" rel="stylesheet">
    
    <link href="<?= base_url('assets/style/footer.min.css') ?>" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('node_modules/boxicons/css/boxicons.min.css')?>" />

</head>

<body>
    <?= $this->include('partials/menu') ?>
    <?= $this->renderSection('content') ?>
    <?= $this->include('partials/footer') ?>
    <script src="<?= base_url('node_modules/bootstrap/dist/js/bootstrap.min.js') ?>">


    </script>
    <?= $this->renderSection('js') ?>

</body>

</html>