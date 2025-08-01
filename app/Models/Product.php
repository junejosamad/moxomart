<?php
/**
 * Product Model
 * Handles product data and operations
 */

namespace App\Models;

use App\Core\Model;

class Product extends Model {
  protected $table = 'products';
  protected $fillable = [
    'name', 'slug', 'description', 'short_description', 'sku', 'price',
    'compare_price', 'cost_price', 'stock_quantity', 'track_quantity', 'weight', 'dimensions', 'category_id', 'brand', 'status', 'featured',
    'meta_title', 'meta_description'
  ];
  
  /**
   * Find product by slug
   */
  public function findBySlug($slug) {
    return $this->findFirst(['slug' => $slug, 'status' => 'active']);
  }
  
  /**
   * Get product by slug (alias for findBySlug)
   */
  public function getBySlug($slug) {
    return $this->findBySlug($slug);
  }
  
  /**
   * Get featured products
   */
  public function getFeatured($limit = 8) {
    return $this->findAll(['featured' => true, 'status' => 'active'], 'created_at DESC', $limit);
  }
  
  /**
   * Get products by category
   */
  public function getByCategory($categoryId, $limit = null) {
    return $this->findAll(['category_id' => $categoryId, 'status' => 'active'], 'name ASC', $limit);
  }
  
  /**
   * Search products
   */
  public function search($query, $categoryId = null, $minPrice = null, $maxPrice = null, $page = 1, $perPage = 12) {
    $sql = "SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.status = 'active' AND (p.name LIKE ? OR p.description LIKE ?)";
    
    $params = ["%{$query}%", "%{$query}%"];
    
    if ($categoryId) {
      $sql .= " AND p.category_id = ?";
      $params[] = $categoryId;
    }
    
    if ($minPrice) {
      $sql .= " AND p.price >= ?";
      $params[] = $minPrice;
    }
    
    if ($maxPrice) {
      $sql .= " AND p.price <= ?";
      $params[] = $maxPrice;
    }
    
    // Get total count
    $countSql = str_replace("SELECT p.*, c.name as category_name", "SELECT COUNT(*)", $sql);
    $countStmt = $this->db->prepare($countSql);
    $countStmt->execute($params);
    $totalCount = $countStmt->fetchColumn();
    
    // Add pagination
    $offset = ($page - 1) * $perPage;
    $sql .= " ORDER BY p.name ASC LIMIT {$perPage} OFFSET {$offset}";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    return [
      'data' => $products,
      'current_page' => $page,
      'per_page' => $perPage,
      'total' => $totalCount,
      'last_page' => ceil($totalCount / $perPage)
    ];
  }
  
  /**
   * Get product images
   */
  public function getImages($productId) {
    $stmt = $this->db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, is_primary DESC");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
  }
  
  /**
   * Get primary image
   */
  public function getPrimaryImage($productId) {
    $stmt = $this->db->prepare("SELECT * FROM product_images WHERE product_id = ? AND is_primary = 1 LIMIT 1");
    $stmt->execute([$productId]);
    $result = $stmt->fetch();
    
    if (!$result) {
      // Get first image if no primary set
      $stmt = $this->db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC LIMIT 1");
      $stmt->execute([$productId]);
      $result = $stmt->fetch();
    }
    
    return $result;
  }
  
  /**
   * Add product image
   */
  public function addImage($productId, $imagePath, $altText = '', $isPrimary = false) {
    $stmt = $this->db->prepare("INSERT INTO product_images (product_id, image_path, alt_text, is_primary, created_at) VALUES (?, ?, ?, ?, NOW())");
    return $stmt->execute([$productId, $imagePath, $altText, $isPrimary ? 1 : 0]);
  }
  
  /**
   * Get product attributes
   */
  public function getAttributes($productId) {
    $stmt = $this->db->prepare("SELECT * FROM product_attributes WHERE product_id = ? ORDER BY name ASC");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
  }
  
  /**
   * Add product attribute
   */
  public function addAttribute($productId, $name, $value) {
    $stmt = $this->db->prepare("INSERT INTO product_attributes (product_id, name, value, created_at) VALUES (?, ?, ?, NOW())");
    return $stmt->execute([$productId, $name, $value]);
  }
  
  /**
   * Get product reviews
   */
  public function getReviews($productId, $approved = true) {
    $sql = "SELECT pr.*, u.first_name, u.last_name 
            FROM product_reviews pr 
            JOIN users u ON pr.user_id = u.id 
            WHERE pr.product_id = ?";
    
    $params = [$productId];
    
    if ($approved) {
      $sql .= " AND pr.is_approved = 1";
    }
    
    $sql .= " ORDER BY pr.created_at DESC";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }
  
  /**
   * Get average rating
   */
  public function getAverageRating($productId) {
    $stmt = $this->db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM product_reviews WHERE product_id = ? AND is_approved = 1");
    $stmt->execute([$productId]);
    $result = $stmt->fetch();
    
    return [
      'average' => $result['avg_rating'] ? round($result['avg_rating'], 1) : 0,
      'count' => $result['review_count']
    ];
  }
  
