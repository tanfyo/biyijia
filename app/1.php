<?php
// 文件: /go.php (独立服务器、动态落地页最终版)

// ==========================================================
//  ▼▼▼【核心配置】主推广系统数据库信息 ▼▼▼
// ==========================================================
$db_host = '103.117.122.131'; // 主数据库服务器的公网IP地址
$db_name = 'k1yy';             // 主数据库的名称
$db_user = 'k1yy';             // 主数据库的用户名
$db_pass = 'a11225500';        // 主数据库的密码
$db_charset = 'utf8mb4';
// ==========================================================
//  ▲▲▲ 苹果CMS落地页域名将从以上数据库中动态获取 ▲▲▲
// ==========================================================

// 初始化PDO连接变量
$pdo = null;

try {
    // 远程连接数据库
    $dsn = "mysql:host={$db_host};dbname={$db_name};charset={$db_charset}";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // 数据库连接失败是致命错误，无法继续
    header("HTTP/1.1 503 Service Unavailable");
    exit("Service Unavailable (DB Connect Error)");
}

/**
 * 输出包含JS设备检测的HTML页面来执行跳转
 */
function perform_checked_redirect($destination_url) {
    // 如果目标URL为空或无效，则提供一个绝对安全的备用地址
    if (empty($destination_url) || !filter_var($destination_url, FILTER_VALIDATE_URL)) {
        $destination_url = 'https://www.qq.com/'; // 绝对安全的备用地址
    }
    $safe_destination_url = json_encode($destination_url);
    echo <<<HTML
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>正在安全跳转...</title></head><body>
<script>
    (function() {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (!isMobile) {
            window.location.replace('https://www.qq.com'); 
        } else {
            window.location.replace({$safe_destination_url});
        }
    })();
</script>
<noscript><meta http-equiv="refresh" content="0;url={$destination_url}" /></noscript>
</body></html>
HTML;
    exit;
}

// --- 主逻辑开始 ---
try {
    // 【核心修改】在处理任何跳转前，先从远程数据库获取当前启用的落地页域名
    $stmt_domain = $pdo->prepare("SELECT domain_url FROM domains WHERE domain_type = 'landing' AND status = 'active' LIMIT 1");
    $stmt_domain->execute();
    $active_landing_domain = $stmt_domain->fetchColumn();

    // 如果数据库中没有配置“正在使用”的落地页，这是一个严重的配置错误
    if (!$active_landing_domain) {
        // 记录错误日志，方便管理员排查
        error_log("CRITICAL ERROR in go.php: No active landing page domain found in the database!");
        // 同样显示服务不可用
        header("HTTP/1.1 503 Service Unavailable");
        exit("Service Unavailable (Config Error)");
    }

    // 将动态获取的域名作为默认跳转地址
    $fallback_url = rtrim($active_landing_domain, '/');

    // 获取短代码
    $short_code = $_GET['c'] ?? null;
    if (!$short_code) {
        perform_checked_redirect($fallback_url);
    }

    // 从【远程数据库】查询 promo_links 表
    $stmt = $pdo->prepare("SELECT id, user_id, movie_id FROM promo_links WHERE short_code = :short_code LIMIT 1");
    $stmt->execute(['short_code' => $short_code]);
    $link_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($link_data) {
        $promo_link_id = $link_data['id'];
        $user_id = $link_data['user_id'];
        $movie_id = $link_data['movie_id'];

        // 向【远程数据库】的 clicks 表记录点击
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
        
        $sql_insert = "INSERT INTO clicks (short_link_id, user_id, favorite_id, ip_address, user_agent) VALUES (:short_link_id, :user_id, :favorite_id, :ip_address, :user_agent)";
        $insert_stmt = $pdo->prepare($sql_insert);
        $insert_stmt->execute([
            ':short_link_id' => $promo_link_id,
            ':user_id'       => $user_id,
            ':favorite_id'   => $movie_id,
            ':ip_address'    => $ip_address,
            ':user_agent'    => $user_agent,
        ]);

        // 使用【动态获取】的域名拼接最终的落地页链接
        $final_url_with_uid = $fallback_url . '/index.php/vod/detail/id/' . $movie_id . '.html?uid=' . $user_id;

        // 执行包含JS检测的跳转
        perform_checked_redirect($final_url_with_uid);
        
    } else {
        // 如果短代码无效，跳转到动态获取的落地页首页
        perform_checked_redirect($fallback_url);
    }

} catch (PDOException $e) {
    // 数据库操作异常时，安全地退出
    // error_log('go.php (standalone) PDOException: ' . $e->getMessage());
    // 此时 $fallback_url 可能还未定义，所以提供一个最终的备用地址
    perform_checked_redirect('https://www.qq.com/');
}
?>
