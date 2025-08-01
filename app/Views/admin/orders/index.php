<?php
/**
 * Admin Orders List Template
 */
$pageTitle = 'Orders Management';
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
  
  <div class="d-flex">
    <!-- Sidebar -->
    <?php include APP_PATH . '/Views/layouts/admin-sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="flex-grow-1" style="margin-left: 250px;">
      <!-- Top Navigation -->
      <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
          <h4 class="mb-0">Orders Management</h4>
          
          <div class="navbar-nav ms-auto">
            <button class="btn btn-outline-primary me-2" onclick="exportOrders()">
              <i class="fas fa-download me-2"></i>Export CSV
            </button>
          </div>
        </div>
      </nav>
      
      <!-- Orders Content -->
      <div class="container-fluid p-4">
        <?php if ($flash = getFlash('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= e($flash) ?>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="card bg-primary text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h6 class="card-title">Total Orders</h6>
                    <h3 class="mb-0"><?= number_format($stats['total_orders']) ?></h3>
                  </div>
                  <div class="align-self-center">
                    <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="card bg-success text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h6 class="card-title">Total Revenue</h6>
                    <h3 class="mb-0"><?= formatPrice($stats['total_revenue']) ?></h3>
                  </div>
                  <div class="align-self-center">
                    <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="card bg-warning text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h6 class="card-title">Pending Orders</h6>
                    <h3 class="mb-0"><?= number_format($stats['pending_orders']) ?></h3>
                  </div>
                  <div class="align-self-center">
                    <i class="fas fa-clock fa-2x opacity-75"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="card bg-info text-white">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div>
                    <h6 class="card-title">Processing</h6>
                    <h3 class="mb-0"><?= number_format($stats['processing_orders']) ?></h3>
                  </div>
                  <div class="align-self-center">
                    <i class="fas fa-cog fa-2x opacity-75"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Filters -->
        <div class="card mb-4">
          <div class="card-body">
            <form method="GET" class="row g-3">
              <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                  <option value="">All Statuses</option>
                  <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                  <option value="processing" <?= ($filters['status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processing</option>
                  <option value="shipped" <?= ($filters['status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                  <option value="delivered" <?= ($filters['status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                  <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
              </div>
              
              <div class="col-md-3">
                <label for="payment_status" class="form-label">Payment Status</label>
                <select class="form-select" id="payment_status" name="payment_status">
                  <option value="">All Payment Statuses</option>
                  <option value="pending" <?= ($filters['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                  <option value="paid" <?= ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                  <option value="failed" <?= ($filters['payment_status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
                  <option value="refunded" <?= ($filters['payment_status'] ?? '') === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                </select>
              </div>
              
              <div class="col-md-3">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $filters['date_from'] ?? '' ?>">
              </div>
              
              <div class="col-md-3">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $filters['date_to'] ?? '' ?>">
              </div>
              
              <div class="col-md-6">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Order number, customer name, email..." 
                       value="<?= e($filters['search'] ?? '') ?>">
              </div>
              
              <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                  <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="<?= url('admin/orders') ?>" class="btn btn-outline-secondary">
                  <i class="fas fa-times me-2"></i>Clear
                </a>
              </div>
            </form>
          </div>
        </div>
        
        <!-- Orders Table -->
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Total</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($orders['data'])): ?>
                  <tr>
                    <td colspan="7" class="text-center py-4">
                      <div class="empty-state">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No orders found</p>
                      </div>
                    </td>
                  </tr>
                  <?php else: ?>
                  <?php foreach ($orders['data'] as $order): ?>
                  <tr>
                    <td>
                      <strong><?= e($order['order_number']) ?></strong>
                    </td>
                    <td>
                      <?php if ($order['user_id']): ?>
                        <div class="customer-info">
                          <strong><?= e($order['billing_first_name'] . ' ' . $order['billing_last_name']) ?></strong>
                          <small><?= e($order['user_email'] ?? '') ?></small>
                        </div>
                      <?php else: ?>
                        <div class="customer-info">
                          <strong>Guest</strong>
                          <small><?= e($order['guest_email']) ?></small>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="date-info">
                        <strong><?= formatDate($order['created_at'], 'M j, Y') ?></strong>
                        <small><?= formatDate($order['created_at'], 'g:i A') ?></small>
                      </div>
                    </td>
                    <td>
                      <span class="status-badge status-<?= $order['status'] ?>">
                        <?= ucfirst($order['status']) ?>
                      </span>
                    </td>
                    <td>
                      <span class="payment-badge payment-<?= $order['payment_status'] ?>">
                        <?= ucfirst($order['payment_status']) ?>
                      </span>
                    </td>
                    <td>
                      <strong><?= formatPrice($order['total_amount']) ?></strong>
                    </td>
                    <td>
                      <div class="action-buttons">
                        <a href="<?= url('admin/orders/' . $order['id']) ?>" 
                           class="btn-icon" title="View Details">
                          <i class="fas fa-eye"></i>
                        </a>
                        
                        <?php if ($order['status'] !== 'cancelled' && $order['status'] !== 'delivered'): ?>
                          <button class="btn-icon update-status" 
                                  data-order-id="<?= $order['id'] ?>" 
                                  title="Update Status">
                            <i class="fas fa-edit"></i>
                          </button>
                        <?php endif; ?>

                        <?php if ($order['payment_status'] === 'paid' && empty($order['tracking_number'])): ?>
                          <button class="btn-icon add-tracking" 
                                  data-order-id="<?= $order['id'] ?>" 
                                  title="Add Tracking">
                            <i class="fas fa-truck"></i>
                          </button>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($orders['last_page'] > 1): ?>
            <div class="pagination">
              <?php if ($orders['current_page'] > 1): ?>
                <a href="?page=<?= $orders['current_page'] - 1 ?><?= http_build_query(array_filter($filters)) ? '&' . http_build_query(array_filter($filters)) : '' ?>" 
                   class="pagination-btn">
                  <i class="fas fa-chevron-left"></i>
                  Previous
                </a>
              <?php endif; ?>

              <div class="pagination-info">
                Page <?= $orders['current_page'] ?> of <?= $orders['last_page'] ?>
                (<?= number_format($orders['total']) ?> total orders)
              </div>

              <?php if ($orders['current_page'] < $orders['last_page']): ?>
                <a href="?page=<?= $orders['current_page'] + 1 ?><?= http_build_query(array_filter($filters)) ? '&' . http_build_query(array_filter($filters)) : '' ?>" 
                   class="pagination-btn">
                  Next
                  <i class="fas fa-chevron-right"></i>
                </a>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Update Status Modal -->
  <div id="status-modal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Update Order Status</h3>
        <button class="modal-close">&times;</button>
      </div>
      <form id="status-form" method="POST">
        <?= csrfField() ?>
        <div class="modal-body">
          <div class="form-group">
            <label for="new_status">New Status:</label>
            <select id="new_status" name="status" required>
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div class="form-group">
            <label for="status_notes">Notes (Optional):</label>
            <textarea id="status_notes" name="notes" rows="3" 
                      placeholder="Add any notes about this status change..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update Status</button>
          <button type="button" class="btn btn-secondary modal-close">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Tracking Modal -->
  <div id="tracking-modal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Add Tracking Information</h3>
        <button class="modal-close">&times;</button>
      </div>
      <form id="tracking-form" method="POST">
        <?= csrfField() ?>
        <div class="modal-body">
          <div class="form-group">
            <label for="tracking_number">Tracking Number:</label>
            <input type="text" id="tracking_number" name="tracking_number" required>
          </div>
          <div class="form-group">
            <label for="tracking_url">Tracking URL (Optional):</label>
            <input type="url" id="tracking_url" name="tracking_url" 
                   placeholder="https://tracking.carrier.com/track?id=...">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add Tracking</button>
          <button type="button" class="btn btn-secondary modal-close">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?= asset('js/app.min.js') ?>"></script>
  <script>
    // Modal functionality
    const modals = document.querySelectorAll('.modal');
    const modalCloses = document.querySelectorAll('.modal-close');

    modalCloses.forEach(close => {
      close.addEventListener('click', () => {
        modals.forEach(modal => modal.style.display = 'none');
      });
    });

    // Update status
    document.querySelectorAll('.update-status').forEach(btn => {
      btn.addEventListener('click', () => {
        const orderId = btn.dataset.orderId;
        const modal = document.getElementById('status-modal');
        const form = document.getElementById('status-form');
        
        form.action = `/admin/orders/${orderId}/status`;
        modal.style.display = 'block';
      });
    });

    // Add tracking
    document.querySelectorAll('.add-tracking').forEach(btn => {
      btn.addEventListener('click', () => {
        const orderId = btn.dataset.orderId;
        const modal = document.getElementById('tracking-modal');
        const form = document.getElementById('tracking-form');
        
        form.action = `/admin/orders/${orderId}/tracking`;
        modal.style.display = 'block';
      });
    });

    // Close modal on outside click
    window.addEventListener('click', (e) => {
      modals.forEach(modal => {
        if (e.target === modal) {
          modal.style.display = 'none';
        }
      });
    });

    function updateOrderStatus(orderId, status) {
      if (confirm(`Are you sure you want to update this order status to "${status}"?`)) {
        fetch(`<?= url('admin/orders/') ?>${orderId}/status`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Error updating order status');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error updating order status');
        });
      }
    }
    
    function exportOrders() {
      const params = new URLSearchParams(window.location.search);
      params.set('export', 'csv');
      window.location.href = '<?= url('admin/orders') ?>?' + params.toString();
    }
  </script>
</body>
</html>

<?php
function getOrderStatusColor($status) {
  switch ($status) {
    case 'pending': return 'warning';
    case 'processing': return 'info';
    case 'shipped': return 'primary';
    case 'delivered': return 'success';
    case 'cancelled': return 'danger';
    default: return 'secondary';
  }
}

function getPaymentStatusColor($status) {
  switch ($status) {
    case 'pending': return 'warning';
    case 'paid': return 'success';
    case 'failed': return 'danger';
    case 'refunded': return 'info';
    default: return 'secondary';
  }
}


?>
