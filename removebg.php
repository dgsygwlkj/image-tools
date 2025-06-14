<?php include 'includes/header.php'; ?>

<section class="py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold mb-8 text-blue-900">图片去背景工具</h1>
        
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- 上传和设置区域 -->
                <div class="w-full md:w-1/3 space-y-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="imageUpload">选择图片</label>
                        <input type="file" id="imageUpload" accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="bgColor">选择要去除的背景色</label>
                        <input type="color" id="bgColor" value="#ffffff" class="w-full h-10 cursor-pointer">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="toleranceRange">颜色容差: <span id="toleranceValue">30</span></label>
                        <input type="range" id="toleranceRange" min="0" max="100" value="30" class="w-full h-2 bg-blue-200 rounded-lg appearance-none cursor-pointer">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="replaceBg">替换背景为</label>
                        <select id="replaceBg" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="transparent">透明</option>
                            <option value="color">自定义颜色</option>
                        </select>
                    </div>
                    
                    <div id="replaceColorControl" class="hidden">
                        <label class="block text-gray-700 font-medium mb-2" for="replaceColor">新背景颜色</label>
                        <input type="color" id="replaceColor" value="#000000" class="w-full h-10 cursor-pointer">
                    </div>
                    
                    <button id="removeBgBtn" class="btn-primary w-full py-3 rounded-lg font-semibold shadow hover:shadow-md transition disabled:opacity-50" disabled>
                        去除背景
                    </button>
                    
                    <button id="downloadBtn" class="btn-primary w-full py-3 rounded-lg font-semibold shadow hover:shadow-md transition disabled:opacity-50" disabled>
                        下载处理后的图片
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
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">处理后图片</h3>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 flex items-center justify-center min-h-48 bg-gray-100 bg-checkered">
                            <canvas id="processedCanvas" class="max-w-full max-h-64 hidden"></canvas>
                            <p id="processedPlaceholder" class="text-gray-500">处理后图片将显示在这里</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.bg-checkered {
    background-image: 
        linear-gradient(45deg, #ccc 25%, transparent 25%),
        linear-gradient(-45deg, #ccc 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, #ccc 75%),
        linear-gradient(-45deg, transparent 75%, #ccc 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageUpload = document.getElementById('imageUpload');
    const bgColor = document.getElementById('bgColor');
    const toleranceRange = document.getElementById('toleranceRange');
    const toleranceValue = document.getElementById('toleranceValue');
    const replaceBg = document.getElementById('replaceBg');
    const replaceColorControl = document.getElementById('replaceColorControl');
    const replaceColor = document.getElementById('replaceColor');
    const removeBgBtn = document.getElementById('removeBgBtn');
    const downloadBtn = document.getElementById('downloadBtn');
    const originalImage = document.getElementById('originalImage');
    const originalPlaceholder = document.getElementById('originalPlaceholder');
    const processedCanvas = document.getElementById('processedCanvas');
    const processedPlaceholder = document.getElementById('processedPlaceholder');
    
    let processedBlob = null;
    
    // 更新容差值显示
    toleranceRange.addEventListener('input', function() {
        toleranceValue.textContent = this.value;
    });
    
    // 显示/隐藏替换颜色控件
    replaceBg.addEventListener('change', function() {
        replaceColorControl.classList.toggle('hidden', this.value !== 'color');
    });
    
    // 处理图片上传
    imageUpload.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            
            const reader = new FileReader();
            reader.onload = function(event) {
                originalImage.src = event.target.result;
                originalImage.classList.remove('hidden');
                originalPlaceholder.classList.add('hidden');
                
                // 启用去背景按钮
                removeBgBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // 去除背景
    removeBgBtn.addEventListener('click', function() {
        if (!originalImage.complete || !originalImage.naturalWidth) {
            showNotification('请先上传有效的图片', 'error');
            return;
        }
        
        const img = new Image();
        img.onload = function() {
            // 设置canvas尺寸
            processedCanvas.width = img.width;
            processedCanvas.height = img.height;
            const ctx = processedCanvas.getContext('2d');
            
            // 绘制原始图片
            ctx.drawImage(img, 0, 0);
            
            // 获取图像数据
            const imageData = ctx.getImageData(0, 0, processedCanvas.width, processedCanvas.height);
            const data = imageData.data;
            
            // 解析目标背景色
            const bgHex = bgColor.value;
            const targetR = parseInt(bgHex.substr(1, 2), 16);
            const targetG = parseInt(bgHex.substr(3, 2), 16);
            const targetB = parseInt(bgHex.substr(5, 2), 16);
            
            // 获取容差值
            const tolerance = parseInt(toleranceRange.value);
            
            // 处理每个像素
            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                
                // 计算与目标颜色的差异
                const diff = Math.sqrt(
                    Math.pow(r - targetR, 2) +
                    Math.pow(g - targetG, 2) +
                    Math.pow(b - targetB, 2)
                );
                
                // 如果颜色在容差范围内，则视为背景
                if (diff <= tolerance) {
                    if (replaceBg.value === 'transparent') {
                        // 设置为透明
                        data[i + 3] = 0;
                    } else {
                        // 替换为指定颜色
                        const replaceHex = replaceColor.value;
                        data[i] = parseInt(replaceHex.substr(1, 2), 16);
                        data[i + 1] = parseInt(replaceHex.substr(3, 2), 16);
                        data[i + 2] = parseInt(replaceHex.substr(5, 2), 16);
                    }
                }
            }
            
            // 更新图像数据
            ctx.putImageData(imageData, 0, 0);
            
            // 显示处理后的canvas
            processedCanvas.classList.remove('hidden');
            processedPlaceholder.classList.add('hidden');
            
            // 转换为Blob用于下载
            processedCanvas.toBlob(function(blob) {
                processedBlob = blob;
                downloadBtn.disabled = false;
            }, 'image/png');
        };
        img.src = originalImage.src;
    });
    
    // 下载处理后的图片
    downloadBtn.addEventListener('click', function() {
        if (!processedBlob) {
            showNotification('请先去除图片背景', 'error');
            return;
        }
        
        const url = URL.createObjectURL(processedBlob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'nobg_' + imageUpload.files[0].name;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
    
    // 辅助函数：RGB颜色差异计算
    function colorDiff(r1, g1, b1, r2, g2, b2) {
        return Math.sqrt(Math.pow(r1 - r2, 2) + Math.pow(g1 - g2, 2) + Math.pow(b1 - b2, 2));
    }
});
</script>

<?php include 'includes/footer.php'; ?>