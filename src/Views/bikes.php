<?php /** @var array $bikes */ ?>
<section class="dashboard">
    <aside class="sidebar">
        <h2>Tableau de bord</h2>
        <a href="<?= BASE_URL ?>/dashboard">Demandes en attente</a>
        <a href="<?= BASE_URL ?>/dashboard/reservations">Toutes les réservations</a>
        <a class="active" href="<?= BASE_URL ?>/dashboard/bikes">Vélos</a>
        <a href="<?= BASE_URL ?>/dashboard/users">Utilisateurs</a>
    </aside>
    <div class="dashboard-content">
        <div class="section-heading"><div><p class="eyebrow">Flotte</p><h1>Gestion des vélos</h1></div></div>
        <form class="inline-form" method="post" enctype="multipart/form-data" action="<?= BASE_URL ?>/dashboard/bikes">
            <?= csrfField() ?>
            <input name="registration_number" aria-label="Numéro d’immatriculation" placeholder="Numéro d’immatriculation" maxlength="50" required>
            <label class="check"><input type="checkbox" name="availability" checked> Disponible</label>
            <input type="file" name="photo" aria-label="Photo du vélo" accept="image/jpeg,image/png,image/webp">
            <button class="button" type="submit">Ajouter</button>
        </form>
        <div class="table-wrap">
            <table><thead><tr><th>Photo</th><th>Immatriculation</th><th>Disponibilité</th><th>Action</th></tr></thead><tbody>
            <?php foreach ($bikes as $bike): ?>
                <tr>
                    <td><?php if (!empty($bike['photo'])): ?><img class="bike-thumb" src="<?= PUBLIC_URL ?>/<?= htmlspecialchars($bike['photo']) ?>" alt=""><?php else: ?>🚲<?php endif; ?></td>
                    <td><strong><?= htmlspecialchars($bike['registration_number']) ?></strong></td>
                    <td><span class="status <?= $bike['availability'] ? 'available' : 'unavailable' ?>"><?= $bike['availability'] ? 'Disponible' : 'Indisponible' ?></span></td>
                    <td>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <form method="post" action="<?= BASE_URL ?>/dashboard/bike/delete" data-confirm="Supprimer ce vélo ?">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= $bike['bike_id'] ?>">
                                <button class="button-small danger" type="submit">Supprimer</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody></table>
        </div>
    </div>
</section>
