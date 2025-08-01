<?php

namespace App\Controllers;

use App\Core\Controller;

class BlogController extends Controller
{
    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $category = $_GET['category'] ?? '';
        $search = $_GET['search'] ?? '';

        $conditions = ['status' => 'published'];
        if ($category) {
            $conditions['category_id'] = $category;
        }

        // Get blog posts
        $posts = $this->getBlogPosts($page, 12, $conditions, $search);
        
        // Get categories for filter
        $categories = $this->getBlogCategories();

        $meta = [
            'title' => 'Blog - Moxo Mart',
            'description' => 'Read our latest articles, tips, and updates'
        ];

        return $this->render('blog/index', [
            'posts' => $posts,
            'categories' => $categories,
            'selectedCategory' => $category,
            'search' => $search,
            'meta' => $meta
        ]);
    }

    public function show($slug)
    {
        $post = $this->getBlogPostBySlug($slug);
        
        if (!$post || $post['status'] !== 'published') {
            http_response_code(404);
            return $this->render('errors/404');
        }

        // Get related posts
        $relatedPosts = $this->getRelatedPosts($post['id'], $post['category_id'], 3);

        // Get author info
        $author = $this->getAuthor($post['author_id']);

        $meta = [
            'title' => $post['meta_title'] ?: $post['title'] . ' - Moxo Mart Blog',
            'description' => $post['meta_description'] ?: $post['excerpt'],
            'og_title' => $post['title'],
            'og_description' => $post['excerpt'],
            'og_image' => $post['featured_image'] ? asset($post['featured_image']) : null
        ];

        return $this->render('blog/show', [
            'post' => $post,
            'author' => $author,
            'relatedPosts' => $relatedPosts,
            'meta' => $meta
        ]);
    }

    private function getBlogPosts($page = 1, $perPage = 12, $conditions = [], $search = '')
    {
        $offset = ($page - 1) * $perPage;
        
        // Build WHERE clause
        $whereParts = [];
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $whereParts[] = "bp.{$column} = ?";
            $params[] = $value;
        }
        
        if ($search) {
            $whereParts[] = "(bp.title LIKE ? OR bp.excerpt LIKE ? OR bp.content LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        $whereClause = !empty($whereParts) ? 'WHERE ' . implode(' AND ', $whereParts) : '';
        
        // Get total count
        $countSql = "SELECT COUNT(*) FROM blog_posts bp {$whereClause}";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $totalCount = $stmt->fetchColumn();
        
        // Get posts
        $sql = "SELECT bp.*, u.first_name, u.last_name, c.name as category_name 
                FROM blog_posts bp 
                JOIN users u ON bp.author_id = u.id 
                LEFT JOIN categories c ON bp.category_id = c.id 
                {$whereClause}
                ORDER BY bp.published_at DESC 
                LIMIT {$perPage} OFFSET {$offset}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
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

    private function getBlogPostBySlug($slug)
    {
        $sql = "SELECT bp.*, u.first_name, u.last_name, c.name as category_name 
                FROM blog_posts bp 
                JOIN users u ON bp.author_id = u.id 
                LEFT JOIN categories c ON bp.category_id = c.id 
                WHERE bp.slug = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    private function getBlogCategories()
    {
        $sql = "SELECT c.*, COUNT(bp.id) as post_count 
                FROM categories c 
                LEFT JOIN blog_posts bp ON c.id = bp.category_id AND bp.status = 'published'
                WHERE c.is_active = 1 
                GROUP BY c.id 
                HAVING post_count > 0 
                ORDER BY c.name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getRelatedPosts($postId, $categoryId, $limit = 3)
    {
        if (!$categoryId) {
            return [];
        }

        $sql = "SELECT bp.*, u.first_name, u.last_name 
                FROM blog_posts bp 
                JOIN users u ON bp.author_id = u.id 
                WHERE bp.category_id = ? 
                AND bp.id != ? 
                AND bp.status = 'published' 
                ORDER BY bp.published_at DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId, $postId, $limit]);
        return $stmt->fetchAll();
    }

    private function getAuthor($authorId)
    {
        $sql = "SELECT id, first_name, last_name, email FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$authorId]);
        return $stmt->fetch();
    }
} 