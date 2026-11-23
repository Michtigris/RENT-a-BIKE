<?php
$statuses = ['pending' => 'En attente', 'accepted' => 'Acceptée', 'rejected' => 'Refusée'];
?>
<?php foreach ($reservations as $reservation): ?>
    <tr>
        <td><strong><?= htmlspecialchars($reservation['res_num']) ?></strong></td>
        <td><?= htmlspecialchars($reservation['firstname'] . ' ' . $reservation['lastname']) ?></td>
        <td><?= htmlspecialchars($reservation['registration_number']) ?></td>
        <td><?= date('d/m/Y H:i', strtotime($reservation['start_date'])) ?><br><small>au <?= date('d/m/Y H:i', strtotime($reservation['end_date'])) ?></small></td>
        <td><span class="status <?= $reservation['status'] ?>"><?= $statuses[$reservation['status']] ?></span></td>
        <td>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <form method="post" action="<?= BASE_URL ?>/dashboard/reservation/delete" data-confirm="Supprimer cette réservation ?">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $reservation['reservation_id'] ?>">
                    <input type="hidden" name="q" value="<?= htmlspecialchars($term) ?>">
                    <button class="button-small danger" type="submit">Supprimer</button>
                </form>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
<?php if (!$reservations): ?>
    <tr><td colspan="6" class="empty-state">Aucune réservation trouvée.</td></tr>
<?php endif; ?>
