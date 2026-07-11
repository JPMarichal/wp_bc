<?php
require_once '/var/www/html/wp-load.php';

$posts = get_posts( array(
    'post_type'      => 'bc_location',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_key'       => '_bc_loc_source',
    'meta_value'     => 'openbible',
    'fields'         => 'ids',
) );

$fixed = 0;
$skipped_conflict = 0;
$skipped_alias = 0;
$already_clean = 0;
$conflicts = array();

foreach ( $posts as $post_id ) {
    $post = get_post( $post_id );
    $slug = $post->post_name;

    // Skip if doesn't start with openbible-
    if ( strpos( $slug, 'openbible-' ) !== 0 ) {
        $already_clean++;
        continue;
    }

    $rest = substr( $slug, 9 ); // remove 'openbible-'
    $parts = explode( '-', $rest );
    $n = count( $parts );

    // Need even number of parts
    if ( $n % 2 !== 0 ) {
        echo "SKIP (odd parts): ID=$post_id slug=$slug\n";
        $already_clean++;
        continue;
    }

    $half = $n / 2;
    $first = array_slice( $parts, 0, $half );
    $second = array_slice( $parts, $half );

    if ( $first !== $second ) {
        echo "SKIP (mismatch): ID=$post_id slug=$slug parts=" . implode( '-', $first ) . " vs " . implode( '-', $second ) . "\n";
        $already_clean++;
        continue;
    }

    $clean = implode( '-', $first );

    // Skip if already alias
    $alias_of = get_post_meta( $post_id, '_bc_loc_alias_of', true );
    if ( ! empty( $alias_of ) ) {
        $skipped_alias++;
        continue;
    }

    // Check for conflicts
    $existing = get_posts( array(
        'post_type'      => 'bc_location',
        'post_status'    => 'publish',
        'name'           => $clean,
        'exclude'        => array( $post_id ),
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ) );

    if ( ! empty( $existing ) ) {
        $conflicts[] = "CONFLICT: ID=$post_id $slug -> $clean taken by ID={$existing[0]}";
        $skipped_conflict++;
        continue;
    }

    $result = wp_update_post( array(
        'ID'        => $post_id,
        'post_name' => $clean,
    ) );

    if ( is_wp_error( $result ) ) {
        echo "ERROR: ID=$post_id: " . $result->get_error_message() . "\n";
    } else {
        echo "FIXED: ID=$post_id $slug -> $clean\n";
        $fixed++;
    }
}

echo "\n=== Summary ===\n";
echo "Fixed: $fixed\n";
echo "Already clean: $already_clean\n";
echo "Skipped (conflict): $skipped_conflict\n";
echo "Skipped (alias_of): $skipped_alias\n";
echo "\nConflicts:\n";
foreach ( $conflicts as $c ) {
    echo "  $c\n";
}
