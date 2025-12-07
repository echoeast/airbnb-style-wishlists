// Create wishlist function

window.addEventListener('load', () => {
    if (sessionStorage.getItem('showWishlistCreatedPopup') === '1') {
        showWishlistCreatedPopup();
        sessionStorage.removeItem('showWishlistCreatedPopup');
    }
});

function createWishlist() {
    const name = document.getElementById('new-wishlist-name').value.trim();
    if (!name) {
        alert('Please enter a wishlist name.');
        return;
    }

    const productId = window.selectedProductId;

    fetch(wishlist_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'create_wishlist',
            nonce: wishlist_ajax.nonce,
            name: name,
            product_id: productId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            sessionStorage.setItem('showWishlistCreatedPopup', '1');
            reloadAndPreserveScroll();
        } else {
            alert(data.data?.message || 'Error creating wishlist.');
        }
    });
}
				  
window.showWishlistCreatedPopup = function() {
    const popup = document.querySelector('.wishlist-created__popup');
    if (popup) {
        popup.style.display = 'block';
        popup.classList.add('show');

        console.log('Wishlist created popup shown');

        setTimeout(() => {
            popup.classList.remove('show');
            popup.style.display = 'none';
        }, 3000);
    } else {
        console.warn('No .wishlist-created__popup element found');
    }
}
				  
// Store scroll position before reload
function reloadAndPreserveScroll() {
    sessionStorage.setItem('scrollPos', window.scrollY);
    location.reload();
}

// Restore scroll position on page load
window.addEventListener('load', () => {
    const scrollPos = sessionStorage.getItem('scrollPos');
    if (scrollPos !== null) {
        window.scrollTo(0, parseInt(scrollPos, 10));
        sessionStorage.removeItem('scrollPos'); // Clean up
    }
});

// Update wishlist count function
function updateWishlistCount(wishlistId) {
    fetch(wishlist_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'get_wishlist_count',
            nonce: wishlist_ajax.nonce,
            wishlist_id: wishlistId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update all elements with wishlist count for this wishlist
            const countElements = document.querySelectorAll(`[data-wishlist-count="${wishlistId}"], .wishlist-count[data-wishlist="${wishlistId}"]`);
            countElements.forEach(element => {
                element.textContent = data.data.countText;
            });
        }
    })
    .catch(error => {
        console.error('Error updating wishlist count:', error);
    });
}
				  
// Added to wishlist popup function
function addToWishlist(wishlistId) {
    const productId = window.selectedProductId;
    console.log('Trying to add product', productId, 'to wishlist', wishlistId);

    if (!productId || productId === '0') {
        alert('No product selected');
        return;
    }

    const btn = document.querySelector(`.wishlist-add-btn[data-wishlist="${wishlistId}"]`);

    fetch(wishlist_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'add_to_wishlist',
            nonce: wishlist_ajax.nonce,
            wishlist_id: wishlistId,
            product_id: productId
        })
    }).then(r => r.json()).then(data => {
        if (btn) {
            btn.textContent = data.success ? '✔' : '✖';
        }

        if (data.success) {
            showWishlistPopup();
            // Update the count after successful add
            updateWishlistCount(wishlistId);
        } else {
            console.error('Error from server:', data);
        }
    });
}

window.showWishlistPopup = function() {
    const popup = document.querySelector('.added-to-wishlist__popup');
    if (popup) {
        popup.style.display = 'block';
        popup.classList.add('show');

        console.log('Added to wishlist popup shown');

        setTimeout(() => {
            popup.classList.remove('show');
            popup.style.display = 'none';
        }, 3000);
    } else {
        console.warn('No .added-to-wishlist__popup element found');
    }
}



// Remove from wishlist function
function removeFromWishlist(wid, pid) {
    fetch(wishlist_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'remove_from_wishlist',
            nonce: wishlist_ajax.nonce,
            wishlist_id: wid,
            product_id: pid
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update the count after successful remove
            updateWishlistCount(wid);
            // Optionally reload or remove the product element from DOM
            location.reload();
			// Show removed from wishlist popup
			showRemovedFromWishlistPopup();
        } else {
            alert(data.data?.message || 'Error removing from wishlist.');
        }
    });
}
				  
window.showRemovedFromWishlistPopup = function() {
    const popup = document.querySelector('.removed-from-wishlist__popup');
    if (popup) {
        popup.style.display = 'block';
        popup.classList.add('show');

        console.log('Removed from wishlist popup shown');

        setTimeout(() => {
            popup.classList.remove('show');
            popup.style.display = 'none';
        }, 3000);
    } else {
        console.warn('No .removed-from-wishlist__popup element found');
    }
}

