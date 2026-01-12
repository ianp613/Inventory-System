<?php

    class User
    {
        public $table = "user";
        public $fillable = [
            "name",
            "email",
            "privileges",
            "passkey",
            "username",
            "password"
        ];
    }

    class User_Group{
        public $table = "user_group";
        public $fillable = [
            "group_name",
            "type",
            "supervisors", // Format ID = [1|2|3|4|5|6|7]
            "users" // Format ID = [1|2|3|4|5|6|7]
        ];
    }

    class Equipment
    {
        public $table = "equipment";
        public $fillable = [
            "gid",
            "uid",
            "name"
        ];
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
            "remarks"
        ];
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
            "wan1",
            "wan2",
            "active"
        ];
        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "name:" => "Name:",
            "ip:" => "IP:",
            "subnet:" => "Subnet:",
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

    class ISP{
        public $table = "isp";
        public $fillable = [
            "gid",
            "uid",
            "isp_name",
            "name",
            "wan_ip",
            "subnet",
            "gateway",
            "dns1",
            "dns2",
            "webmgmtpt"
        ];
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
        ];
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

    class Wifi{
        public $table = "wifi";
        public $fillable = [
            "gid",
            "uid",
            "name",
            "password"
        ];
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
            "restock_point"
        ];
        public $label = [
            "gid:" => "GID:",
            "uid:" => "UID:",
            "code:" => "Code:",
            "description:" => "Description:",
            "measurement:" => "Measurement:",
            "unit:" => "Unit:",
            "stock:" => "Stock:",
            "restock_point:" => "Restock Point:"
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
            "stock",
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
        public $main = "description";
    }
?>