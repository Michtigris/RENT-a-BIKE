<?php /** @var array $bikes */ ?>
<section class="dashboard">
    <aside class="sidebar">
        <h2>Tableau de bord</h2>
        <a href="<?= BASE_URL ?>/dashboard">Demandes en attente</a>
        <a href="<?= BASE_URL ?>/dashboard/reservations">Toutes les réservations</a>
        <a class="active" href="<?= BASE_URL ?>/dashboard/bikes">Vélos</a>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="<?= BASE_URL ?>/dashboard/users">Utilisateurs</a>
        <?php endif; ?>
    </aside>
    <div class="dashboard-content">
        <div class="section-heading"><div><p class="eyebrow">Flotte</p><h1>Gestion des vélos</h1></div></div>
        <form class="inline-form" method="post" enctype="multipart/form-data" action="<?= BASE_URL ?>/dashboard/bikes">
            <input name="registration_number" placeholder="Numéro d'immatriculation" required>
            <label class="check"><input type="checkbox" name="availability" checked> Disponible</label>
            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp">
            <button class="button" type="submit">Ajouter</button>
        </form>
        <div class="table-wrap">
            <table><thead><tr><th>Photo</th><th>Immatriculation</th><th>Disponibilité</th><th>Action</th></tr></thead><tbody>
            <?php foreach ($bikes as $bike): ?>
                <tr>
                    <td><?php if (!empty($bike['photo'])): ?><img class="bike-thumb" src="<?= BASE_URL ?>/public/<?= htmlspecialchars($bike['photo']) ?>" alt=""><?php else: ?>🚲<?php endif; ?></td>
                    <td><strong><?= htmlspecialchars($bike['registration_number']) ?></strong></td>
                    <td><span class="status <?= $bike['availability'] ? 'available' : 'unavailable' ?>"><?= $bike['availability'] ? 'Disponible' : 'Indisponible' ?></span></td>
                    <td><?php if ($_SESSION['user']['role'] === 'admin'): ?><button class="button-small danger" data-delete-bike="<?= $bike['bike_id'] ?>">Supprimer</button><?php endif; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody></table>
        </div>
    </div>
</section>
