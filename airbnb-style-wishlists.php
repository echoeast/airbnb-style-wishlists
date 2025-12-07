<?php
/**
 * Plugin Name: Airbnb Style Wishlists & Enquiry System
 * Description: Adds Airbnb-style wishlist functionality and site-wide enquiry system for Products.
 * Version: 2.2
 * Author: Joseph Lewis
 */

// Test update: v2.2 - Auto-update is working! 🎉

if (!defined('ABSPATH')) exit;

// Define plugin version constant
define('AIRBNB_WISHLISTS_VERSION', '2.2');

// ============================================================================
// UUPD - AUTOMATIC UPDATES FROM GITHUB
// ============================================================================

add_action('plugins_loaded', function() {
    require_once __DIR__ . '/updater.php';

    \UUPD\V1\UUPD_Updater_V1::register([
        'plugin_file'     => plugin_basename(__FILE__),           // "Airbnb Style Wishlists/airbnb-style-wishlists.php"
        'slug'            => 'airbnb-style-wishlists',            // Must match plugin folder/slug
        'name'            => 'Airbnb Style Wishlists & Enquiry System',
        'version'         => AIRBNB_WISHLISTS_VERSION,
        'server'          => 'https://raw.githubusercontent.com/echoeast/airbnb-style-wishlists/main/uupd/',
        
        // Optional: Add GitHub token if repo is private or to avoid rate limits
        // 'github_token'    => 'ghp_YourTokenHere',
        
        // Optional: Allow pre-release versions (beta, rc)
        'allow_prerelease'=> false,
        
        // Optional: Custom text domain (defaults to slug)
        'textdomain'      => 'airbnb-style-wishlists',
    ]);
}, 20);

// ✅ Bricks Dynamic Data: Wishlist ID
add_filter('bricks/dynamic_tags_list', function($tags) {
    $tags[] = [
        'name'  => '{wishlist_id}',
        'label' => 'Wishlist ID',
        'group' => 'Wishlist',
    ];
    return $tags;
});

add_filter('bricks/dynamic_data/render_content', function($content, $post, $context = 'text') {
    // Only look for dynamic tag {wishlist_id}
    if (strpos($content, '{wishlist_id}') === false) {
        return $content;
    }
    
    // Get the wishlist ID
    $wishlist_id = get_queried_object_id();
    
    // Replace the tag with the value
    $content = str_replace('{wishlist_id}', $wishlist_id, $content);
    
    return $content;
}, 20, 3);

// ✅ Register Wishlist CPT
add_action('init', function() {
    register_post_type('wishlist', [
        'labels' => [
            'name' => 'Wishlists',
            'singular_name' => 'Wishlist',
        ],
        'public' => true,
        'has_archive' => false,
        'show_in_rest' => true,
        'supports' => ['title', 'thumbnail'],
        'menu_icon' => 'dashicons-heart',
        'rewrite' => [
            'slug' => 'wishlist',
            'with_front' => false,
            'pages' => false,
            'feeds' => false,
            'ep_mask' => EP_PERMALINK,
        ],
    ]);
});

// ✅ Force wishlist slug to post ID
add_action('save_post_wishlist', function($post_id, $post, $update) {
    // Only run for published wishlists
    if ($post->post_status !== 'publish') return;

    // Only update if the slug is not already the post ID
    if ($post->post_name !== (string)$post_id) {
        // Remove action to prevent infinite loop
        remove_action('save_post_wishlist', __FUNCTION__, 10);

        // Update the post_name (slug) to the post ID
        wp_update_post([
            'ID' => $post_id,
            'post_name' => $post_id
        ]);

        // Re-add the action
        add_action('save_post_wishlist', __FUNCTION__, 10, 3);
    }
}, 10, 3);

// ✅ Enqueue Scripts & Styles
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('wishlist-js', plugin_dir_url(__FILE__) . 'wishlist.js', [], null, true);
    wp_enqueue_style('wishlist-css', plugin_dir_url(__FILE__) . 'wishlist.css');

    wp_localize_script('wishlist-js', 'wishlist_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wishlist_nonce'),
    ]);
});

