<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name', 'slug', 'description', 'image', 'parent_id', 
        'sort_order', 'is_active'
    ];

    public function getBySlug($slug)
    {
        return $this->findBy('slug', $slug);
    }

    public function getMainCategories()
    {
        return $this->where(['parent_id' => null, 'is_active' => 1], 'sort_order ASC, name ASC');
    }

    public function getSubCategories($parentId)
    {
        return $this->where(['parent_id' => $parentId, 'is_active' => 1], 'sort_order ASC, name ASC');
    }

    public function getWithProductCount()
    {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM categories c 
                LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'
                WHERE c.is_active = 1 
                GROUP BY c.id 
                ORDER BY c.sort_order ASC, c.name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getHierarchy()
    {
        $categories = $this->all('sort_order ASC, name ASC');
        return $this->buildTree($categories);
    }

    private function buildTree($categories, $parentId = null)
    {
        $tree = [];
        
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $category['children'] = $this->buildTree($categories, $category['id']);
                $tree[] = $category;
            }
        }
        
        return $tree;
    }

    public function getBreadcrumb($categoryId)
    {
        $breadcrumb = [];
        $category = $this->find($categoryId);
        
        while ($category) {
            array_unshift($breadcrumb, $category);
            $category = $category['parent_id'] ? $this->find($category['parent_id']) : null;
        }
        
        return $breadcrumb;
    }

    public function hasProducts($categoryId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetchColumn() > 0;
    }

    public function hasSubCategories($categoryId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM categories WHERE parent_id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getPopular($limit = 6)
    {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM categories c 
                LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'
                WHERE c.is_active = 1 AND c.parent_id IS NULL
                GROUP BY c.id 
                HAVING product_count > 0
                ORDER BY product_count DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
