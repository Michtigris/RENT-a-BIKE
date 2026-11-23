<?php /** @var string $reservationNum */ ?>
<section class="confirmation">
    <div class="success-icon">✓</div>
    <p class="eyebrow">Demande enregistrée</p>
    <h1>Réservation confirmée</h1>
    <p>Conservez votre identifiant de réservation :</p>
    <strong class="reservation-number"><?= htmlspecialchars($reservationNum) ?></strong>
    <p>La demande sera validée par un employé municipal.</p>
    <a class="button" href="<?= BASE_URL ?>/">Retour à l'accueil</a>
</section>
