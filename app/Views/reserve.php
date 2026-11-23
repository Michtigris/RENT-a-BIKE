<?php
/** @var array $bike */
/** @var array $errors */
?>
<section class="form-page">
    <a class="back-link" href="<?= BASE_URL ?>/">← Retour aux vélos</a>
    <div class="form-card">
        <div class="form-bike">
            <span class="status available">Disponible</span>
            <h1><?= htmlspecialchars($bike['registration_number']) ?></h1>
            <?php if (!empty($bike['photo'])): ?>
                <img src="<?= BASE_URL ?>/public/<?= htmlspecialchars($bike['photo']) ?>" alt="">
            <?php endif; ?>
        </div>
        <form method="post" action="<?= BASE_URL ?>/reserve">
            <input type="hidden" name="bike_id" value="<?= $bike['bike_id'] ?>">
            <h2>Vos informations</h2>
            <?php foreach ($errors as $error): ?>
                <div class="alert"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
            <div class="field-grid">
                <label>Prénom<input name="firstname" required value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>"></label>
                <label>Nom<input name="lastname" required value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>"></label>
            </div>
            <label>Identifiant numérique<input name="login" type="number" min="1000" required value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"></label>
            <label>Mot de passe<input name="password" type="password" minlength="6" required></label>
            <div class="field-grid">
                <label>Date de début<input name="start_date" type="datetime-local" required value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>"></label>
                <label>Date de fin<input name="end_date" type="datetime-local" required value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>"></label>
            </div>
            <button class="button full" type="submit">Confirmer la réservation</button>
        </form>
    </div>
</section>
