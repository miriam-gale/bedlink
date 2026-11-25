<?php
// lang/ee.php — Ewe. Public words only (Mimi's section + wards).
// TEMPLATE for a native speaker to fill in: replace each '' with the translation.
// Empty lines are skipped, so the English word shows until it is filled in (nothing breaks).
// Keep {n}, {time} etc. exactly as they are. Staff/ambulance words stay in English on purpose.
$words = [

  // --- Shared buttons and messages ---
  'ui.yes'                => 'Ɛ̃',   // EN: Yes
  'ui.cancel'             => 'Te fli ɖe eme',   // EN: Cancel
  'ui.back'               => 'Megbe',   // EN: Back
  'ui.close'              => 'Tu',   // EN: Close
  'ui.call'               => 'Yɔ',   // EN: Call
  'ui.directions'         => 'Mɔfiamewo',   // EN: Directions
  'ui.offline'            => 'Èle Internet dzi o. Nyatakaka mamlɛtɔ siwo wodzra ɖo la ɖeɖefia.',   // EN: You are offline. Showing the last saved information.

  'err.server'            => 'Nane gblẽ. Taflatse gadze agbagba ake.',   // EN: Something went wrong. Please try again.
  'err.offline'           => 'Internet ƒe kadodo aɖeke meli o.',   // EN: No internet connection.

  // --- Menu ---
  'nav.language'          => 'Gbegbɔgblɔ',   // EN: Language
  'nav.login'             => 'Ge ɖe eme',   // EN: Log in
  'nav.logout'            => 'Do go le eme',   // EN: Log out
  'nav.menu'              => 'Menu vevitɔ',   // EN: Main menu
  'nav.search'            => 'Di aba aɖe',   // EN: Find a bed
  'nav.ambulance'         => 'Dɔnɔdzikpɔʋu',   // EN: Ambulance
  'nav.bedboard'          => 'Abatidzivɔ',   // EN: Bed board
  'nav.incoming'          => 'Nusiwo gbɔna',   // EN: Incoming
  'nav.theme'             => 'Trɔ kekeli alo viviti ƒe nɔnɔme',   // EN: Switch light or dark mode

  // --- How fresh the information is ---
  'conf.live'             => 'Nɔ agbe',   // EN: Live
  'conf.recent'           => 'Si gbɔ medidi o',   // EN: Recent
  'conf.uncertain'        => 'Kakaɖedzimanɔamesi · yɔ do ŋgɔ',   // EN: Uncertain · call ahead

  // --- Public search page ---
  'pub.search.title'      => 'Di aba si te ɖe ŋuwò femaxee',   // EN: Find a free bed near you
    'pub.search.gps_wait'   => 'Afisi nèle didi...',   // EN: Finding where you are...
  'pub.search.gps_ok'     => 'Kɔdzi siwo te ɖe ŋuwò ɖeɖefia',   // EN: Showing hospitals near you
  'pub.search.gps_fail'   => 'Míete ŋu ke ɖe afi si nèle ŋu o. Ŋlɔ wò du la ɖe agbalẽ me:',   // EN: We could not find your location. Type your town:
  'pub.search.gps_town'   => 'Wole kɔdzi siwo te ɖe {town} ŋu fia.',   // EN: Showing hospitals near {town}
    'pub.search.town'       => 'Wò du',   // EN: Your town
  'pub.search.ward'       => 'Beléle ka ƒomevie?',   // EN: What kind of care?
  'pub.search.service'    => 'Ele be wòanɔ esi',   // EN: Must have
  'pub.search.any'        => 'Nu sia nu',   // EN: Anything
  'pub.search.nhis'  => 'Dziɖuɖu & NHIS ɖeɖeko',   // EN: Government & NHIS only
  'pub.search.none'       => 'Womekpɔ aba femaxee aɖeke le teƒe si te ɖe afima ŋu o. Te kɔdzi bubu kpɔ alo nàƒo ka na 112',   // EN: No free beds found nearby. Try another ward or call 112.
  'pub.card.free.one'     => '{n} aba femaxee',   // EN: {n} free bed
  'pub.card.free.other'   => '{n} abadzivɔwo femaxee',   // EN: {n} free beds
    'pub.card.dist'         => '{km} km · abe {min} min',   // EN: {km} km · about {min} min
  'pub.card.soon'         => '{n} ateŋu anye ablɔɖe le gaƒoƒo 2 me',   // EN: {n} may be free within 2 hours
  'pub.card.updated'      => 'Wowɔ asitɔtrɔ le eŋu {time} si va yi',   // EN: Updated {time} ago

  // --- Get help ---
  'help.button'           => 'Di kpekpeɖeŋu fifia',   // EN: Get help now
  'help.hold'             => 'Lée ɖe asi sɛkɛnd 3',   // EN: Hold for 3 seconds

  // --- Wards ---
  'ward.emergency'        => 'Kpomenya',   // EN: Emergency
  'ward.maternity'        => 'Vidzidzi',   // EN: Maternity
  'ward.children'         => 'Ðeviwo tɔ',   // EN: Children's
  'ward.general'          => 'Gbadza',   // EN: General
    'ward.icu'              => 'ICU',   // EN: ICU

  // --- Services ---
  'svc.ct_scan'           => 'CT scan',   // EN: CT scan
  'svc.icu'               => 'ICU',       // EN: ICU
  'svc.trauma_surgery'    => 'Amekoko si wowɔna na nuveviwɔame',   // EN: Trauma surgery
  'svc.theatre'           => 'Amekokoxɔ',   // EN: Theatre (operating room)
  'svc.blood_bank'        => 'Ʋudzraɖoƒe',   // EN: Blood bank
  'svc.obstetrics'        => 'Vidzidziŋutinunya',   // EN: Obstetrics (pregnancy and birth care)

  // --- Footer ---
  'footer.tagline'        => 'Amewo ƒe kadodo kple beléle si li, kabakaba wu.',   // EN: Connecting people to available care, faster.
  'footer.sub'            => 'Kpekpeɖeŋu nana belélename ƒe ɖoɖowɔwɔ le Ghana katã.',   // EN: Supporting care coordination across Ghana.
  'footer.call'           => 'Yɔ',   // EN: Call
  'footer.emergency'      => 'Kpekpeɖeŋunana kpata',   // EN: Emergency assistance
  'footer.explore'        => 'Dzro BedLink me',   // EN: Explore BedLink
  'footer.find_desc'      => 'Di beléle si li',   // EN: Search for available care

  // --- Not allowed page ---
  'err.403.title'         => 'Màte ŋu aʋu axa sia o',   // EN: You can't open this page
  'err.403.text'          => 'Axa sia nye zãla ƒomevi bubu tɔ. Ne èsusu be vodadae nye esia la, te ɖe wò kɔdzidzikpɔla ŋu.',   // EN: This page is for a different kind of user. If you think this is a mistake, contact your hospital admin.
  'err.403.home'          => 'Yi ɖadi aba',   // EN: Go to Find a bed

    // --- Homepage redesign ---
  'pub.hero.eyebrow'      => 'Beléle si le bɔbɔe',   // EN: Healthcare, made easier
  'pub.hero.title'        => 'Di kɔdzi aba femaxee',   // EN: Find a free hospital bed
  'pub.hero.near'         => 'si te ɖe ŋuwò',   // EN: near you
  'pub.hero.sub'          => 'Kpɔ kɔdzi aba siwo li le teƒe si te ɖe ŋuwò, eye nàkpɔ beléle si hiã wò kabakaba.',   // EN: Check hospital bed availability near you and find the care you need, faster.
  'pub.hero.img_alt'      => 'Ghana dɔyɔdɔwɔla si tsi tsitre ɖe kɔdzi aɖe xa',   // EN: Ghanaian health worker standing beside a hospital
  'pub.search.town_ph'    => 'Ŋlɔ wò du',   // EN: Enter your town
  'pub.results.title'     => 'Kɔdzi siwo me aba le',   // EN: Available hospitals
  'pub.results.sub'       => 'Kɔdzi siwo sɔ kple wò didi',   // EN: Hospitals matching your search
  'pub.results.start'     => 'Di kɔdziwo be nàkpɔ aba femaxee siwo li.',   // EN: Search for hospitals to see available beds.
  'pub.card.nhis'         => 'NHIS',   // EN: NHIS
  'pub.card.call'         => 'Yɔ kɔdzi la',   // EN: Call hospital
  'pub.feat.live.title'   => 'Aba siwo li fifia',   // EN: Real-time availability
  'pub.feat.live.desc'    => 'Kpɔ aba femaxee le kɔdzi vovovowo me.',   // EN: Find available beds across hospitals.
  'pub.feat.trust.title'  => 'Kɔdzi siwo dzi woate ŋu aɖo ŋu ɖo',   // EN: Trusted hospitals
  'pub.feat.trust.desc'   => 'Dziɖuɖu kple NHIS kɔdziwo hã le eme.',   // EN: Government and NHIS facilities included.
  'pub.feat.fast.title'   => 'Beléle kabakaba',   // EN: Faster care
  'pub.feat.fast.desc'    => 'Kpɔ aba si sɔ eye woada gbe le ŋuwò kabakaba.',   // EN: Find the right bed and get treated sooner.
];
return array_filter($words, fn($w) => $w !== '');   // skip the ones not translated yet