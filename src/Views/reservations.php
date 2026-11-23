<?php /** @var array $reservations */ ?>
<section class="dashboard">
    <aside class="sidebar">
        <h2>Tableau de bord</h2>
        <a href="<?= BASE_URL ?>/dashboard">Demandes en attente</a>
        <a class="active" href="<?= BASE_URL ?>/dashboard/reservations">Toutes les réservations</a>
        <a href="<?= BASE_URL ?>/dashboard/bikes">Vélos</a>
        <a href="<?= BASE_URL ?>/dashboard/users">Utilisateurs</a>
    </aside>
    <div class="dashboard-content">
        <div class="section-heading">
            <h1>Toutes les réservations</h1>
            <form method="get" action="<?= BASE_URL ?>/dashboard/reservations">
                <input id="reservation-search" class="search" type="search" name="q" aria-label="Rechercher une réservation" placeholder="Nom, numéro, vélo..." value="<?= htmlspecialchars($term) ?>" data-url="<?= BASE_URL ?>/dashboard/search">
                <button class="button-small" type="submit">Rechercher</button>
            </form>
        </div>
        <p class="alert" id="search-error" role="status" hidden>La recherche automatique a échoué. Utilisez le bouton Rechercher.</p>
        <div class="table-wrap">
            <table id="reservations-table">
                <thead><tr><th>Référence</th><th>Citoyen</th><th>Vélo</th><th>Période</th><th>Statut</th><th>Action</th></tr></thead>
                <tbody><?php require __DIR__ . '/reservationRows.php'; ?></tbody>
            </table>
        </div>
    </div>
</section>
