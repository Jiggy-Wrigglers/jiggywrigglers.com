<?php
/**
 * SureRank ACF Bridge
 *
 * Feeds ACF field content into SureRank's post analyzer so xpath checks
 * (//h1, //h2, //img, //a) can see ACF content.
 *
 * Auto-detects heading levels by scanning PHP template files for the HTML
 * tags that wrap each get_field() / get_sub_field() call. Results are
 * cached in a transient for one hour.
 *
 * @package Jiggy_Wrigglers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'surerank_post_analyzer_content', 'jiggy_wrigglers_surerank_acf_bridge', 10, 2 );

function jiggy_wrigglers_surerank_acf_bridge( $content, $post ) {
	if ( ! function_exists( 'get_field_objects' ) || ! $post instanceof WP_Post ) {
		return $content;
	}

	$fields = get_field_objects( $post->ID );
	if ( empty( $fields ) ) {
		return $content;
	}

	$maps  = jiggy_wrigglers_surerank_auto_detect( $post );
	$extra = jiggy_wrigglers_surerank_process_fields( $fields, $maps['heading_map'], $maps['sub_heading_map'] );

	if ( empty( $extra ) ) {
		return $content;
	}

	return $content . "\n" . implode( "\n", $extra );
}

// ---------------------------------------------------------------------------
// Auto-detection: scans template PHP files to build heading maps
// ---------------------------------------------------------------------------

function jiggy_wrigglers_surerank_auto_detect( $post ) {
	$cache_key = 'jiggy_wrigglers_surerank_' . $post->ID;
	$cached    = get_transient( $cache_key );
	if ( false !== $cached && is_array( $cached ) ) {
		return $cached;
	}

	$files      = jiggy_wrigglers_surerank_collect_template_files( $post );
	$heading_map     = [];
	$sub_heading_map = [];

	foreach ( $files as $file ) {
		if ( ! file_exists( $file ) ) {
			continue;
		}

		$source = file_get_contents( $file );

		$var_map = jiggy_wrigglers_surerank_extract_var_map( $source );

		$repeaters     = jiggy_wrigglers_surerank_extract_repeaters( $source );
		$sub_field_map = jiggy_wrigglers_surerank_extract_sub_field_map( $source );

		$lines = explode( "\n", $source );
		foreach ( $lines as $line ) {
			if ( ! preg_match( '/<(h[1-6])\b/i', $line, $tag_match ) ) {
				continue;
			}

			$tag = strtolower( $tag_match[1] );

			foreach ( $var_map as $var => $field_name ) {
				if ( strpos( $line, '$' . $var ) !== false ) {
					$heading_map[ $field_name ] = $tag;
				}
			}

			foreach ( $sub_field_map as $array_key => $field_name ) {
				$pattern = '/\$\w+\[\s*[\'"]' . preg_quote( $array_key, '/' ) . '[\'"]\]/';
				if ( preg_match( $pattern, $line ) ) {
					foreach ( $repeaters as $repeater ) {
						if ( ! isset( $sub_heading_map[ $repeater ] ) ) {
							$sub_heading_map[ $repeater ] = [];
						}
						$sub_heading_map[ $repeater ][ $field_name ] = $tag;
					}
				}
			}
		}
	}

	$heading_map     = apply_filters( 'jiggy_wrigglers_surerank_heading_map', $heading_map );
	$sub_heading_map = apply_filters( 'jiggy_wrigglers_surerank_sub_heading_map', $sub_heading_map );

	$result = [
		'heading_map'     => $heading_map,
		'sub_heading_map' => $sub_heading_map,
	];

	set_transient( $cache_key, $result, HOUR_IN_SECONDS );

	return $result;
}

function jiggy_wrigglers_surerank_collect_template_files( $post ) {
	$files        = [];
	$template_dir = get_template_directory();

	$page_template = get_page_template_slug( $post );
	if ( $page_template ) {
		$file   = $template_dir . '/' . $page_template;
		$files[] = $file;
		$files  = array_merge( $files, jiggy_wrigglers_surerank_find_includes( $file ) );
		return array_unique( $files );
	}

	if ( $post->post_type === 'post' ) {
		$single = $template_dir . '/single.php';
		$files[] = $single;
		$single_includes = jiggy_wrigglers_surerank_find_includes( $single );
		foreach ( $single_includes as $inc ) {
			$files[] = $inc;
			$files   = array_merge( $files, jiggy_wrigglers_surerank_find_includes( $inc ) );
		}
		return array_unique( $files );
	}

	$posts_page_id = get_option( 'page_for_posts' );
	if ( $posts_page_id && (int) $post->ID === (int) $posts_page_id ) {
		$home = $template_dir . '/home.php';
		if ( file_exists( $home ) ) {
			$files[] = $home;
			$files   = array_merge( $files, jiggy_wrigglers_surerank_find_includes( $home ) );
		}
		return array_unique( $files );
	}

	return array_unique( $files );
}

function jiggy_wrigglers_surerank_find_includes( $file ) {
	$includes = [];
	if ( ! file_exists( $file ) ) {
		return $includes;
	}

	$source        = file_get_contents( $file );
	$template_dir  = get_template_directory();

	preg_match_all(
		"/locate_template\s*\(\s*['\"]([^'\"]+)['\"]/s",
		$source,
		$m1
	);
	foreach ( $m1[1] as $path ) {
		$full = $template_dir . '/' . $path;
		if ( file_exists( $full ) ) {
			$includes[] = $full;
		}
	}

	preg_match_all(
		"/get_template_directory\s*\(\s*\)\s*\.\s*['\"]([^'\"]+)['\"]/s",
		$source,
		$m2
	);
	foreach ( $m2[1] as $path ) {
		$full = $template_dir . '/' . $path;
		if ( file_exists( $full ) && ! in_array( $full, $includes, true ) ) {
			$includes[] = $full;
		}
	}

	return $includes;
}

function jiggy_wrigglers_surerank_extract_var_map( $source ) {
	$map = [];

	preg_match_all(
		'/\$(\w+)\s*=\s*get_field\s*\(\s*[\'"]([^\'"]+)[\'"]/s',
		$source,
		$matches,
		PREG_SET_ORDER
	);

	foreach ( $matches as $m ) {
		$map[ $m[1] ] = $m[2];
	}

	return $map;
}

function jiggy_wrigglers_surerank_extract_repeaters( $source ) {
	$repeaters = [];

	preg_match_all(
		"/have_rows\s*\(\s*['\"]([^'\"]+)['\"]/s",
		$source,
		$matches,
		PREG_SET_ORDER
	);

	foreach ( $matches as $m ) {
		$repeaters[] = $m[1];
	}

	return array_unique( $repeaters );
}

function jiggy_wrigglers_surerank_extract_sub_field_map( $source ) {
	$map = [];

	preg_match_all(
		'/[\'"](\w+)[\'"]\s*=>\s*get_sub_field\s*\(\s*[\'"]([^\'"]+)[\'"]/s',
		$source,
		$matches,
		PREG_SET_ORDER
	);

	foreach ( $matches as $m ) {
		$map[ $m[1] ] = $m[2];
	}

	return $map;
}

// ---------------------------------------------------------------------------
// Field processing
// ---------------------------------------------------------------------------

function jiggy_wrigglers_surerank_process_fields( $fields, $heading_map, $sub_heading_map ) {
	$extra = [];

	foreach ( $fields as $name => $field ) {
		$value = $field['value'] ?? '';
		$type  = $field['type'] ?? '';

		if ( jiggy_wrigglers_surerank_is_empty( $value ) ) {
			continue;
		}

		if ( isset( $heading_map[ $name ] ) && is_string( $value ) && $value !== '' ) {
			$tag     = $heading_map[ $name ];
			$extra[] = '<' . $tag . '>' . esc_html( $value ) . '</' . $tag . '>';
			continue;
		}

		if ( $type === 'wysiwyg' && is_string( $value ) ) {
			$extra[] = $value;
			continue;
		}

		if ( in_array( $type, [ 'text', 'textarea' ], true ) && is_string( $value ) && $value !== '' ) {
			$extra[] = '<p>' . esc_html( $value ) . '</p>';
			continue;
		}

		if ( $type === 'image' && is_array( $value ) && ! empty( $value['url'] ) ) {
			$extra[] = jiggy_wrigglers_surerank_format_image( $value );
			continue;
		}

		if ( $type === 'gallery' && is_array( $value ) ) {
			foreach ( $value as $image ) {
				if ( is_array( $image ) && ! empty( $image['url'] ) ) {
					$extra[] = jiggy_wrigglers_surerank_format_image( $image );
				}
			}
			continue;
		}

		if ( $type === 'link' && is_array( $value ) && ! empty( $value['url'] ) ) {
			$extra[] = jiggy_wrigglers_surerank_format_link( $value );
			continue;
		}

		if ( $type === 'repeater' && is_array( $value ) ) {
			$sub_map = $sub_heading_map[ $name ] ?? [];
			foreach ( $value as $row ) {
				if ( is_array( $row ) ) {
					$extra = array_merge( $extra, jiggy_wrigglers_surerank_process_repeater_row( $row, $sub_map ) );
				}
			}
			continue;
		}

		if ( $type === 'file' && is_array( $value ) && ! empty( $value['url'] ) ) {
			continue;
		}
	}

	return $extra;
}

function jiggy_wrigglers_surerank_process_repeater_row( $row, $sub_map ) {
	$extra = [];

	foreach ( $row as $sub_name => $sub_value ) {
		if ( jiggy_wrigglers_surerank_is_empty( $sub_value ) ) {
			continue;
		}

		if ( isset( $sub_map[ $sub_name ] ) && is_string( $sub_value ) ) {
			$tag     = $sub_map[ $sub_name ];
			$extra[] = '<' . $tag . '>' . esc_html( $sub_value ) . '</' . $tag . '>';
			continue;
		}

		if ( is_array( $sub_value ) && ! empty( $sub_value['url'] ) ) {
			if ( ! empty( $sub_value['title'] ) && isset( $sub_value['alt'] ) ) {
				$extra[] = jiggy_wrigglers_surerank_format_image( $sub_value );
			} else {
				$extra[] = jiggy_wrigglers_surerank_format_link( $sub_value );
			}
			continue;
		}

		if ( is_string( $sub_value ) && $sub_value !== '' ) {
			$extra[] = '<p>' . esc_html( $sub_value ) . '</p>';
		}
	}

	return $extra;
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function jiggy_wrigglers_surerank_format_image( $image ) {
	return sprintf(
		'<img src="%s" alt="%s" />',
		esc_url( $image['url'] ),
		esc_attr( $image['alt'] ?? '' )
	);
}

function jiggy_wrigglers_surerank_format_link( $link ) {
	return sprintf(
		'<a href="%s">%s</a>',
		esc_url( $link['url'] ),
		esc_html( $link['title'] ?? $link['url'] )
	);
}

function jiggy_wrigglers_surerank_is_empty( $value ) {
	if ( $value === '0' || $value === 0 ) {
		return false;
	}
	return empty( $value );
}