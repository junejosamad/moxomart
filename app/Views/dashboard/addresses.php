<?php
if (!isset($user)) {
    $user = getCurrentUser() ?? [];
}
?>
<?php include_once APP_PATH . '/Views/layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?= $user['avatar'] ?? asset('images/default-avatar.png') ?>" alt="Avatar" class="rounded-circle me-3" width="50" height="50">
                        <div>
                            <h6 class="mb-0"><?= e(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></h6>
                            <small class="text-muted"><?= e($user['email']) ?></small>
                        </div>
                    </div>
                    <hr>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="<?= url('dashboard') ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="<?= url('dashboard/orders') ?>">
                            <i class="fas fa-box me-2"></i>My Orders
                        </a>
                        <a class="nav-link" href="<?= url('dashboard/profile') ?>">
                            <i class="fas fa-user-edit me-2"></i>Profile
                        </a>
                        <a class="nav-link active bg-success text-white rounded" href="<?= url('dashboard/addresses') ?>">
                            <i class="fas fa-map-marker-alt me-2"></i>Addresses
                        </a>
                        <a class="nav-link" href="<?= url('wishlist') ?>">
                            <i class="fas fa-heart me-2"></i>Wishlist
                        </a>
                        <a class="nav-link text-danger" href="<?= url('logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">My Addresses</h2>
                    <p class="text-muted mb-0">Manage your delivery addresses</p>
                </div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addressModal">
                    <i class="fas fa-plus me-2"></i>Add New Address
                </button>
            </div>

            <!-- Addresses Grid -->
            <div class="row">
                <?php if (empty($addresses)): ?>
                <div class="col-12">
                    <div class="card text-center py-5">
                        <div class="card-body">
                            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                            <h5>No addresses found</h5>
                            <p class="text-muted">Add your first delivery address to get started</p>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addressModal">
                                <i class="fas fa-plus me-2"></i>Add Address
                            </button>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <?php foreach ($addresses as $address): ?>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 <?= $address['is_default'] ? 'border-success' : '' ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="card-title mb-1">
                                        <?= e(($address['first_name'] ?? '') . ' ' . ($address['last_name'] ?? '')) ?>
                                        <?php if ($address['is_default']): ?>
                                        <span class="badge bg-success ms-2">Default</span>
                                        <?php endif; ?>
                                    </h6>
                                    <span class="badge bg-light text-dark"><?= ucfirst($address['type']) ?></span>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item edit-address" href="#" 
                                               data-id="<?= $address['id'] ?>"
                                               data-type="<?= $address['type'] ?>"
                                               data-name="<?= e($address['name']) ?>"
                                               data-phone="<?= e($address['phone']) ?>"
                                               data-address1="<?= e($address['address_line_1']) ?>"
                                               data-address2="<?= e($address['address_line_2']) ?>"
                                               data-city="<?= e($address['city']) ?>"
                                               data-state="<?= e($address['state']) ?>"
                                               data-postal="<?= e($address['postal_code']) ?>"
                                               data-default="<?= $address['is_default'] ?>">
                                                <i class="fas fa-edit me-2"></i>Edit
                                            </a>
                                        </li>
                                        <?php if (!$address['is_default']): ?>
                                        <li>
                                            <form method="POST" action="<?= url('dashboard/addresses/set-default') ?>" class="d-inline">
                                                <input type="hidden" name="address_id" value="<?= $address['id'] ?>">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fas fa-star me-2"></i>Set as Default
                                                </button>
                                            </form>
                                        </li>
                                        <?php endif; ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger delete-address" href="#" data-id="<?= $address['id'] ?>">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="address-details">
                                <p class="mb-2">
                                    <i class="fas fa-phone text-muted me-2"></i>
                                    <?= e($address['phone']) ?>
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                    <?= e($address['address_line_1']) ?><br>
                                    <?php if (!empty($address['address_line_2'])): ?>
                                    <span class="ms-4"><?= e($address['address_line_2']) ?></span><br>
                                    <?php endif; ?>
                                    <span class="ms-4"><?= e($address['city']) ?>, <?= e($address['state']) ?> <?= e($address['postal_code']) ?></span><br>
                                    <span class="ms-4"><?= e($address['country']) ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addressForm" method="POST" action="<?= url('dashboard/addresses/save') ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="addressModalLabel">Add New Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="address_id" id="address_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="address_type" class="form-label">Address Type</label>
                            <select class="form-select" name="type" id="address_type" required>
                                <option value="billing">Billing</option>
                                <option value="shipping">Shipping</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" id="address_name" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="address_phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="phone" id="address_phone" placeholder="+92-XXX-XXXXXXX" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address_city" class="form-label">City</label>
                            <select class="form-select" name="city" id="address_city" required>
                                <option value="">Select City</option>
                                <?php foreach ($cities as $city): ?>
                                <option value="<?= $city ?>"><?= $city ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address_line_1" class="form-label">Address Line 1</label>
                        <input type="text" class="form-control" name="address_line_1" id="address_line_1" placeholder="House/Flat number, Street name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address_line_2" class="form-label">Address Line 2 (Optional)</label>
                        <input type="text" class="form-control" name="address_line_2" id="address_line_2" placeholder="Area, Landmark">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="address_state" class="form-label">State/Province</label>
                            <select class="form-select" name="state" id="address_state" required>
                                <option value="">Select State</option>
                                <option value="Punjab">Punjab</option>
                                <option value="Sindh">Sindh</option>
                                <option value="Khyber Pakhtunkhwa">Khyber Pakhtunkhwa</option>
                                <option value="Balochistan">Balochistan</option>
                                <option value="Gilgit-Baltistan">Gilgit-Baltistan</option>
                                <option value="Azad Kashmir">Azad Kashmir</option>
                                <option value="Islamabad Capital Territory">Islamabad Capital Territory</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address_postal" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" name="postal_code" id="address_postal" placeholder="12345" maxlength="5" required>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1">
                        <label class="form-check-label" for="is_default">
                            Set as default address
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this address? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="<?= url('dashboard/addresses/delete') ?>" class="d-inline">
                    <input type="hidden" name="address_id" id="delete_address_id">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit address functionality
    document.querySelectorAll('.edit-address').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Populate form with address data
            document.getElementById('address_id').value = this.dataset.id;
            document.getElementById('address_type').value = this.dataset.type;
            document.getElementById('address_name').value = this.dataset.name;
            document.getElementById('address_phone').value = this.dataset.phone;
            document.getElementById('address_line_1').value = this.dataset.address1;
            document.getElementById('address_line_2').value = this.dataset.address2;
            document.getElementById('address_city').value = this.dataset.city;
            document.getElementById('address_state').value = this.dataset.state;
            document.getElementById('address_postal').value = this.dataset.postal;
            document.getElementById('is_default').checked = this.dataset.default == '1';
            
            // Update modal title
            document.getElementById('addressModalLabel').textContent = 'Edit Address';
            
            // Show modal
            new bootstrap.Modal(document.getElementById('addressModal')).show();
        });
    });
    
    // Delete address functionality
    document.querySelectorAll('.delete-address').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('delete_address_id').value = this.dataset.id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
    
    // Reset form when modal is closed
    document.getElementById('addressModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('addressForm').reset();
        document.getElementById('address_id').value = '';
        document.getElementById('addressModalLabel').textContent = 'Add New Address';
    });
    
    // Phone number formatting
    document.getElementById('address_phone').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.startsWith('92')) {
            value = '+' + value;
        } else if (value.startsWith('0')) {
            value = '+92' + value.substring(1);
        } else if (!value.startsWith('+92')) {
            value = '+92' + value;
        }
        this.value = value;
    });
    
    // Postal code validation
    document.getElementById('address_postal').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 5);
    });
});
</script>

<?php include_once APP_PATH . '/Views/layouts/footer.php'; ?>