// Delete wishlist function
function deleteWishlist(wishlistId) {
    fetch(wishlist_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'delete_wishlist',
            nonce: wishlist_ajax.nonce,
            wishlist_id: wishlistId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Set flag to show popup after redirect
            sessionStorage.setItem('showWishlistDeletedPopup', '1');
            window.location.href = '/wishlists/'; // Adjust URL as needed
        } else {
            alert(data.data?.message || 'Error deleting wishlist.');
        }
    })
    .catch(error => {
        console.error('Error deleting wishlist:', error);
        alert('Error deleting wishlist.');
    });
}
				  
window.showWishlistDeletedPopup = function() {
    const popup = document.querySelector('.wishlist-deleted__popup');
    if (popup) {
        popup.style.display = 'block';
        popup.classList.add('show');

        console.log('Wishlist deleted popup shown');

        setTimeout(() => {
            popup.classList.remove('show');
            popup.style.display = 'none';
        }, 3000);
    } else {
        console.warn('No .wishlist-deleted__popup element found');
    }
}

// Show deleted popup on Wishlists page if flag is set

document.addEventListener('DOMContentLoaded', function() {
    if (sessionStorage.getItem('showWishlistDeletedPopup') === '1') {
        showWishlistDeletedPopup();
        sessionStorage.removeItem('showWishlistDeletedPopup');
    }
});

function toggleWishlistTitleEdit(el) {
    const box = el.closest('.wishlist-title-editor');
    if (!box) return;

    const titleElement = box.querySelector('.wishlist-title');
    const inputElement = box.querySelector('.wishlist-title-input');
    const editIcon = box.querySelector('.edit-icon');
    const saveIcon = box.querySelector('.save-icon');

    if (titleElement) titleElement.style.display = 'none';
    if (inputElement) inputElement.style.display = 'inline-block';
    if (editIcon) editIcon.style.display = 'none';
    if (saveIcon) saveIcon.style.display = 'inline-block';
}

function saveWishlistTitle(el) {
    // Find the container from the clicked element
    const box = el.closest('.wishlist-title-editor');
    if (!box) {
        console.error('No .wishlist-title-editor found');
        return;
    }

    const id = box.dataset.id;
    const inputElement = box.querySelector('.wishlist-title-input');
    if (!inputElement) {
        console.error('No input element found');
        return;
    }
    
    const val = inputElement.value.trim();

    if (!val) {
        alert('Please enter a valid name.');
        return;
    }

    fetch(wishlist_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'rename_wishlist',
            nonce: wishlist_ajax.nonce,
            wishlist_id: id,
            new_name: val
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
			//Show Renamed wishlist popup
			showRenamedWishlistPopup();
            const newTitle = data.data.name;
            
            // Update the editor elements
            const titleElement = box.querySelector('.wishlist-title');
            const editIcon = box.querySelector('.edit-icon');
            const saveIcon = box.querySelector('.save-icon');

            if (titleElement) {
                titleElement.textContent = newTitle;
                titleElement.style.display = 'inline-block';
            }
            
            // Check if this is a Bricks Builder setup (input should stay visible)
            const isBricksBuilder = box.hasAttribute('data-keep-input') || box.classList.contains('bricks-editor');
            
            if (!isBricksBuilder) {
                // Shortcode behavior: hide input, show edit icon
                if (inputElement) inputElement.style.display = 'none';
                if (editIcon) editIcon.style.display = 'inline-block';
                if (saveIcon) saveIcon.style.display = 'none';
            } else {
                // Bricks Builder behavior: keep input visible, update its value
                if (inputElement) {
                    inputElement.value = newTitle;
                    inputElement.defaultValue = newTitle;
                }
            }
            
            // Update any other elements that display the wishlist title
            // This will update Bricks Builder text elements with {post_title}
            console.log('Looking for elements to update with new title:', newTitle);
            
            // Try multiple selectors to find title elements
            const selectors = ['.wishlist-name', '.wishlist-settings-bar-name'];
            let updatedElements = 0;
            
            selectors.forEach(selector => {
                const elements = document.querySelectorAll(selector);
                console.log(`Found ${elements.length} elements with selector: ${selector}`);
                
                elements.forEach(element => {
                    if (!element) {
                        console.log('Skipping null element');
                        return;
                    }
                    
                    const currentText = element.textContent.trim();
                    console.log(`Element:`, element, `Current text: "${currentText}"`);
                    
                    if (currentText !== '' && currentText !== newTitle) {
                        console.log(`Updating element text from "${currentText}" to "${newTitle}"`);
                        element.textContent = newTitle;
                        updatedElements++;
                    }
                });
            });
            
            console.log(`Updated ${updatedElements} elements with new title`);
            
            // Update page title if it contains the wishlist name
            const oldTitle = inputElement.defaultValue || inputElement.value;
            if (document.title.includes(oldTitle)) {
                document.title = document.title.replace(oldTitle, newTitle);
            }
            
        } else {
            alert(data.data?.message || 'Error saving wishlist name.');
        }
    });
}

