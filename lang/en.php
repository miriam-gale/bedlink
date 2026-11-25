<?php
// lang/en.php — the MASTER list of every word on the site (English).
// Keys look like area.screen.item. Each person adds keys ONLY in their own section,
// at the end of it, so Git never clashes. tw.php, gaa.php and ee.php use the same keys.
return [

  // === P1: Mimi — shared words, public pages, Get help ===
  'ui.yes'              => 'Yes',
  'ui.cancel'           => 'Cancel',
  'ui.back'             => 'Back',
  'ui.close'            => 'Close',
  'ui.call'             => 'Call',
  'ui.directions'       => 'Directions',
  'ui.offline'          => 'You are offline. Showing the last saved information.',
  'err.server'          => 'Something went wrong. Please try again.',
  'err.offline'         => 'No internet connection.',
  'nav.language'        => 'Language',
  'nav.login'           => 'Log in',
  'nav.logout'          => 'Log out',
  'nav.find_bed'        => 'Find a bed',
  'theme.toggle'        => 'Toggle dark mode',
  'nav.menu'            => 'Main menu',
  'nav.search'          => 'Find a bed',
  'nav.ambulance'       => 'Ambulance',
  'nav.bedboard'        => 'Bed board',
  'nav.incoming'        => 'Incoming',
  'nav.theme'           => 'Switch light or dark mode',

  'conf.live'           => 'Live',
  'conf.recent'         => 'Recent',
  'conf.uncertain'      => 'Uncertain · call ahead',

  'pub.search.title'    => 'Find a free bed near you',
  'pub.search.nhis_only'=> 'Government & NHIS only',
  'pub.search.none'     => 'No free beds found nearby. Try another ward or call 112.',
  'pub.card.free.one'   => '{n} free bed',
  'pub.card.free.other' => '{n} free beds',
  'pub.card.updated'    => 'Updated {time} ago',

  'help.button'         => 'Get help now',
  'help.hold'           => 'Hold for 3 seconds',

  'ward.general'        => 'General',

  'footer.tagline'      => 'Connecting people to available care, faster.',
  'footer.sub'          => 'Supporting care coordination across Ghana.',
  'footer.call'         => 'Call',
  'footer.emergency'    => 'Emergency assistance',
  'footer.explore'      => 'Explore BedLink',
  'footer.find_desc'    => 'Search for available care',

  'err.403.title'       => 'You can\'t open this page',
  'err.403.text'        => 'This page is for a different kind of user. If you think this is a mistake, contact your hospital admin.',
  'err.403.home'        => 'Go to Find a bed',


  // === P2: Jeff — ambulance and staff ===
  // --- Ambulance home (AMB-01) ---
  'amb.ward'            => 'Ward needed',
  'amb.needs'           => 'Needs',
  'amb.blood'           => 'Blood needed',
  'amb.blood_type'      => 'Blood type',
  'amb.units'           => 'Units of blood',
  'amb.town'            => 'Town or landmark',
  'amb.need_location'   => 'We need your location to find a hospital.',
  'amb.gps_wait'        => 'Finding your location…',
  'amb.gps_ok'          => 'GPS ± {m} m',
  'amb.gps_fail'        => 'No GPS. Type your town or a landmark:',
  'amb.gps_town'        => 'Using {town}',
  'amb.choose_town'     => 'Choose your town or landmark',
  'amb.title'           => 'Find the best hospital',
  'amb.subtitle'        => 'Choose what your patient needs',
  'amb.find'           => 'Find best hospital',
  'amb.hero_alt'        => 'Bedlink emergency ambulance',
  'amb.units_less'        => 'Decrease blood units',
  'amb.units_more'        => 'Increase blood units',

  'ward.emergency'      => 'Emergency',
  'ward.maternity'      => 'Maternity',
  'ward.icu'            => 'ICU',
  'ward.children'       => "Children's",

  'svc.ct_scan'         => 'CT scan',
  'svc.trauma_surgery'  => 'Trauma surgery',
  'svc.theatre'         => 'Theatre',
  'svc.icu'             => 'ICU',

  // --- Matches + Confirm hold (AMB-02) ---
  'amb.results_title'   => 'Best hospitals for you',
  'amb.eta'             => '{min} min away',
  'amb.beds.one'        => '1 free bed',
  'amb.beds.other'      => '{n} free beds',
  'amb.updated'         => 'updated {min} min ago',
  'amb.hold'            => 'Hold bed & alert hospital',
  'amb.confirm_hold'    => 'Confirm hold',
  'amb.blood_hold'      => 'Also holding {units} units of {type} blood',
  'amb.no_match'        => 'No match within {km} km.',
  'amb.no_blood'        => 'No blood needed',
  'amb.no_blood_nearby' => 'No blood of that type is available nearby.',
  'amb.widen'           => 'Widen search',
  'amb.coordinator_told'=> 'The regional coordinator has been told.',

  // --- Handover (AMB-03) ---
  'ho.triage'           => 'Triage',
  'ho.not_measured'     => 'Nothing measured yet',
  'ho.treatment'        => 'Treatment given',
  'ho.notes'            => 'Notes',
  'ho.no_names'         => 'Clinical notes only. Never write a name or ID number.',
  'ho.send'             => 'Send to hospital',
  'ho.skip'             => 'Skip handover',
  'ho.need_triage'      => 'Choose a triage colour first.',
  'ho.need_treatment'   => 'Choose a treatment, or "None".',

  'triage.red'          => 'Red',
  'triage.yellow'       => 'Yellow',
  'triage.green'        => 'Green',
  'triage.none'         => 'Not triaged',

  'vital.pulse'         => 'Pulse',
  'vital.bp_sys'        => 'BP top',
  'vital.bp_dia'        => 'BP bottom',
  'vital.spo2'          => 'SpO₂ %',
  'vital.resp'          => 'Breaths/min',
  'vital.gcs'           => 'GCS',

  'treat.oxygen'            => 'Oxygen',
  'treat.iv_fluids'         => 'IV fluids',
  'treat.splint'            => 'Splint',
  'treat.bleeding_control'  => 'Bleeding control',
  'treat.cpr'               => 'CPR',
  'treat.medicine'          => 'Medicine',
  'treat.none'              => 'None',

  // --- Trip (AMB-04) ---
  'trip.held_for'       => 'Bed held for',
  'trip.navigate'       => 'Navigate',
  'trip.start'          => 'Start trip',
  'trip.arrived'        => 'We have arrived',
  'trip.cancel'         => 'Cancel trip',
  'trip.confirm_cancel' => 'Cancel this trip and release the bed?',
  'trip.rejected'       => 'Hospital said no: {reason}',
  'trip.diverting'      => 'This hospital is now diverting.',
  'trip.hold_instead'   => 'Hold {name} ({min} min) instead',

  'stage.pending'       => 'Sent',
  'stage.accepted'      => 'Accepted',
  'stage.in_transit'    => 'On the way',
  'stage.arrived'       => 'Arrived',

  'tick.sent'           => 'Sent',
  'tick.seen'           => 'Seen',
  'tick.ready'          => 'Team ready',
  'tick.none'           => 'Handover not sent yet',

  // --- Bed board (NUR-01) ---
  'bed.ward'            => 'Ward',
  'bed.scan'            => 'Scan bed',
  'bed.free'            => 'Free',
  'bed.occupied'        => 'Occupied',
  'bed.cleaning'        => 'Cleaning',
  'bed.held'            => 'Held',
  'bed.held_explain'    => 'Held for an incoming ambulance. It changes by itself when they arrive or cancel.',
  'bed.saved'           => '{label} is now {status}',
  'bed.synced'          => 'Synced {s} s ago',

  // --- Divert (NUR-M1 / NUR-M2) ---
  'divert.title'          => 'Divert ambulances',
  'divert.label'          => 'Diverting',
  'divert.on'             => 'Diverting is ON',
  'divert.turn_on'        => 'Turn diverting on',
  'divert.confirm_off'    => 'Turn diverting off?',
  'divert.how_long'       => 'For how long?',
  'divert.until_off'      => 'Until I turn it off',
  'divert.reason'         => 'Reason',
  'divert.full'           => 'Hospital full',
  'divert.no_staff'       => 'No staff',
  'divert.equipment_down' => 'Equipment down',
  'divert.other'          => 'Other',
  'divert.crews_alerted.one'   => '1 ambulance crew alerted',
  'divert.crews_alerted.other' => '{n} ambulance crews alerted',

  // --- Incoming (NUR-03 / NUR-04) ---
  'inc.title'           => 'Incoming patients',
  'inc.new'             => 'New patient coming',
  'inc.tap_for_sound'   => 'Tap anywhere once to turn the alarm sound on.',
  'inc.eta'             => '{min} min · {km} km',
  'inc.no_gps'          => 'No GPS from the ambulance',
  'inc.bed'             => 'Bed {bed}',
  'inc.five_min'        => 'less than 5 min away',
  'inc.not_measured'    => 'Vitals not measured',
  'inc.treatment'       => 'Treatment: {what}',
  'inc.accept'          => 'Accept',
  'inc.reject'          => 'Reject ({s} s)',
  'inc.reject_why'      => 'Why are you rejecting?',
  'inc.r_no_bed'        => 'No bed',
  'inc.r_no_staff'      => 'No staff',
  'inc.r_equipment'     => 'Equipment problem',
  'inc.got_it'          => "I've got it",
  'inc.claimed'         => 'You are handling this patient.',
  'inc.claimed_by'      => 'Handled by {name}',
  'inc.done_accept'     => 'Accepted',
  'inc.done_reject'     => 'Rejected',

  // --- Shift checklist (NUR-02) ---
  'shift.title'         => 'Shift checklist',
  'shift.beds'          => 'Beds',
  'shift.held'          => 'Held beds cannot be changed here.',
  'shift.equipment'     => 'Equipment working',
  'shift.specialists'   => 'Specialists on duty',
  'shift.summary'       => 'Summary of changes',
  'shift.nothing'       => 'No changes',
  'shift.skipped'       => 'Skipped',
  'shift.submit'        => 'Save shift update',
  'shift.changes.one'   => '1 change',
  'shift.changes.other' => '{n} changes',

  'eq.ct_scan'          => 'CT scanner',
  'eq.xray'             => 'X-ray',
  'eq.ventilator'       => 'Ventilator',
  'eq.theatre'          => 'Theatre',
  'eq.oxygen_plant'     => 'Oxygen plant',

  'sp.surgeon'          => 'Surgeon',
  'sp.obstetrician'     => 'Obstetrician',
  'sp.anaesthetist'     => 'Anaesthetist',
  'sp.paediatrician'    => 'Paediatrician',

  // --- Blood bank (BLD-01) ---
  'blood.units.one'     => '1 unit',
  'blood.units.other'   => '{n} units',
  'blood.reserve'       => 'Reserve: {n}',
  'blood.saved'         => '{type} updated to {n}',


  // === P3: Hariel — login, accounts, admin ===
  'login.title'       => 'Log in',
  'login.email'       => 'Email',
  'login.password'    => 'Password',
  'login.submit'      => 'Log in',
  'login.register'    => 'Create an account',
  'login.expired'     => 'You were logged out because you were inactive. Please log in again.',
  'login.err.empty'   => 'Enter your email and password.',
  'login.err.bad'     => 'Wrong email or password.',
  'login.err.locked'  => 'Too many wrong passwords. This account is locked for a while.',
  'login.err.pending' => 'Your account is waiting for approval by an administrator.',
  'login.err.inactive' => 'This account has been disabled. Please contact an administrator.',

  // === P4: Yaw — matching, trips, emergency ===

  // === P5: James — hospital, blood, simulators ===

];