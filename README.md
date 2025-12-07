# Airbnb Style Wishlists & Enquiry System

**Version:** 2.0  
**Author:** Joseph Lewis  
**WordPress Plugin for Bricks Builder**

---

## Overview

This plugin provides two main features:
1. **Wishlists** - Users can create multiple named wishlists and save products to them (similar to Airbnb)
2. **Enquiry System** - Site-wide shopping cart where users can add products with quantities for enquiry

### Auto-Updates via GitHub

This plugin uses [UUPD (Universal Updater Drop-In)](https://github.com/stingray82/uupd) for automatic updates from GitHub. Once set up, you'll receive update notifications in your WordPress admin just like WordPress.org plugins.

**Setup Instructions:** See `UUPD-SETUP.md` for complete GitHub integration guide.

---

## Table of Contents

- [Installation](#installation)
- [Wishlist System](#wishlist-system)
- [Enquiry System](#enquiry-system)
- [Bricks Builder Components](#bricks-builder-components)
- [Shortcodes](#shortcodes)
- [Dynamic Data Tags](#dynamic-data-tags)
- [JavaScript Functions](#javascript-functions)
- [Popup Notifications](#popup-notifications)
- [Custom Queries](#custom-queries)

---

## Installation

1. Upload plugin folder to `/wp-content/plugins/`
2. Activate plugin in WordPress admin
3. Create your Bricks Builder pages/templates
4. Add components using guides below

---

## Wishlist System

### Features
- Create multiple wishlists with custom names
- Add/remove products from wishlists
- Rename wishlists inline
- Delete wishlists
- View product count per wishlist
- Automatic thumbnail (uses first product's `thumbnail_image` ACF field)

### Database Storage
- Custom Post Type: `wishlist`
- Products stored in post meta: `_wishlist_items` (array of product IDs)
- Slug: Uses post ID (e.g., `/wishlist/123/`)

---

## Enquiry System

### Features
- Site-wide enquiry (one enquiry for entire site)
- Add products with quantities
- Increase/decrease quantities
- Remove products
- Send entire wishlist to enquiry
- Clear enquiry
- Auto-save all changes

### Database Storage
- WordPress option: `bw_site_enquiry`
- Structure: `['product_id' => quantity, ...]`
- Example: `['123' => 2, '456' => 5]`

---

## Bricks Builder Components

### 1. Product Pages - Add to Wishlist

**Save Button (opens wishlist popup):**
```html
<button 
    class="open-wishlist-popup save-product-icon"
    data-product-id="{post_id}"
>
    Save
</button>
```

### 2. Product Pages - Add to Enquiry

**Quantity Input:**
```html
<input 
    type="number" 
    id="enquiry-quantity-{post_id}"
    class="enquiry-quantity-input"
    value="1" 
    min="1"
    data-product-id="{post_id}"
/>
```

**Add to Enquiry Button:**
```html
<button 
    class="add-to-enquiry-btn"
    data-product-id="{post_id}"
    onclick="addToEnquiry({post_id})"
>
    Add to Enquiry
</button>
```

### 3. Wishlist Popup - Create New Wishlist

**Input Field:**
```html
<input 
    id="new-wishlist-name" 
    type="text" 
    placeholder="Wishlist name" 
    class="wishlist-input"
/>
```

**Create Button:**
```html
<button 
    onclick="createWishlist()" 
    class="create-wishlist-button"
>
    Create new wishlist
</button>
```

### 4. Wishlist Popup - Add to Existing Wishlist

**Query Loop:** Set query type to `Post`
- Post type: `wishlist`
- Order: Date DESC

**Inside Loop - Add Button:**
```html
<button 
    class="wishlist-add-btn" 
    data-wishlist="{post_id}" 
    onclick="addToWishlist({post_id})"
>
    ＋
</button>
```

### 5. Wishlist Single Page - Products in Wishlist

**Query Loop:** Set query ID to `wishlist_products`
- This automatically shows products in the current wishlist

**Inside Loop - Remove Button:**
```html
<button 
    class="wishlist-remove-btn" 
    data-wishlist="{wishlist_id}" 
    data-product="{post_id}" 
    onclick="removeFromWishlist({wishlist_id}, {post_id})"
>
    ×
</button>
```

### 6. Wishlist Single Page - Editable Title

Use dynamic data `{post_title}` in a Basic Text element, OR use the shortcode:

```
[wishlist_title_editor]
```

This creates an inline-editable title with pencil/save icons.

### 7. Wishlist Single Page - Send to Enquiry

**Button:**
```html
<button 
    class="send-wishlist-to-enquiry-btn"
    data-wishlist-id="{wishlist_id}"
    onclick="sendWishlistToEnquiry({wishlist_id})"
>
    Send to Enquiry
</button>
```

### 8. Enquiry Page - Products in Enquiry

**Query Loop:** Set query ID to `enquiry_items`
- This automatically shows products in the enquiry

**Inside Loop - Quantity Controls:**

**Decrease Button:**
```html
<button 
    class="enquiry-decrease-qty"
    data-product-id="{post_id}"
    onclick="decreaseEnquiryQty({post_id})"
>
    ⊖
</button>
```

**Quantity Display:**
```html
<span 
    class="enquiry-quantity-display"
    data-product-id="{post_id}"
    id="enquiry-qty-{post_id}"
>
    {enquiry_quantity}
</span>
```

**Increase Button:**
```html
<button 
    class="enquiry-increase-qty"
    data-product-id="{post_id}"
    onclick="increaseEnquiryQty({post_id})"
>
    ⊕
</button>
```

**Remove Button:**
```html
<button 
    class="enquiry-remove-btn"
    data-product-id="{post_id}"
    onclick="removeFromEnquiry({post_id})"
>
    Remove
</button>
```

### 9. Header/Navbar - Enquiry Count Badge

**Display Count:**
```html
<span 
    class="enquiry-count-badge"
    id="enquiry-count"
>
    {enquiry_count}
</span>
```

Or use shortcode: `[enquiry_count]`

---

## Shortcodes

### Wishlist Shortcodes

**Create Wishlist Form:**
```
[create_wishlist_form]
```

**Add to Wishlist Button:**
```
[add_to_wishlist_button wishlist_id="123" product_id="456"]
```

**Remove from Wishlist Button:**
```
[remove_from_wishlist_button wishlist_id="123" product_id="456"]
```

**Delete Wishlist Button:**
```
[delete_wishlist_button wishlist_id="123" text="Delete" class="my-class"]
```

**Wishlist Title Editor:**
```
[wishlist_title_editor]
```

**Wishlist Item Count:**
```
[wishlist_item_count wishlist_id="123"]
```

### Enquiry Shortcodes

**Enquiry Count:**
```
[enquiry_count]
```

**Add to Enquiry Button:**
```
[add_to_enquiry_button product_id="123" text="Add to Enquiry" class="my-class"]
```

**Clear Enquiry Button:**
```
[clear_enquiry_button text="Clear All" class="my-class"]
```

---

## Dynamic Data Tags

### Wishlist Tags

**`{wishlist_id}`**  
- Returns: Current wishlist post ID
- Use: In Bricks elements on wishlist single pages

**`{wishlist_item_count}`**  
- Returns: Number of products in wishlist
- Use: Display count on wishlist cards

### Enquiry Tags

**`{enquiry_quantity}`**  
- Returns: Quantity for current product in enquiry loop
- Use: Inside `enquiry_items` query loop

**`{enquiry_count}`**  
- Returns: Total unique products in enquiry
- Use: Badges, headers

**`{enquiry_total_quantity}`**  
- Returns: Sum of all product quantities
- Use: "You have 15 items in your enquiry"

---

## JavaScript Functions

### Wishlist Functions

**`createWishlist()`**  
Creates new wishlist and adds current product (if `window.selectedProductId` is set)

**`addToWishlist(wishlistId)`**  
Adds current product to specified wishlist

**`removeFromWishlist(wishlistId, productId)`**  
Removes product from wishlist

**`deleteWishlist(wishlistId)`**  
Deletes entire wishlist and redirects to `/wishlists/`

**`toggleWishlistTitleEdit(element)`**  
Switches to edit mode for wishlist title

**`saveWishlistTitle(element)`**  
Saves renamed wishlist title

**`updateWishlistCount(wishlistId)`**  
Updates product count display for wishlist

### Enquiry Functions

**`addToEnquiry(productId)`**  
Adds product to enquiry (reads quantity from input field `enquiry-quantity-{productId}`)

**`sendWishlistToEnquiry(wishlistId)`**  
Adds all products from wishlist to enquiry (quantity 1 each)

**`increaseEnquiryQty(productId)`**  
Increases quantity by 1

**`decreaseEnquiryQty(productId)`**  
Decreases quantity by 1 (removes if reaches 0)

**`removeFromEnquiry(productId)`**  
Removes product from enquiry

**`clearEnquiry()`**  
Clears entire enquiry

**`updateEnquiryCount()`**  
Updates enquiry count badge in header

---

## Popup Notifications

Create Bricks Builder popups with these classes for auto-show notifications:

### Wishlist Popups

- `.wishlist-created__popup` - Shown when wishlist created
- `.added-to-wishlist__popup` - Shown when product added to wishlist
- `.removed-from-wishlist__popup` - Shown when product removed from wishlist
- `.renamed-wishlist__popup` - Shown when wishlist renamed
- `.wishlist-deleted__popup` - Shown when wishlist deleted

### Enquiry Popups

- `.added-to-enquiry__popup` - Shown when product added to enquiry
- `.wishlist-added-to-enquiry__popup` - Shown when wishlist sent to enquiry
- `.removed-from-enquiry__popup` - Shown when product removed from enquiry
- `.enquiry-cleared__popup` - Shown when enquiry cleared

**Popup Behavior:**
- Auto-show for 3 seconds
- Fade in/out with `.show` class
- Requires `display: none` default style

---

## Custom Queries

### Bricks Query Loops

**`wishlist_products`**  
- Use in: Wishlist single page
- Returns: Products in current wishlist
- Auto-orders by insertion order

**`enquiry_items`**  
- Use in: Enquiry page
- Returns: Products in enquiry
- Access quantity via `{enquiry_quantity}` tag

---

## CSS Classes Reference

### Wishlist Classes

- `.wishlist-create` - Wishlist creation form container
- `.wishlist-input` - Name input field
- `.create-wishlist-button` - Create button
- `.wishlist-add-btn` - Add to wishlist button (+ icon)
- `.wishlist-remove-btn` - Remove from wishlist button (× icon)
- `.wishlist-title-editor` - Editable title container
- `.wishlist-title` - Title display span
- `.wishlist-title-input` - Title edit input
- `.edit-icon` - Pencil icon
- `.save-icon` - Save icon
- `.wishlist-count` - Product count display
- `.wishlist-delete-btn` - Delete wishlist button
- `.open-wishlist-popup` - Button to open wishlist selection popup
- `.save-product-icon` - Alternative save button class

### Enquiry Classes

- `.enquiry-quantity-input` - Quantity input on product pages
- `.add-to-enquiry-btn` - Add to enquiry button
- `.send-wishlist-to-enquiry-btn` - Send wishlist to enquiry button
- `.enquiry-decrease-qty` - Decrease quantity button (⊖)
- `.enquiry-quantity-display` - Quantity number display
- `.enquiry-increase-qty` - Increase quantity button (⊕)
- `.enquiry-remove-btn` - Remove from enquiry button
- `.enquiry-count` - Enquiry count badge
- `.enquiry-count-badge` - Alternative count badge class
- `.clear-enquiry-btn` - Clear enquiry button

---

## AJAX Actions

### Wishlist Actions

- `create_wishlist` - Create new wishlist
- `add_to_wishlist` - Add product to wishlist
- `remove_from_wishlist` - Remove product from wishlist
- `rename_wishlist` - Rename wishlist
- `delete_wishlist` - Delete wishlist
- `get_wishlist_count` - Get product count

### Enquiry Actions

- `add_to_enquiry` - Add product with quantity
- `add_wishlist_to_enquiry` - Add all wishlist products
- `increase_enquiry_quantity` - Increment quantity
- `decrease_enquiry_quantity` - Decrement quantity
- `update_enquiry_quantity` - Set specific quantity
- `remove_from_enquiry` - Remove product
- `get_enquiry_data` - Get full enquiry data
- `clear_enquiry` - Clear all items

All AJAX actions require `wishlist_nonce` for security.

---

## Notes

- **Product Post Type:** Assumes `product` custom post type exists
- **ACF Field:** Uses `thumbnail_image` field for wishlist thumbnails
- **Security:** All AJAX calls use WordPress nonce verification
- **Guest Users:** Both systems work without login (site-wide enquiry, public wishlists)
- **Scroll Preservation:** Wishlist actions preserve scroll position on page reload

---

## Support

For issues or questions, contact Joseph Lewis.

**Last Updated:** December 2025