// ✅ AJAX: Create Wishlist
add_action('wp_ajax_create_wishlist', function() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    $name = sanitize_text_field($_POST['name'] ?? '');
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    if (!$name) wp_send_json_error(['message' => 'Missing name']);
    $id = wp_insert_post([
        'post_type' => 'wishlist',
        'post_title' => $name,
        'post_status' => 'publish',
    ]);
    if (is_wp_error($id)) wp_send_json_error(['message' => 'Error creating wishlist']);

    // If a product ID is provided, add it to the wishlist
    if ($product_id) {
        update_post_meta($id, '_wishlist_items', [$product_id]);
        
        // Set wishlist thumbnail from the first product
        $acf_image = get_field('thumbnail_image', $product_id); // ACF field: returns array or ID depending on settings

        // If ACF is set to return an array, get the ID from it
        if (is_array($acf_image) && isset($acf_image['ID'])) {
            $thumb_id = $acf_image['ID'];
        } elseif (is_numeric($acf_image)) {
            $thumb_id = $acf_image;
        } else {
            $thumb_id = null;
        }

        if ($thumb_id) {
            set_post_thumbnail($id, $thumb_id);
        }
    }

    wp_send_json_success(['id' => $id, 'name' => $name]);
});
add_action('wp_ajax_nopriv_create_wishlist', function() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    $name = sanitize_text_field($_POST['name'] ?? '');
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    if (!$name) wp_send_json_error(['message' => 'Missing name']);
    $id = wp_insert_post([
        'post_type' => 'wishlist',
        'post_title' => $name,
        'post_status' => 'publish',
    ]);
    if (is_wp_error($id)) wp_send_json_error(['message' => 'Error creating wishlist']);

    // If a product ID is provided, add it to the wishlist
    if ($product_id) {
        update_post_meta($id, '_wishlist_items', [$product_id]);
        
        // Set wishlist thumbnail from the first product
        $acf_image = get_field('thumbnail_image', $product_id); // ACF field: returns array or ID depending on settings

        // If ACF is set to return an array, get the ID from it
        if (is_array($acf_image) && isset($acf_image['ID'])) {
            $thumb_id = $acf_image['ID'];
        } elseif (is_numeric($acf_image)) {
            $thumb_id = $acf_image;
        } else {
            $thumb_id = null;
        }

        if ($thumb_id) {
            set_post_thumbnail($id, $thumb_id);
        }
    }

    wp_send_json_success(['id' => $id, 'name' => $name]);
});

// ✅ AJAX: Add to Wishlist
add_action('wp_ajax_add_to_wishlist', function () {
    check_ajax_referer('wishlist_nonce', 'nonce');

    $wishlist_id = (int) $_POST['wishlist_id'];
    $product_id = (int) $_POST['product_id'];

    if (!$wishlist_id || !$product_id) {
        wp_send_json_error(['message' => 'Missing ID(s)']);
    }

    $items = get_post_meta($wishlist_id, '_wishlist_items', true);
    if (!is_array($items)) $items = [];

    // Add product to wishlist if not already present
    if (!in_array($product_id, $items)) {
        $items[] = $product_id;
        update_post_meta($wishlist_id, '_wishlist_items', $items);
    }

    // ✅ Set wishlist thumbnail if this is the first item
    if (count($items) === 1) {
        $acf_image = get_field('thumbnail_image', $product_id); // ACF field: returns array or ID depending on settings

        // If ACF is set to return an array, get the ID from it
        if (is_array($acf_image) && isset($acf_image['ID'])) {
            $thumb_id = $acf_image['ID'];
        } elseif (is_numeric($acf_image)) {
            $thumb_id = $acf_image;
        } else {
            $thumb_id = null;
        }

        if ($thumb_id) {
            set_post_thumbnail($wishlist_id, $thumb_id);
        }
    }

    wp_send_json_success(['message' => 'Added']);
});
add_action('wp_ajax_nopriv_add_to_wishlist', function () {
    check_ajax_referer('wishlist_nonce', 'nonce');

    $wishlist_id = (int) $_POST['wishlist_id'];
    $product_id = (int) $_POST['product_id'];

    if (!$wishlist_id || !$product_id) {
        wp_send_json_error(['message' => 'Missing ID(s)']);
    }

    $items = get_post_meta($wishlist_id, '_wishlist_items', true);
    if (!is_array($items)) $items = [];

    // Add product to wishlist if not already present
    if (!in_array($product_id, $items)) {
        $items[] = $product_id;
        update_post_meta($wishlist_id, '_wishlist_items', $items);
    }

    // ✅ Set wishlist thumbnail if this is the first item
    if (count($items) === 1) {
        $acf_image = get_field('thumbnail_image', $product_id); // ACF field: returns array or ID depending on settings

        // If ACF is set to return an array, get the ID from it
        if (is_array($acf_image) && isset($acf_image['ID'])) {
            $thumb_id = $acf_image['ID'];
        } elseif (is_numeric($acf_image)) {
            $thumb_id = $acf_image;
        } else {
            $thumb_id = null;
        }

        if ($thumb_id) {
            set_post_thumbnail($wishlist_id, $thumb_id);
        }
    }

    wp_send_json_success(['message' => 'Added']);
});


