<?php

    class User
    {
        public $table = "user";
        public $fillable = [
            "name",
            "email",
            "privileges",
            "c_authority",
            "passkey",
            "username",
            "password"
        ];

        public string $name;
        public string $email;
        public string $privileges;
        public string $passkey;
        public string $username;
        public string $password;
    }

    class User_Group{
        public $table = "user_group";
        public $fillable = [
            "group_name",
            "type",
            "supervisors", // Format ID = [1|2|3|4|5|6|7]
            "users" // Format ID = [1|2|3|4|5|6|7]
        ];

        public string $group_name;
        public string $type;
        public string $supervisors; // example: "1|2|3|4"
        public string $users;       // example: "1|2|3|4"
    }

    class WebSocket_Promise{
        public $table = "websocket_promise";
        public $fillable = [
            "gid",
            "type", // example: text / action
            "message",
            "recipient",
        ];

        public string $gid;
        public string $type;
        public string $message;
        public string $recipient;
    }

    class Equipment
    {
        public $table = "equipment";
        public $fillable = [
            "gid",
            "uid",
            "name"
        ];

        public string $gid;
        public string $uid;
        public string $name;

        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "name:" => "Name:"
        ];
        public $ignore = [
            "id",
            "uid"
        ];
        public $main = "name";
    }

    class Equipment_Entry
    {
        public $table = "equipment_entry";
        public $fillable = [
            "gid",
            "uid",
            "eid",
            "description",
            "model_no",
            "barcode",
            "specifications",
            "status",
            "building",
            "room",
            "project",
            "cabinet",
            "remarks"
        ];

        public string $gid;
        public string $uid;
        public string $eid;

        public string $description;
        public string $model_no;
        public string $barcode;
        public string $specifications;
        public string $status;

        public string $building;
        public string $room;
        public string $project;
        public string $cabinet;
        public string $remarks;

        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "eid:" => "EID:",
            "description:" => "Description:",
            "model_no:" => "Model No.:",
            "barcode:" => "Barcode:",
            "specifications:" => "Specifications:",
            "status:" => "Status:",
            "remarks:" => "Remarks:"
        ];
        public $ignore = [
            "gid",
            "uid",
            "eid",
            "at",
            "tio",
            "ti",
            "io",
            "on",
            "ion"
        ];
        public $main = "description";
    }

    class IP_Network
    {
        public $table = "ip_network";
        public $fillable = [
            "gid",
            "uid",
            "rid",
            "name",
            "from",
            "to",
            "subnet"
        ];

        public string $gid;
        public string $uid;
        public string $rid;
        public string $name;
        public string $from;
        public string $to;
        public string $subnet;

        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "rid:" => "RID:",
            "name:" => "Name:",
            "from:" => "IP from:",
            "to:" => "IP to:",
            "subnet:" => "Subnet:"
        ];
        public $ignore = [
            "id",
            "gid",
            "uid",
            "rid",
            "ne",
            "net",
            "et",
            "me"
        ];
        public $main = "name";
    }

    class IP_Address
    {
        public $table = "ip_address";
        public $fillable = [
            "nid",
            "ip",
            "subnet",
            "hostname",
            "site",
            "server",
            "state",
            "status",
            "remarks",
            "webmgmtpt",
            "username",
            "password"
        ];

        public string $nid;
        public string $ip;
        public string $subnet;
        public string $hostname;
        public string $site;
        public string $server;
        public string $state;
        public string $status;
        public string $remarks;
        public string $webmgmtpt;
        public string $username;
        public string $password;

        public $label = [
            "nid:" => "NID:",
            "ip:" => "IP:",
            "subnet:" => "Subnet:",
            "hostname:" => "Hostname:",
            "site:" => "Site:",
            "server:" => "Server:",
            "state:" => "State:",
            "status:" => "Status:",
            "remarks:" => "Remarks:",
            "webmgmtpt:" => "Web Mgmt Port:",
            "username:" => "Username:",
            "password:" => "Password:"
        ];
        public $ignore = [
            "id",
            "nid",
            "er",
            "se",
            "ser",
            "name",
            "na",
            "am",
            "me",
            "nam",
            "ame",
            "te",
        ];
        public $main = "hostname";
    }

    class Routers{
        public $table = "routers";
        public $fillable = [
            "gid",
            "uid",
            "name",
            "ip",
            "subnet",
            "webmgmtpt",
            "wan1",
            "wan2",
            "active"
        ];

        public string $gid;
        public string $uid;
        public string $name;
        public string $ip;
        public string $subnet;
        public string $webmgmtpt;
        public string $wan1;
        public string $wan2;
        public string $active;

        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "name:" => "Name:",
            "ip:" => "IP:",
            "subnet:" => "Subnet:",
            "webmgmtpt:" => "Web Management Port:",
            "wan1:" => "WAN 1:",
            "wan2:" => "WAN 2:",
            "active:" => "Active WAN:",
        ];
        public $ignore = [
            "id",
            "gid",
            "uid",
            "wan",
            "wa",
            "an",
            "me"
        ];
        public $main = "name";
    }

    class ISP_Configuration{
        public $table = "isp_configuration";
        public $fillable = [
            "gid",
            "uid",
            "name",
            "subnet",
            "gateway",
            "dns1",
            "dns2",
        ];

        public string $gid;
        public string $uid;
        public string $name;
        public string $subnet;
        public string $gateway;
        public string $dns1;
        public string $dns2;

    }

    class ISP{
        public $table = "isp";
        public $fillable = [
            "gid",
            "uid",
            "isp_name",
            "name",
            "wan_ip",
            "configuration"
        ];

        public string $gid;
        public string $uid;
        public string $isp_name;
        public string $name;
        public string $wan_ip;
        public string $webmgmtpt;
        public string $configuration;

        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "isp_name:" => "ISP Name:",
            "name:" => "Name:",
            "wan_ip:" => "WAN IP:",
            "subnet:" => "Subnet:",
            "gateway:" => "Gateway:",
            "dns1:" => "DNS 1:",
            "dns2:" => "DNS 2:",
            "webmgmtpt:" => "Web Mgmt Port:",
            "configuration:" => "Configuration:",
        ];
        public $ignore = [
            "id",
            "gid",
            "uid",
            "name",
            "na",
            "am",
            "me",
            "nam",
            "ame",
            "dns",
            "dn",
            "ns",
            "isp",
            "is",
            "sp"
        ];
        public $main = "name";
    }

    class Settings{
        public $table = "settings";
        public $fillable = [
            "gid",
            "uid",
            "sound",
            "theme",
            "inform",
        ];

        public string $gid;
        public string $uid;
        public string $sound;
        public string $theme;

        public $ignore = [
            "id",
            "uid"
        ];
    }

    class Logs{
        public $table = "logs";
        public $fillable = [
            "gid",
            "uid",
            "log"
        ];
        
        public string $gid;
        public string $uid;
        public string $log;

        public $ignore = [
            "id",
            "uid"
        ];
        public $main = "log";
    }

    class CCTV_Location{
        public $table = "cctv_location";
        public $fillable = [
            "gid",
            "uid",
            "map_location",
            "floorplan",
            "remarks",
            "camera_size",
        ];

        public string $gid;
        public string $uid;
        public string $map_location;
        public string $floorplan;
        public string $remarks;
        public string $camera_size;

        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "map_location:" => "Location:",
            "floorplan:" => "Floorplan:",
            "remarks:" => "Remarks:",
            "camera_size:" => "Camera Size(px):",
        ];
        public $ignore = [
            "id",
            "gid",
            "uid",
            "lo",
            "ma",
            "map",
            "ma",
            "ap",
            "me"
        ];
        public $main = "map_location";
    }

    class CCTV_Camera{
        public $table = "cctv_camera";
        public $fillable = [
            "gid",
            "uid",
            "lid",
            "camera_id",
            "camera_type",
            "camera_subtype",
            "camera_ip_address",
            "camera_port_no",
            "camera_username",
            "camera_password",
            "camera_angle",
            "camera_location",
            "camera_brand",
            "camera_model_no",
            "camera_barcode",
            "camera_status",
            "camera_remarks",
            "cx",
            "cy"
        ];

        public string $gid;
        public string $uid;
        public string $lid;
        public string $camera_id;
        public string $camera_type;
        public string $camera_subtype;
        public string $camera_ip_address;
        public string $camera_port_no;
        public string $camera_username;
        public string $camera_password;
        public string $camera_angle;
        public string $camera_location;
        public string $camera_brand;
        public string $camera_model_no;
        public string $camera_barcode;
        public string $camera_status;
        public string $camera_remarks;
        public string $cx;
        public string $cy;

        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "lid:" => "LID:",
            "camera_id:" => "Name:",
            "camera_type:" => "Type:",
            "camera_subtype:" => "Subtype:",
            "camera_ip_address:" => "IP:",
            "camera_port_no:" => "Port No:",
            "camera_username:" => "Username:",
            "camera_password:" => "Password:",
            "camera_angle:" => "Angle(°deg):",
            "camera_location:" => "Location:",
            "camera_brand:" => "Brand:",
            "camera_model_no:" => "Model No.:",
            "camera_barcode:" => "Barcode:",
            "camera_status:" => "Status:",
            "camera_remarks:" => "Remarks:",
            "cx:" => "CX:",
            "cy:" => "CY:",
        ];
        public $ignore = [
            "id",
            "gid",
            "uid",
            "lid",
            "camera",
            "ca",
            "am",
            "me",
            "er",
            "ra",
            "cam",
            "ame",
            "mer",
            "era",
            "pe",
            "ty",
            "yp",
            "ar"
        ];
        public $main = "camera_id";
    }

    class MAC_Address{
        public $table = "mac_address";
        public $fillable = [
            "gid",
            "uid",
            "wid",
            "mac",
            "name",
            "device",
            "project",
            "location",
            "remarks"
        ];

        public string $gid;
        public string $uid;
        public string $wid;
        public string $mac;
        public string $name;
        public string $device;
        public string $project;
        public string $location;
        public string $remarks;

        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "wid:" => "WID:",
            "name:" => "Name:",
            "mac:" => "MAC:",
            "device:" => "Device:",
            "project:" => "Project:",
            "location:" => "Location:",
            "remarks:" => "Remarks:"
        ];
        public $ignore = [
            "id",
            "gid",
            "uid",
            "wid",
            "ma"
        ];
        public $main = "name";
    }

    class Terminals{
        public $table = "terminals";
        public $fillable = [
            "gid",
            "uid",
            "terminal_no",
            "cabinet_no",
            "ip_address",
            "building",
            "room",
            "project",
            "remarks",
            "tech_recommendation",
            "unit_type",
            "casing",
            "motherboard_model",
            "motherboard_barcode",
            "cpu",
            "ram",
            "storage",
            "psu",
            "gpu",
            "cs",
            "ec",
            "id_",
            "od",
            "sp",
            "ups_brand",
            "ups_casing_model",
            "ups_casing_barcode",
            "ups_battery",
            "ups_status",
            "kaspersky",
            "bitdefender",
            "windows_update",
            "operating_system",
            "windows_license"
        ];

        public string $terminal_no;
        public string $cabinet_no;
        public string $ip_address;
        public string $building;
        public string $room;
        public string $project;
        public string $remarks;
        public string $tech_recommendation;
        public string $unit_type;
        public string $casing;
        public string $motherboard_model;
        public string $motherboard_barcode;
        public string $cpu;
        public string $ram;
        public string $storage;
        public string $psu;
        public string $gpu;
        public string $cs;
        public string $ec;
        public string $id;
        public string $od;
        public string $sp;
        public string $ups_brand;
        public string $ups_casing_model;
        public string $ups_casing_barcode;
        public string $ups_battery;
        public string $ups_status;
        public string $kasperky;
        public string $bitdefender;
        public string $windows_update;
        public string $operating_system;
        public string $windows_license;
    }

    class Wifi{
        public $table = "wifi";
        public $fillable = [
            "gid",
            "uid",
            "name",
            "password"
        ];

        public string $gid;
        public string $uid;
        public string $name;
        public string $password;

        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "name:" => "Name:",
            "password:" => "Password:"
        ];
        public $ignore = [
            "id",
            "uid"
        ];
        public $main = "name";
    }

    class Consumables{
        public $table = "consumables";
        public $fillable = [
            "gid",
            "uid",
            "code",
            "description",
            "measurement",
            "unit",
            "stock",
            "restock_point",
            "last_restock"
        ];

        public string $gid;
        public string $uid;
        public string $code;
        public string $description;
        public string $measurement;
        public string $unit;
        public string $stock;
        public string $restock_point;
        public string $last_restock;

        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "code:" => "Code:",
            "description:" => "Description:",
            "measurement:" => "Measurement:",
            "unit:" => "Unit:",
            "stock:" => "Stock:",
            "restock_point:" => "Restock Point:",
            "last_restock:" => "Last Restock:",
            "last_reStock:" => "Last Restock:"
        ];
        public $ignore = [
            "id",
            "gid",
            "uid",
            "es",
            "de",
            "re",
            "me",
            "nt",
            "st",
            "to",
            "oc",
            "ck",
            "sto",
            "toc",
            "ock",
            "es",
            "res",
            "est",
        ];
        public $main = "description";
    }

    class Consumable_Log{
        public $table = "consumable_logs";
        public $fillable = [
            "gid",
            "uid",
            "cid",
            "date",
            "time",
            "quantity_deduction",
            "remarks",
        ];

        public string $gid;
        public string $uid;
        public string $cid;
        public string $date;
        public string $time;
        public string $quantity_deduction;
        public string $remarks;

        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "cid:" => "CID:",
            "date:" => "Date:",
            "time:" => "Time:",
            "quantity_deduction:" => "Quantity Deduction:",
            "remarks:" => "Remarks:",
        ];
        public $ignore = [
            "id",
            "gid",
            "uid",
            "cid",
            "ti"
        ];
        public $main = "remarks";
    }

    class Consumable_Request{
        public $table = "consumable_requests";
        public $fillable = [
            "gid",
            "uid",
            "cid",
            "date",
            "time",
            "requested_quantity",
            "remarks",
            "status",
            "declined_remarks",
        ];

        public string $gid;
        public string $uid;
        public string $cid;
        public string $date;
        public string $time;
        public string $requested_quantity;
        public string $remarks;
        public string $status;
        public string $declined_remarks;

        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "cid:" => "CID:",
            "date:" => "Date:",
            "time:" => "Time:",
            "requested_quantity:" => "Requested Quantity:",
            "remarks:" => "Remarks:",
            "status:" => "Status:",
            "declined_remarks:" => "Declined Remarks:",
        ];
        public $ignore = [
            "id",
            "gid",
            "uid",
            "cid",
            "ti",
            "re",
            "em",
            "ma",
            "ar",
            "rk",
            "ks",
            "rem",
            "ema",
            "mar",
            "ark",
            "rks",
            "ed"
            
        ];
        public $main = "remarks";
    }

    class Post_It{
        public $table = "post_it";
        public $fillable = [
            "name",
            "recipient",
            "message",
        ];

        public string $name;
        public string $recipient;
        public string $message;
    }

    class YK_Room{
        public $table = "yk_room";
        public $fillable = [
            "room_id",
            "room_name"
        ];

        public string $room_id;
        public string $room_name;
    }

        class YK_Reserved{
        public $table = "yk_reserved";
        public $fillable = [
            "rid",
            "yt_link",
            "yk_singer"
        ];

        public string $rid;
        public string $yt_link;
        public string $yk_singer;
    }
?>