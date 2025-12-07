# Enquiry System - Implementation Summary

## What Was Added

### PHP Functions (airbnb-style-wishlists.php)

**Helper Functions:**
- `bw_get_site_enquiry()` - Gets enquiry from database
- `bw_update_site_enquiry($enquiry)` - Saves enquiry to database

**AJAX Endpoints (8 new endpoints):**
1. `add_to_enquiry` - Add product with quantity
2. `add_wishlist_to_enquiry` - Bulk add from wishlist
3. `increase_enquiry_quantity` - Increment by 1
4. `decrease_enquiry_quantity` - Decrement by 1
5. `update_enquiry_quantity` - Set specific amount
6. `remove_from_enquiry` - Remove product
7. `get_enquiry_data` - Fetch current enquiry
8. `clear_enquiry` - Clear all items

**Bricks Integration:**
- Custom query: `enquiry_items` - For looping products in enquiry
- Dynamic tags:
  - `{enquiry_quantity}` - Current product quantity
  - `{enquiry_count}` - Total unique products
  - `{enquiry_total_quantity}` - Sum of all quantities

**Shortcodes:**
- `[enquiry_count]` - Display count badge
- `[add_to_enquiry_button]` - Add button with attributes
- `[clear_enquiry_button]` - Clear all button

### JavaScript Functions (wishlist.js)

**Core Functions:**
- `updateEnquiryCount()` - Updates count badge
- `addToEnquiry(productId)` - Add with quantity from input
- `sendWishlistToEnquiry(wishlistId)` - Bulk add
- `increaseEnquiryQty(productId)` - +1
- `decreaseEnquiryQty(productId)` - -1
- `removeFromEnquiry(productId)` - Remove item
- `clearEnquiry()` - Clear all

**Popup Functions:**
- `showAddedToEnquiryPopup()` - Success notification
- `showWishlistAddedToEnquiryPopup()` - Wishlist added
- `showRemovedFromEnquiryPopup()` - Item removed
- `showEnquiryClearedPopup()` - Enquiry cleared

**Auto-Update:**
- Enquiry count badge updates on page load
- Real-time updates after all actions

### CSS Styles (wishlist.css)

**New Classes:**
- `.enquiry-quantity-input` - Number input styling
- `.add-to-enquiry-btn` - Add button
- `.enquiry-decrease-qty` - Decrease button (⊖)
- `.enquiry-increase-qty` - Increase button (⊕)
- `.enquiry-quantity-display` - Quantity number
- `.enquiry-remove-btn` - Remove button
- `.enquiry-count-badge` - Count badge
- `.send-wishlist-to-enquiry-btn` - Wishlist transfer button
- `.clear-enquiry-btn` - Clear all button

### Documentation

**README.md** - Comprehensive documentation including:
- Full feature list
- All shortcodes with parameters
- JavaScript function reference
- CSS class reference
- AJAX action list
- Bricks Builder component guides
- Dynamic data tag reference

**QUICK-REFERENCE.md** - Quick copy-paste guide for:
- Product page components
- Wishlist popup components
- Wishlist page components
- Enquiry page components
- Header/navbar components
- Popup notification setup

---

## How It Works

### Data Storage
- **Location:** WordPress options table
- **Key:** `bw_site_enquiry`
- **Format:** `['product_id' => quantity, ...]`
- **Example:** `['123' => 2, '456' => 5, '789' => 1]`

### User Flow

**Adding Products:**
1. User enters quantity on product page
2. Clicks "Add to Enquiry"
3. AJAX saves to database
4. Popup notification shows
5. Count badge updates

**Managing Enquiry:**
1. User visits Enquiry page
2. Bricks query loop loads all products
3. Each product shows quantity controls
4. Click +/- to adjust quantities
5. Changes save instantly via AJAX
6. Page updates without reload

**Sending Wishlist:**
1. User on wishlist page
2. Clicks "Send to Enquiry"
3. All products added with quantity 1
4. Count badge updates
5. Success notification shows

---

## Key Features

✅ **Auto-Save** - All changes persist immediately  
✅ **Site-Wide** - One enquiry for entire site  
✅ **Real-Time Updates** - Count badges update instantly  
✅ **Guest Friendly** - Works without login  
✅ **Quantity Controls** - Increase/decrease with buttons  
✅ **Bulk Actions** - Send entire wishlists  
✅ **Clean UI** - Styled buttons and controls  
✅ **Popup Feedback** - Visual confirmation for all actions  
✅ **Bricks Integrated** - Custom queries and dynamic data  
✅ **Secure** - Nonce verification on all AJAX calls

---

## Next Steps (Future Implementation)

- [ ] Email enquiry functionality
- [ ] Export enquiry as PDF/CSV
- [ ] Enquiry form submission
- [ ] Minimum order quantities
- [ ] Product availability checks
- [ ] Price calculations (if needed)

---

## Testing Checklist

- [ ] Add product to enquiry from product page
- [ ] Send wishlist to enquiry
- [ ] Increase quantity on enquiry page
- [ ] Decrease quantity on enquiry page
- [ ] Remove product from enquiry
- [ ] Clear entire enquiry
- [ ] Verify count badge updates
- [ ] Check all popup notifications
- [ ] Test with empty enquiry
- [ ] Test with multiple products

---

## Plugin Version History

**v2.0** - Added enquiry system + UUPD GitHub auto-updates  
**v1.2** - Wishlist functionality  
**v1.0** - Initial release

---

## Auto-Updates via GitHub

This plugin now includes **UUPD (Universal Updater Drop-In)** for automatic updates from GitHub.

**Setup Guide:** See `GITHUB-SETUP-CHECKLIST.md` for complete step-by-step instructions.

**How it works:**
1. Plugin checks GitHub for updates
2. Compares version numbers
3. Shows update notification in WordPress admin
4. Downloads and installs from GitHub releases

**Documentation:** [UUPD on GitHub](https://github.com/stingray82/uupd)

---

**Implementation Date:** December 2025  
**Status:** ✅ Complete and Ready to Use

