<?php
// Prevent direct access
if (!defined('APP_PATH')) {
    die('Direct access not allowed');
}

/**
 * Admin Dashboard Template
 * Overview of key metrics and recent activity
 */

// Admin layout header
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - SadaCart</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="<?= asset('css/main.min.css') ?>" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  
  <div class="d-flex">
    <!-- Sidebar -->
    <?php include APP_PATH . '/Views/layouts/admin-sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="flex-grow-1" style="margin-left: 250px;">
      <!-- Top Navigation -->
      <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
          <h4 class="mb-0">Dashboard</h4>
          
          <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle me-2"></i>
                <?= e(getCurrentUser()['first_name']) ?>
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?= url() ?>"><i class="fas fa-home me-2"></i>View Site</a></li>
                <li><a class="dropdown-item" href="<?= url('dashboard/profile') ?>"><i class="fas fa-user me-2"></i>Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
              </ul>
            </div>
          </div>
        </div>
      </nav>
      
      <!-- Dashboard Content -->
      <div class="container-fluid p-4">
        <!-- Stats Cards -->
        <div class="row mb-4">
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                      Total Orders
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                      <?= $stats['total_orders'] ?? 0 ?>
                    </div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                      Revenue (Monthly)
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                      <?= formatPrice($stats['monthly_revenue'] ?? 0) ?>
                    </div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                      Products
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                      <?= $stats['total_products'] ?? 0 ?>
                    </div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-box fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                      Customers
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                      <?= $stats['total_customers'] ?? 0 ?>
                    </div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-users fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Charts Row -->
        <div class="row mb-4">
          <!-- Sales Chart -->
          <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Sales Overview</h6>
              </div>
              <div class="card-body">
                <canvas id="salesChart" width="400" height="200"></canvas>
              </div>
            </div>
          </div>
          
          <!-- Top Products -->
          <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Products</h6>
              </div>
              <div class="card-body">
                <?php if (!empty($topProducts)): ?>
                <?php foreach ($topProducts as $product): ?>
                <div class="d-flex align-items-center mb-3">
                  <div class="me-3">
                    <img src="<?= asset($product['image'] ?? 'images/placeholder-product.jpg') ?>" 
                         alt="<?= e($product['name']) ?>" 
                         class="rounded" 
                         style="width: 50px; height: 50px; object-fit: cover;">
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-1"><?= e($product['name']) ?></h6>
                    <small class="text-muted"><?= $product['sales_count'] ?> sold</small>
                  </div>
                  <div class="text-end">
                    <strong><?= formatPrice($product['revenue']) ?></strong>
                  </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="text-muted text-center">No sales data available</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Recent Orders and Activity -->
        <div class="row">
          <!-- Recent Orders -->
          <div class="col-lg-8">
            <div class="card shadow mb-4">
              <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                <a href="<?= url('admin/orders') ?>" class="btn btn-primary btn-sm">View All</a>
              </div>
              <div class="card-body">
                <?php if (!empty($recentOrders)): ?>
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($recentOrders as $order): ?>
                      <tr>
                        <td>
                          <a href="<?= url('admin/orders/' . $order['id']) ?>" class="text-decoration-none">
                            <?= e($order['order_number']) ?>
                          </a>
                        </td>
                        <td>
                          <?php if ($order['user_id']): ?>
                          <?= e($order['billing_first_name'] . ' ' . $order['billing_last_name']) ?>
                          <?php else: ?>
                          <span class="text-muted">Guest</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <span class="badge bg-<?= getStatusColor($order['status']) ?>">
                            <?= ucfirst($order['status']) ?>
                          </span>
                        </td>
                        <td><?= formatPrice($order['total_amount']) ?></td>
                        <td><?= formatDate($order['created_at']) ?></td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center">No recent orders</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
          
          <!-- Recent Activity -->
          <div class="col-lg-4">
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
              </div>
              <div class="card-body">
                <?php if (!empty($recentActivity)): ?>
                <?php foreach ($recentActivity as $activity): ?>
                <div class="d-flex align-items-start mb-3">
                  <div class="me-3">
                    <i class="fas fa-<?= getActivityIcon($activity['action']) ?> text-muted"></i>
                  </div>
                  <div class="flex-grow-1">
                    <p class="mb-1 small"><?= e($activity['description']) ?></p>
                    <small class="text-muted"><?= formatDate($activity['created_at']) ?></small>
                  </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="text-muted text-center">No recent activity</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode($chartData['labels'] ?? []) ?>,
        datasets: [{
          label: 'Sales',
          data: <?= json_encode($chartData['sales'] ?? []) ?>,
          borderColor: '#0A1D56',
          backgroundColor: 'rgba(10, 29, 86, 0.1)',
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '$' + value;
              }
            }
          }
        }
      }
    });
  </script>
</body>
</html>

<?php
function getStatusColor($status) {
  $colors = [
    'pending' => 'warning',
    'processing' => 'info',
    'shipped' => 'primary',
    'delivered' => 'success',
    'cancelled' => 'danger'
  ];
  return $colors[$status] ?? 'secondary';
}

function getActivityIcon($action) {
  $icons = [
    'login' => 'sign-in-alt',
    'order_placed' => 'shopping-cart',
    'product_created' => 'plus',
    'user_registered' => 'user-plus'
  ];
  return $icons[$action] ?? 'info-circle';
}
?>
