<?php
header('Content-Type: application/json');

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => '仅支持POST请求']));
}

// 验证CSRF令牌
session_start();
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'CSRF验证失败']));
}

// 检查文件上传
if (empty($_FILES['image'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => '未上传图片文件']));
}

// 验证文件类型
$allowedTypes = [
    'image/bmp' => 'bmp',
    'image/gif' => 'gif',
    'image/tiff' => 'tiff',
    'image/svg+xml' => 'svg',
    'image/x-icon' => 'ico',
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
];

$fileInfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($fileInfo, $_FILES['image']['tmp_name']);
finfo_close($fileInfo);

if (!array_key_exists($mimeType, $allowedTypes)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => '不支持的文件类型']));
}

// 验证目标格式
$targetFormat = $_POST['format'] ?? '';
if (!array_key_exists($targetFormat, $allowedTypes)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => '不支持的目标格式']));
}

// 创建临时目录
$tempDir = __DIR__ . '/../temp/';
if (!file_exists($tempDir)) {
    mkdir($tempDir, 0755, true);
}

// 清理过期文件(24小时)
foreach (glob($tempDir . '*') as $file) {
    if (filemtime($file) < time() - 86400) {
        unlink($file);
    }
}

// 生成唯一文件名
$sourceFile = $tempDir . uniqid('img_') . '.' . $allowedTypes[$mimeType];
$targetFile = $tempDir . uniqid('conv_') . '.' . $allowedTypes[$targetFormat];

// 移动上传文件
if (!move_uploaded_file($_FILES['image']['tmp_name'], $sourceFile)) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => '文件保存失败']));
}

// 执行格式转换
try {
    if (!extension_loaded('gd') && !extension_loaded('imagick')) {
        throw new Exception('服务器未安装GD或ImageMagick扩展');
    }

    if (extension_loaded('imagick')) {
        // 使用ImageMagick转换
        $imagick = new Imagick($sourceFile);
        $imagick->setImageFormat($allowedTypes[$targetFormat]);
        $imagick->writeImage($targetFile);
        $imagick->clear();
    } else {
        // 使用GD库转换
        switch ($mimeType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourceFile);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourceFile);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($sourceFile);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourceFile);
                break;
            case 'image/bmp':
                $image = imagecreatefrombmp($sourceFile);
                break;
            default:
                throw new Exception('不支持的源格式');
        }

        switch ($targetFormat) {
            case 'image/jpeg':
                imagejpeg($image, $targetFile, 90);
                break;
            case 'image/png':
                imagepng($image, $targetFile);
                break;
            case 'image/webp':
                imagewebp($image, $targetFile);
                break;
            case 'image/gif':
                imagegif($image, $targetFile);
                break;
            case 'image/bmp':
                imagebmp($image, $targetFile);
                break;
            default:
                throw new Exception('不支持的目标格式');
        }

        imagedestroy($image);
    }

    // 返回下载链接和文件信息
    $downloadUrl = '/temp/' . basename($targetFile);
    $fileSize = filesize($targetFile);
    
    // 记录文件创建时间
    file_put_contents($targetFile.'.meta', time());
    
    echo json_encode([
        'success' => true,
        'url' => $downloadUrl,
        'extension' => $allowedTypes[$targetFormat],
        'size' => $fileSize,
        'expires' => time() + 300 // 5分钟有效期
    ]);

    // 删除源文件
    unlink($sourceFile);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    
    // 清理文件
    if (file_exists($sourceFile)) unlink($sourceFile);
    if (file_exists($targetFile)) unlink($targetFile);
}
?>