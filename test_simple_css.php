<!DOCTYPE html>
<html>
<head>
    <title>Simple CSS Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-box { padding: 20px; margin: 10px; border: 2px solid #007bff; background: #f8f9fa; }
        .success { border-color: #28a745; background: #d4edda; }
        .error { border-color: #dc3545; background: #f8d7da; }
    </style>
    
    <!-- Test different CSS loading methods -->
    
    <!-- Method 1: Generated URL via PHP -->
    <?php 
    require_once __DIR__ . '/app/Core/helpers.php';
    echo '<link rel="stylesheet" href="' . asset('css/main.min.css') . '" id="css-generated">';
    ?>
    
    <!-- Method 2: Direct path -->
    <link rel="stylesheet" href="/assets/css/main.min.css" id="css-direct">
    
    <!-- Method 3: Full URL -->
    <link rel="stylesheet" href="http://<?= $_SERVER['HTTP_HOST'] ?>/assets/css/main.min.css" id="css-full">
    
    <!-- Method 4: Public folder path -->
    <link rel="stylesheet" href="/public/assets/css/main.min.css" id="css-public">
    
</head>
<body>

<h1>CSS Loading Test</h1>

<div class="test-box">
    <h3>This box should have a blue border if inline CSS works</h3>
    <p>If you can see this styled box, the HTML/CSS rendering is working.</p>
</div>

<div class="test-box" id="bootstrap-test">
    <h3>Bootstrap Test</h3>
    <p>This should be styled by Bootstrap if main.min.css loads:</p>
    <button class="btn btn-primary">Primary Button</button>
    <div class="alert alert-success">Success Alert</div>
</div>

<div class="test-box">
    <h3>CSS Loading Status</h3>
    <div id="css-status">Testing CSS files...</div>
</div>

<div class="test-box">
    <h3>Manual Tests</h3>
    <p>Click these links to test CSS files directly:</p>
    <ul>
        <li><a href="<?= asset('css/main.min.css') ?>" target="_blank">Generated URL</a></li>
        <li><a href="/assets/css/main.min.css" target="_blank">Direct Path</a></li>
        <li><a href="http://<?= $_SERVER['HTTP_HOST'] ?>/assets/css/main.min.css" target="_blank">Full HTTP URL</a></li>
        <li><a href="/public/assets/css/main.min.css" target="_blank">Public Path</a></li>
    </ul>
</div>

<script>
function checkCSSLoad() {
    const results = [];
    const links = document.querySelectorAll('link[rel="stylesheet"]');
    
    links.forEach(link => {
        const id = link.id;
        const href = link.href;
        
        // Try to access the stylesheet
        try {
            const sheets = document.styleSheets;
            let loaded = false;
            
            for (let i = 0; i < sheets.length; i++) {
                if (sheets[i].href === href) {
                    try {
                        // Try to access rules to see if CSS loaded
                        sheets[i].cssRules;
                        loaded = true;
                        break;
                    } catch (e) {
                        // CSS might be loaded but from different origin
                        loaded = true;
                        break;
                    }
                }
            }
            
            results.push(`${id}: ${loaded ? '✅ LOADED' : '❌ FAILED'} - ${href}`);
        } catch (e) {
            results.push(`${id}: ❌ ERROR - ${href} - ${e.message}`);
        }
    });
    
    document.getElementById('css-status').innerHTML = results.join('<br>');
}

// Check after page loads
window.addEventListener('load', () => {
    setTimeout(checkCSSLoad, 1000);
});
</script>

</body>
</html>