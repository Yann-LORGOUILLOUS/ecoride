<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-5" style="max-width: 900px;">

    <?php if (!empty($flash)): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> text-center">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="text-center mb-4">
        <span class="px-4 py-2 rounded-pill fw-bold"
              style="background-color: var(--ecoride-primary-soft); color: var(--ecoride-primary);">
            PROPOSER UN TRAJET
        </span>
    </div>

    <form method="post" class="bg-white rounded-4 p-4 shadow-sm">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <input type="text" name="city_from" class="form-control rounded-pill"
                       placeholder="Ville de départ"
                       value="<?= htmlspecialchars($old['city_from'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="postal_from" class="form-control rounded-pill"
                       placeholder="Code postal">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <input type="text" name="city_to" class="form-control rounded-pill"
                       placeholder="Ville d’arrivée"
                       value="<?= htmlspecialchars($old['city_to'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="postal_to" class="form-control rounded-pill"
                       placeholder="Code postal">
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="datetime-local" name="departure_datetime"
                       class="form-control rounded-pill"
                       value="<?= htmlspecialchars($old['departure_datetime'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <input type="datetime-local" name="arrival_datetime"
                       class="form-control rounded-pill"
                       value="<?= htmlspecialchars($old['arrival_datetime'] ?? '') ?>" required>
            </div>
        </div>

        <hr class="my-4">

        <div class="row g-3 align-items-center mb-3">
            <div class="col-md-4 fw-semibold text-secondary">
                Véhicule / Énergie :
            </div>
            <div class="col-md-8">
                <select name="vehicule_id" class="form-select rounded-pill" required>
                    <option value="">Veuillez sélectionner un véhicule</option>
                    <?php foreach ($vehicules as $vehicule): ?>
                        <option value="<?= $vehicule['id'] ?>"
                            data-seats="<?= (int)$vehicule['seats_total'] ?>"
                            <?= (($old['vehicule_id'] ?? '') == $vehicule['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($vehicule['brand'] . ' ' . $vehicule['model']) ?>
                            – <?= htmlspecialchars($vehicule['energy_type']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row g-3 align-items-center mb-4">
            <div class="col-md-4 fw-semibold text-secondary">
                Places disponibles :
            </div>
            <div class="col-md-8">
                <input type="number" min="1" id="seats_available" name="seats_available"
                    class="form-control rounded-pill"
                    value="<?= htmlspecialchars($old['seats_available'] ?? '') ?>" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="fw-semibold text-secondary mb-2">Description du trajet :</label>
            <textarea name="driver_notes" rows="4"
                      class="form-control rounded-4"
                      placeholder="Informations complémentaires, contraintes, ambiance…"><?= htmlspecialchars($old['driver_notes'] ?? '') ?></textarea>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <span class="fw-semibold text-secondary">Animaux acceptés ?</span>
                <div class="mt-2">
                    <label class="me-3">
                        <input type="checkbox" name="pets_allowed" value="1"
                            <?= !empty($old['pets_allowed']) ? 'checked' : '' ?>>
                        OUI
                    </label>
                </div>
            </div>

            <div class="col-md-6">
                <span class="fw-semibold text-secondary">Véhicule fumeur ?</span>
                <div class="mt-2">
                    <label class="me-3">
                        <input type="checkbox" name="smoking_allowed" value="1"
                            <?= !empty($old['smoking_allowed']) ? 'checked' : '' ?>>
                        OUI
                    </label>
                </div>
            </div>

        <hr class="my-4">

        <div class="row g-3 align-items-center mb-4">
            <div class="col-md-4 fw-semibold text-secondary">
                Prix en crédits :
            </div>
            <div class="col-md-8">
                <input
                type="number"
                class="form-control rounded-pill"
                id="price_credits"
                name="price_credits"
                min="3"
                step="1"
                required
                value="<?= htmlspecialchars((string)($old['price_credits'] ?? '')) ?>"
            >
            </div>
            <p class="text-muted small mt-2">La plateforme prélève 2 crédits par réservation (inclus dans ce prix).</p>
        </div>

        <hr class="my-4">

        <div class="text-center">
            <button type="submit"
                    class="btn btn-ecoride-primary px-5 py-2 rounded-pill fw-bold">
                PROPOSER CE TRAJET
            </button>

            <p class="text-muted small mt-2">
                Le trajet sera visible après validation
            </p>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const vehiculeSelect = document.querySelector('select[name="vehicule_id"]');
    const seatsInput = document.getElementById('seats_available');

    if (!vehiculeSelect || !seatsInput) return;

    const applyLimit = () => {
        const selected = vehiculeSelect.options[vehiculeSelect.selectedIndex];
        const totalSeats = parseInt(selected.dataset.seats, 10);

        if (!Number.isNaN(totalSeats) && totalSeats > 1) {
            const maxPassengers = totalSeats - 1;
            seatsInput.max = String(maxPassengers);

            const current = parseInt(seatsInput.value, 10);
            if (!Number.isNaN(current) && current > maxPassengers) {
                seatsInput.value = String(maxPassengers);
            }
        } else {
            seatsInput.removeAttribute('max');
        }
    };

    vehiculeSelect.addEventListener('change', applyLimit);
    applyLimit();
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
