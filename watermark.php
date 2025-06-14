<?php include 'includes/header.php'; ?>

<section class="py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold mb-8 text-blue-900">图片加水印工具</h1>
        
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- 上传和设置区域 -->
                <div class="w-full md:w-1/3 space-y-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="imageUpload">选择图片</label>
                        <input type="file" id="imageUpload" accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="watermarkText">水印文字</label>
                        <input type="text" id="watermarkText" value="Sample Watermark" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="textColor">文字颜色</label>
                            <input type="color" id="textColor" value="#ffffff" class="w-full h-10 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="bgColor">背景颜色</label>
                            <input type="color" id="bgColor" value="#000000" class="w-full h-10 cursor-pointer">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="fontSize">文字大小</label>
                            <input type="range" id="fontSize" min="10" max="50" value="20" class="w-full h-2 bg-blue-200 rounded-lg appearance-none cursor-pointer">
                            <span id="fontSizeValue" class="text-sm">20px</span>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="opacity">透明度</label>
                            <input type="range" id="opacity" min="0" max="100" value="50" class="w-full h-2 bg-blue-200 rounded-lg appearance-none cursor-pointer">
                            <span id="opacityValue" class="text-sm">50%</span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="watermarkPosition">水印位置</label>
                        <select id="watermarkPosition" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="center">居中</option>
                            <option value="top-left">左上</option>
                            <option value="top-right">右上</option>
                            <option value="bottom-left">左下</option>
                            <option value="bottom-right">右下</option>
                            <option value="repeat">重复平铺</option>
                        </select>
                    </div>
                    
                    <div id="angleControl">
                        <label class="block text-gray-700 font-medium mb-2" for="angle">旋转角度</label>
                        <input type="range" id="angle" min="0" max="360" value="0" class="w-full h-2 bg-blue-200 rounded-lg appearance-none cursor-pointer">
                        <span id="angleValue" class="text-sm">0°</span>
                    </div>
                    
                    <div id="spacingControl">
                        <label class="block text-gray-700 font-medium mb-2" for="spacing">水印间距</label>
                        <input type="range" id="spacing" min="0" max="200" value="50" class="w-full h-2 bg-blue-200 rounded-lg appearance-none cursor-pointer">
                        <span id="spacingValue" class="text-sm">50px</span>
                    </div>
                    
                    <button id="applyBtn" class="btn-primary w-full py-3 rounded-lg font-semibold shadow hover:shadow-md transition disabled:opacity-50" disabled>
                        应用水印
                    </button>
                    
                    <button id="downloadBtn" class="btn-primary w-full py-3 rounded-lg font-semibold shadow hover:shadow-md transition disabled:opacity-50" disabled>
                        下载带水印图片
                    </button>
                </div>
                
                <!-- 预览区域 -->
                <div class="w-full md:w-2/3">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">预览</h3>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 flex items-center justify-center min-h-96">
                        <canvas id="previewCanvas" class="max-w-full max-h-96 hidden"></canvas>
                        <p id="previewPlaceholder" class="text-gray-500">预览将显示在这里</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageUpload = document.getElementById('imageUpload');
    const watermarkText = document.getElementById('watermarkText');
    const textColor = document.getElementById('textColor');
    const bgColor = document.getElementById('bgColor');
    const fontSize = document.getElementById('fontSize');
    const fontSizeValue = document.getElementById('fontSizeValue');
    const opacity = document.getElementById('opacity');
    const opacityValue = document.getElementById('opacityValue');
    const watermarkPosition = document.getElementById('watermarkPosition');
    const angle = document.getElementById('angle');
    const angleValue = document.getElementById('angleValue');
    const spacing = document.getElementById('spacing');
    const spacingValue = document.getElementById('spacingValue');
    const applyBtn = document.getElementById('applyBtn');
    const downloadBtn = document.getElementById('downloadBtn');
    const previewCanvas = document.getElementById('previewCanvas');
    const previewPlaceholder = document.getElementById('previewPlaceholder');
    
    let originalImage = null;
    let watermarkedBlob = null;
    
    // 更新滑块值显示
    fontSize.addEventListener('input', function() {
        fontSizeValue.textContent = this.value + 'px';
    });
    
    opacity.addEventListener('input', function() {
        opacityValue.textContent = this.value + '%';
    });
    
    angle.addEventListener('input', function() {
        angleValue.textContent = this.value + '°';
    });
    
    spacing.addEventListener('input', function() {
        spacingValue.textContent = this.value + 'px';
    });
    
    // 处理图片上传
    imageUpload.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            
            const reader = new FileReader();
            reader.onload = function(event) {
                originalImage = new Image();
                originalImage.onload = function() {
                    previewCanvas.width = originalImage.width;
                    previewCanvas.height = originalImage.height;
                    
                    // 显示原始图片
                    const ctx = previewCanvas.getContext('2d');
                    ctx.drawImage(originalImage, 0, 0);
                    
                    previewCanvas.classList.remove('hidden');
                    previewPlaceholder.classList.add('hidden');
                    
                    // 启用应用按钮
                    applyBtn.disabled = false;
                };
                originalImage.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // 应用水印
    applyBtn.addEventListener('click', function() {
        if (!originalImage) {
            showNotification('请先上传图片', 'error');
            return;
        }
        
        const canvas = document.createElement('canvas');
        canvas.width = originalImage.width;
        canvas.height = originalImage.height;
        const ctx = canvas.getContext('2d');
        
        // 绘制原始图片
        ctx.drawImage(originalImage, 0, 0);
        
        // 设置水印样式
        ctx.font = `${fontSize.value}px Arial`;
        ctx.fillStyle = textColor.value;
        ctx.strokeStyle = bgColor.value;
        ctx.globalAlpha = opacity.value / 100;
        
        const text = watermarkText.value;
        const textWidth = ctx.measureText(text).width;
        const textHeight = parseInt(fontSize.value);
        const angleRad = angle.value * Math.PI / 180;
        const spacingValue = parseInt(spacing.value);
        
        // 根据位置绘制水印
        const position = watermarkPosition.value;
        
        if (position === 'repeat') {
            // 重复平铺水印
            const patternCanvas = document.createElement('canvas');
            const patternSize = textWidth + spacingValue;
            patternCanvas.width = patternSize;
            patternCanvas.height = patternSize;
            
            const patternCtx = patternCanvas.getContext('2d');
            patternCtx.font = ctx.font;
            patternCtx.fillStyle = ctx.fillStyle;
            patternCtx.strokeStyle = ctx.strokeStyle;
            patternCtx.globalAlpha = ctx.globalAlpha;
            
            // 绘制单个水印
            patternCtx.save();
            patternCtx.translate(patternSize / 2, patternSize / 2);
            patternCtx.rotate(angleRad);
            patternCtx.fillText(text, -textWidth / 2, textHeight / 4);
            patternCtx.strokeText(text, -textWidth / 2, textHeight / 4);
            patternCtx.restore();
            
            // 创建平铺图案
            const pattern = ctx.createPattern(patternCanvas, 'repeat');
            ctx.fillStyle = pattern;
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        } else {
            // 单个水印
            let x, y;
            
            switch(position) {
                case 'top-left':
                    x = 20;
                    y = 20 + textHeight;
                    break;
                case 'top-right':
                    x = canvas.width - textWidth - 20;
                    y = 20 + textHeight;
                    break;
                case 'bottom-left':
                    x = 20;
                    y = canvas.height - 20;
                    break;
                case 'bottom-right':
                    x = canvas.width - textWidth - 20;
                    y = canvas.height - 20;
                    break;
                default: // center
                    x = (canvas.width - textWidth) / 2;
                    y = (canvas.height + textHeight) / 2;
            }
            
            // 绘制水印
            ctx.save();
            ctx.translate(x + textWidth / 2, y - textHeight / 2);
            ctx.rotate(angleRad);
            ctx.fillText(text, -textWidth / 2, textHeight / 4);
            ctx.strokeText(text, -textWidth / 2, textHeight / 4);
            ctx.restore();
        }
        
        // 更新预览
        previewCanvas.width = originalImage.width;
        previewCanvas.height = originalImage.height;
        const previewCtx = previewCanvas.getContext('2d');
        previewCtx.drawImage(canvas, 0, 0);
        
        // 转换为Blob用于下载
        canvas.toBlob(function(blob) {
            watermarkedBlob = blob;
            downloadBtn.disabled = false;
        }, 'image/jpeg', 0.9);
    });
    
    // 下载带水印图片
    downloadBtn.addEventListener('click', function() {
        if (!watermarkedBlob) {
            showNotification('请先应用水印', 'error');
            return;
        }
        
        const url = URL.createObjectURL(watermarkedBlob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'watermarked_' + imageUpload.files[0].name;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
});
</script>

<?php include 'includes/footer.php'; ?>