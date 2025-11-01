<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TinyMCE Debug</title>
    <link rel="stylesheet" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="module" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .debug-info { background: #f0f0f0; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>TinyMCE Debug Test</h1>
    
    <div class="debug-info">
        <h3>Debug Information:</h3>
        <div id="debug-content">
            <p>Loading debug information...</p>
        </div>
    </div>

    <h3>Test Editor:</h3>
    <form id="test-form" onsubmit="return testSubmit(event);">
        <textarea id="content" name="content" style="width: 100%; height: 200px;">
            This is a test to see if TinyMCE loads properly.
        </textarea>
        <div style="margin-top: 20px;">
            <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Test Content Saving</button>
            <button type="button" onclick="getContent()" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Get Content</button>
        </div>
    </form>

    <div id="test-result" style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; display: none;"></div>

    <script>
        function testSubmit(event) {
            event.preventDefault();
            
            // Save TinyMCE content to textarea
            if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                tinymce.get('content').save();
            }
            
            const formData = new FormData(document.getElementById('test-form'));
            const content = formData.get('content');
            
            const resultDiv = document.getElementById('test-result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `
                <h4>Form Submission Test Results:</h4>
                <p><strong>Content Length:</strong> ${content.length} characters</p>
                <p><strong>Content Preview:</strong></p>
                <pre style="background: #e9ecef; padding: 10px; border-radius: 4px; max-height: 200px; overflow-y: auto;">${content.substring(0, 500)}${content.length > 500 ? '...' : ''}</pre>
                <p style="color: green; font-weight: bold;">✅ Content saving test successful!</p>
            `;
            
            return false;
        }
        
        function getContent() {
            let content = '';
            if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                content = tinymce.get('content').getContent();
            } else {
                content = document.getElementById('content').value;
            }
            
            const resultDiv = document.getElementById('test-result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `
                <h4>Current Editor Content:</h4>
                <pre style="background: #e9ecef; padding: 10px; border-radius: 4px; max-height: 300px; overflow-y: auto;">${content}</pre>
            `;
        }

        function updateDebug(message, type = 'info') {
            const debugDiv = document.getElementById('debug-content');
            const className = type === 'success' ? 'success' : (type === 'error' ? 'error' : '');
            debugDiv.innerHTML += `<p class="${className}">${new Date().toLocaleTimeString()}: ${message}</p>`;
        }

        // Check if TinyMCE is available
        updateDebug('TinyMCE object available: ' + (typeof tinymce !== 'undefined'));
        
        if (typeof tinymce !== 'undefined') {
            updateDebug('TinyMCE version: ' + (tinymce.majorVersion + '.' + tinymce.minorVersion), 'success');
            
            // Initialize TinyMCE with better configuration
                tinymce.init({
                    selector: '#content',
                    height: 400,
                    menubar: false,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'help', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | preview | code',
                    toolbar_mode: 'floating',
                    content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
                    setup: function (editor) {
                        editor.on('init', function() {
                            updateDebug('TinyMCE editor initialized successfully!', 'success');
                        });
                        editor.on('change', function () {
                            updateDebug('Content changed');
                            editor.save(); // Ensure content is saved to textarea
                        });
                        editor.on('blur', function () {
                            editor.save(); // Save content when editor loses focus
                        });
                    },
                    branding: false,
                    promotion: false,
                    cache_suffix: '?v=6.8.2', // Prevent caching issues
                    convert_urls: false // Prevent URL conversion issues
                });
        } else {
            updateDebug('TinyMCE failed to load from CDN', 'error');
        }
    </script>
</body>
</html>