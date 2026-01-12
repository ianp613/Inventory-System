<?php
    Migrate::$migration = [
        "UserMigration",
        "UserGroupMigration",
        "EquipmentMigration",
        "EquipmentEntryMigration",
        "ip_networkMigration",
        "ip_addressMigration",
        "RoutersMigration",
        "ISPMigration",
        "cctvLocationMigration",
        "cctvCamera",
        "SettingsMigration",
        "LogMigration",
        "mac_addressMigration",
        "wifiMigration",
        "ConsumablesMigration",
        "Consumable_LogMigration"
    ];

    class UserMigration
    {
        public static function index(){
            Migrate::attrib_table("user");
            Migrate::attrib_string(1000);
            Migrate::string("name");
            Migrate::string("email");
            Migrate::string("privileges");
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
            Migrate::string("router");
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
            Migrate::string("subnet");
            Migrate::string("gateway");
            Migrate::string("dns1");
            Migrate::string("dns2");
            Migrate::string("webmgmtpt");
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
        }
    }

    class Consumable_LogMigration
    {
        public static function index(){
            Migrate::attrib_table("consumable_logs");
            Migrate::attrib_string(1000);
            Migrate::string("uid");
            Migrate::string("cid");
            Migrate::string("date");
            Migrate::string("time");
            Migrate::string("quantity_deduction");
            Migrate::string("remarks");
        }
    }
?>