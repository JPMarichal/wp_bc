<?php
/**
 * Fix remaining numbered bc_location variants without alias_of.
 *
 * Maps each numbered variant (e.g. "Beth-aven 2") to its canonical base
 * (e.g. "Bet-avén", "Betel", "Cedes", etc.) by discovering the correct
 * Spanish-named post or OpenBible entry.
 *
 * Also fixes ~18 backwards OpenBible aliases where the OpenBible "(2)" post
 * was set as alias of the gnosis numbered variant (wrong direction).
 */

// Load WordPress
require_once dirname(__DIR__) . '/wp-load.php';

$dry_run = in_array('--dry-run', $argv ?? []);
$verbose = in_array('--verbose', $argv ?? []);

if ($dry_run) {
    echo "=== DRY RUN — no changes will be made ===\n\n";
}

/**
 * Set _bc_loc_alias_of on a post, skipping if already set correctly.
 */
function set_alias(int $variant_id, int $canonical_id, string $reason, bool $dry_run, bool $verbose): void {
    $existing = get_post_meta($variant_id, '_bc_loc_alias_of', true);
    if ($existing == $canonical_id) {
        if ($verbose) echo "  SKIP {$variant_id}: already alias_of = {$canonical_id}\n";
        return;
    }
    $variant = get_post($variant_id);
    $canonical = get_post($canonical_id);
    if (!$variant || !$canonical) {
        echo "  ERROR: post not found ({$variant_id} or {$canonical_id})\n";
        return;
    }
    echo "  ALIAS {$variant_id} ({$variant->post_title}) → {$canonical_id} ({$canonical->post_title}) [{$reason}]\n";
    if ($existing) {
        echo "    (was alias_of = {$existing})\n";
    }
    if (!$dry_run) {
        update_post_meta($variant_id, '_bc_loc_alias_of', $canonical_id);
    }
}

/**
 * Delete _bc_loc_alias_of from a post (make it canonical).
 */
function clear_alias(int $post_id, string $reason, bool $dry_run, bool $verbose): void {
    $existing = get_post_meta($post_id, '_bc_loc_alias_of', true);
    if (!$existing) {
        if ($verbose) echo "  SKIP {$post_id}: no alias_of to clear\n";
        return;
    }
    $post = get_post($post_id);
    echo "  CLEAR {$post_id} ({$post->post_title}) alias_of={$existing} [{$reason}]\n";
    if (!$dry_run) {
        delete_post_meta($post_id, '_bc_loc_alias_of');
    }
}

echo "==============================================\n";
echo "STEP 1: Fix backwards OpenBible aliases\n";
echo "   (OpenBible post points TO gnosis variant)\n";
echo "==============================================\n";

// Mapping: backwards OpenBible post → should now point to Spanish base ID
// Format: [openbible_id => correct_base_id]
$backwards_fixes = [
    // Spanish base exists → point OpenBible post to Spanish base
    14  => 13,   // Achzib (2) → Aczib
    28  => 27,   // Ain (2) → Aín (redirect from chain 28→3225→27 to direct 28→27)
    99  => 98,   // Bet-shemesh (2) → Bet-semes
    106 => 105,  // Bezek (2) → Bezec
    170 => 169,  // En-gannim (2) → En-ganim
    216 => 215,  // Goshen (2) → Gosén
    268 => 267,  // Janoah (2) → Janoa
    378 => 377,  // Ophrah (2) → Ofra
    426 => 425,  // Shamir (2) → Samir
    448 => 3157, // Tamar (2) → Tamar
    462 => 461,  // Timnah (2) → Timna
    493 => 492,  // Zanoah (2) → Zanoa

    // No Spanish base — OpenBible post should be the canonical itself
    // These had alias_of pointing TO a gnosis variant; reverse it
    263 => null, // Jabneel (2) → was → 3356 (Jabneel 2). Make canonical.
    277 => null, // Jezreel (2) → was → 2934 (Jezreel). Make canonical.
    353 => null, // Moreh (2) → was → 3403 (Moreh 2). Make canonical.
    364 => null, // Nebo (2) → was → 3411 (Nebo 2). Make canonical.
    465 => null, // Tiphsah (2) → was → 3482 (Tiphsah 2). Make canonical.
];

foreach ($backwards_fixes as $openbible_id => $canonical_id) {
    clear_alias($openbible_id, 'reverse backwards alias', $dry_run, $verbose);
    if ($canonical_id) {
        set_alias($openbible_id, $canonical_id, 'OpenBible→Spanish', $dry_run, $verbose);
    }
}

echo "\n==============================================\n";
echo "STEP 2: Set alias_of on numbered variants\n";
echo "==============================================\n";

