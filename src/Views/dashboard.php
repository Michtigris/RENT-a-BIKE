<?php /** @var array $reservations */ ?>
<section class="dashboard">
    <aside class="sidebar">
        <h2>Tableau de bord</h2>
        <a class="active" href="<?= BASE_URL ?>/dashboard">Demandes en attente</a>
        <a href="<?= BASE_URL ?>/dashboard/reservations">Toutes les réservations</a>
        <a href="<?= BASE_URL ?>/dashboard/bikes">Vélos</a>
        <a href="<?= BASE_URL ?>/dashboard/users">Utilisateurs</a>
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
                    <tr>
                        <td><strong><?= htmlspecialchars($reservation['res_num']) ?></strong></td>
                        <td><?= htmlspecialchars($reservation['firstname'] . ' ' . $reservation['lastname']) ?></td>
                        <td><?= htmlspecialchars($reservation['registration_number']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($reservation['start_date'])) ?><br><small>au <?= date('d/m/Y H:i', strtotime($reservation['end_date'])) ?></small></td>
                        <td>
                            <form class="actions" method="post" action="<?= BASE_URL ?>/dashboard/reservation/status">
                                <?= csrfField() ?>
                                <input type="hidden" name="reservation_id" value="<?= $reservation['reservation_id'] ?>">
                                <button class="button-small success" name="status" value="accepted">Accepter</button>
                                <button class="button-small danger" name="status" value="rejected">Refuser</button>
                            </form>
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
