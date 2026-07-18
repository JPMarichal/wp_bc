<?php
/**
 * External Links SEO
 *
 * Forces target="_blank" and rel="noopener noreferrer" on all external
 * front-end links inside post content and excerpts.
 */

add_filter( 'the_content', 'bc_external_links_new_window', 999 );
add_filter( 'the_excerpt', 'bc_external_links_new_window', 999 );

function bc_external_links_new_window( $content ) {
    if ( ! is_string( $content ) || empty( $content ) ) {
        return $content;
    }

    $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
    if ( ! $site_host ) {
        $site_host = $_SERVER['HTTP_HOST'] ?? '';
    }
    $site_host = preg_replace( '/^www\./', '', strtolower( $site_host ) );

    if ( empty( $site_host ) ) {
        return $content;
    }

    if ( class_exists( 'DOMDocument' ) && function_exists( 'libxml_use_internal_errors' ) ) {
        libxml_use_internal_errors( true );

        $doc = new DOMDocument();
        $html = '<div id="bc-external-links-wrap">' . $content . '</div>';
        $doc->loadHTML( $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );

        libxml_clear_errors();

        $anchors = $doc->getElementsByTagName( 'a' );

        $hrefs = [];
        foreach ( $anchors as $a ) {
            $hrefs[] = $a->getAttribute( 'href' );
        }

        for ( $i = 0; $i < count( $hrefs ); $i++ ) {
            $a = $anchors->item( $i );
            if ( ! $a instanceof DOMElement ) {
                continue;
            }

            $href = trim( $a->getAttribute( 'href' ) );

            if ( empty( $href ) || strpos( $href, '#' ) === 0 ) {
                continue;
            }

            if ( preg_match( '/^(mailto|tel|javascript|data:)/i', $href ) ) {
                continue;
            }

            $parsed = wp_parse_url( $href );

            if ( ! isset( $parsed['host'] ) ) {
                continue;
            }

            $link_host = preg_replace( '/^www\./', '', strtolower( $parsed['host'] ) );

            if ( $link_host === $site_host ) {
                continue;
            }

            $existing_target = $a->getAttribute( 'target' );
            $existing_rel    = $a->getAttribute( 'rel' );

            if ( $existing_target !== '_blank' ) {
                $a->setAttribute( 'target', '_blank' );
            }

            $rel_values = array_filter( explode( ' ', $existing_rel ) );
            $rel_values = array_map( 'strtolower', $rel_values );
            $rel_values = array_diff( $rel_values, [ 'noopener', 'noreferrer' ] );
            $rel_values[] = 'noopener';
            $rel_values[] = 'noreferrer';

            $a->setAttribute( 'rel', implode( ' ', array_unique( $rel_values ) ) );
        }

        foreach ( $doc->getElementsByTagName( 'a' ) as $a ) {
            if ( ! $a instanceof DOMElement ) {
                continue;
            }

            if ( $a->getAttribute( 'target' ) !== '_blank' ) {
                continue;
            }

            $has_icon = false;
            foreach ( $a->childNodes as $child ) {
                if ( $child instanceof DOMElement && strpos( ' ' . $child->getAttribute( 'class' ) . ' ', ' fa-external-link-alt ' ) !== false ) {
                    $has_icon = true;
                    break;
                }
            }

            if ( ! $has_icon ) {
                $a->appendChild( $doc->createTextNode( ' ' ) );
                $icon = $doc->createElement( 'i' );
                $icon->setAttribute( 'class', 'fas fa-external-link-alt' );
                $icon->setAttribute( 'aria-hidden', 'true' );
                $a->appendChild( $icon );
            }
        }

        $body = $doc->saveHTML( $doc->documentElement );
        if ( $body !== false ) {
            $content = preg_replace( '/^<\?xml.*?\?>\s*/', '', $body );
        }

        libxml_use_internal_errors( false );
    } else {
        $content = preg_replace_callback(
            '/<a\s+([^>]*?)href\s*=\s*(["\'])([^"\']+)\2([^>]*)>([\s\S]*?)<\/a>/i',
            function ( $matches ) use ( $site_host ) {
                $href = trim( $matches[3] );

                if ( empty( $href ) || strpos( $href, '#' ) === 0 ) {
                    return $matches[0];
                }

                if ( preg_match( '/^(mailto|tel|javascript|data:)/i', $href ) ) {
                    return $matches[0];
                }

                $parsed = wp_parse_url( $href );

                if ( ! isset( $parsed['host'] ) ) {
                    return $matches[0];
                }

                $link_host = preg_replace( '/^www\./', '', strtolower( $parsed['host'] ) );

                if ( $link_host === $site_host ) {
                    return $matches[0];
                }

                $before = $matches[1];
                $after  = $matches[4];
                $inner  = $matches[5];

                if ( ! preg_match( '/\btarget\s*=\s*["\'][^"\']*["\']/i', $before . $after ) ) {
                    $before .= ' target="_blank"';
                }

                $existing_rel = '';
                if ( preg_match( '/\brel\s*=\s*["\']([^"\']*)["\']/i', $before . $after, $rel_match ) ) {
                    $existing_rel = $rel_match[1];
                }

                $rel_values = array_filter( explode( ' ', $existing_rel ) );
                $rel_values = array_map( 'strtolower', $rel_values );
                $rel_values = array_diff( $rel_values, [ 'noopener', 'noreferrer' ] );
                $rel_values[] = 'noopener';
                $rel_values[] = 'noreferrer';

                $rel_attr = ' rel="' . implode( ' ', array_unique( $rel_values ) ) . '"';

                $before = preg_replace( '/\s*\btarget\s*=\s*["\'][^"\']*["\']/i', '', $before );
                $before = preg_replace( '/\s*\brel\s*=\s*["\'][^"\']*["\']/i', '', $before );

                if ( strpos( $inner, 'fa-external-link-alt' ) === false ) {
                    $inner .= ' <i class="fas fa-external-link-alt" aria-hidden="true"></i>';
                }

                return '<a ' . trim( $before ) . $rel_attr . ' href="' . $href . '"' . $after . '>' . $inner . '</a>';
            },
            $content
        );
    }

    return $content;
}