// ✅ AJAX: Remove from Wishlist
add_action('wp_ajax_remove_from_wishlist', function () {
    check_ajax_referer('wishlist_nonce', 'nonce');

    $wishlist_id = (int) $_POST['wishlist_id'];
    $product_id = (int) $_POST['product_id'];

    if (!$wishlist_id || !$product_id) {
        wp_send_json_error(['message' => 'Missing ID(s)']);
    }

    $items = get_post_meta($wishlist_id, '_wishlist_items', true);
    if (!is_array($items)) $items = [];

    $new_items = array_values(array_diff($items, [$product_id]));
    update_post_meta($wishlist_id, '_wishlist_items', $new_items);

    // ✅ Update wishlist thumbnail
    if (!empty($new_items)) {
        $new_first_product_id = $new_items[0];

        $acf_image = get_field('thumbnail_image', $new_first_product_id);
        if (is_array($acf_image) && isset($acf_image['ID'])) {
            $thumb_id = $acf_image['ID'];
        } elseif (is_numeric($acf_image)) {
            $thumb_id = $acf_image;
        } else {
            $thumb_id = null;
        }

        if ($thumb_id) {
            set_post_thumbnail($wishlist_id, $thumb_id);
        } else {
            delete_post_thumbnail($wishlist_id); // fallback
        }

    } else {
        // If no items remain, remove thumbnail
        delete_post_thumbnail($wishlist_id);
    }

    wp_send_json_success(['message' => 'Removed']);
});
add_action('wp_ajax_nopriv_remove_from_wishlist', function () {
    check_ajax_referer('wishlist_nonce', 'nonce');

    $wishlist_id = (int) $_POST['wishlist_id'];
    $product_id = (int) $_POST['product_id'];

    if (!$wishlist_id || !$product_id) {
        wp_send_json_error(['message' => 'Missing ID(s)']);
    }

    $items = get_post_meta($wishlist_id, '_wishlist_items', true);
    if (!is_array($items)) $items = [];

    $new_items = array_values(array_diff($items, [$product_id]));
    update_post_meta($wishlist_id, '_wishlist_items', $new_items);

    // ✅ Update wishlist thumbnail
    if (!empty($new_items)) {
        $new_first_product_id = $new_items[0];

        $acf_image = get_field('thumbnail_image', $new_first_product_id);
        if (is_array($acf_image) && isset($acf_image['ID'])) {
            $thumb_id = $acf_image['ID'];
        } elseif (is_numeric($acf_image)) {
            $thumb_id = $acf_image;
        } else {
            $thumb_id = null;
        }

        if ($thumb_id) {
            set_post_thumbnail($wishlist_id, $thumb_id);
        } else {
            delete_post_thumbnail($wishlist_id); // fallback
        }

    } else {
        // If no items remain, remove thumbnail
        delete_post_thumbnail($wishlist_id);
    }

    wp_send_json_success(['message' => 'Removed']);
});


