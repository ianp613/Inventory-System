<?php
    function invalidate_isp_router_caches($redis, $g_id) {
        $redis->del("icore_current_configuration:all");
        $redis->del("icore_isp_router:all" . $g_id);
        $redis->del("icore_available_isp:all" . $g_id);
        $redis->del("icore_routers:all" . $g_id);

        $keys = $redis->keys("icore_router_isp:all*");
        foreach ($keys as $key) {
            $redis->del($key);
        }
    }

    function invalidate_user_group_caches($redis, $g_id = null) {
        // User-related caches
        $redis->del("icore_user:all");
        $redis->del("icore_user:raw_all");
        $redis->del("icore_accounts_dropdown:all");
        $redis->del("icore_accounts:allglobal");
        if ($g_id !== null) {
            $redis->del("icore_accounts:all" . $g_id);
        }

        // Group-related caches
        $redis->del("icore_group:all");
    }

    function invalidate_wifi_caches($redis, $g_id = null) {
        $redis->del("icore_wifi:all");
        if ($g_id !== null) {
            $redis->del("icore_wifi:session" . $g_id);
        }
    }
    function invalidate_mac_caches($redis, $g_id = null) {
        $keys = $redis->keys("icore_mac:*");
        foreach ($keys as $key) {
            $redis->del($key);
        }
    }

    function invalidate_user_cache($redis, $user_id) {
        $redis->del("icore_user:" . $user_id);
    }
?>