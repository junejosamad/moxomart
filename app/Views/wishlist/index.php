<?php
// Define APP_PATH if not already defined
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__, 2) . '/app');
}

// Ensure we have the required variables
if (!isset($wishlistItems)) {
    $wishlistItems = [];
}

include APP_PATH . '/Views/layouts/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/" class="text-success">Home</a></li>
                    <li class="breadcrumb-item active">Wishlist</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-success">My Wishlist</h1>
                <?php if (!empty($wishlistItems)): ?>
                    <div>
                        <button class="btn btn-outline-success me-2" onclick="addAllToCart()">
                            <i class="fas fa-shopping-cart me-2"></i>Add All to Cart
                        </button>
                        <button class="btn btn-outline-danger" onclick="clearWishlist()">
                            <i class="fas fa-trash me-2"></i>Clear Wishlist
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($wishlistItems)): ?>
        <div class="row">
            <?php foreach ($wishlistItems as $item): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4" id="wishlist-item-<?= $item['id'] ?>">
                    <div class="card h-100 product-card">
                        <div class="position-relative">
                            <img src="<?= $item['image'] ?? '/assets/images/placeholder.jpg' ?>" 
                                 class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>"
                                 style="height: 250px; object-fit: cover;">
                            
                            <!-- Wishlist Remove Button -->
                            <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                    onclick="removeFromWishlist(<?= $item['id'] ?>)"
                                    title="Remove from wishlist">
                                <i class="fas fa-times"></i>
                            </button>

                            <!-- Stock Status -->
                            <?php if ($item['stock_quantity'] <= 0): ?>
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-danger">Out of Stock</span>
                                </div>
                            <?php elseif ($item['stock_quantity'] <= 5): ?>
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-warning">Low Stock</span>
                                </div>
                            <?php endif; ?>

                            <!-- Discount Badge -->
                            <?php if ($item['discount_percentage'] > 0): ?>
                                <div class="position-absolute bottom-0 start-0 m-2">
                                    <span class="badge bg-success"><?= $item['discount_percentage'] ?>% OFF</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <a href="/products/<?= $item['slug'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($item['name']) ?>
                                </a>
                            </h5>
                            
                            <p class="card-text text-muted small flex-grow-1">
                                <?= htmlspecialchars(substr($item['description'], 0, 100)) ?>...
                            </p>

                            <!-- Rating -->
                            <div class="mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="stars me-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?= $i <= $item['average_rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <small class="text-muted">(<?= $item['review_count'] ?> reviews)</small>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="mb-3">
                                <?php if ($item['discount_percentage'] > 0): ?>
                                    <div class="d-flex align-items-center">
                                        <span class="h5 text-success mb-0 me-2">
                                            ₨<?= number_format($item['discounted_price']) ?>
                                        </span>
                                        <span class="text-muted text-decoration-line-through">
                                            ₨<?= number_format($item['price']) ?>
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <span class="h5 text-success mb-0">₨<?= number_format($item['price']) ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-auto">
                                <?php if ($item['stock_quantity'] > 0): ?>
                                    <button class="btn btn-success w-100 mb-2" 
                                            onclick="addToCart(<?= $item['id'] ?>)">
                                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100 mb-2" disabled>
                                        <i class="fas fa-times me-2"></i>Out of Stock
                                    </button>
                                <?php endif; ?>
                                
                                <div class="d-flex gap-2">
                                    <a href="/products/<?= $item['slug'] ?>" class="btn btn-outline-success flex-fill">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <button class="btn btn-outline-primary flex-fill" 
                                            onclick="shareProduct(<?= $item['id'] ?>)">
                                        <i class="fas fa-share me-1"></i>Share
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Added to Wishlist Date -->
                        <div class="card-footer bg-light">
                            <small class="text-muted">
                                <i class="fas fa-heart text-danger me-1"></i>
                                Added <?= date('M d, Y', strtotime($item['added_at'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Wishlist pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link text-success" href="?page=<?= $currentPage - 1 ?>">Previous</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <a class="page-link <?= $i === $currentPage ? 'bg-success border-success' : 'text-success' ?>" 
                               href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link text-success" href="?page=<?= $currentPage + 1 ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>

    <?php else: ?>
        <!-- Empty Wishlist -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-heart fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">Your wishlist is empty</h3>
                    <p class="text-muted mb-4">Save items you love by clicking the heart icon on any product</p>
                    <a href="/products" class="btn btn-success btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Recently Viewed Products -->
    <?php if (!empty($recentlyViewed)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="text-success mb-4">Recently Viewed</h3>
            </div>
            <?php foreach ($recentlyViewed as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <img src="<?= $product['image'] ?? '/assets/images/placeholder.jpg' ?>" 
                             class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="/products/<?= $product['slug'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h6>
                            <p class="text-success fw-bold">₨<?= number_format($product['price']) ?></p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-success flex-fill" 
                                        onclick="addToWishlist(<?= $product['id'] ?>)">
                                    <i class="fas fa-heart me-1"></i>Add to Wishlist
                                </button>
                                <button class="btn btn-sm btn-success flex-fill" 
                                        onclick="addToCart(<?= $product['id'] ?>)">
                                    <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.stars i {
    font-size: 0.875rem;
}
</style>

<script>
function removeFromWishlist(productId) {
    if (confirm('Remove this item from your wishlist?')) {
        fetch('/api/wishlist/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('wishlist-item-' + productId).remove();
                
                // Check if wishlist is empty
                const remainingItems = document.querySelectorAll('[id^="wishlist-item-"]');
                if (remainingItems.length === 0) {
                    location.reload();
                }
            } else {
                alert('Error removing item from wishlist');
            }
        });
    }
}

function addToCart(productId) {
    fetch('/api/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Item added to cart successfully!');
            updateCartCount();
        } else {
            alert(data.message || 'Error adding item to cart');
        }
    });
}

function addToWishlist(productId) {
    fetch('/api/wishlist/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Item added to wishlist successfully!');
        } else {
            alert(data.message || 'Error adding item to wishlist');
        }
    });
}

function addAllToCart() {
    if (confirm('Add all wishlist items to your cart?')) {
        fetch('/api/wishlist/add-all-to-cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`${data.added_count} items added to cart successfully!`);
                updateCartCount();
            } else {
                alert('Error adding items to cart');
            }
        });
    }
}

function clearWishlist() {
    if (confirm('Are you sure you want to clear your entire wishlist?')) {
        fetch('/api/wishlist/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error clearing wishlist');
            }
        });
    }
}

function shareProduct(productId) {
    const url = window.location.origin + '/products/' + productId;
    
    if (navigator.share) {
        navigator.share({
            title: 'Check out this product',
            url: url
        });
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            alert('Product link copied to clipboard!');
        });
    }
}

function updateCartCount() {
    fetch('/api/cart/count')
        .then(response => response.json())
        .then(data => {
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(el => {
                el.textContent = data.count;
            });
        });
}
</script>

<?php include APP_PATH . '/Views/layouts/footer.php'; ?>
