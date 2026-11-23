<?php /** @var string $reservationNum */ ?>
<section class="confirmation">
    <div class="success-icon">✓</div>
    <h1>Demande enregistrée</h1>
    <p>Conservez votre identifiant de réservation :</p>
    <strong class="reservation-number"><?= htmlspecialchars($reservationNum) ?></strong>
    <p>La demande sera validée par un employé municipal.</p>
    <a class="button" href="<?= BASE_URL ?>/">Retour à l'accueil</a>
</section>
