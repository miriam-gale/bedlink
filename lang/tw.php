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
  'pub.search.title'    => 'Hwehwɛ mpa a ɛda hɔ wɔ wo nkyɛn', 
    'pub.search.gps_wait' => 'Yɛrehwehwɛ baabi a wowɔ…',                               // CHECK
  'pub.search.gps_ok'   => 'Yɛrekyerɛ ayaresabea a ɛbɛn wo',                         // CHECK
  'pub.search.gps_fail' => 'Yɛanhu baabi a wowɔ. Kyerɛw wo kuro din:',               // CHECK
  'pub.search.gps_town' => 'Yɛrekyerɛ ayaresabea a ɛbɛn {town}',                     // CHECK
  'pub.search.town'     => 'Wo kuro',                                                // CHECK
  'pub.search.ward'     => 'Ayaresa bɛn na wohia?',                                  // CHECK
  'pub.search.service'  => 'Ɛsɛ sɛ ɛwɔ hɔ',                                          // CHECK
  'pub.search.any'      => 'Biara',                                                  // CHECK                   // CHECK
  'pub.search.nhis'=> 'Aban ne NHIS nko ara',                                   // CHECK
  'pub.search.none'     => 'Yɛanhu mpa biara a ɛda hɔ wɔ wo nkyɛn. Hwɛ dan foforɔ anaa frɛ 112.', // CHECK
  'pub.card.free.one'   => 'Mpa {n} da hɔ',                                          // CHECK
  'pub.card.free.other' => 'Mpa {n} da hɔ',
    'pub.card.dist'       => 'Kilomita {km} · bɛyɛ simma {min}',                       // CHECK
  'pub.card.soon'       => 'Mpa {n} betumi ada hɔ nnɔnhwerew 2 mu',                  // CHECK                                      // CHECK
  'pub.card.updated'    => 'Yɛsesaa no {time} a atwam',                              // CHECK

  // --- Get help ---
  'help.button'         => 'Nya mmoa seesei',                                        // CHECK
  'help.hold'           => 'Mia so kɔsi sɛkɛn 3',                                    // CHECK

  // --- Wards and services ---
  'ward.emergency'      => 'Ntɛmntɛm ayaresa',                                   // CHECK
  'ward.maternity'      => 'Awodan',                                                 // CHECK
  'ward.children'       => 'Mmofra dan',                                             // CHECK
  'ward.general'        => 'Ayarefoɔ dan',                                           // CHECK
  'ward.icu'            => 'ICU',
  'svc.ct_scan'         => 'CT scan',
    'svc.trauma_surgery'  => 'Apirakuru oprehyɛn',                                     // CHECK
  'svc.theatre'         => 'Oprehyɛn dan',                                           // CHECK
  'svc.icu'             => 'ICU',
    'svc.blood_bank'      => 'Mogya korabea',                                          // CHECK
  'svc.obstetrics'      => 'Awoɔ ho ayaresa',                                        // CHECK

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
  'err.403.home'        => 'Kɔ "Hwehwɛ mpa"',
                                          // CHECK
    'err.403.login'       => 'Fa akawnt foforɔ wura mu',
];