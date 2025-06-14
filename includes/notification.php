<?php
/**
 * 通用通知组件
 * 提供美观的页面通知功能
 */

function enqueueNotificationStyles() {
    echo '
    <style>
        .notification {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            z-index: 50;
            display: flex;
            align-items: center;
            transform: translateY(0.5rem);
            opacity: 0;
            transition: all 0.3s ease;
        }
        .notification.show {
            transform: translateY(0);
            opacity: 1;
        }
        .notification.hide {
            transform: translateY(-0.5rem);
            opacity: 0;
        }
        .notification-info {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .notification-error {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .notification-success {
            background-color: #dcfce7;
            color: #166534;
        }
        .notification-close {
            margin-left: 1rem;
            font-size: 1.25rem;
            cursor: pointer;
        }
        .notification-close:hover {
            opacity: 0.7;
        }
    </style>
    ';
}

function showNotificationJS() {
    echo '
    <script>
    (function() {
        const notificationQueue = [];
        let isShowing = false;
        
        function processQueue() {
            if (notificationQueue.length === 0 || isShowing) return;
            
            isShowing = true;
            const {message, type} = notificationQueue.shift();
            
            // 创建或获取通知容器
            let container = document.getElementById("notification-container");
            if (!container) {
                container = document.createElement("div");
                container.id = "notification-container";
                container.style.position = "fixed";
                container.style.top = "20px";
                container.style.right = "20px";
                container.style.zIndex = "1000";
                document.body.appendChild(container);
            }

            const notification = document.createElement("div");
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <span>${message}</span>
                <span class="notification-close" onclick="closeNotification()">×</span>
            `;
            container.innerHTML = ""; // 清空容器
            container.appendChild(notification);
            
            // 显示动画
            setTimeout(() => notification.classList.add("show"), 10);
            
            // 自动隐藏
            setTimeout(() => {
                notification.classList.remove("show");
                notification.classList.add("hide");
                setTimeout(() => {
                    notification.remove();
                    isShowing = false;
                    processQueue();
                }, 300);
            }, 4700);
        }
        
        function closeNotification() {
            const container = document.getElementById("notification-container");
            if (container) {
                const notification = container.querySelector(".notification");
                if (notification) {
                    notification.classList.remove("show");
                    notification.classList.add("hide");
                    setTimeout(() => {
                        notification.remove();
                        isShowing = false;
                        processQueue();
                    }, 300);
                }
            }
        }
        
        window.showNotification = function(message, type = "info") {
            notificationQueue.push({message, type});
            processQueue();
        };
    })();
    </script>
    ';
}
?>