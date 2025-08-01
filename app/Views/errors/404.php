<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Moxo Mart</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 40px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #667eea;
            margin: 0;
            line-height: 1;
        }
        .error-message {
            font-size: 24px;
            color: #666;
            margin: 20px 0;
        }
        .error-description {
            font-size: 16px;
            color: #888;
            margin: 20px 0 40px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0 10px;
        }
        .btn:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }
        .btn-secondary:hover {
            background: #cbd5e0;
            color: #2d3748;
        }
        .suggestions {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
        }
        .suggestions h3 {
            color: #4a5568;
            margin-bottom: 20px;
        }
        .suggestions ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .suggestions li {
            margin: 10px 0;
        }
        .suggestions a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .suggestions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">404</h1>
        <h2 class="error-message">Page Not Found</h2>
        <p class="error-description">
            Oops! The page you're looking for doesn't exist. It might have been moved, deleted, or you entered the wrong URL.
        </p>
        
        <div>
            <a href="<?= url('/') ?>" class="btn">Go Home</a>
            <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
        </div>
        
        <div class="suggestions">
            <h3>You might be looking for:</h3>
            <ul>
                <li><a href="<?= url('/products') ?>">Browse Products</a></li>
                <li><a href="<?= url('/blog') ?>">Read Our Blog</a></li>
                <li><a href="<?= url('/contact') ?>">Contact Support</a></li>
                <li><a href="<?= url('/faq') ?>">FAQ</a></li>
            </ul>
        </div>
    </div>
</body>
</html> 