// Mapping: numbered variant ID → canonical base ID
$variants = [
    // Achzib
    3220 => 13,   // Achzib 2 → Aczib

    // Aijalon
    3223 => 26,   // Aijalon 2 → Ajalón

    // Baalah
    3245 => 3010, // Baalah 1 → mount Baalah
    3246 => 3010, // Baalah 2 → mount Baalah

    // Baalath — use gnosis Baalat as base
    3247 => 614,  // Baalath 2 → Baalat

    // Babylon
    3248 => 2742, // Babylon 2 → Babilonia
    3249 => 2742, // Babylon 3 → Babilonia

    // Bealoth
    3250 => 620,  // Bealoth 2 → Bealot

    // Beth-aven
    3255 => 86,   // Beth-aven 2 → Bet-avén

    // Beth-shemesh
    3264 => 98,   // Beth-shemesh 2 → Bet-semes
    3265 => 98,   // Beth-shemesh 3 → Bet-semes

    // Bethel
    3256 => 81,   // Bethel 1 → Betel
    3257 => 81,   // Bethel 2 → Betel
    3258 => 81,   // Bethel 3 → Betel

    // Bezek
    3266 => 105,  // Bezek 1 → Bezec
    // 3267 will be set below (was part of backwards fix)

    // Bozrah
    3268 => 109,  // Bozrah 2 → Bosra

    // Calneh
    3272 => 116,  // Calneh 2 → Calne

    // City of Palms — NO BASE FOUND, skip
    // 3274, 3275

    // Cush
    3276 => 659,  // Cush 1 → Cus
    3277 => 659,  // Cush 2 → Cus

    // Dumah
    3283 => 153,  // Dumah 1 → Duma
    3284 => 153,  // Dumah 2 → Duma

    // Eder
    3287 => 670,  // Eder 2 → Edar

    // En-gannim
    // 3290 will be set below (was part of backwards fix)

    // Ephraim
    3291 => 2844, // Ephraim 2 → Efraín

    // Ephron
    3292 => 680,  // Ephron 2 → Efrón

    // Galilee
    3299 => 688,  // Galilee 2 → Galilea

    // Gath
    3300 => 193,  // Gath 1 → Gat
    3301 => 193,  // Gath 2 → Gat
    3302 => 193,  // Gath 3 → Gat

    // Gath-rimmon
    3303 => 195,  // Gath-rimmon 2 → Gat-rimón

    // Gederah
    3310 => 200,  // Gederah 2 → Gedera
    3311 => 200,  // Gederah 3 → Gedera

    // Gibeah
    3318 => 190,  // Gibeah 1 → Gabaa
    3319 => 190,  // Gibeah 2 → Gabaa
    3320 => 190,  // Gibeah 4 → Gabaa

    // Gilead
    3323 => 694,  // Gilead 2 → Galaad

    // Goshen
    3329 => 215,  // Goshen 2 → Gosén
    3330 => 215,  // Goshen 3 → Gosén

    // Hamath
    3335 => 226,  // Hamath 2 → Hamat

    // Hammath
    3336 => 227,  // Hammath 2 → Hamat (hammath)

    // Havilah
    3340 => 711,  // Havilah 2 → Havila
    3341 => 711,  // Havilah 3 → Havila

    // Hazazon-tamar
    3342 => 714,  // Hazazon-tamar 2 → Hazezon-tamar

    // Holon
    3351 => 249,  // Holon 1 → Helón
    3352 => 249,  // Holon 2 → Helón

    // Janoah
    // 3357 will be set below

    // Jarmuth
    3358 => 270,  // Jarmuth 2 → Jarmut

    // Kanah
    3364 => 283,  // Kanah 1 → Caná
    3365 => 283,  // Kanah 2 → Caná

    // Kedesh
    3366 => 285,  // Kedesh 2 → Cedes
    3367 => 285,  // Kedesh 3 → Cedes
    3368 => 285,  // Kedesh 4 → Cedes
    3369 => 285,  // Kedesh 5 → Cedes

    // Kiriathaim
    3373 => 290,  // Kiriathaim 2 → Quiriataim

    // Libnah
    3376 => 753,  // Libnah 2 → Libna

    // Mahaneh-dan
    3387 => 311,  // Mahaneh-dan 2 → Mahane-dan

    // Mount Hor
    3406 => 347,  // Mount Hor 2 → Monte Hor

    // Naamah
    3407 => 356,  // Naamah 2 → Naama

    // Ophrah
    // 3418 will be set below

    // Ramoth
    3432 => 812,  // Ramoth 2 → Ramot

    // Red Sea
    3433 => 813,  // Red Sea 2 → Mar Rojo
    3434 => 813,  // Red Sea 3 → Mar Rojo

    // Rehoboth
    3436 => 401,  // Rehoboth 2 → Rehobot

    // Rhodes
    3437 => 405,  // Rhodes 2 → Rodas

    // Riblah
    3438 => 406,  // Riblah 2 → Ribla

    // Rimmon
    3439 => 407,  // Rimmon 1 → Rimón
    3440 => 407,  // Rimmon 2 → Rimón
    3441 => 407,  // Rimmon 3 → Rimón

    // River
    3442 => 817,  // River 2 → Río
    3443 => 817,  // River 3 → Río

    // Shaaraim
    3453 => 831,  // Shaaraim 2 → Saaraim

    // Shamir
    // 3454 will be set below

    // Socoh
    3459 => 435,  // Socoh 1 → Soco
    3460 => 435,  // Socoh 2 → Soco
    3461 => 435,  // Socoh 3 → Soco

    // South
    3462 => 3151, // South 1 → Sur
    3463 => 3151, // South 2 → Sur
    3464 => 3151, // South 3 → Sur

    // Syria
    3467 => 851,  // Syria 1 → Siria
    3468 => 851,  // Syria 2 → Siria

    // Tappuah
    3474 => 449,  // Tappuah 2 → Tapúa

    // Tarshish
    3475 => 450,  // Tarshish 2 → Tarsis

    // Timnah
    3480 => 461,  // Timnah 2 → Timna
    3481 => 461,  // Timnah 3 → Timna

    // Tiphsah
    3482 => 465,  // Tiphsah 2 → Tiphsah (2)

    // Zanoah
    3496 => 492,  // Zanoah 2 → Zanoa

    // Zaphon
    3497 => 494,  // Zaphon 2 → Zafón

    // Zeredah
    3498 => 877,  // Zeredah 2 → Zereda

    // Ziph
    3500 => 507,  // Ziph 1 → Zif
    3501 => 507,  // Ziph 2 → Zif
];

