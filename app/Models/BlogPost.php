<?php

namespace App\Models;

use App\Core\Model;

class BlogPost extends Model
{
    protected $table = 'blog_posts';
    protected $fillable = ['title', 'slug', 'content', 'excerpt', 'featured_image', 'author_id', 'category_id', 'status', 'meta_title', 'meta_description', 'tags', 'published_at'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get published blog posts
     */
    public function getPublishedPosts($limit = 10, $offset = 0, $categoryId = null)
    {
        $sql = "SELECT bp.*, u.first_name as author_name, c.name as category_name 
                FROM {$this->table} bp 
                LEFT JOIN users u ON bp.author_id = u.id 
                LEFT JOIN categories c ON bp.category_id = c.id 
                WHERE bp.status = 'published' AND bp.published_at <= NOW()";
        
        $params = [];
        
        if ($categoryId) {
            $sql .= " AND bp.category_id = ?";
            $params[] = $categoryId;
        }
        
        $sql .= " ORDER BY bp.published_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get blog post by slug
     */
    public function getBySlug($slug)
    {
        $sql = "SELECT bp.*, u.first_name as author_name, c.name as category_name 
                FROM {$this->table} bp 
                LEFT JOIN users u ON bp.author_id = u.id 
                LEFT JOIN categories c ON bp.category_id = c.id 
                WHERE bp.slug = ? AND bp.status = 'published' AND bp.published_at <= NOW()";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    /**
     * Get featured posts
     */
    public function getFeaturedPosts($limit = 5)
    {
        $sql = "SELECT bp.*, u.first_name as author_name, c.name as category_name 
                FROM {$this->table} bp 
                LEFT JOIN users u ON bp.author_id = u.id 
                LEFT JOIN categories c ON bp.category_id = c.id 
                WHERE bp.status = 'published' AND bp.published_at <= NOW() 
                ORDER BY bp.published_at DESC LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get related posts
     */
    public function getRelatedPosts($postId, $categoryId, $limit = 4)
    {
        $sql = "SELECT bp.*, u.first_name as author_name, c.name as category_name 
                FROM {$this->table} bp 
                LEFT JOIN users u ON bp.author_id = u.id 
                LEFT JOIN categories c ON bp.category_id = c.id 
                WHERE bp.status = 'published' AND bp.published_at <= NOW() 
                AND bp.id != ? AND bp.category_id = ? 
                ORDER BY bp.published_at DESC LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$postId, $categoryId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Search blog posts
     */
    public function searchPosts($query, $limit = 10, $offset = 0)
    {
        $searchTerm = "%{$query}%";
        $sql = "SELECT bp.*, u.first_name as author_name, c.name as category_name 
                FROM {$this->table} bp 
                LEFT JOIN users u ON bp.author_id = u.id 
                LEFT JOIN categories c ON bp.category_id = c.id 
                WHERE bp.status = 'published' AND bp.published_at <= NOW() 
                AND (bp.title LIKE ? OR bp.content LIKE ?) 
                ORDER BY bp.published_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Create blog post
     */
    public function createPost($data)
    {
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['title']);
        }

        // Set published_at if status is published
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }

    /**
     * Update blog post
     */
    public function updatePost($postId, $data)
    {
        // Update slug if title changed
        if (isset($data['title'])) {
            $data['slug'] = $this->generateSlug($data['title'], $postId);
        }

        // Set published_at if status changed to published
        if (isset($data['status']) && $data['status'] === 'published') {
            $currentPost = $this->find($postId);
            if ($currentPost && $currentPost['status'] !== 'published') {
                $data['published_at'] = date('Y-m-d H:i:s');
            }
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->update($postId, $data);
    }

    /**
     * Generate unique slug
     */
    private function generateSlug($title, $excludeId = null)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    private function slugExists($slug, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = ?";
        $params = [$slug];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Get posts by author
     */
    public function getPostsByAuthor($authorId, $limit = 10, $offset = 0)
    {
        $sql = "SELECT bp.*, u.first_name as author_name, c.name as category_name 
                FROM {$this->table} bp 
                LEFT JOIN users u ON bp.author_id = u.id 
                LEFT JOIN categories c ON bp.category_id = c.id 
                WHERE bp.author_id = ? AND bp.status = 'published' AND bp.published_at <= NOW() 
                ORDER BY bp.published_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$authorId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Get post statistics
     */
    public function getPostStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total_posts,
                    SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published_posts,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_posts,
                    SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured_posts
                FROM {$this->table}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get recent blog posts
     */
    public function getRecent($limit = 5)
    {
        $sql = "SELECT bp.*, u.first_name as author_name, c.name as category_name 
                FROM {$this->table} bp 
                LEFT JOIN users u ON bp.author_id = u.id 
                LEFT JOIN categories c ON bp.category_id = c.id 
                WHERE bp.status = 'published' AND bp.published_at <= NOW() 
                ORDER BY bp.published_at DESC LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Increment view count
     */
    public function incrementViews($postId)
    {
        $sql = "UPDATE {$this->table} SET views = views + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$postId]);
    }
}
