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
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'); // 模拟浏览器User-Agent

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
        <title>专属福利：注册币安，开启您的加密货币之旅！</title>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Noto Sans SC', sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                color: #333;
                text-align: center;
                line-height: 1.6;
            }
            .container {
                background-color: #ffffff;
                padding: 40px;
                border-radius: 16px;
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
                max-width: 800px;
                width: 90%;
                animation: fadeIn 1s ease-out;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .header {
                margin-bottom: 30px;
            }
            .header img {
                width: 120px;
                margin-bottom: 15px;
            }
            h1 {
                color: #F0B90B; /* 币安黄色 */
                margin-bottom: 15px;
                font-size: 2.5em;
                font-weight: 700;
            }
            h2 {
                color: #333;
                font-size: 1.8em;
                margin-top: 35px;
                margin-bottom: 20px;
                border-bottom: 2px solid #eee;
                padding-bottom: 10px;
            }
            p {
                font-size: 1.05em;
                margin-bottom: 15px;
            }
            .highlight {
                color: #F0B90B;
                font-weight: 700;
                font-size: 1.1em;
            }
            .bullet-point {
                text-align: left;
                margin-left: auto;
                margin-right: auto;
                max-width: 600px;
                padding-left: 20px;
                list-style-type: disc;
                color: #555;
            }
            .bullet-point li {
                margin-bottom: 10px;
                font-size: 1em;
            }
            .invite-box {
                background-color: #fff8e1; /* 浅黄色背景 */
                border: 1px solid #ffe082;
                padding: 20px;
                border-radius: 10px;
                margin: 30px auto;
                max-width: 500px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            }
            .invite-code-display {
                font-family: 'Courier New', Courier, monospace;
                font-size: 1.6em;
                font-weight: 700;
                color: #d84315; /* 醒目的红色 */
                display: inline-block;
                padding: 10px 15px;
                background-color: #fff;
                border: 1px dashed #ffa000;
                border-radius: 5px;
                user-select: all; /* 允许选择复制 */
                margin-right: 10px;
            }
            .copy-button {
                background-color: #28a745;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                font-size: 1em;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }
            .copy-button:hover {
                background-color: #218838;
            }
            .commission-img {
                max-width: 100%;
                height: auto;
                margin-top: 30px;
                border-radius: 10px;
                box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            }
            .action-area {
                margin-top: 40px;
            }
            .main-button {
                display: inline-block;
                background-color: #F0B90B; /* 币安黄色 */
                color: #1a1a1a;
                padding: 15px 35px;
                border-radius: 8px;
                text-decoration: none;
                font-size: 1.3em;
                font-weight: 700;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(240, 185, 11, 0.4);
            }
            .main-button:hover {
                background-color: #e0ac0a;
                transform: translateY(-3px);
                box-shadow: 0 6px 20px rgba(240, 185, 11, 0.6);
            }
            .countdown {
                font-size: 1.1em;
                margin-top: 25px;
                color: #6c757d;
            }
            .countdown span {
                font-weight: 700;
                color: #007bff;
            }

            @media (max-width: 768px) {
                h1 { font-size: 2em; }
                h2 { font-size: 1.5em; }
                .container { padding: 25px; }
                .invite-code-display { font-size: 1.3em; display: block; margin-bottom: 10px; margin-right: 0;}
                .copy-button { display: block; width: 100%; }
            }
        </style>
        <script>
            let countdown = 8; // 自动跳转秒数延长，给用户更多阅读时间
            const targetUrl = "<?php echo $foundAccessibleUrl; ?>";

            function copyInviteCode() {
                const inviteCode = document.getElementById('inviteCodeDisplay').innerText;
                navigator.clipboard.writeText(inviteCode).then(() => {
                    alert('邀请码已复制到剪贴板！');
                }).catch(err => {
                    console.error('无法复制文本: ', err);
                    alert('复制失败，请手动复制邀请码。');
                });
            }

            function updateCountdown() {
                const countdownElement = document.getElementById('countdownDisplay');
                if (countdownElement) {
                    countdownElement.innerText = countdown;
                    if (countdown === 0) {
                        window.location.href = targetUrl;
                    } else {
                        countdown--;
                        setTimeout(updateCountdown, 1000);
                    }
                }
            }
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('copyCodeBtn').addEventListener('click', copyInviteCode);
                updateCountdown(); // 启动倒计时
            });
        </script>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="https://www.binance.com/static/images/brand/logo_horizontal_color.png" alt="币安 Binance Logo">
                <h1>专属福利：注册币安，开启您的加密货币之旅！</h1>
                <p>我们为您精心挑选了币安的注册链接，助您安全、高效地进入加密货币世界。</p>
            </div>

            <h2>🎉 您的专属注册福利 🎉</h2>
            <div class="invite-box">
                <p style="font-size: 1.2em;">通过此链接注册，您将立即获得：</p>
                <p><span class="highlight">高达 20% 的交易返佣！</span></p>
                <p>每笔交易都能节省费用，让您的加密投资更具优势。</p>
                <p style="margin-top: 20px;">请务必使用或确认您的专属邀请码：</p>
                <div>
                    <span id="inviteCodeDisplay" class="invite-code-display">R851UX3N</span>
                    <button id="copyCodeBtn" class="copy-button">一键复制</button>
                </div>
            </div>

            
