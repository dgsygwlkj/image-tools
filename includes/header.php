<?php
session_start();

// 生成CSRF令牌
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $currentPage = basename($_SERVER['PHP_SELF']);
    $seoData = [
        'index.php' => [
            'title' => '在线图片处理工具箱 - 免费在线图片编辑工具',
            'keywords' => '图片处理,在线工具,图片压缩,格式转换,加水印,去背景'
        ],
        'compress.php' => [
            'title' => '在线图片压缩工具 - 减小图片大小而不损失质量',
            'keywords' => '图片压缩,减小图片大小,优化图片,在线压缩工具'
        ],
        'convert.php' => [
            'title' => '图片格式转换工具 - JPG, PNG, WEBP互转',
            'keywords' => '图片转换,JPG转PNG,WEBP转换,图片格式转换'
        ],
        'watermark.php' => [
            'title' => '图片加水印工具 - 保护您的图片版权',
            'keywords' => '图片水印,版权保护,文字水印,在线水印工具'
        ],
        'removebg.php' => [
            'title' => '图片去背景工具 - 轻松创建透明背景',
            'keywords' => '去除背景,透明背景,图片编辑,在线去背景工具'
        ]
    ];
    
    $defaultSeo = [
        'title' => '图片工具箱 - 在线图片处理工具',
        'keywords' => '图片处理,在线工具,图片编辑'
    ];
    
    $pageSeo = $seoData[$currentPage] ?? $defaultSeo;
    ?>
    <title><?php echo htmlspecialchars($pageSeo['title']); ?></title>
    <meta name="keywords" content="<?php echo htmlspecialchars($pageSeo['keywords']); ?>">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .nav-bg {
            background-color: #1e3a8a;
        }
        .btn-primary {
            background-color: #3b82f6;
        }
        .btn-primary:hover {
            background-color: #2563eb;
        }
    </style>
    <?php 
    include_once 'notification.php'; 
    enqueueNotificationStyles();
    showNotificationJS();
    ?>
</head>
<body class="bg-gray-50">
    <header class="nav-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold">图片工具箱</a>
            <nav class="hidden md:flex space-x-6">
                <a href="index.php" class="hover:text-blue-200">首页</a>
                <a href="compress.php" class="hover:text-blue-200">图片压缩</a>
                <a href="convert.php" class="hover:text-blue-200">格式转换</a>
                <a href="watermark.php" class="hover:text-blue-200">加水印</a>
                <a href="removebg.php" class="hover:text-blue-200">去背景</a>
            </nav>
            <button class="mobile-menu-button md:hidden focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </header>
    <main class="container mx-auto px-4 py-8">