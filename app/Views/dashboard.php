<?php /** @var array $reservations */ ?>
<section class="dashboard">
    <aside class="sidebar">
        <h2>Tableau de bord</h2>
        <a class="active" href="<?= BASE_URL ?>/dashboard">Demandes en attente</a>
        <a href="<?= BASE_URL ?>/dashboard/reservations">Toutes les réservations</a>
        <a href="<?= BASE_URL ?>/dashboard/bikes">Vélos</a>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="<?= BASE_URL ?>/dashboard/users">Utilisateurs</a>
        <?php endif; ?>
    </aside>
    <div class="dashboard-content">
        <div class="section-heading">
            <div>
                <p class="eyebrow">À traiter</p>
                <h1>Demandes en attente</h1>
            </div>
            <span class="count"><?= count($reservations) ?></span>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Référence</th><th>Citoyen</th><th>Vélo</th><th>Période</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr data-reservation="<?= $reservation['reservation_id'] ?>">
                        <td><strong><?= htmlspecialchars($reservation['res_num']) ?></strong></td>
                        <td><?= htmlspecialchars($reservation['firstname'] . ' ' . $reservation['lastname']) ?></td>
                        <td><?= htmlspecialchars($reservation['registration_number']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($reservation['start_date'])) ?><br><small>au <?= date('d/m/Y H:i', strtotime($reservation['end_date'])) ?></small></td>
                        <td class="actions">
                            <button class="button-small success" data-status="accepted">Accepter</button>
                            <button class="button-small danger" data-status="rejected">Refuser</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$reservations): ?>
                    <tr><td colspan="5" class="empty-state">Aucune demande en attente.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
