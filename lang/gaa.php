<?php
// lang/gaa.php — Ga. Public words only (Mimi's section + wards).
// TEMPLATE for a native speaker to fill in: replace each '' with the translation.
// Empty lines are skipped, so the English word shows until it is filled in (nothing breaks).
// Keep {n}, {time} etc. exactly as they are. Staff/ambulance words stay in English on purpose.
// Drafted with Google Translate. Needs review by a native Ga speaker.
$words = [

  // --- Shared buttons and messages ---
  'ui.yes'                => 'Hɛɛ',   // EN: Yes
  'ui.cancel'             => 'Foo mli',   // EN: Cancel
  'ui.back'               => 'Sɛɛ',   // EN: Back
  'ui.close'              => 'Kpa',   // EN: Close
  'ui.call'               => 'Tswaa',   // EN: Call
  'ui.directions'         => 'Gbɛtsɔɔmɔi',   // EN: Directions
  'ui.offline'            => 'Obɛ intanɛt lɛ nɔ. Naagbee saji ni akɛto lɛ tsɔɔmɔ.',   // EN: You are offline. Showing the last saved information.
  'err.server'            => 'Nɔ ko eyaaa nɔ jogbaŋŋ. Ofainɛ kaa ekoŋŋ.',   // EN: Something went wrong. Please try again.
  'err.offline'           => 'Intanɛt bɛ.',   // EN: No internet connection.

  // --- Menu ---
  'nav.language'          => 'Wiemɔ',   // EN: Language
  'nav.login'             => 'Botemɔ mli',   // EN: Log in
  'nav.logout'            => 'Je kpo',   // EN: Log out
  'nav.menu'              => 'Menu titri',   // EN: Main menu
  'nav.search'            => 'Taomɔ saatso',   // EN: Find a bed
  'nav.ambulance'         => 'Helatsamɔ tsɔne',   // EN: Ambulance
  'nav.bedboard'          => 'Saatso nɔ tsɔne',   // EN: Bed board
  'nav.incoming'          => 'Nɔ ni baa',   // EN: Incoming
  'nav.theme'             => 'Tsakemɔ kane loo duŋ gbɛ',   // EN: Switch light or dark mode

  // --- How fresh the information is ---
  'conf.live'             => 'Wala',   // EN: Live
  'conf.recent'           => 'Etsɛko tsɔ',   // EN: Recent
  'conf.uncertain'        => 'Nɔ ni bɛ faŋŋ · tswaa hiɛ',   // EN: Uncertain · call ahead

  // --- Public search page ---
  'pub.search.title'      => 'Taomɔ saatso ni aheee nɔ ko yɛ he ko ni bɛŋkɛ bo',   // EN: Find a free bed near you

  'pub.search.gps_wait'   => 'He ni oyɔɔ lɛ taomɔ...',   // EN: Finding where you are...
  'pub.search.gps_ok'     => 'Helatsamɔhei ni bɛŋkɛ bo lɛ miitsɔɔ',   // EN: Showing hospitals near you
  'pub.search.gps_fail'   => 'Wɔ nyɛɛɛ wɔ na he ni oyɔɔ lɛ. ŋmaa omaŋ lɛ:',   // EN: We could not find your location. Type your town:
  'pub.search.gps_town'   => 'Atsɔɔ helatsamɔhei ni bɛŋkɛ {town}',   // EN: Showing hospitals near {town}
  'pub.search.town'       => 'Omaŋ lɛ',   // EN: Your town
  'pub.search.ward'       => 'Mɛɛ kwɛmɔ nɛkɛ?',   // EN: What kind of care?
  'pub.search.service'    => 'Esa akɛ ená',   // EN: Must have
  'pub.search.any'        => ' Fɛɛ eko',   // EN: Anything

  'pub.search.nhis'  => 'Nɔyeli & NHIS pɛ',   // EN: Government & NHIS only

  'pub.search.none'       => 'Anaaa saatsoi ni aheee nɔ ko yɛ he ko ni bɛŋkɛ. Kaa ward kroko okwɛ loo tswaa 112.',   // EN: No free beds found nearby. Try another ward or call 112.

  'pub.card.free.one'     => '{n} saatso ni aheee nɔ ko',   // EN: {n} free bed
  'pub.card.free.other'   => '{n} saatsoi ni aheee nɔ ko',   // EN: {n} free beds
    'pub.card.dist'         => ' {km} kilomitai . ehe {min} miniti',   // EN: {km} km · about {min} min
  'pub.card.soon'         => ' {n} baanyɛ afee yaka yɛ ŋmɛlɛtswai 2 mli',   // EN: {n} may be free within 2 hours
  'pub.card.updated'      => 'Atsake {time} etsɛɛ',   // EN: Updated {time} ago

  // --- Get help ---
  'help.button'           => 'Na yelikɛbuamɔ bianɛ',   // EN: Get help now
  'help.hold'             => 'Hiɛ sɛkɛndii 3 mli',   // EN: Hold for 3 seconds

  // --- Wards ---
  'ward.emergency'        => 'Oshra',   // EN: Emergency
  'ward.maternity'        => 'Fɔmɔ',   // EN: Maternity
  'ward.children'         => 'Gbekɛbii anɔ̃',   // EN: Children's
  'ward.general'          => 'Hefee he',   // EN: General
    'ward.icu'              => 'ICU',   // EN: ICU

  // --- Services ---
  'svc.ct_scan'           => 'CT scan',   // EN: CT scan
  'svc.icu'               => 'ICU',       // EN: ICU
  'svc.trauma_surgery'    => 'Trauma opireshɛŋ',   // EN: Trauma surgery
  'svc.theatre'           => 'Sini jiemɔhe (opireshɛn tsu)',   // EN: Theatre (operating room)
  'svc.blood_bank'        => 'La shikatoohe',   // EN: Blood bank
  'svc.obstetrics'        => 'fɔmɔ he helatsamɔ (hɔ kɛ fɔmɔ hekwɛmɔ)',   // EN: Obstetrics (pregnancy and birth care)

  // --- Footer ---
  'footer.tagline'        => 'Ni akɛ gbɔmɛi atsa helatsamɔ ni yɔɔ lɛ nɔ, oya.',   // EN: Connecting people to available care, faster.

  'footer.sub'            => 'Fiɔ helatsamɔ he gbɛjianɔtoo sɛɛ yɛ Ghana fɛɛ.',   // EN: Supporting care coordination across Ghana.

  'footer.call'           => 'Tswaa',   // EN: Call
  'footer.emergency'      => 'Oyaiyeli yelikɛbuamɔ',   // EN: Emergency assistance

  'footer.explore'        => 'Kwɛmɔ BedLink lɛ',   // EN: Explore BedLink
  'footer.find_desc'      => 'Taomɔ helatsamɔ ni yɔɔ',   // EN: Search for available care

  // --- Not allowed page ---
  'err.403.title'         => 'Onyɛŋ ogbele baafa nɛɛ',   // EN: You can't open this page

  'err.403.text'          => 'Baafa nɛɛ ji kɛha mɔ kroko ni kɛtsuɔ nii. Kɛji osusuɔ akɛ enɛ ji tɔmɔ lɛ, na ohelatsamɔhe nɔkwɛlɔ lɛ.',   // EN: This page is for a different kind of user. If you think this is a mistake, contact your hospital admin.

  'err.403.home'          => 'Yaa Taomɔ saatso',   // EN: Go to Find a bed
];

return array_filter($words, fn($w) => $w !== '');   // skip the ones not translated yet