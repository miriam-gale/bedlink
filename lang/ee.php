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
  'pub.search.nhis_only'  => 'Dziɖuɖu & NHIS ɖeɖeko',   // EN: Government & NHIS only
  'pub.search.none'       => 'Womekpɔ aba femaxee aɖeke le teƒe si te ɖe afima ŋu o. Te kɔdzi bubu kpɔ alo nàƒo ka na 112',   // EN: No free beds found nearby. Try another ward or call 112.
  'pub.card.free.one'     => '{n} aba femaxee',   // EN: {n} free bed
  'pub.card.free.other'   => '{n} abadzivɔwo femaxee',   // EN: {n} free beds
  'pub.card.updated'      => 'Wowɔ asitɔtrɔ le eŋu {time} si va yi',   // EN: Updated {time} ago

  // --- Get help ---
  'help.button'           => 'Di kpekpeɖeŋu fifia',   // EN: Get help now
  'help.hold'             => 'Lée ɖe asi sɛkɛnd 3',   // EN: Hold for 3 seconds

  // --- Wards ---
  'ward.emergency'        => 'Kpomenya',   // EN: Emergency
  'ward.maternity'        => 'Vidzidzi',   // EN: Maternity
  'ward.children'         => 'Ðeviwo tɔ',   // EN: Children's
  'ward.general'          => 'Gbadza',   // EN: General

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
];
return array_filter($words, fn($w) => $w !== '');   // skip the ones not translated yet