window.showRenamedWishlistPopup = function() {
    const popup = document.querySelector('.renamed-wishlist__popup');
    if (popup) {
        popup.style.display = 'block';
        popup.classList.add('show');

        console.log('Renamed wishlist popup shown');

        setTimeout(() => {
            popup.classList.remove('show');
            popup.style.display = 'none';
        }, 3000);
    } else {
        console.warn('No .renamed-wishlist__popup element found');
    }
}

// Listen for clicks on buttons that open the wishlist popup
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.open-wishlist-popup, .save-product-icon');
    console.log('Click detected, button:', btn);
    
    if (btn) {
        const pid = btn.getAttribute('data-product-id');
        console.log('Product ID from button:', pid);
        
        if (pid) {
            window.selectedProductId = pid;
            console.log('Selected product ID:', pid);

            // Wait a short moment to ensure popup content loads, then update buttons inside popup
            setTimeout(() => {
                document.querySelectorAll('.wishlist-add-btn').forEach(button => {
                    button.dataset.product = pid;
                    button.textContent = '＋'; // Reset icon to plus on popup open
                });
            }, 100);
        } else {
            console.error('No data-product-id found on button:', btn);
        }
    } else {
        console.log('Click was not on .open-wishlist-popup or .save-product-icon button');
    }
});
				  
// Listen for Bricks popup open event and update product IDs on wishlist buttons inside the popup
document.addEventListener('bricks:popup-open', function(event) {
    if (event.detail.popupSlug === 'wishlist-popup') { // Replace 'wishlist-popup' with your actual popup slug
        const pid = window.selectedProductId;
        if (!pid) return;

        // Update all add-to-wishlist buttons in the popup with the current product ID
        document.querySelectorAll('.wishlist-add-btn').forEach(button => {
            button.dataset.product = pid;
            button.textContent = '＋'; // reset icon in case it was changed before
        });
    }
});

// Added to wishlist confirmation
function showAddedToWishlistPopup() {
    const popup = document.querySelector('.added-to-wishlist__popup');
    if (!popup) return;

    popup.style.display = 'block';
    popup.style.opacity = '1';

    setTimeout(() => {
        popup.style.opacity = '0';
        setTimeout(() => {
            popup.style.display = 'none';
        }, 500); // Matches your fade-out duration
    }, 2000); // How long the popup stays visible
}

// ============================================================================
// ENQUIRY SYSTEM
// ============================================================================

// Update enquiry count badge
function updateEnquiryCount() {
    fetch(wishlist_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'get_enquiry_data',
            nonce: wishlist_ajax.nonce
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const countElement = document.getElementById('enquiry-count');
            if (countElement) {
                countElement.textContent = data.data.count;
            }
            // Update all elements with class enquiry-count
            document.querySelectorAll('.enquiry-count').forEach(el => {
                el.textContent = data.data.count;
            });
        }
    })
    .catch(error => console.error('Error updating enquiry count:', error));
}

// Add to enquiry
function addToEnquiry(productId) {
    // Get quantity from input field
    const quantityInput = document.getElementById(`enquiry-quantity-${productId}`);
    const quantity = quantityInput ? parseInt(quantityInput.value) : 1;

    if (quantity < 1) {
        alert('Please enter a valid quantity');
        return;
    }

    fetch(wishlist_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'add_to_enquiry',
            nonce: wishlist_ajax.nonce,
            product_id: productId,
            quantity: quantity
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAddedToEnquiryPopup();
            updateEnquiryCount();
            // Reset quantity input
            if (quantityInput) quantityInput.value = 1;
        } else {
            alert(data.data?.message || 'Error adding to enquiry');
        }
    })
    .catch(error => {
        console.error('Error adding to enquiry:', error);
        alert('Error adding to enquiry');
    });
}

// Send wishlist to enquiry
function sendWishlistToEnquiry(wishlistId) {
    if (!wishlistId) {
        alert('Invalid wishlist ID');
        return;
    }

    fetch(wishlist_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'add_wishlist_to_enquiry',
            nonce: wishlist_ajax.nonce,
            wishlist_id: wishlistId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showWishlistAddedToEnquiryPopup();
            updateEnquiryCount();
        } else {
            alert(data.data?.message || 'Error adding wishlist to enquiry');
        }
    })
    .catch(error => {
        console.error('Error adding wishlist to enquiry:', error);
        alert('Error adding wishlist to enquiry');
    });
}

