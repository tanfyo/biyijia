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

$foundAccessible = false;

foreach ($urls as $url) {
    if (checkUrl($url)) {
        // 如果 URL 可访问，则重定向用户并退出循环
        header("Location: " . $url);
        $foundAccessible = true;
        exit();
    }
}

// 如果所有 URL 都不可访问，可以显示一个错误消息或默认页面
if (!$foundAccessible) {
    echo "抱歉，目前所有推荐链接都无法访问。请稍后再试或联系网站管理员。";
    // 或者重定向到一个默认的备用页面
    // header("Location: https://your-default-fallback-page.com");
    // exit();
}

?>
