<?php include 'includes/header.php'; ?>

<section class="py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold mb-8 text-blue-900">图片压缩工具</h1>
        
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- 上传和设置区域 -->
                <div class="w-full md:w-1/3 space-y-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="imageUpload">选择图片</label>
                        <input type="file" id="imageUpload" accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="qualityRange">压缩质量: <span id="qualityValue">80</span>%</label>
                        <input type="range" id="qualityRange" min="10" max="100" value="80" class="w-full h-2 bg-blue-200 rounded-lg appearance-none cursor-pointer">
                    </div>
                    
                    <button id="compressBtn" class="btn-primary w-full py-3 rounded-lg font-semibold shadow hover:shadow-md transition disabled:opacity-50" disabled>
                        压缩图片
                    </button>
                    
                    <button id="downloadBtn" class="btn-primary w-full py-3 rounded-lg font-semibold shadow hover:shadow-md transition disabled:opacity-50" disabled>
                        下载压缩后图片
                    </button>
                </div>
                
                <!-- 预览区域 -->
                <div class="w-full md:w-2/3 space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">原始图片</h3>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 flex items-center justify-center min-h-48">
                            <img id="originalImage" class="max-w-full max-h-64 hidden" alt="原始图片">
                            <p id="originalPlaceholder" class="text-gray-500">预览将显示在这里</p>
                        </div>
                        <p id="originalSize" class="text-sm text-gray-500 mt-2"></p>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">压缩后图片</h3>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 flex items-center justify-center min-h-48">
                            <img id="compressedImage" class="max-w-full max-h-64 hidden" alt="压缩后图片">
                            <p id="compressedPlaceholder" class="text-gray-500">压缩后图片将显示在这里</p>
                        </div>
                        <p id="compressedSize" class="text-sm text-gray-500 mt-2"></p>
                        <p id="compressionRatio" class="text-sm font-medium mt-2"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageUpload = document.getElementById('imageUpload');
    const qualityRange = document.getElementById('qualityRange');
    const qualityValue = document.getElementById('qualityValue');
    const compressBtn = document.getElementById('compressBtn');
    const downloadBtn = document.getElementById('downloadBtn');
    const originalImage = document.getElementById('originalImage');
    const originalPlaceholder = document.getElementById('originalPlaceholder');
    const originalSize = document.getElementById('originalSize');
    const compressedImage = document.getElementById('compressedImage');
    const compressedPlaceholder = document.getElementById('compressedPlaceholder');
    const compressedSize = document.getElementById('compressedSize');
    const compressionRatio = document.getElementById('compressionRatio');
    
    let originalFile = null;
    let compressedBlob = null;
    
    // 更新质量显示
    qualityRange.addEventListener('input', function() {
        qualityValue.textContent = this.value;
    });
    
    // 处理图片上传
    imageUpload.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            originalFile = e.target.files[0];
            
            // 显示原始图片
            const reader = new FileReader();
            reader.onload = function(event) {
                originalImage.src = event.target.result;
                originalImage.classList.remove('hidden');
                originalPlaceholder.classList.add('hidden');
                originalSize.textContent = `文件大小: ${formatFileSize(originalFile.size)}`;
                
                // 启用压缩按钮
                compressBtn.disabled = false;
            };
            reader.readAsDataURL(originalFile);
        }
    });
    
    // 压缩图片
    compressBtn.addEventListener('click', function() {
        if (!originalFile) {
            showNotification('请先上传图片', 'error');
            return;
        }
        
        const img = new Image();
        img.onload = function() {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            // 保持宽高比
            const MAX_WIDTH = 800;
            const MAX_HEIGHT = 800;
            let width = img.width;
            let height = img.height;
            
            if (width > height) {
                if (width > MAX_WIDTH) {
                    height *= MAX_WIDTH / width;
                    width = MAX_WIDTH;
                }
            } else {
                if (height > MAX_HEIGHT) {
                    width *= MAX_HEIGHT / height;
                    height = MAX_HEIGHT;
                }
            }
            
            canvas.width = width;
            canvas.height = height;
            
            // 绘制图片到canvas
            ctx.drawImage(img, 0, 0, width, height);
            
            // 获取压缩后的数据URL
            const quality = qualityRange.value / 100;
            const mimeType = originalFile.type || 'image/jpeg';
            const dataURL = canvas.toDataURL(mimeType, quality);
            
            // 显示压缩后图片
            compressedImage.src = dataURL;
            compressedImage.classList.remove('hidden');
            compressedPlaceholder.classList.add('hidden');
            
            // 计算并显示大小信息
            const compressedSizeBytes = Math.round((dataURL.length * 3) / 4); // 近似计算
            compressedSize.textContent = `文件大小: ${formatFileSize(compressedSizeBytes)}`;
            
            const ratio = (1 - compressedSizeBytes / originalFile.size) * 100;
            compressionRatio.textContent = `压缩率: ${ratio.toFixed(1)}% 节省`;
            compressionRatio.className = ratio > 0 ? 
                'text-sm font-medium mt-2 text-green-600' : 
                'text-sm font-medium mt-2 text-red-600';
            
            // 转换为Blob用于下载
            dataURLToBlob(dataURL, mimeType).then(blob => {
                compressedBlob = blob;
                downloadBtn.disabled = false;
            });
        };
        img.src = URL.createObjectURL(originalFile);
    });
    
    // 下载压缩后图片
    downloadBtn.addEventListener('click', function() {
        if (!compressedBlob) {
            showNotification('请先压缩图片', 'error');
            return;
        }
        
        const url = URL.createObjectURL(compressedBlob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'compressed_' + originalFile.name;
        
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
    
    // 辅助函数：格式化文件大小
    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' bytes';
        else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        else return (bytes / 1048576).toFixed(1) + ' MB';
    }
    
    // 辅助函数：将DataURL转换为Blob
    function dataURLToBlob(dataURL, mimeType) {
        return fetch(dataURL)
            .then(res => res.blob())
            .then(blob => new Blob([blob], { type: mimeType }));
    }
});
</script>

<?php include 'includes/footer.php'; ?>