// Mock data for now. Later, swap this ONE line for the real API URL
const SEARCH_URL = '/bedlink/assets/mock/search.json';

// Wait until the page has fully loaded, then run
document.addEventListener('DOMContentLoaded', async () => {

    // api() is our helper from app.js: it fetches the URL and returns the JSON
    const result = await api(SEARCH_URL);

    
        // The list of hospitals is inside data.results
    const hospitals = result.data.results;

    const list = document.getElementById('hospital-list');
    const tpl  = document.getElementById('hospital-card');   // the template's id

    // Make one card for each hospital

     
    // Which ward the user picked (Emergency is selected by default)
    let ward = 'emergency';

    // Make one card for each hospital
    
    hospitals.forEach(h => {
        const card = tpl.content.cloneNode(true);       // copy the blueprint

        card.querySelector('.hc-name').textContent = h.name;
        card.querySelector('.hc-town').textContent = h.town;

                // Free beds for the chosen ward (Emergency for now)
        const free = h.beds[ward];
        card.querySelector('.hc-beds').textContent =
            tn('pub.card.free', free, { n: free });

        // Distance + travel time
        card.querySelector('.hc-dist').textContent =
            t('pub.card.dist', { km: h.distance_km, min: h.eta_min });

        // How fresh the info is
        card.querySelector('.hc-updated').textContent =
            t('pub.card.updated', { time: h.updated_min_ago + ' min' });

        // NHIS badge: only show it if the hospital accepts NHIS
        card.querySelector('.hc-nhis').hidden = !h.accepts_nhis;

        // Call link: tapping it dials the hospital
        card.querySelector('.hc-call').href = 'tel:' + h.phone;

        list.appendChild(card);                         // put it on the page
    });

     document.getElementById('empty-results').hidden = true;
});

   