// ✅ AJAX: Rename Wishlist
add_action('wp_ajax_rename_wishlist', function() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    $w = (int) ($_POST['wishlist_id'] ?? 0);
    $title = sanitize_text_field($_POST['new_name'] ?? '');

    if (!$title) wp_send_json_error(['message' => 'Missing name']);

    wp_update_post([
        'ID' => $w,
        'post_title' => $title,
    ]);

    wp_send_json_success(['name' => $title]);
});
add_action('wp_ajax_nopriv_rename_wishlist', function() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    $w = (int) ($_POST['wishlist_id'] ?? 0);
    $title = sanitize_text_field($_POST['new_name'] ?? '');

    if (!$title) wp_send_json_error(['message' => 'Missing name']);

    wp_update_post([
        'ID' => $w,
        'post_title' => $title,
    ]);

    wp_send_json_success(['name' => $title]);
});

// ✅ AJAX: Get Wishlist Count
add_action('wp_ajax_get_wishlist_count', function() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    $wishlist_id = (int) ($_POST['wishlist_id'] ?? 0);

    if (!$wishlist_id) {
        wp_send_json_error(['message' => 'Missing wishlist ID']);
    }

    $items = get_post_meta($wishlist_id, '_wishlist_items', true);
    if (!is_array($items)) $items = [];
    
    $count = count($items);
    $label = ($count === 1) ? 'product' : 'products';
    $countText = $count . ' ' . $label;

    wp_send_json_success(['count' => $count, 'countText' => $countText]);
});
add_action('wp_ajax_nopriv_get_wishlist_count', function() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    $wishlist_id = (int) ($_POST['wishlist_id'] ?? 0);

    if (!$wishlist_id) {
        wp_send_json_error(['message' => 'Missing wishlist ID']);
    }

    $items = get_post_meta($wishlist_id, '_wishlist_items', true);
    if (!is_array($items)) $items = [];
    
    $count = count($items);
    $label = ($count === 1) ? 'product' : 'products';
    $countText = $count . ' ' . $label;

    wp_send_json_success(['count' => $count, 'countText' => $countText]);
});

// ✅ AJAX: Delete Wishlist
add_action('wp_ajax_delete_wishlist', function() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    $wishlist_id = (int) ($_POST['wishlist_id'] ?? 0);

    if (!$wishlist_id) {
        wp_send_json_error(['message' => 'Missing wishlist ID']);
    }

    // Check if user can delete this wishlist (optional security check)
    $wishlist = get_post($wishlist_id);
    if (!$wishlist || $wishlist->post_type !== 'wishlist') {
        wp_send_json_error(['message' => 'Invalid wishlist']);
    }

    // Delete the wishlist
    $result = wp_delete_post($wishlist_id, true); // true = force delete

    if ($result) {
        wp_send_json_success(['message' => 'Wishlist deleted successfully']);
    } else {
        wp_send_json_error(['message' => 'Error deleting wishlist']);
    }
});
add_action('wp_ajax_nopriv_delete_wishlist', function() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    $wishlist_id = (int) ($_POST['wishlist_id'] ?? 0);

    if (!$wishlist_id) {
        wp_send_json_error(['message' => 'Missing wishlist ID']);
    }

    // Check if user can delete this wishlist (optional security check)
    $wishlist = get_post($wishlist_id);
    if (!$wishlist || $wishlist->post_type !== 'wishlist') {
        wp_send_json_error(['message' => 'Invalid wishlist']);
    }

    // Delete the wishlist
    $result = wp_delete_post($wishlist_id, true); // true = force delete

    if ($result) {
        wp_send_json_success(['message' => 'Wishlist deleted successfully']);
    } else {
        wp_send_json_error(['message' => 'Error deleting wishlist']);
    }
});

