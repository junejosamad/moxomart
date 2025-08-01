<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - Moxo Mart</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
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
            color: #fc4a1a;
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
            background: #fc4a1a;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0 10px;
        }
        .btn:hover {
            background: #e04016;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(252, 74, 26, 0.4);
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }
        .btn-secondary:hover {
            background: #cbd5e0;
            color: #2d3748;
        }
        .status-info {
            margin-top: 30px;
            padding: 20px;
            background: #fff5f5;
            border-left: 4px solid #fc4a1a;
            border-radius: 5px;
            text-align: left;
        }
        .status-info h4 {
            color: #fc4a1a;
            margin: 0 0 10px 0;
        }
        .status-info p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">500</h1>
        <h2 class="error-message">Internal Server Error</h2>
        <p class="error-description">
            We're experiencing some technical difficulties. Our team has been notified and is working to fix the issue. Please try again in a few minutes.
        </p>
        
        <div>
            <a href="<?= url('/') ?>" class="btn">Go Home</a>
            <a href="javascript:location.reload()" class="btn btn-secondary">Try Again</a>
        </div>
        
        <div class="status-info">
            <h4>What can you do?</h4>
            <p>• Try refreshing the page<br>
            • Go back to the previous page<br>
            • Visit our homepage<br>
            • Contact our support team if the issue persists</p>
        </div>
        
        <?php if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development'): ?>
        <div style="margin-top: 30px; padding: 20px; background: #f7fafc; border-radius: 5px; text-align: left;">
            <h4 style="color: #2d3748; margin: 0 0 10px 0;">Debug Information (Development Mode)</h4>
            <p style="margin: 0; color: #4a5568; font-size: 12px; font-family: monospace;">
                Error occurred at: <?= date('Y-m-d H:i:s') ?><br>
                Request URI: <?= $_SERVER['REQUEST_URI'] ?? 'unknown' ?><br>
                Request Method: <?= $_SERVER['REQUEST_METHOD'] ?? 'unknown' ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html> 