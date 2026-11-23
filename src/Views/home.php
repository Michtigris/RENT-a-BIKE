<?php /** @var array $bikes */ ?>
<section class="hero">
    <div>
        <p class="eyebrow">Location gratuite</p>
        <h1>Pédalez dans votre ville</h1>
        <p>Choisissez un vélo disponible et réservez-le pour la période souhaitée.</p>
    </div>
    <div class="hero-icon" aria-hidden="true">🚲</div>
</section>

<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Notre flotte</p>
            <h2>Vélos disponibles</h2>
        </div>
        <span class="count"><?= count($bikes) ?> vélo<?= count($bikes) > 1 ? 's' : '' ?></span>
    </div>
    <div class="bike-grid">
        <?php if (!$bikes): ?>
            <div class="empty-state">Aucun vélo disponible pour le moment.</div>
        <?php endif; ?>
        <?php foreach ($bikes as $bike): ?>
            <article class="bike-card">
                <?php if (!empty($bike['photo'])): ?>
                    <img src="<?= PUBLIC_URL ?>/<?= htmlspecialchars($bike['photo']) ?>" alt="Vélo <?= htmlspecialchars($bike['registration_number']) ?>">
                <?php else: ?>
                    <div class="bike-placeholder" aria-hidden="true">🚲</div>
                <?php endif; ?>
                <div class="bike-card-body">
                    <span class="status available">Disponible</span>
                    <h3><?= htmlspecialchars($bike['registration_number']) ?></h3>
                    <a class="button" href="<?= BASE_URL ?>/reserve?bike=<?= $bike['bike_id'] ?>">Réserver</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