  /**
   * Get related products
   */
  public function getRelated($productId, $categoryId, $limit = 4) {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE category_id = ? AND id != ? AND status = 'active' ORDER BY RAND() LIMIT ?");
    $stmt->execute([$categoryId, $productId, $limit]);
    return $stmt->fetchAll();
  }
  
  /**
   * Update stock quantity
   */
  public function updateStock($productId, $quantity, $operation = 'decrease') {
    $operator = $operation === 'decrease' ? '-' : '+';
    $stmt = $this->db->prepare("UPDATE {$this->table} SET stock_quantity = stock_quantity {$operator} ? WHERE id = ?");
    return $stmt->execute([$quantity, $productId]);
  }
  
  /**
   * Check if product is in stock
   */
  public function isInStock($productId, $quantity = 1) {
    $product = $this->find($productId);
    if (!$product || !$product['track_quantity']) {
      return true;
    }
    
    return $product['stock_quantity'] >= $quantity;
  }
  
  /**
   * Get low stock products
   */
  public function getLowStock($threshold = 10) {
    return $this->findAll(['track_quantity' => 1], 'stock_quantity ASC')
           ->filter(function($product) use ($threshold) {
               return $product['stock_quantity'] <= $threshold;
           });
  }
  
  /**
   * Get popular products
   */
  public function getPopular($limit = 8) {
    $sql = "SELECT p.*, COUNT(oi.product_id) as order_count 
            FROM {$this->table} p 
            LEFT JOIN order_items oi ON p.id = oi.product_id 
            WHERE p.status = 'active' 
            GROUP BY p.id 
            ORDER BY order_count DESC, p.created_at DESC 
            LIMIT ?";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
  }
  
  /**
   * Get product with full details
   */
  public function getFullProduct($id) {
    $product = $this->find($id);
    
    if ($product) {
      $product['images'] = $this->getImages($id);
      $product['attributes'] = $this->getAttributes($id);
      $product['reviews'] = $this->getReviews($id);
      $product['average_rating'] = $this->getAverageRating($id);
      
      // Get category name
      if ($product['category_id']) {
        $stmt = $this->db->prepare("SELECT name FROM categories WHERE id = ?");
        $stmt->execute([$product['category_id']]);
        $category = $stmt->fetch();
        $product['category_name'] = $category['name'] ?? '';
      }
    }
    
    return $product;
  }

  /**
   * Paginate products with category names and low stock threshold
   */
  public function paginateWithDetails($page = 1, $perPage = 15, $conditions = [], $orderBy = null) {
    $page = max(1, (int)$page);
    $perPage = min(100, max(1, (int)$perPage));
    $offset = ($page - 1) * $perPage;
    
    // Get total count
    $totalCount = $this->count($conditions);
    
    // Build query with category names
    $sql = "SELECT p.*, c.name as category_name 
            FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id";
    $params = [];
    
    if (!empty($conditions)) {
      $whereParts = [];
      foreach ($conditions as $column => $value) {
        if ($column === 'search') {
          $whereParts[] = "(p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)";
          $params[] = "%{$value}%";
          $params[] = "%{$value}%";
          $params[] = "%{$value}%";
        } else {
          $column = $this->sanitizeColumnName($column);
          $whereParts[] = "p.{$column} = ?";
          $params[] = $value;
        }
      }
      $sql .= " WHERE " . implode(' AND ', $whereParts);
    }
    
    if ($orderBy) {
      $orderBy = $this->sanitizeOrderBy($orderBy);
      $sql .= " ORDER BY p.{$orderBy}";
    }
    
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $perPage;
    $params[] = $offset;
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll();
    
    // Add low stock threshold to each product
    foreach ($data as &$product) {
      $product['low_stock_threshold'] = 10; // Default threshold
    }
    
    return [
      'data' => $data,
      'current_page' => $page,
      'per_page' => $perPage,
      'total' => $totalCount,
      'last_page' => ceil($totalCount / $perPage),
      'from' => $offset + 1,
      'to' => min($offset + $perPage, $totalCount)
    ];
  }
  
  /**
   * Create product with slug generation
   */
  public function createProduct($data) {
    // Generate slug if not provided
    if (empty($data['slug'])) {
      $data['slug'] = $this->generateUniqueSlug($data['name']);
    }
    
    return $this->create($data);
  }
  
  /**
   * Generate unique slug
   */
  private function generateUniqueSlug($name, $id = null) {
    $baseSlug = generateSlug($name);
    $slug = $baseSlug;
    $counter = 1;
    
    while ($this->slugExists($slug, $id)) {
      $slug = $baseSlug . '-' . $counter;
      $counter++;
    }
    
    return $slug;
  }
  
  /**
   * Check if slug exists
   */
  private function slugExists($slug, $excludeId = null) {
    $sql = "SELECT COUNT(*) FROM {$this->table} WHERE slug = ?";
    $params = [$slug];
    
    if ($excludeId) {
      $sql .= " AND id != ?";
      $params[] = $excludeId;
    }
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() > 0;
  }
}
?>
