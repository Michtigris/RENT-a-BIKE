<?php /** @var string $view */ ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'VéloLibre') ?></title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>/assets/style.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="<?= BASE_URL ?>/"><span>VL</span> VéloLibre</a>
        <nav>
            <a href="<?= BASE_URL ?>/">Vélos</a>
            <?php if (!empty($_SESSION['user'])): ?>
                <a href="<?= BASE_URL ?>/dashboard">Tableau de bord</a>
                <form method="post" action="<?= BASE_URL ?>/logout">
                    <?= csrfField() ?>
                    <button class="button-small" type="submit">Déconnexion</button>
                </form>
            <?php else: ?>
                <a class="nav-button" href="<?= BASE_URL ?>/login">Employés</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <?php if (isset($_SESSION['message'])): ?>
            <p class="alert" role="status"><?= htmlspecialchars($_SESSION['message']) ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <?php require __DIR__ . '/' . $view . '.php'; ?>
    </main>
    <script src="<?= PUBLIC_URL ?>/assets/app.js"></script>
</body>
</html>
