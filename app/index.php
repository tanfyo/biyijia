<?php

function checkUrl($url) {
    // 初始化 cURL 会话
    $ch = curl_init($url);

    // 设置 cURL 选项
    curl_setopt($ch, CURLOPT_NOBODY, true); // 只获取头部信息，不下载页面内容
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟踪重定向
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 设置超时时间为5秒
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 禁用 SSL 证书检查（如果遇到问题可以尝试）
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 禁用主机名检查（如果遇到问题可以尝试）

    // 执行 cURL 请求
    curl_exec($ch);

    // 获取 HTTP 状态码
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // 关闭 cURL 会话
    curl_close($ch);

    // 判断状态码是否为 200 (OK)
    return ($statusCode == 200);
}

// 定义要检查的 URL 列表
$urls = [
    "https://www.binance.com/zh-CN/join?ref=R851UX3N",
    "https://www.maxweb.academy/zh-CN/join?ref=R851UX3N",
    "https://www.maxweb.ac/zh-CN/join?ref=R851UX3N"
];

$foundAccessibleUrl = '';

foreach ($urls as $url) {
    if (checkUrl($url)) {
        $foundAccessibleUrl = $url;
        break; // 找到一个可访问的URL就退出循环
    }
}

// 如果找到可访问的 URL，则显示一个友好的跳转页面
if ($foundAccessibleUrl) {
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>即将跳转至币安注册页面</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                background-color: #f0f2f5;
                color: #333;
                text-align: center;
            }
            .container {
                background-color: #fff;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                max-width: 600px;
                width: 90%;
            }
            h1 {
                color: #007bff;
                margin-bottom: 25px;
                font-size: 2.2em;
            }
            p {
                font-size: 1.1em;
                line-height: 1.8;
                margin-bottom: 20px;
            }
            .highlight {
                color: #dc3545;
                font-weight: bold;
            }
            .button {
                display: inline-block;
                background-color: #28a745;
                color: white;
                padding: 12px 25px;
                border-radius: 8px;
                text-decoration: none;
                font-size: 1.1em;
                margin-top: 20px;
                transition: background-color 0.3s ease;
            }
            .button:hover {
                background-color: #218838;
            }
            .countdown {
                font-size: 1.2em;
                margin-top: 30px;
                color: #6c757d;
            }
            .commission-img {
                max-width: 100%;
                height: auto;
                margin-top: 30px;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            }
            .invite-code {
                background-color: #e9ecef;
                padding: 10px 15px;
                border-radius: 6px;
                display: inline-block;
                margin-top: 15px;
                font-family: 'Courier New', Courier, monospace;
                font-weight: bold;
                color: #495057;
            }
        </style>
        <script>
            let countdown = 5; // 自动跳转秒数
            function updateCountdown() {
                document.getElementById('countdownDisplay').innerText = countdown;
                if (countdown === 0) {
                    window.location.href = "<?php echo $foundAccessibleUrl; ?>";
                } else {
                    countdown--;
                    setTimeout(updateCountdown, 1000);
                }
            }
            document.addEventListener('DOMContentLoaded', updateCountdown);
        </script>
    </head>
    <body>
        <div class="container">
            <h1>欢迎注册币安！</h1>
            <p>我们很高兴您选择通过我们的推荐链接注册币安。这将为您带来丰厚的福利：</p>
            <p>您将享受 <span class="highlight">20% 的交易返佣</span>！这意味着您的每笔交易都将节省一部分费用。</p>
            <p>请确保在注册时填写或确认邀请码：<span class="invite-code">R851UX3N</span></p>
            <p>以下是返佣截图供您参考：</p>
            <img src="https://cdn.hashnode.com/res/hashnode/image/upload/v1744770343067/6a4cf59a-deca-4de7-a628-4679cae64fc4.png" alt="返佣截图" class="commission-img">
            <p class="countdown">页面将在 <span id="countdownDisplay">5</span> 秒后自动跳转至币安注册页面。</p>
            <a href="<?php echo $foundAccessibleUrl; ?>" class="button">立即跳转 (如果不想等待)</a>
        </div>
    </body>
    </html>
    <?php
    exit();
} else {
    // 如果所有 URL 都不可访问，可以显示一个错误消息或默认页面
    echo "抱歉，目前所有推荐链接都无法访问。请稍后再试或联系网站管理员。";
    // 或者重定向到一个默认的备用页面
    // header("Location: https://embeds.hashnode.co/?p=666e906a6853631959cc136c&w=biancn");
    exit();
}

?>
