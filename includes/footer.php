</main>
    <footer class="nav-bg text-white py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p>&copy; <?php echo date('Y'); ?> 图片工具箱. 保留所有权利.</p>
                    <div class="flex items-center mt-2 space-x-4 text-sm">
                        <div class="flex items-center">
                            <img src="./static/icp.svg" width="16" class="mr-1">
                            <a href="https://beian.miit.gov.cn" target="_blank">辽ICP备2024028118号-1</a>
                        </div>
                        <div class="flex items-center">
                            <img src="./static/mps.png" width="16" class="mr-1">
                            <a href="https://beian.mps.gov.cn/#/query/webSearch?code=11010502056960" target="_blank" rel="noreferrer">京公网安备11010502056960号</a>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-4">
                    <a href="index.php" class="hover:text-blue-200">首页</a>
                    <a href="compress.php" class="hover:text-blue-200">压缩</a>
                    <a href="convert.php" class="hover:text-blue-200">转换</a>
                    <a href="watermark.php" class="hover:text-blue-200">水印</a>
                    <a href="removebg.php" class="hover:text-blue-200">去背景</a>
                </div>
            </div>
        </div>
    </footer>
    <script>
        // 移动端菜单切换
        document.querySelector('.mobile-menu-button').addEventListener('click', function() {
            const nav = document.querySelector('nav.md\\:flex');
            nav.classList.toggle('hidden');
            nav.classList.toggle('flex');
            nav.classList.toggle('flex-col');
            nav.classList.toggle('absolute');
            nav.classList.toggle('top-16');
            nav.classList.toggle('left-0');
            nav.classList.toggle('right-0');
            nav.classList.toggle('bg-blue-900');
            nav.classList.toggle('p-4');
            nav.classList.toggle('space-y-4');
        });
    </script>
</body>
</html>