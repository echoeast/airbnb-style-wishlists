# Quick Reference Guide - Component Setup

This is a quick cheat sheet for building components in Bricks Builder.

---

## Product Page Components

### Save to Wishlist Button
```html
<button class="open-wishlist-popup" data-product-id="{post_id}">
    Save
</button>
```

### Add to Enquiry Section
```html
<!-- Quantity Input -->
<input 
    type="number" 
    id="enquiry-quantity-{post_id}"
    class="enquiry-quantity-input"
    value="1" 
    min="1"
/>

<!-- Add Button -->
<button 
    class="add-to-enquiry-btn"
    onclick="addToEnquiry({post_id})"
>
    Add to Enquiry
</button>
```

---

## Wishlist Popup (Bricks Popup Element)

### Create New Wishlist Form
```html
<input id="new-wishlist-name" type="text" placeholder="Wishlist name" />
<button onclick="createWishlist()">Create</button>
```

### Existing Wishlists (Query Loop)
**Query Type:** Post → Wishlist

**Inside Loop:**
```html
<!-- Wishlist Name -->
{post_title}

<!-- Add Button -->
<button class="wishlist-add-btn" onclick="addToWishlist({post_id})">
    ＋
</button>

<!-- Product Count -->
{wishlist_item_count} products
```

---

## Wishlist Single Page

### Page Title (Editable)
Use shortcode:
```
[wishlist_title_editor]
```

### Product Count
```
[wishlist_item_count]
```

### Send to Enquiry Button
```html
<button 
    class="send-wishlist-to-enquiry-btn"
    onclick="sendWishlistToEnquiry({wishlist_id})"
>
    Send to Enquiry
</button>
```

### Delete Wishlist Button
```html
<button onclick="deleteWishlist({wishlist_id})">
    Delete Wishlist
</button>
```

### Products Query Loop
**Query ID:** `wishlist_products`

**Inside Loop:**
```html
<!-- Product Image -->
{thumbnail_image}

<!-- Product Name -->
{post_title}

<!-- Remove Button -->
<button 
    class="wishlist-remove-btn"
    onclick="removeFromWishlist({wishlist_id}, {post_id})"
>
    ×
</button>
```

---

## Enquiry Page

### Products Query Loop
**Query ID:** `enquiry_items`

**Inside Loop:**
```html
<!-- Product Image -->
{thumbnail_image}

<!-- Product Name -->
{post_title}

<!-- Quantity Controls -->
<button class="enquiry-decrease-qty" onclick="decreaseEnquiryQty({post_id})">
    ⊖
</button>

<span id="enquiry-qty-{post_id}">
    {enquiry_quantity}
</span>

<button class="enquiry-increase-qty" onclick="increaseEnquiryQty({post_id})">
    ⊕
</button>

<!-- Remove Button -->
<button 
    class="enquiry-remove-btn"
    onclick="removeFromEnquiry({post_id})"
>
    Remove
</button>
```

### Clear Enquiry Button
```html
<button class="clear-enquiry-btn" onclick="clearEnquiry()">
    Clear Enquiry
</button>
```

---

## Header/Navbar

### Enquiry Count Badge
```html
<span id="enquiry-count" class="enquiry-count-badge">
    {enquiry_count}
</span>
```

Or use shortcode:
```
[enquiry_count]
```

---

## Popup Notifications

Create Bricks Popup elements with these exact classes:

### Wishlist Popups
- `.wishlist-created__popup`
- `.added-to-wishlist__popup`
- `.removed-from-wishlist__popup`
- `.renamed-wishlist__popup`
- `.wishlist-deleted__popup`

### Enquiry Popups
- `.added-to-enquiry__popup`
- `.wishlist-added-to-enquiry__popup`
- `.removed-from-enquiry__popup`
- `.enquiry-cleared__popup`

**Required CSS for popups:**
```css
.your-popup-class {
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.your-popup-class.show {
    opacity: 1;
}
```

---

## Important Notes

1. **{post_id}** - Use Bricks dynamic data or replace with actual post ID
2. **{wishlist_id}** - Use `{wishlist_id}` dynamic tag on wishlist pages
3. **All onclick functions** are globally available (no need to import)
4. **Auto-save** - All enquiry changes save automatically via AJAX
5. **Count badges** update automatically after any action

---

## Dynamic Data Tags Available

### Wishlist
- `{wishlist_id}` - Current wishlist ID
- `{wishlist_item_count}` - Product count in wishlist

### Enquiry
- `{enquiry_quantity}` - Quantity for current product
- `{enquiry_count}` - Total unique products
- `{enquiry_total_quantity}` - Sum of all quantities

---

## Query IDs for Bricks Loops

- `wishlist_products` - Products in current wishlist
- `enquiry_items` - Products in enquiry

Set these as the **Query ID** in your Bricks Query Loop settings.

