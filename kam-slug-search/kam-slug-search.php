<?php

/**
 * Plugin Name: Search by Slug(KAM)
 * Plugin URI:
 * Description: Adding Search by Slug to wordpress search using keyword "slug:".
 * Author: Korchuk Andrii
 * Version: 0.1
 * License: GPL2
 * Text Domain: kam-ss-plg
 */

if (!defined('ABSPATH')) exit;

if (defined('KAMSLUGSEARCH_PLG')) exit;

define('KAMSLUGSEARCH_PLG', __FILE__);

class KAM_SS_PLG
{
    public static function init()
    {
        add_filter('posts_search', array(__CLASS__, 'add_search_by_slug'), 99, 2);
    }
    public static function add_search_by_slug($search, $query)
    {
        global $wpdb;

        if (!is_admin() || !$query->is_search()) return $search;

        if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
            $current_lang = apply_filters('wpml_current_language', NULL);
            do_action('wpml_switch_language', $current_lang);
        }

        $s = $query->get('s');
        $slug_marker = __('slug:', 'kam-ss-plg');
        $num = strlen($slug_marker);
        if (strpos($s, $slug_marker) !== false && strpos($s, $slug_marker) === 0) {
            $search = $wpdb->prepare(
                " AND {$wpdb->posts}.post_name LIKE %s",
                '%' . mb_strtolower($wpdb->esc_like(trim(mb_substr($s, $num)))) . '%'
            );
        }
        return $search;
    }
}

KAM_SS_PLG::init();