// Increase enquiry quantity
function increaseEnquiryQty(productId) {
    fetch(wishlist_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'increase_enquiry_quantity',
            nonce: wishlist_ajax.nonce,
            product_id: productId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update quantity display
            const qtyElement = document.getElementById(`enquiry-qty-${productId}`);
            if (qtyElement) {
                qtyElement.textContent = data.data.quantity;
            }
            updateEnquiryCount();
        } else {
            alert(data.data?.message || 'Error increasing quantity');
        }
    })
    .catch(error => console.error('Error increasing quantity:', error));
}

// Decrease enquiry quantity
function decreaseEnquiryQty(productId) {
    fetch(wishlist_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'decrease_enquiry_quantity',
            nonce: wishlist_ajax.nonce,
            product_id: productId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.data.removed) {
                // Product was removed, reload page
                location.reload();
            } else {
                // Update quantity display
                const qtyElement = document.getElementById(`enquiry-qty-${productId}`);
                if (qtyElement) {
                    qtyElement.textContent = data.data.quantity;
                }
                updateEnquiryCount();
            }
        } else {
            alert(data.data?.message || 'Error decreasing quantity');
        }
    })
    .catch(error => console.error('Error decreasing quantity:', error));
}

// Remove from enquiry
function removeFromEnquiry(productId) {
    if (!confirm('Remove this product from enquiry?')) {
        return;
    }

    fetch(wishlist_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'remove_from_enquiry',
            nonce: wishlist_ajax.nonce,
            product_id: productId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showRemovedFromEnquiryPopup();
            updateEnquiryCount();
            // Reload to remove item from list
            setTimeout(() => location.reload(), 500);
        } else {
            alert(data.data?.message || 'Error removing from enquiry');
        }
    })
    .catch(error => {
        console.error('Error removing from enquiry:', error);
        alert('Error removing from enquiry');
    });
}

// Clear entire enquiry
function clearEnquiry() {
    if (!confirm('Clear entire enquiry?')) {
        return;
    }

    fetch(wishlist_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'clear_enquiry',
            nonce: wishlist_ajax.nonce
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showEnquiryClearedPopup();
            updateEnquiryCount();
            setTimeout(() => location.reload(), 500);
        } else {
            alert(data.data?.message || 'Error clearing enquiry');
        }
    })
    .catch(error => {
        console.error('Error clearing enquiry:', error);
        alert('Error clearing enquiry');
    });
}

// Popup functions
window.showAddedToEnquiryPopup = function() {
    const popup = document.querySelector('.added-to-enquiry__popup');
    if (popup) {
        popup.style.display = 'block';
        popup.classList.add('show');
        console.log('Added to enquiry popup shown');
        setTimeout(() => {
            popup.classList.remove('show');
            popup.style.display = 'none';
        }, 3000);
    } else {
        console.warn('No .added-to-enquiry__popup element found');
    }
}

window.showWishlistAddedToEnquiryPopup = function() {
    const popup = document.querySelector('.wishlist-added-to-enquiry__popup');
    if (popup) {
        popup.style.display = 'block';
        popup.classList.add('show');
        console.log('Wishlist added to enquiry popup shown');
        setTimeout(() => {
            popup.classList.remove('show');
            popup.style.display = 'none';
        }, 3000);
    } else {
        console.warn('No .wishlist-added-to-enquiry__popup element found');
    }
}

window.showRemovedFromEnquiryPopup = function() {
    const popup = document.querySelector('.removed-from-enquiry__popup');
    if (popup) {
        popup.style.display = 'block';
        popup.classList.add('show');
        console.log('Removed from enquiry popup shown');
        setTimeout(() => {
            popup.classList.remove('show');
            popup.style.display = 'none';
        }, 3000);
    } else {
        console.warn('No .removed-from-enquiry__popup element found');
    }
}

window.showEnquiryClearedPopup = function() {
    const popup = document.querySelector('.enquiry-cleared__popup');
    if (popup) {
        popup.style.display = 'block';
        popup.classList.add('show');
        console.log('Enquiry cleared popup shown');
        setTimeout(() => {
            popup.classList.remove('show');
            popup.style.display = 'none';
        }, 3000);
    } else {
        console.warn('No .enquiry-cleared__popup element found');
    }
}

// Update enquiry count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateEnquiryCount();
});