// ✅ Bricks Query Loop: wishlist_products
add_filter('bricks/posts/query/wishlist_products', function($query) {
    if (!is_singular('wishlist')) return $query;

    $raw_ids = get_post_meta(get_the_ID(), '_wishlist_items', true);

    $ids = is_array($raw_ids)
        ? $raw_ids
        : explode(',', $raw_ids);

    $ids = array_filter(array_map('intval', $ids));

    $query['post_type'] = 'product';
    $query['post__in'] = !empty($ids) ? $ids : [0];
    $query['orderby'] = 'post__in';

    return $query;
});


// ✅ Shortcode: Create Wishlist Form
add_shortcode('create_wishlist_form', function() {
    return <<<HTML
<div class="wishlist-create">
    <input id="new-wishlist-name" type="text" placeholder="Wishlist name" class="wishlist-input" />
    <button onclick="createWishlist()" class="create-wishlist-button">Create new wishlist</button>
</div>
HTML;
});

// ✅ Shortcode: Add to Wishlist Button
// Now accepts both wishlist_id AND product_id (product_id optional if you want to rely on JS)
add_shortcode('add_to_wishlist_button', function($atts) {
    $a = shortcode_atts([
        'wishlist_id' => 0,
        'product_id' => 0,
    ], $atts);

    $wishlist_id = (int) $a['wishlist_id'];
    $product_id = (int) $a['product_id'];

    // The JS function only receives wishlist_id, product_id is passed via window.selectedProductId,
    // but if you want to hardcode product_id (e.g. in some static place), you can pass it via onclick.
    // We'll embed it as data attributes for easier JS use and avoid inline onclick.

    return "<button class='wishlist-add-btn' data-wishlist='{$wishlist_id}' data-product='{$product_id}' onclick='addToWishlist({$wishlist_id})'>＋</button>";
});

// ✅ Shortcode: Remove from Wishlist Button
add_shortcode('remove_from_wishlist_button', function($atts) {
    $a = shortcode_atts([
        'wishlist_id' => 0,
        'product_id' => get_the_ID(),
    ], $atts);

    $wishlist_id = (int) $a['wishlist_id'];
    $product_id = (int) $a['product_id'];

    // If no wishlist_id provided, get it from the main query (before the loop changes context)
    if (!$wishlist_id) {
        global $wp_query;
        // Get the wishlist ID from the main query
        if (isset($wp_query->queried_object) && $wp_query->queried_object->post_type === 'wishlist') {
            $wishlist_id = $wp_query->queried_object->ID;
        } elseif (is_singular('wishlist')) {
            $wishlist_id = get_queried_object_id();
        }
    }

    return "<button class='wishlist-remove-btn' data-wishlist='{$wishlist_id}' data-product='{$product_id}' onclick='removeFromWishlist({$wishlist_id}, {$product_id})'>×</button>";
});

// ✅ Shortcode: Delete Wishlist Button
add_shortcode('delete_wishlist_button', function($atts) {
    $a = shortcode_atts([
        'wishlist_id' => get_the_ID(),
        'text' => 'Delete Wishlist',
        'class' => 'wishlist-delete-btn',
    ], $atts);

    $wishlist_id = (int) $a['wishlist_id'];
    $text = esc_html($a['text']);
    $class = esc_attr($a['class']);

    return "<button class='{$class}' onclick='deleteWishlist({$wishlist_id})'>{$text}</button>";
});

// ✅ Shortcode: Editable Wishlist Title
add_shortcode('wishlist_title_editor', function() {
    if (!is_singular('wishlist')) return '';

    $id = get_the_ID();
    $title = esc_html(get_the_title($id));

    return <<<HTML
<div class="wishlist-title-editor" data-id="$id">
    <span class="wishlist-title">$title</span>
    <input type="text" class="wishlist-title-input" value="$title" style="display:none;" />
    <i class="ti-pencil wishlist-icon edit-icon" onclick="toggleWishlistTitleEdit(this)"></i>
    <i class="ti-save-alt wishlist-icon save-icon" onclick="saveWishlistTitle(this)" style="display:none;"></i>
</div>
HTML;
});

