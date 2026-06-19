<?php
    Migrate::$migration = [
        "UserMigration",
        "UserGroupMigration",
        "WebSocket_PromiseMigration",
        "EquipmentMigration",
        "EquipmentEntryMigration",
        "ip_networkMigration",
        "ip_addressMigration",
        "RoutersMigration",
        "ISPMigration",
        "ISP_ConfigurationMigration",
        "cctvLocationMigration",
        "cctvCamera",
        "SettingsMigration",
        "LogMigration",
        "mac_addressMigration",
        "TerminalsMigration",
        "wifiMigration",
        "ConsumablesMigration",
        "Consumable_LogMigration",
        "Consumable_RequestMigration",
        "Post_ItMigration",
        "YK_RoomMigration",
        "YK_ReservedMigration",
        "PGUserMigration",
        "PG_TicketMigration",
        "PG_WSMigration",
        "PG_WS_AssessmentMigration"
    ];

    class UserMigration
    {
        public static function index(){
            Migrate::attrib_table("user");
            Migrate::attrib_string(1000);
            Migrate::string("name");
            Migrate::string("email");
            Migrate::string("privileges");
            Migrate::string("c_authority");
            Migrate::string("passkey");
            Migrate::string("username");
            Migrate::string("password");
        }
    }

    class UserGroupMigration
    {
        public static function index(){
            Migrate::attrib_table("user_group");
            Migrate::attrib_string(1000);
            Migrate::string("group_name");
            Migrate::string("type");
            Migrate::string("supervisors");
            Migrate::string("users");
        }
    }

    class WebSocket_PromiseMigration
    {
        public static function index(){
            Migrate::attrib_table("websocket_promise");
            Migrate::attrib_string(1000);
            Migrate::string("gid");
            Migrate::string("type");
            Migrate::string("message");
            Migrate::string("recipient");
        }
    }

    class EquipmentMigration
    {
        public static function index(){
            Migrate::attrib_table("equipment");
            Migrate::attrib_string(255);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("name");
        }
    }

    class EquipmentEntryMigration
    {
        public static function index(){
            Migrate::attrib_table("equipment_entry");
            Migrate::attrib_string(255);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("eid");
            Migrate::string("description");
            Migrate::string("model_no");
            Migrate::string("barcode");
            Migrate::string("specifications");
            Migrate::string("status");
            Migrate::string("building");
            Migrate::string("room");
            Migrate::string("project");
            Migrate::string("cabinet");
            Migrate::string("remarks");
        }
    }

    class ip_networkMigration
    {
        public static function index(){
            Migrate::attrib_table("ip_network");
            Migrate::attrib_string(255);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("rid");
            Migrate::string("name");
            Migrate::string("from");
            Migrate::string("to");
            Migrate::string("subnet");
        }
    }

    class ip_addressMigration
    {
        public static function index(){
            Migrate::attrib_table("ip_address");
            Migrate::attrib_string(255);
            Migrate::string("nid");
            Migrate::string("ip");
            Migrate::string("subnet");
            Migrate::string("hostname");
            Migrate::string("site");
            Migrate::string("server");
            Migrate::string("state");
            Migrate::string("status");
            Migrate::string("remarks");
            Migrate::string("webmgmtpt");
            Migrate::string("username");
            Migrate::string("password");
        }
    }

    class RoutersMigration
    {
        public static function index(){
            Migrate::attrib_table("routers");
            Migrate::attrib_string(255);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("name");
            Migrate::string("ip");
            Migrate::string("subnet");
            Migrate::string("webmgmtpt");
            Migrate::string("wan1");
            Migrate::string("wan2");
            Migrate::string("active");
        }
    }

    class ISPMigration
    {
        public static function index(){
            Migrate::attrib_table("isp");
            Migrate::attrib_string(255);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("isp_name");
            Migrate::string("name");
            Migrate::string("wan_ip");
            Migrate::string("configuration");            
        }
    }

    class ISP_ConfigurationMigration
    {
        public static function index(){
            Migrate::attrib_table("isp_configuration");
            Migrate::attrib_string(255);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("name");
            Migrate::string("subnet");
            Migrate::string("gateway");
            Migrate::string("dns1");
            Migrate::string("dns2");        
        }
    }

    class cctvLocationMigration
    {
        public static function index(){
            Migrate::attrib_table("cctv_location");
            Migrate::attrib_string(255);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("map_location");
            Migrate::string("floorplan");
            Migrate::string("remarks");
            Migrate::string("camera_size");
        }
    }

    class cctvCamera
    {
        public static function index(){
            Migrate::attrib_table("cctv_camera");
            Migrate::attrib_string(255);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("lid");
            Migrate::string("camera_id");
            Migrate::string("camera_type");
            Migrate::string("camera_subtype");
            Migrate::string("camera_ip_address");
            Migrate::string("camera_port_no");
            Migrate::string("camera_username");
            Migrate::string("camera_password");
            Migrate::string("camera_angle");
            Migrate::string("camera_location");
            Migrate::string("camera_brand");
            Migrate::string("camera_model_no");
            Migrate::string("camera_barcode");
            Migrate::string("camera_status");
            Migrate::string("camera_remarks");
            Migrate::string("cx");
            Migrate::string("cy");
        }
    }

    class SettingsMigration
    {
        public static function index(){
            Migrate::attrib_table("settings");
            Migrate::attrib_string(255);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("sound");
            Migrate::string("theme");
            Migrate::string("inform");
        }
    }
    class LogMigration
    {
        public static function index(){
            Migrate::attrib_table("logs");
            Migrate::attrib_string(255);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("log");
        }
    }

    class mac_addressMigration
    {
        public static function index(){
            Migrate::attrib_table("mac_address");
            Migrate::attrib_string(255);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("wid");
            Migrate::string("mac");
            Migrate::string("name");
            Migrate::string("device");
            Migrate::string("project");
            Migrate::string("location");
            Migrate::string("remarks");
        }
    }

    class TerminalsMigration
    {
        public static function index(){
            Migrate::attrib_table("terminals");
            Migrate::attrib_string("TEXT");
            // change Varchar to TEXT
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("terminal_no");
            Migrate::string("cabinet_no");
            Migrate::string("ip_address");
            Migrate::string("building");
            Migrate::string("room");
            Migrate::string("project");
            Migrate::string("remarks");
            Migrate::string("tech_recommendation");
            Migrate::string("unit_type");
            Migrate::string("casing");
            Migrate::string("motherboard_model");
            Migrate::string("motherboard_barcode");
            Migrate::string("cpu");
            Migrate::string("ram");
            Migrate::string("storage");
            Migrate::string("psu");
            Migrate::string("gpu");
            Migrate::string("cs");
            Migrate::string("ec");
            Migrate::string("id");
            Migrate::string("od");
            Migrate::string("sp");
            Migrate::string("ups_brand");
            Migrate::string("ups_casing_model");
            Migrate::string("ups_casing_barcode");
            Migrate::string("ups_battery");
            Migrate::string("ups_status");
            Migrate::string("kaspersky");
            Migrate::string("bitdefender");
            Migrate::string("windows_update");
            Migrate::string("operating_system");
            Migrate::string("windows_license");
        }
    }

    class wifiMigration
    {
        public static function index(){
            Migrate::attrib_table("wifi");
            Migrate::attrib_string(255);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("name");
            Migrate::string("password");
        }
    }

    class ConsumablesMigration
    {
        public static function index(){
            Migrate::attrib_table("consumables");
            Migrate::attrib_string(255);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("code");
            Migrate::string("description");
            Migrate::string("measurement");
            Migrate::string("unit");
            Migrate::string("stock");
            Migrate::string("restock_point");
            Migrate::string("last_restock");
        }
    }

    class Consumable_LogMigration
    {
        public static function index(){
            Migrate::attrib_table("consumable_logs");
            Migrate::attrib_string(1000);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("cid");
            Migrate::string("date");
            Migrate::string("time");
            Migrate::string("quantity_deduction");
            Migrate::string("remarks");
        }
    }

    class Consumable_RequestMigration
    {
        public static function index(){
            Migrate::attrib_table("consumable_requests");
            Migrate::attrib_string(1000);
            Migrate::string("gid");
            Migrate::string("uid");
            Migrate::string("cid");
            Migrate::string("date");
            Migrate::string("time");
            Migrate::string("requested_quantity");
            Migrate::string("remarks");
            Migrate::string("status");
            Migrate::string("declined_remarks");
        }
    }

    class Post_ItMigration
    {
        public static function index(){
            Migrate::attrib_table("post_it");
            Migrate::attrib_string(1000);
            Migrate::string("name");
            Migrate::string("recipient");
            Migrate::string("message");
        }
    }

    class YK_RoomMigration
    {
        public static function index(){
            Migrate::attrib_table("yk_room");
            Migrate::attrib_string(1000);
            Migrate::string("room_id");
            Migrate::string("room_name");
        }
    }

    class YK_ReservedMigration
    {
        public static function index(){
            Migrate::attrib_table("yk_reserved");
            Migrate::attrib_string(1000);
            Migrate::string("rid");
            Migrate::string("yt_link");
            Migrate::string("yt_singer");
        }
    }

    class PGUserMigration
    {
        public static function index(){
            Migrate::attrib_table("pg_user");
            Migrate::attrib_string(1000);
            Migrate::string("fname");
            Migrate::string("lname");
            Migrate::string("email");
            Migrate::string("job_title");
            Migrate::string("phone");
            Migrate::string("employee_id");
            Migrate::string("privileges");
            Migrate::string("account");
            Migrate::string("username");
            Migrate::string("password");
        }
    }

    class PG_TicketMigration{
        public static function index(){
            Migrate::attrib_table("pg_ticket");
            Migrate::attrib_string(1000);
            Migrate::string("sup_id");
            Migrate::string("ticket_no");
            Migrate::string("incident_datetime");
            Migrate::string("fluctuation_type");
            Migrate::string("priority");
            Migrate::string("area");
            Migrate::string("duration_minutes");
            Migrate::string("description");
        }
    }

    class PG_WSMigration{
        public static function index(){
            Migrate::attrib_table("pg_ws");
            Migrate::attrib_string(1000);
            Migrate::string("ticket_id");
            Migrate::string("ws_number");
            Migrate::string("assigned_user");
            Migrate::string("ups_status");
            Migrate::string("system_unit_status");
            Migrate::string("monitor_status");
            Migrate::string("notes");
            Migrate::string("sign_off_queue");
            Migrate::string("tech_id");
        }
    }

    class PG_WS_AssessmentMigration{
        public static function index(){
            Migrate::attrib_table("pg_ws_assessment");
            Migrate::attrib_string(1000);
            Migrate::string("ws_id");
            Migrate::string("assessed_at");
            Migrate::string("ups_condition");
            Migrate::string("system_unit_condition");
            Migrate::string("monitor_condition");
            Migrate::string("technical_findings");
            Migrate::string("parts_needed");
            Migrate::string("escalate_to");
        }
    }
?>