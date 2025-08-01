<?php
/**
 * Blog Index View
 * Display blog posts listing
 */

$pageTitle = 'Blog';
$metaTags = generateMetaTags(
    'Blog - Latest News & Updates',
    'Stay updated with the latest news, tips, and updates from SadaCart.',
    asset('images/blog-og.jpg')
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTags['title']) ?></title>
    <meta name="description" content="<?= e($metaTags['description']) ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= e($metaTags['og_title']) ?>">
    <meta property="og:description" content="<?= e($metaTags['og_description']) ?>">
    <meta property="og:image" content="<?= $metaTags['og_image'] ?>">
    <meta property="og:url" content="<?= $metaTags['og_url'] ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:title" content="<?= e($metaTags['twitter_title']) ?>">
    <meta name="twitter:description" content="<?= e($metaTags['twitter_description']) ?>">
    <meta name="twitter:image" content="<?= $metaTags['twitter_image'] ?>">
    
    <link href="<?= asset('css/main.min.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../app/Views/layouts/header.php'; ?>
    
    <main class="main-content">
        <!-- Blog Header -->
        <section class="blog-header">
            <div class="container">
                <div class="blog-header-content">
                    <h1>Our Blog</h1>
                    <p>Stay updated with the latest news, tips, and insights from the SadaCart team.</p>
                </div>
            </div>
        </section>

        <!-- Blog Content -->
        <section class="blog-section">
            <div class="container">
                <div class="blog-layout">
                    <!-- Main Content -->
                    <div class="blog-main">
                        <?php if (!empty($posts['data'])): ?>
                            <div class="blog-grid">
                                <?php foreach ($posts['data'] as $index => $post): ?>
                                    <article class="blog-card <?= $index === 0 ? 'featured' : '' ?>">
                                        <?php if ($post['featured_image']): ?>
                                            <div class="blog-card-image">
                                                <a href="/blog/<?= e($post['slug']) ?>">
                                                    <img src="<?= asset('uploads/' . $post['featured_image']) ?>" 
                                                         alt="<?= e($post['title']) ?>" 
                                                         loading="lazy">
                                                </a>
                                                <?php if ($post['category_name']): ?>
                                                    <span class="blog-category">
                                                        <a href="/blog/category/<?= e($post['category_slug']) ?>">
                                                            <?= e($post['category_name']) ?>
                                                        </a>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="blog-card-content">
                                            <div class="blog-meta">
                                                <span class="blog-date">
                                                    <i class="fas fa-calendar"></i>
                                                    <?= formatDate($post['published_at'], 'M j, Y') ?>
                                                </span>
                                                <span class="blog-author">
                                                    <i class="fas fa-user"></i>
                                                    <?= e($post['author_name']) ?>
                                                </span>
                                                <?php if ($post['reading_time']): ?>
                                                    <span class="blog-reading-time">
                                                        <i class="fas fa-clock"></i>
                                                        <?= $post['reading_time'] ?> min read
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <h2 class="blog-title">
                                                <a href="/blog/<?= e($post['slug']) ?>">
                                                    <?= e($post['title']) ?>
                                                </a>
                                            </h2>
                                            
                                            <p class="blog-excerpt">
                                                <?= e($post['excerpt'] ?: truncate(strip_tags($post['content']), 150)) ?>
                                            </p>
                                            
                                            <div class="blog-footer">
                                                <a href="/blog/<?= e($post['slug']) ?>" class="read-more">
                                                    Read More
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                                
                                                <?php if (!empty($post['tags'])): ?>
                                                    <div class="blog-tags">
                                                        <?php foreach (explode(',', $post['tags']) as $tag): ?>
                                                            <span class="tag"><?= e(trim($tag)) ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>

                            <!-- Pagination -->
                            <?php if ($posts['last_page'] > 1): ?>
                                <div class="pagination">
                                    <?php if ($posts['current_page'] > 1): ?>
                                        <a href="?page=<?= $posts['current_page'] - 1 ?>" 
                                           class="pagination-btn">
                                            <i class="fas fa-chevron-left"></i>
                                            Previous
                                        </a>
                                    <?php endif; ?>

                                    <div class="pagination-numbers">
                                        <?php
                                        $start = max(1, $posts['current_page'] - 2);
                                        $end = min($posts['last_page'], $posts['current_page'] + 2);
                                        ?>
                                        
                                        <?php if ($start > 1): ?>
                                            <a href="?page=1" class="pagination-number">1</a>
                                            <?php if ($start > 2): ?>
                                                <span class="pagination-ellipsis">...</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = $start; $i <= $end; $i++): ?>
                                            <?php if ($i == $posts['current_page']): ?>
                                                <span class="pagination-number active"><?= $i ?></span>
                                            <?php else: ?>
                                                <a href="?page=<?= $i ?>" class="pagination-number"><?= $i ?></a>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        
                                        <?php if ($end < $posts['last_page']): ?>
                                            <?php if ($end < $posts['last_page'] - 1): ?>
                                                <span class="pagination-ellipsis">...</span>
                                            <?php endif; ?>
                                            <a href="?page=<?= $posts['last_page'] ?>" class="pagination-number"><?= $posts['last_page'] ?></a>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($posts['current_page'] < $posts['last_page']): ?>
                                        <a href="?page=<?= $posts['current_page'] + 1 ?>" 
                                           class="pagination-btn">
                                            Next
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-newspaper"></i>
                                <h3>No Blog Posts Yet</h3>
                                <p>We're working on some great content. Check back soon!</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar -->
                    <aside class="blog-sidebar">
                        <!-- Search -->
                        <div class="sidebar-widget">
                            <h3>Search</h3>
                            <form method="GET" action="/blog" class="search-form">
                                <div class="search-input-group">
                                    <input type="text" name="search" 
                                           placeholder="Search posts..." 
                                           value="<?= e($_GET['search'] ?? '') ?>">
                                    <button type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Categories -->
                        <?php if (!empty($categories)): ?>
                            <div class="sidebar-widget">
                                <h3>Categories</h3>
                                <ul class="category-list">
                                    <?php foreach ($categories as $category): ?>
                                        <li>
                                            <a href="/blog/category/<?= e($category['slug']) ?>">
                                                <?= e($category['name']) ?>
                                                <span class="post-count">(<?= $category['post_count'] ?>)</span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- Recent Posts -->
                        <?php if (!empty($recentPosts)): ?>
                            <div class="sidebar-widget">
                                <h3>Recent Posts</h3>
                                <div class="recent-posts">
                                    <?php foreach ($recentPosts as $recentPost): ?>
                                        <article class="recent-post">
                                            <?php if ($recentPost['featured_image']): ?>
                                                <div class="recent-post-image">
                                                    <a href="/blog/<?= e($recentPost['slug']) ?>">
                                                        <img src="<?= asset('uploads/' . $recentPost['featured_image']) ?>" 
                                                             alt="<?= e($recentPost['title']) ?>">
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            <div class="recent-post-content">
                                                <h4>
                                                    <a href="/blog/<?= e($recentPost['slug']) ?>">
                                                        <?= e($recentPost['title']) ?>
                                                    </a>
                                                </h4>
                                                <div class="recent-post-meta">
                                                    <span class="date">
                                                        <?= formatDate($recentPost['published_at'], 'M j, Y') ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Popular Tags -->
                        <?php if (!empty($popularTags)): ?>
                            <div class="sidebar-widget">
                                <h3>Popular Tags</h3>
                                <div class="tag-cloud">
                                    <?php foreach ($popularTags as $tag): ?>
                                        <a href="/blog?tag=<?= urlencode($tag['name']) ?>" 
                                           class="tag-link" 
                                           style="font-size: <?= min(1.2, 0.8 + ($tag['count'] / 10)) ?>em;">
                                            <?= e($tag['name']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Newsletter Signup -->
                        <div class="sidebar-widget newsletter-widget">
                            <h3>Stay Updated</h3>
                            <p>Subscribe to our newsletter for the latest updates and exclusive content.</p>
                            <form class="newsletter-form" data-newsletter-form>
                                <input type="email" name="email" placeholder="Your email address" required>
                                <button type="submit">
                                    <i class="fas fa-paper-plane"></i>
                                    Subscribe
                                </button>
                            </form>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </main>
    
    <?php include '../app/Views/layouts/footer.php'; ?>
    
    <script src="<?= asset('js/app.min.js') ?>"></script>
</body>
</html>