// ✅ Shortcode: Wishlist Item Count
add_shortcode('wishlist_item_count', function($atts) {
    $a = shortcode_atts([
        'wishlist_id' => get_the_ID(),
    ], $atts);

    $wishlist_id = (int) $a['wishlist_id'];
    
    if (!$wishlist_id) return '<span class="wishlist-count" data-wishlist="0">0 products</span>';
    
    $items = get_post_meta($wishlist_id, '_wishlist_items', true);
    
    if (!is_array($items)) {
        $items = [];
    }
    
    $count = count($items);
    $label = ($count === 1) ? 'product' : 'products';
    
    return '<span class="wishlist-count" data-wishlist="' . $wishlist_id . '">' . $count . ' ' . $label . '</span>';
});

// ✅ Bricks Dynamic Data: Wishlist Item Count
add_filter('bricks/dynamic_data/wishlist_item_count', function($post_id) {
    if (!$post_id) return '0';
    
    $items = get_post_meta($post_id, '_wishlist_items', true);
    
    if (!is_array($items)) {
        $items = [];
    }
    
    return count($items);
});

// ============================================================================
// ENQUIRY SYSTEM (Site-Wide Shopping Cart)
// ============================================================================

// ✅ Helper: Get Site Enquiry
function bw_get_site_enquiry() {
    $enquiry = get_option('bw_site_enquiry', []);
    return is_array($enquiry) ? $enquiry : [];
}

// ✅ Helper: Update Site Enquiry
function bw_update_site_enquiry($enquiry) {
    update_option('bw_site_enquiry', $enquiry);
}

// ✅ AJAX: Add to Enquiry
add_action('wp_ajax_add_to_enquiry', 'bw_add_to_enquiry');
add_action('wp_ajax_nopriv_add_to_enquiry', 'bw_add_to_enquiry');
function bw_add_to_enquiry() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    
    $product_id = (int) ($_POST['product_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 1);
    
    if (!$product_id || $quantity < 1) {
        wp_send_json_error(['message' => 'Invalid product ID or quantity']);
    }
    
    $enquiry = bw_get_site_enquiry();
    
    // If product already exists, add to existing quantity
    if (isset($enquiry[$product_id])) {
        $enquiry[$product_id] += $quantity;
    } else {
        $enquiry[$product_id] = $quantity;
    }
    
    bw_update_site_enquiry($enquiry);
    
    $total_count = array_sum($enquiry);
    wp_send_json_success([
        'message' => 'Added to enquiry',
        'count' => $total_count
    ]);
}

// ✅ AJAX: Add Wishlist to Enquiry
add_action('wp_ajax_add_wishlist_to_enquiry', 'bw_add_wishlist_to_enquiry');
add_action('wp_ajax_nopriv_add_wishlist_to_enquiry', 'bw_add_wishlist_to_enquiry');
function bw_add_wishlist_to_enquiry() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    
    $wishlist_id = (int) ($_POST['wishlist_id'] ?? 0);
    
    if (!$wishlist_id) {
        wp_send_json_error(['message' => 'Invalid wishlist ID']);
    }
    
    $wishlist_items = get_post_meta($wishlist_id, '_wishlist_items', true);
    if (!is_array($wishlist_items) || empty($wishlist_items)) {
        wp_send_json_error(['message' => 'Wishlist is empty']);
    }
    
    $enquiry = bw_get_site_enquiry();
    
    // Add each wishlist item with quantity of 1 (or increment if already exists)
    foreach ($wishlist_items as $product_id) {
        if (isset($enquiry[$product_id])) {
            $enquiry[$product_id] += 1;
        } else {
            $enquiry[$product_id] = 1;
        }
    }
    
    bw_update_site_enquiry($enquiry);
    
    $total_count = array_sum($enquiry);
    wp_send_json_success([
        'message' => 'Wishlist added to enquiry',
        'count' => $total_count,
        'items_added' => count($wishlist_items)
    ]);
}

