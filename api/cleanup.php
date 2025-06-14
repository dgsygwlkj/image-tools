<?php
/**
 * 临时文件清理脚本
 * 用法: php cleanup.php [options]
 * 选项:
 *   --expiry=N      设置过期时间(秒，默认300)
 *   --dry-run       只显示将要删除的文件，不实际删除
 *   --verbose       显示详细操作日志
 */

// 配置参数
$options = getopt('', ['expiry:', 'dry-run', 'verbose']);
$config = [
    'tempDir' => __DIR__ . '/temp/',
    'expiryTime' => $options['expiry'] ?? 300,
    'dryRun' => isset($options['dry-run']),
    'verbose' => isset($options['verbose'])
];

// 初始化统计
$stats = [
    'total' => 0,
    'expired' => 0,
    'deleted' => 0,
    'errors' => 0
];

try {
    // 验证目录
    if (!is_dir($config['tempDir'])) {
        throw new RuntimeException("临时目录不存在: {$config['tempDir']}");
    }

    // 遍历目录
    $iterator = new RecursiveDirectoryIterator(
        $config['tempDir'],
        FilesystemIterator::SKIP_DOTS
    );

    foreach (new RecursiveIteratorIterator($iterator) as $file) {
        $stats['total']++;
        $filePath = $file->getPathname();
        
        // 跳过.meta文件（由主逻辑处理）
        if (substr($filePath, -5) === '.meta') {
            continue;
        }

        $metaFile = $filePath . '.meta';
        $isExpired = false;

        // 检查过期状态
        if (file_exists($metaFile)) {
            $createdTime = (int)file_get_contents($metaFile);
            $isExpired = (time() - $createdTime) > $config['expiryTime'];
        } else {
            $isExpired = $file->getMTime() < (time() - $config['expiryTime']);
        }

        if ($isExpired) {
            $stats['expired']++;
            
            if ($config['verbose']) {
                echo "[发现过期文件] {$filePath}\n";
            }

            if (!$config['dryRun']) {
                // 删除主文件
                if (@unlink($filePath)) {
                    $stats['deleted']++;
                    
                    // 删除对应的.meta文件（如果存在）
                    if (file_exists($metaFile) && @unlink($metaFile)) {
                        $stats['deleted']++;
                    }
                } else {
                    $stats['errors']++;
                    if ($config['verbose']) {
                        echo "[删除失败] {$filePath}\n";
                    }
                }
            }
        }
    }

    // 输出统计
    echo "\n清理统计:\n";
    echo "总文件数: {$stats['total']}\n";
    echo "过期文件: {$stats['expired']}\n";
    if (!$config['dryRun']) {
        echo "已删除文件: {$stats['deleted']}\n";
        echo "删除错误: {$stats['errors']}\n";
    } else {
        echo "[模拟运行] 未实际删除任何文件\n";
    }

} catch (Exception $e) {
    echo "[错误] {$e->getMessage()}\n";
    exit(1);
}

exit(0);