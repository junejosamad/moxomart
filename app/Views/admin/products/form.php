<?php
/**
 * Admin Product Form View
 * Create/Edit Product Form
 */

$pageTitle = isset($product) ? 'Edit Product' : 'Add New Product';
$isEdit = isset($product);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - Admin | SadaCart</title>
    <link href="<?= asset('css/main.min.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="admin-body">
    <?php include '../app/Views/layouts/admin-sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1><?= e($pageTitle) ?></h1>
            <div class="admin-breadcrumb">
                <a href="/admin">Dashboard</a>
                <span>/</span>
                <a href="/admin/products">Products</a>
                <span>/</span>
                <span><?= $isEdit ? 'Edit' : 'Add New' ?></span>
            </div>
        </div>

        <div class="admin-main">
            <?php if ($flash = getFlash('error')): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= e($flash) ?>
                </div>
            <?php endif; ?>

            <?php if ($flash = getFlash('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= e($flash) ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="product-form">
                <?= csrfField() ?>
                
                <div class="form-grid">
                    <!-- Main Product Information -->
                    <div class="form-section">
                        <h3>Product Information</h3>
                        
                        <div class="form-group">
                            <label for="name">Product Name *</label>
                            <input type="text" id="name" name="name" 
                                   value="<?= e($product['name'] ?? '') ?>" 
                                   required maxlength="255">
                        </div>

                        <div class="form-group">
                            <label for="slug">URL Slug</label>
                            <input type="text" id="slug" name="slug" 
                                   value="<?= e($product['slug'] ?? '') ?>" 
                                   placeholder="Auto-generated from name">
                            <small>Leave blank to auto-generate from product name</small>
                        </div>

                        <div class="form-group">
                            <label for="short_description">Short Description</label>
                            <textarea id="short_description" name="short_description" 
                                      rows="3" maxlength="500"><?= e($product['short_description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="description">Full Description</label>
                            <textarea id="description" name="description" 
                                      rows="8" class="rich-editor"><?= e($product['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="category_id">Category *</label>
                                <select id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" 
                                                <?= ($product['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                            <?= e($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="brand">Brand</label>
                                <input type="text" id="brand" name="brand" 
                                       value="<?= e($product['brand'] ?? '') ?>" 
                                       maxlength="100">
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Inventory -->
                    <div class="form-section">
                        <h3>Pricing & Inventory</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="sku">SKU *</label>
                                <input type="text" id="sku" name="sku" 
                                       value="<?= e($product['sku'] ?? '') ?>" 
                                       required maxlength="50">
                            </div>

                            <div class="form-group">
                                <label for="price">Price *</label>
                                <div class="input-group">
                                    <span class="input-prefix">$</span>
                                    <input type="number" id="price" name="price" 
                                           value="<?= $product['price'] ?? '' ?>" 
                                           step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="compare_price">Compare Price</label>
                                <div class="input-group">
                                    <span class="input-prefix">$</span>
                                    <input type="number" id="compare_price" name="compare_price" 
                                           value="<?= $product['compare_price'] ?? '' ?>" 
                                           step="0.01" min="0">
                                </div>
                                <small>Original price for showing discounts</small>
                            </div>

                            <div class="form-group">
                                <label for="cost_price">Cost Price</label>
                                <div class="input-group">
                                    <span class="input-prefix">$</span>
                                    <input type="number" id="cost_price" name="cost_price" 
                                           value="<?= $product['cost_price'] ?? '' ?>" 
                                           step="0.01" min="0">
                                </div>
                                <small>For profit calculations (not shown to customers)</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="stock_quantity">Stock Quantity *</label>
                                <input type="number" id="stock_quantity" name="stock_quantity" 
                                       value="<?= $product['stock_quantity'] ?? '0' ?>" 
                                       min="0" required>
                            </div>

                            <div class="form-group">
                                <label for="low_stock_threshold">Low Stock Alert</label>
                                <input type="number" id="low_stock_threshold" name="low_stock_threshold" 
                                       value="<?= $product['low_stock_threshold'] ?? '5' ?>" 
                                       min="0">
                                <small>Alert when stock falls below this number</small>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping -->
                    <div class="form-section">
                        <h3>Shipping</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="weight">Weight (lbs)</label>
                                <input type="number" id="weight" name="weight" 
                                       value="<?= $product['weight'] ?? '' ?>" 
                                       step="0.01" min="0">
                            </div>

                            <div class="form-group">
                                <label for="dimensions">Dimensions (L x W x H)</label>
                                <input type="text" id="dimensions" name="dimensions" 
                                       value="<?= e($product['dimensions'] ?? '') ?>" 
                                       placeholder="12 x 8 x 4 inches">
                            </div>
                        </div>
                    </div>

                    <!-- Product Images -->
                    <div class="form-section">
                        <h3>Product Images</h3>
                        
                        <div class="image-upload-area">
                            <input type="file" id="product_images" name="product_images[]" 
                                   multiple accept="image/*" class="file-input">
                            <label for="product_images" class="file-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Click to upload images or drag and drop</span>
                                <small>Supports: JPG, PNG, GIF (Max 5MB each)</small>
                            </label>
                        </div>

                        <?php if ($isEdit && !empty($product['images'])): ?>
                            <div class="existing-images">
                                <h4>Current Images</h4>
                                <div class="image-grid">
                                    <?php foreach ($product['images'] as $image): ?>
                                        <div class="image-item" data-image-id="<?= $image['id'] ?>">
                                            <img src="<?= asset('uploads/' . $image['image_path']) ?>" 
                                                 alt="<?= e($image['alt_text']) ?>">
                                            <div class="image-actions">
                                                <button type="button" class="btn-icon set-primary" 
                                                        title="Set as Primary" 
                                                        <?= $image['is_primary'] ? 'disabled' : '' ?>>
                                                    <i class="fas fa-star"></i>
                                                </button>
                                                <button type="button" class="btn-icon delete-image" 
                                                        title="Delete Image">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <?php if ($image['is_primary']): ?>
                                                <span class="primary-badge">Primary</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- SEO -->
                    <div class="form-section">
                        <h3>SEO Settings</h3>
                        
                        <div class="form-group">
                            <label for="meta_title">Meta Title</label>
                            <input type="text" id="meta_title" name="meta_title" 
                                   value="<?= e($product['meta_title'] ?? '') ?>" 
                                   maxlength="60">
                            <small>Recommended: 50-60 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="meta_description">Meta Description</label>
                            <textarea id="meta_description" name="meta_description" 
                                      rows="3" maxlength="160"><?= e($product['meta_description'] ?? '') ?></textarea>
                            <small>Recommended: 150-160 characters</small>
                        </div>
                    </div>

                    <!-- Status & Visibility -->
                    <div class="form-section">
                        <h3>Status & Visibility</h3>
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="active" <?= ($product['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>
                                    Active
                                </option>
                                <option value="inactive" <?= ($product['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>
                                    Inactive
                                </option>
                                <option value="draft" <?= ($product['status'] ?? '') == 'draft' ? 'selected' : '' ?>>
                                    Draft
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="featured" value="1" 
                                       <?= ($product['featured'] ?? false) ? 'checked' : '' ?>>
                                <span class="checkmark"></span>
                                Featured Product
                            </label>
                            <small>Show this product in featured sections</small>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <?= $isEdit ? 'Update Product' : 'Create Product' ?>
                    </button>
                    
                    <a href="/admin/products" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>

                    <?php if ($isEdit): ?>
                        <button type="button" class="btn btn-danger" id="delete-product">
                            <i class="fas fa-trash"></i>
                            Delete Product
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <script src="<?= asset('js/app.min.js') ?>"></script>
    <script>
        // Auto-generate slug from product name
        document.getElementById('name').addEventListener('input', function() {
            const slug = this.value.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            document.getElementById('slug').value = slug;
        });

        // Image upload preview
        document.getElementById('product_images').addEventListener('change', function() {
            // Handle image preview logic here
        });

        // Delete product confirmation
        <?php if ($isEdit): ?>
        document.getElementById('delete-product').addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                // Create form to delete product
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/products/<?= $product['id'] ?>/delete';
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = 'csrf_token';
                csrfInput.value = '<?= generateCsrfToken() ?>';
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