// ✅ AJAX: Increase Enquiry Quantity
add_action('wp_ajax_increase_enquiry_quantity', 'bw_increase_enquiry_quantity');
add_action('wp_ajax_nopriv_increase_enquiry_quantity', 'bw_increase_enquiry_quantity');
function bw_increase_enquiry_quantity() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    
    $product_id = (int) ($_POST['product_id'] ?? 0);
    
    if (!$product_id) {
        wp_send_json_error(['message' => 'Invalid product ID']);
    }
    
    $enquiry = bw_get_site_enquiry();
    
    if (isset($enquiry[$product_id])) {
        $enquiry[$product_id]++;
    } else {
        $enquiry[$product_id] = 1;
    }
    
    bw_update_site_enquiry($enquiry);
    
    $total_count = array_sum($enquiry);
    wp_send_json_success([
        'message' => 'Quantity increased',
        'quantity' => $enquiry[$product_id],
        'count' => $total_count
    ]);
}

// ✅ AJAX: Decrease Enquiry Quantity
add_action('wp_ajax_decrease_enquiry_quantity', 'bw_decrease_enquiry_quantity');
add_action('wp_ajax_nopriv_decrease_enquiry_quantity', 'bw_decrease_enquiry_quantity');
function bw_decrease_enquiry_quantity() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    
    $product_id = (int) ($_POST['product_id'] ?? 0);
    
    if (!$product_id) {
        wp_send_json_error(['message' => 'Invalid product ID']);
    }
    
    $enquiry = bw_get_site_enquiry();
    
    if (isset($enquiry[$product_id])) {
        $enquiry[$product_id]--;
        
        // Remove if quantity reaches 0
        if ($enquiry[$product_id] <= 0) {
            unset($enquiry[$product_id]);
        }
    }
    
    bw_update_site_enquiry($enquiry);
    
    $total_count = array_sum($enquiry);
    $new_quantity = isset($enquiry[$product_id]) ? $enquiry[$product_id] : 0;
    
    wp_send_json_success([
        'message' => 'Quantity decreased',
        'quantity' => $new_quantity,
        'count' => $total_count,
        'removed' => $new_quantity === 0
    ]);
}

// ✅ AJAX: Update Enquiry Quantity (set specific value)
add_action('wp_ajax_update_enquiry_quantity', 'bw_update_enquiry_quantity');
add_action('wp_ajax_nopriv_update_enquiry_quantity', 'bw_update_enquiry_quantity');
function bw_update_enquiry_quantity() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    
    $product_id = (int) ($_POST['product_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 0);
    
    if (!$product_id) {
        wp_send_json_error(['message' => 'Invalid product ID']);
    }
    
    $enquiry = bw_get_site_enquiry();
    
    if ($quantity <= 0) {
        unset($enquiry[$product_id]);
    } else {
        $enquiry[$product_id] = $quantity;
    }
    
    bw_update_site_enquiry($enquiry);
    
    $total_count = array_sum($enquiry);
    wp_send_json_success([
        'message' => 'Quantity updated',
        'quantity' => $quantity,
        'count' => $total_count,
        'removed' => $quantity === 0
    ]);
}

// ✅ AJAX: Remove from Enquiry
add_action('wp_ajax_remove_from_enquiry', 'bw_remove_from_enquiry');
add_action('wp_ajax_nopriv_remove_from_enquiry', 'bw_remove_from_enquiry');
function bw_remove_from_enquiry() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    
    $product_id = (int) ($_POST['product_id'] ?? 0);
    
    if (!$product_id) {
        wp_send_json_error(['message' => 'Invalid product ID']);
    }
    
    $enquiry = bw_get_site_enquiry();
    unset($enquiry[$product_id]);
    bw_update_site_enquiry($enquiry);
    
    $total_count = array_sum($enquiry);
    wp_send_json_success([
        'message' => 'Removed from enquiry',
        'count' => $total_count
    ]);
}

// ✅ AJAX: Get Enquiry Data
add_action('wp_ajax_get_enquiry_data', 'bw_get_enquiry_data');
add_action('wp_ajax_nopriv_get_enquiry_data', 'bw_get_enquiry_data');
function bw_get_enquiry_data() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    
    $enquiry = bw_get_site_enquiry();
    $total_count = array_sum($enquiry);
    
    wp_send_json_success([
        'enquiry' => $enquiry,
        'count' => $total_count
    ]);
}

