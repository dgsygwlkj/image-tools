<?php include 'includes/header.php'; ?>

<section class="py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold mb-8 text-blue-900">图片格式转换工具</h1>
        
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- 上传和设置区域 -->
                <div class="w-full md:w-1/3 space-y-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="imageUpload">选择图片</label>
                        <input type="file" id="imageUpload" accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="formatSelect">输出格式</label>
                        <select id="formatSelect" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="image/jpeg">JPEG</option>
                            <option value="image/png">PNG</option>
                            <option value="image/webp">WEBP</option>
                        </select>
                    </div>
                    
                    <div id="qualityControl" class="hidden">
                        <label class="block text-gray-700 font-medium mb-2" for="qualityRange">JPEG质量: <span id="qualityValue">80</span>%</label>
                        <input type="range" id="qualityRange" min="10" max="100" value="80" class="w-full h-2 bg-blue-200 rounded-lg appearance-none cursor-pointer">
                    </div>
                    
                    <button id="convertBtn" class="btn-primary w-full py-3 rounded-lg font-semibold shadow hover:shadow-md transition disabled:opacity-50" disabled>
                        转换图片
                    </button>
                    
                    <button id="downloadBtn" class="btn-primary w-full py-3 rounded-lg font-semibold shadow hover:shadow-md transition disabled:opacity-50" disabled>
                        下载转换后图片
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
                        <p id="originalFormat" class="text-sm text-gray-500"></p>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">转换后图片</h3>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 flex items-center justify-center min-h-48">
                            <img id="convertedImage" class="max-w-full max-h-64 hidden" alt="转换后图片">
                            <p id="convertedPlaceholder" class="text-gray-500">转换后图片将显示在这里</p>
                        </div>
                        <p id="convertedSize" class="text-sm text-gray-500 mt-2"></p>
                        <p id="convertedFormat" class="text-sm text-gray-500"></p>
                        <p id="conversionInfo" class="text-sm font-medium mt-2"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageUpload = document.getElementById('imageUpload');
    const formatSelect = document.getElementById('formatSelect');
    const qualityControl = document.getElementById('qualityControl');
    const qualityRange = document.getElementById('qualityRange');
    const qualityValue = document.getElementById('qualityValue');
    const convertBtn = document.getElementById('convertBtn');
    const downloadBtn = document.getElementById('downloadBtn');
    const originalImage = document.getElementById('originalImage');
    const originalPlaceholder = document.getElementById('originalPlaceholder');
    const originalSize = document.getElementById('originalSize');
    const originalFormat = document.getElementById('originalFormat');
    const convertedImage = document.getElementById('convertedImage');
    const convertedPlaceholder = document.getElementById('convertedPlaceholder');
    const convertedSize = document.getElementById('convertedSize');
    const convertedFormat = document.getElementById('convertedFormat');
    const conversionInfo = document.getElementById('conversionInfo');
    
    let originalFile = null;
    let convertedBlob = null;
    
    // 更新质量显示
    qualityRange.addEventListener('input', function() {
        qualityValue.textContent = this.value;
    });
    
    // 根据选择的格式显示/隐藏质量控制
    formatSelect.addEventListener('change', function() {
        qualityControl.classList.toggle('hidden', this.value !== 'image/jpeg');
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
                
                // 检测原始格式
                const format = originalFile.type || '未知格式';
                originalFormat.textContent = `格式: ${format.split('/')[1] || format}`;
                
                // 启用转换按钮
                convertBtn.disabled = false;
            };
            reader.readAsDataURL(originalFile);
        }
    });
    
    // 检查浏览器支持的格式
    function isFormatSupported(mimeType) {
        const canvas = document.createElement('canvas');
        return canvas.toDataURL(mimeType).startsWith('data:' + mimeType);
    }

    // 转换图片
    convertBtn.addEventListener('click', function() {
        if (!originalFile) {
            showNotification('请先上传图片', 'error');
            return;
        }
        
        const mimeType = formatSelect.value;
        
        // 检查浏览器支持
        if (!isFormatSupported(mimeType)) {
            showNotification(`当前浏览器不支持转换为 ${mimeType} 格式，请尝试使用其他浏览器。`, 'error');
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
            
            // 获取转换后的数据
            const mimeType = formatSelect.value;
            const quality = mimeType === 'image/jpeg' ? qualityRange.value / 100 : 1;
            
            canvas.toBlob(function(blob) {
                if (!blob) return;
                
                convertedBlob = blob;
                
                // 显示转换后图片
                const url = URL.createObjectURL(blob);
                convertedImage.src = url;
                convertedImage.classList.remove('hidden');
                convertedPlaceholder.classList.add('hidden');
                
                // 显示转换信息
                convertedSize.textContent = `文件大小: ${formatFileSize(blob.size)}`;
                convertedFormat.textContent = `格式: ${mimeType.split('/')[1]}`;
                
                const ratio = (1 - blob.size / originalFile.size) * 100;
                conversionInfo.textContent = `转换率: ${ratio.toFixed(1)}% ${ratio > 0 ? '减小' : '增加'}`;
                conversionInfo.className = ratio > 0 ? 
                    'text-sm font-medium mt-2 text-green-600' : 
                    'text-sm font-medium mt-2 text-red-600';
                
                // 启用下载按钮
                downloadBtn.disabled = false;
            }, mimeType, quality);
        };
        img.src = URL.createObjectURL(originalFile);
    });
    
    // 下载转换后图片
    downloadBtn.addEventListener('click', function() {
        if (!convertedBlob) {
            showNotification('请先转换图片', 'error');
            return;
        }
        
        const url = URL.createObjectURL(convertedBlob);
        const a = document.createElement('a');
        a.href = url;
        
        // 根据格式设置文件扩展名
        const format = formatSelect.value.split('/')[1];
        a.download = `converted_${originalFile.name.split('.')[0]}.${format}`;
        
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
});
</script>

<?php include 'includes/footer.php'; ?>