// Also set alias_of for the numbered variants that were previously pointed to
// by backwards OpenBible posts (now those OpenBible posts are canonical or
// point to Spanish base, so the gnosis variant needs a new alias)
$additional = [
    // These were previously the target of a backwards OpenBible alias.
    // Now that the OpenBible post is either canonical or re-pointed to Spanish base,
    // these need to point to the appropriate base.
    3220 => 13,   // Achzib 2 → Aczib (was target of Achzib (2))
    3264 => 98,   // Beth-shemesh 2 → Bet-semes (was target of Bet-shemesh (2))
    3267 => 105,  // Bezek 2 → Bezec (was target of Bezek (2))
    3290 => 169,  // En-gannim 2 → En-ganim (was target of En-gannim (2))
    3322 => 2876, // Gihon 2 → this already has alias_of=2876, keep it
    3329 => 215,  // Goshen 2 → Gosén (was target of Goshen (2))
    3356 => 262,  // Jabneel 2 → this already has alias_of=262, keep it
    3357 => 267,  // Janoah 2 → Janoa (was target of Janoah (2))
    3403 => 3002, // Moreh 2 → Moreh (was target of Moreh (2))
    3411 => 363,  // Nebo 2 → Nebo (was target of Nebo (2))
    3418 => 377,  // Ophrah 2 → Ofra (was target of Ophrah (2))
    3454 => 425,  // Shamir 2 → Samir (was target of Shamir (2))
    3473 => 3157, // Tamar 2 → Tamar (was target of Tamar (2))
    3480 => 461,  // Timnah 2 → Timna (was target of Timnah (2))
    3482 => 465,  // Tiphsah 2 → Tiphsah (2) (was target of Tiphsah (2))
    3496 => 492,  // Zanoah 2 → Zanoa (was target of Zanoah (2))
];

// Merge (additional takes precedence for already-defined keys)
$variants = $additional + $variants;

foreach ($variants as $variant_id => $canonical_id) {
    // Skip Bether (no base exists)
    if (in_array($variant_id, [891, 3259])) {
        if ($verbose) echo "  SKIP {$variant_id}: Bether — no base exists\n";
        continue;
    }
    // Skip City of Palms (no base exists)
    if (in_array($variant_id, [3274, 3275])) {
        if ($verbose) echo "  SKIP {$variant_id}: City of Palms — no base exists\n";
        continue;
    }
    set_alias($variant_id, $canonical_id, 'numbered→base', $dry_run, $verbose);
}

echo "\n==============================================\n";
echo "SUMMARY\n";
echo "==============================================\n";
global $wpdb;
$remaining = $wpdb->get_var("
    SELECT COUNT(*) FROM wp_posts p
    LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_bc_loc_alias_of'
    WHERE p.post_type = 'bc_location' AND p.post_status = 'publish'
      AND p.post_title REGEXP ' [0-9]+$'
      AND pm.meta_id IS NULL
");
echo "Numbered variants STILL without alias_of: {$remaining}\n";
echo "Done.\n";
