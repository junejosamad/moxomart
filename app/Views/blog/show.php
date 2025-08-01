<?php
/**
 * Blog Post Detail View
 * Display individual blog post
 */

$pageTitle = $post['title'];
$metaTags = generateMetaTags(
    $post['meta_title'] ?: $post['title'],
    $post['meta_description'] ?: $post['excerpt'] ?: truncate(strip_tags($post['content']), 160),
    $post['featured_image'] ? asset('uploads/' . $post['featured_image']) : null,
    url('/blog/' . $post['slug'])
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
    <meta property="og:type" content="article">
    <meta property="article:published_time" content="<?= date('c', strtotime($post['published_at'])) ?>">
    <meta property="article:author" content="<?= e($post['author_name']) ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($metaTags['twitter_title']) ?>">
    <meta name="twitter:description" content="<?= e($metaTags['twitter_description']) ?>">
    <meta name="twitter:image" content="<?= $metaTags['twitter_image'] ?>">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "<?= e($post['title']) ?>",
        "description": "<?= e($post['excerpt'] ?: truncate(strip_tags($post['content']), 160)) ?>",
        "image": "<?= $post['featured_image'] ? asset('uploads/' . $post['featured_image']) : '' ?>",
        "author": {
            "@type": "Person",
            "name": "<?= e($post['author_name']) ?>"
        },
        "publisher": {
            "@type": "Organization",
            "name": "<?= e(getSetting('site_name', 'SadaCart')) ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?= asset('images/logo.png') ?>"
            }
        },
        "datePublished": "<?= date('c', strtotime($post['published_at'])) ?>",
        "dateModified": "<?= date('c', strtotime($post['updated_at'])) ?>",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "<?= url('/blog/' . $post['slug']) ?>"
        }
    }
    </script>
    
    <link href="<?= asset('css/main.min.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/themes/prism.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../app/Views/layouts/header.php'; ?>
    
    <main class="main-content">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <div class="container">
                <ol class="breadcrumb-list">
                    <li><a href="/">Home</a></li>
                    <li><a href="/blog">Blog</a></li>
                    <?php if ($post['category_name']): ?>
                        <li><a href="/blog/category/<?= e($post['category_slug']) ?>"><?= e($post['category_name']) ?></a></li>
                    <?php endif; ?>
                    <li class="active"><?= e(truncate($post['title'], 50)) ?></li>
                </ol>
            </div>
        </nav>

        <!-- Blog Post -->
        <article class="blog-post">
            <div class="container">
                <div class="blog-post-layout">
                    <!-- Main Content -->
                    <div class="blog-post-main">
                        <header class="blog-post-header">
                            <?php if ($post['category_name']): ?>
                                <div class="blog-category">
                                    <a href="/blog/category/<?= e($post['category_slug']) ?>">
                                        <?= e($post['category_name']) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <h1 class="blog-post-title"><?= e($post['title']) ?></h1>
                            
                            <div class="blog-post-meta">
                                <div class="meta-item">
                                    <i class="fas fa-user"></i>
                                    <span>By <?= e($post['author_name']) ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <time datetime="<?= date('c', strtotime($post['published_at'])) ?>">
                                        <?= formatDate($post['published_at'], 'F j, Y') ?>
                                    </time>
                                </div>
                                <?php if ($post['reading_time']): ?>
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?= $post['reading_time'] ?> min read</span>
                                    </div>
                                <?php endif; ?>
                                <div class="meta-item">
                                    <i class="fas fa-eye"></i>
                                    <span><?= number_format($post['views'] ?? 0) ?> views</span>
                                </div>
                            </div>

                            <?php if ($post['featured_image']): ?>
                                <div class="blog-post-image">
                                    <img src="<?= asset('uploads/' . $post['featured_image']) ?>" 
                                         alt="<?= e($post['title']) ?>">
                                </div>
                            <?php endif; ?>
                        </header>

                        <div class="blog-post-content">
                            <?= $post['content'] ?>
                        </div>

                        <!-- Tags -->
                        <?php if (!empty($post['tags'])): ?>
                            <div class="blog-post-tags">
                                <h4>Tags:</h4>
                                <div class="tag-list">
                                    <?php foreach (explode(',', $post['tags']) as $tag): ?>
                                        <a href="/blog?tag=<?= urlencode(trim($tag)) ?>" class="tag">
                                            <?= e(trim($tag)) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Social Share -->
                        <div class="blog-post-share">
                            <h4>Share this post:</h4>
                            <div class="share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(url('/blog/' . $post['slug'])) ?>" 
                                   target="_blank" class="share-btn facebook">
                                    <i class="fab fa-facebook-f"></i>
                                    Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?= urlencode(url('/blog/' . $post['slug'])) ?>&text=<?= urlencode($post['title']) ?>" 
                                   target="_blank" class="share-btn twitter">
                                    <i class="fab fa-twitter"></i>
                                    Twitter
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(url('/blog/' . $post['slug'])) ?>" 
                                   target="_blank" class="share-btn linkedin">
                                    <i class="fab fa-linkedin-in"></i>
                                    LinkedIn
                                </a>
                                <a href="mailto:?subject=<?= urlencode($post['title']) ?>&body=<?= urlencode('Check out this article: ' . url('/blog/' . $post['slug'])) ?>" 
                                   class="share-btn email">
                                    <i class="fas fa-envelope"></i>
                                    Email
                                </a>
                            </div>
                        </div>

                        <!-- Author Bio -->
                        <?php if (!empty($post['author_bio'])): ?>
                            <div class="author-bio">
                                <div class="author-avatar">
                                    <?php if ($post['author_avatar']): ?>
                                        <img src="<?= asset('uploads/' . $post['author_avatar']) ?>" 
                                             alt="<?= e($post['author_name']) ?>">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <?= strtoupper(substr($post['author_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="author-info">
                                    <h4>About <?= e($post['author_name']) ?></h4>
                                    <p><?= e($post['author_bio']) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Related Posts -->
                        <?php if (!empty($relatedPosts)): ?>
                            <div class="related-posts">
                                <h3>Related Posts</h3>
                                <div class="related-posts-grid">
                                    <?php foreach ($relatedPosts as $relatedPost): ?>
                                        <article class="related-post">
                                            <?php if ($relatedPost['featured_image']): ?>
                                                <div class="related-post-image">
                                                    <a href="/blog/<?= e($relatedPost['slug']) ?>">
                                                        <img src="<?= asset('uploads/' . $relatedPost['featured_image']) ?>" 
                                                             alt="<?= e($relatedPost['title']) ?>">
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            <div class="related-post-content">
                                                <h4>
                                                    <a href="/blog/<?= e($relatedPost['slug']) ?>">
                                                        <?= e($relatedPost['title']) ?>
                                                    </a>
                                                </h4>
                                                <div class="related-post-meta">
                                                    <span class="date">
                                                        <?= formatDate($relatedPost['published_at'], 'M j, Y') ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Navigation -->
                        <nav class="post-navigation">
                            <?php if (!empty($previousPost)): ?>
                                <div class="nav-previous">
                                    <a href="/blog/<?= e($previousPost['slug']) ?>">
                                        <i class="fas fa-chevron-left"></i>
                                        <div class="nav-content">
                                            <span class="nav-label">Previous Post</span>
                                            <span class="nav-title"><?= e($previousPost['title']) ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($nextPost)): ?>
                                <div class="nav-next">
                                    <a href="/blog/<?= e($nextPost['slug']) ?>">
                                        <div class="nav-content">
                                            <span class="nav-label">Next Post</span>
                                            <span class="nav-title"><?= e($nextPost['title']) ?></span>
                                        </div>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </nav>
                    </div>

                    <!-- Sidebar -->
                    <aside class="blog-sidebar">
                        <!-- Table of Contents -->
                        <div class="sidebar-widget toc-widget">
                            <h3>Table of Contents</h3>
                            <div id="table-of-contents">
                                <!-- Generated by JavaScript -->
                            </div>
                        </div>

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
        </article>
    </main>
    
    <?php include '../app/Views/layouts/footer.php'; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/plugins/autoloader/prism-autoloader.min.js"></script>
    <script src="<?= asset('js/app.min.js') ?>"></script>
    <script>
        // Generate table of contents
        document.addEventListener('DOMContentLoaded', function() {
            const headings = document.querySelectorAll('.blog-post-content h2, .blog-post-content h3, .blog-post-content h4');
            const toc = document.getElementById('table-of-contents');
            
            if (headings.length > 0) {
                const tocList = document.createElement('ul');
                
                headings.forEach((heading, index) => {
                    const id = 'heading-' + index;
                    heading.id = id;
                    
                    const li = document.createElement('li');
                    li.className = 'toc-' + heading.tagName.toLowerCase();
                    
                    const link = document.createElement('a');
                    link.href = '#' + id;
                    link.textContent = heading.textContent;
                    
                    li.appendChild(link);
                    tocList.appendChild(li);
                });
                
                toc.appendChild(tocList);
            } else {
                toc.innerHTML = '<p>No headings found</p>';
            }
        });

        // Smooth scrolling for TOC links
        document.addEventListener('click', function(e) {
            if (e.target.matches('#table-of-contents a')) {
                e.preventDefault();
                const target = document.querySelector(e.target.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    </script>
</body>
</html>
