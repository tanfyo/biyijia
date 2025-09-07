<?php
/**
 * 文件名: /public/go.php (严谨版)
 * 描述: 先执行通用的移动设备JS检测，如果通过，再记录点击并重定向。
 */

// 1. 引入核心文件
require_once __DIR__ . '/../includes/bootstrap.php'; 
require_once __DIR__ . '/config.php'; // 假设这个文件定义了 FINAL_LANDING_DOMAIN

// 2. 检查关键配置是否存在
if (!isset($pdo) || !defined('FINAL_LANDING_DOMAIN') || empty(FINAL_LANDING_DOMAIN)) {
    header("HTTP/1.1 503 Service Unavailable");
    exit("System Configuration Error.");
}

// 3. 定义一个安全的默认跳转地址（首页）
$fallback_url = rtrim(FINAL_LANDING_DOMAIN, '/');

/**
 * 输出一个包含JS设备检测逻辑的HTML页面来执行跳转。
 * 此函数会终止脚本的后续执行。
 *
 * @param string $destination_url 检测通过后（是移动设备），最终要跳转的目标URL。
 */
function perform_checked_redirect($destination_url) {
    // 这是新的、更严谨的设备检测脚本 (非混淆，易于理解)
    // 逻辑：如果不是移动设备，则跳转到指定的拦截页面。
    $detection_script = "
        (function() {
            // 通过检查User-Agent中是否包含常见的移动端关键字来判断设备类型
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            // 如果不是移动设备 (isMobile为false)，则判定为桌面端，立即跳转到拦截页
            if (!isMobile) {
                // 您可以在这里指定桌面端用户被拦截后跳转的地址
                window.location.replace('https://www.qq.com'); 
            }
            // 如果是移动设备，这个脚本不执行任何操作，页面会继续执行下面的第二个脚本
        })();
    ";

    // 将目标URL安全地转换为JSON字符串，以防URL中包含特殊字符破坏JS代码
    $safe_destination_url = json_encode($destination_url);

    // 输出HTML。浏览器会先运行上面的检测脚本。
    // 如果是桌面端，会直接跳转到qq.com。
    // 如果是移动端，则会继续运行下面的第二个脚本，跳转到我们的业务目标地址。
    echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>正在安全跳转...</title>
</head>
<body>
    <!-- 步骤1: 执行设备检测脚本。如果是PC，此脚本会优先跳转到拦截页。 -->
    <script>
        {$detection_script}
    </script>

    <!-- 步骤2: 只有移动设备才能执行到这里。执行此脚本跳转到最终的目标落地页。 -->
    <script>
        window.location.replace({$safe_destination_url});
    </script>
    
    <!-- 备用方案: 为禁用JavaScript的浏览器提供跳转。 -->
    <noscript>
        <meta http-equiv="refresh" content="0;url={$destination_url}" />
    </noscript>
</body>
</html>
HTML;

    // 确保PHP脚本在此处终止
    exit;
}


try {
    // 4. 获取短代码
    $short_code = $_GET['c'] ?? null;
    if (!$short_code) {
        perform_checked_redirect($fallback_url);
    }

    // 5. 查询 promo_links 表
    $stmt = $pdo->prepare("SELECT id, user_id, movie_id FROM promo_links WHERE short_code = :short_code LIMIT 1");
    $stmt->execute(['short_code' => $short_code]);
    $link_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // 6. 检查是否找到了有效的链接记录
    if ($link_data) {
        $promo_link_id = $link_data['id'];
        $user_id = $link_data['user_id'];
        $movie_id = $link_data['movie_id'];

        // 7. 记录本次点击到 `clicks` 表
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
        
        $sql_insert = "INSERT INTO clicks (short_link_id, user_id, favorite_id, ip_address, user_agent, is_fraudulent) VALUES (:short_link_id, :user_id, :favorite_id, :ip_address, :user_agent, 0)";
        
        $insert_stmt = $pdo->prepare($sql_insert);
        
        $insert_stmt->execute([
            ':short_link_id' => $promo_link_id,
            ':user_id'       => $user_id,
            ':favorite_id'   => $movie_id,
            ':ip_address'    => $ip_address,
            ':user_agent'    => $user_agent,
        ]);

        // 8. 拼接最终的落地页链接 (带推广员uid)
        $final_url_base = $fallback_url . '/index.php/vod/detail/id/' . $movie_id . '.html';
        $final_url_with_uid = $final_url_base . (strpos($final_url_base, '?') === false ? '?' : '&') . 'uid=' . $user_id;

        // 9. 执行包含JS检测的跳转
        perform_checked_redirect($final_url_with_uid);
        
    } else {
        // 如果短代码无效，跳转到首页（同样经过JS检测）
        perform_checked_redirect($fallback_url);
    }

} catch (PDOException $e) {
    // 数据库异常时，也跳转到首页（同样经过JS检测）
    // error_log('go.php PDOException: ' . $e->getMessage());
    perform_checked_redirect($fallback_url);
}
?>
