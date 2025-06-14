<?php include 'includes/header.php'; ?>

<section class="hero bg-gradient-to-r from-blue-800 to-blue-600 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-6">在线图片处理工具箱</h1>
        <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">无需上传，直接在浏览器中完成各种图片处理操作</p>
        <div class="flex justify-center space-x-4">
            <a href="compress.php" class="btn-primary px-6 py-3 rounded-full font-semibold shadow-lg hover:shadow-xl transition duration-300">开始使用</a>
            <a href="https://github.com/dgsygwlkj/image-tools" target="_blank" class="flex items-center px-6 py-3 rounded-full font-semibold bg-gray-800 text-white hover:bg-gray-700 shadow-lg hover:shadow-xl transition duration-300">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                </svg>
                GitHub
            </a>
        </div>
    </div>
</section>

<section class="py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-blue-900">功能特色</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- 图片压缩卡片 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <div class="bg-blue-100 p-6">
                    <svg class="w-12 h-12 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-3 text-blue-900">图片压缩</h3>
                    <p class="text-gray-600 mb-4">减小图片文件大小而不明显影响质量</p>
                    <a href="compress.php" class="text-blue-600 font-medium hover:text-blue-800">立即使用 →</a>
                </div>
            </div>

            <!-- 格式转换卡片 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <div class="bg-blue-100 p-6">
                    <svg class="w-12 h-12 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-3 text-blue-900">格式转换</h3>
                    <p class="text-gray-600 mb-4">支持JPG、PNG、WEBP等多种格式互转</p>
                    <a href="convert.php" class="text-blue-600 font-medium hover:text-blue-800">立即使用 →</a>
                </div>
            </div>

            <!-- 加水印卡片 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <div class="bg-blue-100 p-6">
                    <svg class="w-12 h-12 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-3 text-blue-900">加水印</h3>
                    <p class="text-gray-600 mb-4">自定义文字水印，多种样式可选</p>
                    <a href="watermark.php" class="text-blue-600 font-medium hover:text-blue-800">立即使用 →</a>
                </div>
            </div>

            <!-- 去背景卡片 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <div class="bg-blue-100 p-6">
                    <svg class="w-12 h-12 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-3 text-blue-900">去背景</h3>
                    <p class="text-gray-600 mb-4">基于颜色选择去除图片背景</p>
                    <a href="removebg.php" class="text-blue-600 font-medium hover:text-blue-800">立即使用 →</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>