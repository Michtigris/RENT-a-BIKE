<?php /** @var array $users */ ?>
<section class="dashboard">
    <aside class="sidebar">
        <h2>Tableau de bord</h2>
        <a href="<?= BASE_URL ?>/dashboard">Demandes en attente</a>
        <a href="<?= BASE_URL ?>/dashboard/reservations">Toutes les réservations</a>
        <a href="<?= BASE_URL ?>/dashboard/bikes">Vélos</a>
        <a class="active" href="<?= BASE_URL ?>/dashboard/users">Utilisateurs</a>
    </aside>
    <div class="dashboard-content">
        <div class="section-heading"><div><p class="eyebrow">Comptes</p><h1>Utilisateurs</h1></div></div>
        <div class="table-wrap">
            <table><thead><tr><th>Nom</th><th>Identifiant</th><th>Rôle</th><th>Action</th></tr></thead><tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></td>
                    <td><?= htmlspecialchars($user['login']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?php if ($user['user_id'] !== $_SESSION['user']['user_id']): ?><button class="button-small danger" data-delete-user="<?= $user['user_id'] ?>">Supprimer</button><?php endif; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody></table>
        </div>
    </div>
</section>
