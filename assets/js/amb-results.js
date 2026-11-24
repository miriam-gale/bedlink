// Step 2: Mock data source
// Later, replace this with '/bedlink/api/match.php'
const MATCH_URL = '/bedlink/assets/mock/matches.json';

document.addEventListener('DOMContentLoaded', async() => {

    // Step 3: Load hospitals
    const result = await api(MATCH_URL);

    if (!result.ok) {
        toast(t('err.server'));
        return;
    }

    const hospitals = result.data.results;


    // Step 4: Handle no hospital matches
    if (hospitals.length === 0) {
        document.getElementById('amb-empty-text').textContent =
            t('amb.no_match', { km: result.data.radius_km });

        if (result.data.coordinator_told) {
            document.getElementById('amb-coordinator').hidden = false;
        }

        document.getElementById('amb-empty').hidden = false;
        return;
    }


    // Step 5: Create a card for each hospital
    const list = document.getElementById('amb-list');
    const tpl = document.getElementById('amb-card');

    hospitals.forEach(h => {
        const card = tpl.content.cloneNode(true);

        // Hospital image
        const image = card.querySelector('.amb-card__image');
        image.src = h.image;
        image.alt = h.name;

        // Hospital information
        card.querySelector('.amb-card__name').textContent = h.name;
        card.querySelector('.amb-card__town').textContent = h.town;

        card.querySelector('.amb-card__eta').textContent =
            t('amb.eta', { min: h.eta_min });

        card.querySelector('.amb-card__beds').textContent =
            tn('amb.beds', h.free_beds, { n: h.free_beds });

        card.querySelector('.amb-card__updated').textContent =
            t('amb.updated', { min: h.updated_min_ago });

        // Call link
        card.querySelector('.amb-card__call').href =
            'tel:' + h.phone;

        // Google Maps directions
        card.querySelector('.amb-card__dir').href =
            'https://www.google.com/maps/dir/?api=1&destination=' +
            encodeURIComponent(h.latitude + ',' + h.longitude);

        // Remember hospital ID on the hold button
        card.querySelector('.amb-card__hold').dataset.id =
            h.hospital_id;

        list.appendChild(card);
    });


    // Step 6: Hold bed (DEMO ONLY)
    list.addEventListener('click', async(e) => {
        const btn = e.target.closest('.amb-card__hold');

        if (!btn) return;

        const ok = await confirmDialog(t('amb.confirm_hold'));

        if (!ok) return;

        // Demo only — does not reserve a real bed
        toast(t('amb.hold') + ' ✓');

        btn.disabled = true;
    });

});