// assets/js/ambulance.js

const TOWNS = {
    'Circle': [5.5700, -0.2215],
    'Kaneshie': [5.5660, -0.2360],
    'Achimota': [5.6224, -0.2269],
    'Madina': [5.6712, -0.1662],
    'Legon': [5.6508, -0.1870],
    'Tema': [5.6698, -0.0166],
};

const latEl = document.getElementById('lat');
const lngEl = document.getElementById('lng');
const gpsLine = document.getElementById('gps-line');
const townBox = document.getElementById('town-box');
const townIn = document.getElementById('town');
const form = document.getElementById('amb-form');

let gotFix = false;

// Fill the town list from TOWNS.
const townList = document.getElementById('towns');

Object.keys(TOWNS).forEach((name) => {
    const option = document.createElement('option');
    option.value = name;
    townList.append(option);
});

// Keep the ambulance's location updated while it moves.
if ('geolocation' in navigator) {
    navigator.geolocation.watchPosition(onFix, onGpsError, {
        enableHighAccuracy: true,
        maximumAge: 10000,
        timeout: 15000,
    });
} else {
    onGpsError();
}

function onFix(position) {
    gotFix = true;

    latEl.value = position.coords.latitude.toFixed(6);
    lngEl.value = position.coords.longitude.toFixed(6);

    gpsLine.textContent = t('amb.gps_ok', {
        m: Math.round(position.coords.accuracy),
    });

    townBox.hidden = true;
}

function onGpsError() {
    if (gotFix) return;

    gpsLine.textContent = t('amb.gps_fail');
    townBox.hidden = false;
}

townIn.addEventListener('change', () => {
    const position = TOWNS[townIn.value];

    if (!position) return;

    latEl.value = position[0];
    lngEl.value = position[1];

    gpsLine.textContent = t('amb.gps_town', {
        town: townIn.value,
    });
});

form.addEventListener('submit', (event) => {
    event.preventDefault();

    if (!latEl.value || !lngEl.value) {
        toast(t('amb.need_location'), 'error');
        return;
    }

    const formData = new FormData(form);
    const blood = formData.get('blood') || '';

    const query = new URLSearchParams({
        lat: latEl.value,
        lng: lngEl.value,
        ward: formData.get('ward'),
        services: formData.getAll('svc').join(','),
        blood,
        units: blood ? formData.get('units') : 0,
    });

    window.location.href = 'results.php?' + query;
});


const units = document.getElementById('units-input');
const minus = document.getElementById('units-minus');
const plus = document.getElementById('units-plus');

if (units && minus && plus) {

    function updateUnits(value) {
        const amount = Number.parseInt(value, 10);
        const safeAmount = Number.isFinite(amount) ? amount : 0;

        units.value = Math.max(0, Math.min(10, safeAmount));

        minus.disabled = Number(units.value) <= 0;
        plus.disabled = Number(units.value) >= 10;
    }

    minus.addEventListener('click', function() {
        updateUnits(Number(units.value) - 1);
    });

    plus.addEventListener('click', function() {
        updateUnits(Number(units.value) + 1);
    });

    units.addEventListener('change', function() {
        updateUnits(units.value);
    });

    updateUnits(units.value);
}