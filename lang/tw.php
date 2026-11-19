<?php
// lang/tw.php — Twi (Akan). Public words only (Mimi's section + wards/services).
// Any key missing here falls back to English automatically (see lang/js.php and i18n.php).
// Staff/ambulance words (triage, vitals, treatments...) stay in English on purpose: clinical safety.
// DRAFT: every line must be checked by a native Twi speaker before release. Remove "// CHECK" once confirmed.
return [

  // --- Shared buttons and messages ---
  'ui.yes'              => 'Aane',                                                   // CHECK
  'ui.cancel'           => 'Gyae',                                                   // CHECK
  'ui.back'             => 'San kɔ akyi',                                            // CHECK
  'ui.close'            => 'To mu',                                                  // CHECK
  'ui.call'             => 'Frɛ',                                                    // CHECK
  'ui.directions'       => 'Kwan a wobɛfa',                                          // CHECK
  'ui.offline'          => 'Intanɛt nni hɔ. Yɛrekyerɛ nsɛm a yɛkoraa no akyire yi.', // CHECK
  'err.server'          => 'Biribi ankɔ yie. Yɛsrɛ wo, san bɔ mmɔden bio.',          // CHECK
  'err.offline'         => 'Intanɛt nni hɔ.',                                        // CHECK

  // --- Menu ---
  'nav.language'        => 'Kasa',                                                   // CHECK
  'nav.login'           => 'Wura mu',                                                // CHECK
  'nav.logout'          => 'Fi mu',                                                  // CHECK
  'nav.menu'            => 'Menu',                                                   // CHECK
  'nav.search'          => 'Hwehwɛ mpa',                                             // CHECK
  'nav.ambulance'       => 'Ambulans',                                               // CHECK
  'nav.bedboard'        => 'Mpa ho nsɛm',                                            // CHECK
  'nav.incoming'        => 'Ayarefoɔ a wɔreba',                                      // CHECK
  'nav.theme'           => 'Sesa hann ne sum',                                       // CHECK

  // --- How fresh the information is ---
  'conf.live'           => 'Seesei ara',                                             // CHECK
  'conf.recent'         => 'Nnansa yi',                                              // CHECK
  'conf.uncertain'      => 'Yɛnnim pɛpɛɛpɛ · frɛ kan',                               // CHECK

  // --- Public search page ---
  'pub.search.title'    => 'Hwehwɛ mpa a ɛda hɔ wɔ wo nkyɛn',                        // CHECK
  'pub.search.nhis_only'=> 'Aban ne NHIS nko ara',                                   // CHECK
  'pub.search.none'     => 'Yɛanhu mpa biara a ɛda hɔ wɔ wo nkyɛn. Hwɛ dan foforɔ anaa frɛ 112.', // CHECK
  'pub.card.free.one'   => 'Mpa {n} da hɔ',                                          // CHECK
  'pub.card.free.other' => 'Mpa {n} da hɔ',                                          // CHECK
  'pub.card.updated'    => 'Yɛsesaa no {time} a atwam',                              // CHECK

  // --- Get help ---
  'help.button'         => 'Nya mmoa seesei',                                        // CHECK
  'help.hold'           => 'Mia so kɔsi sɛkɛn 3',                                    // CHECK

  // --- Wards and services ---
  'ward.emergency'      => 'Ntɛmntɛm ayaresa',                                       // CHECK
  'ward.maternity'      => 'Awodan',                                                 // CHECK
  'ward.children'       => 'Mmofra dan',                                             // CHECK
  'ward.general'        => 'Ayarefoɔ dan',                                           // CHECK
  'ward.icu'            => 'ICU',
  'svc.ct_scan'         => 'CT scan',
  'svc.trauma_surgery'  => 'Trauma surgery',
  'svc.theatre'         => 'Theatre',
  'svc.icu'             => 'ICU',

  // --- Footer ---
  'footer.tagline'      => 'Yɛde nkurɔfoɔ ka ayaresa a ɛwɔ hɔ ho, ntɛm.',            // CHECK
  'footer.sub'          => 'Yɛboa ayaresa nhyehyɛeɛ wɔ Ghana nyinaa.',               // CHECK
  'footer.call'         => 'Frɛ',                                                    // CHECK
  'footer.emergency'    => 'Mmoa ntɛmntɛm',                                          // CHECK
  'footer.explore'      => 'Hwɛ BedLink mu',                                         // CHECK
  'footer.find_desc'    => 'Hwehwɛ ayaresa a ɛwɔ hɔ',                                // CHECK

  // --- Not allowed page ---
  'err.403.title'       => 'Wontumi mmue krataafa yi',                               // CHECK
  'err.403.text'        => 'Krataafa yi yɛ ma obi foforɔ. Sɛ wosusu sɛ mfomsoɔ wɔ mu a, ka kyerɛ wo ayaresabea panin.', // CHECK
  'err.403.home'        => 'Kɔ "Hwehwɛ mpa"',                                        // CHECK
];