// ✅ AJAX: Clear Enquiry
add_action('wp_ajax_clear_enquiry', 'bw_clear_enquiry');
add_action('wp_ajax_nopriv_clear_enquiry', 'bw_clear_enquiry');
function bw_clear_enquiry() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    
    bw_update_site_enquiry([]);
    
    wp_send_json_success([
        'message' => 'Enquiry cleared',
        'count' => 0
    ]);
}

// ✅ Bricks Query Loop: enquiry_items
add_filter('bricks/posts/query/enquiry_items', function($query) {
    $enquiry = bw_get_site_enquiry();
    
    $product_ids = !empty($enquiry) ? array_keys($enquiry) : [0];
    
    $query['post_type'] = 'product';
    $query['post__in'] = $product_ids;
    $query['orderby'] = 'post__in';
    
    return $query;
});

// ✅ Bricks Dynamic Data: Enquiry Quantity (for current product in loop)
add_filter('bricks/dynamic_tags_list', function($tags) {
    $tags[] = [
        'name'  => '{enquiry_quantity}',
        'label' => 'Enquiry Quantity',
        'group' => 'Enquiry',
    ];
    $tags[] = [
        'name'  => '{enquiry_count}',
        'label' => 'Enquiry Count',
        'group' => 'Enquiry',
    ];
    $tags[] = [
        'name'  => '{enquiry_total_quantity}',
        'label' => 'Enquiry Total Quantity',
        'group' => 'Enquiry',
    ];
    return $tags;
});

add_filter('bricks/dynamic_data/render_content', function($content, $post, $context = 'text') {
    // {enquiry_quantity} - shows quantity for current product
    if (strpos($content, '{enquiry_quantity}') !== false) {
        $enquiry = bw_get_site_enquiry();
        $product_id = get_the_ID();
        $quantity = isset($enquiry[$product_id]) ? $enquiry[$product_id] : 0;
        $content = str_replace('{enquiry_quantity}', $quantity, $content);
    }
    
    // {enquiry_count} - total unique items
    if (strpos($content, '{enquiry_count}') !== false) {
        $enquiry = bw_get_site_enquiry();
        $count = count($enquiry);
        $content = str_replace('{enquiry_count}', $count, $content);
    }
    
    // {enquiry_total_quantity} - sum of all quantities
    if (strpos($content, '{enquiry_total_quantity}') !== false) {
        $enquiry = bw_get_site_enquiry();
        $total = array_sum($enquiry);
        $content = str_replace('{enquiry_total_quantity}', $total, $content);
    }
    
    return $content;
}, 20, 3);

// ✅ Shortcode: Enquiry Count
add_shortcode('enquiry_count', function($atts) {
    $enquiry = bw_get_site_enquiry();
    $count = array_sum($enquiry);
    
    return '<span class="enquiry-count" id="enquiry-count">' . $count . '</span>';
});

// ✅ Shortcode: Add to Enquiry Button
add_shortcode('add_to_enquiry_button', function($atts) {
    $a = shortcode_atts([
        'product_id' => get_the_ID(),
        'text' => 'Add to Enquiry',
        'class' => 'add-to-enquiry-btn',
    ], $atts);
    
    $product_id = (int) $a['product_id'];
    $text = esc_html($a['text']);
    $class = esc_attr($a['class']);
    
    return "<button class='{$class}' data-product-id='{$product_id}' onclick='addToEnquiry({$product_id})'>{$text}</button>";
});

// ✅ Shortcode: Clear Enquiry Button
add_shortcode('clear_enquiry_button', function($atts) {
    $a = shortcode_atts([
        'text' => 'Clear Enquiry',
        'class' => 'clear-enquiry-btn',
    ], $atts);
    
    $text = esc_html($a['text']);
    $class = esc_attr($a['class']);
    
    return "<button class='{$class}' onclick='clearEnquiry()'>{$text}</button>";
});