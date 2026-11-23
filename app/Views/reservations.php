<?php /** @var array $reservations */ ?>
<section class="dashboard">
    <aside class="sidebar">
        <h2>Tableau de bord</h2>
        <a href="<?= BASE_URL ?>/dashboard">Demandes en attente</a>
        <a class="active" href="<?= BASE_URL ?>/dashboard/reservations">Toutes les réservations</a>
        <a href="<?= BASE_URL ?>/dashboard/bikes">Vélos</a>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?><a href="<?= BASE_URL ?>/dashboard/users">Utilisateurs</a><?php endif; ?>
    </aside>
    <div class="dashboard-content">
        <div class="section-heading">
            <div><p class="eyebrow">Recherche dynamique</p><h1>Toutes les réservations</h1></div>
            <input id="reservation-search" class="search" type="search" placeholder="Nom, numéro, vélo...">
        </div>
        <div class="table-wrap">
            <table id="reservations-table"><thead><tr><th>Référence</th><th>Citoyen</th><th>Vélo</th><th>Période</th><th>Statut</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr data-row>
                        <td><strong><?= htmlspecialchars($reservation['res_num']) ?></strong></td>
                        <td><?= htmlspecialchars($reservation['firstname'] . ' ' . $reservation['lastname']) ?></td>
                        <td><?= htmlspecialchars($reservation['registration_number']) ?></td>
                        <td><?= date('d/m/Y', strtotime($reservation['start_date'])) ?> – <?= date('d/m/Y', strtotime($reservation['end_date'])) ?></td>
                        <td><span class="status <?= $reservation['status'] ?>"><?= $reservation['status'] ?></span></td>
                        <td><?php if ($_SESSION['user']['role'] === 'admin'): ?><button class="button-small danger" data-delete-reservation="<?= $reservation['reservation_id'] ?>">Supprimer</button><?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
