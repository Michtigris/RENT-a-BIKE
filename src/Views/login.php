<?php /** @var ?string $error */ ?>
<section class="form-page narrow">
    <form class="form-card simple-form" method="post" action="<?= BASE_URL ?>/login">
        <?= csrfField() ?>
        <p class="eyebrow">Administration</p>
        <h1>Connexion employé</h1>
        <?php if ($error): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <label>Identifiant<input name="login" type="number" required></label>
        <label>Mot de passe<input name="password" type="password" required></label>
        <button class="button full" type="submit">Se connecter</button>
    </form>
</section>
