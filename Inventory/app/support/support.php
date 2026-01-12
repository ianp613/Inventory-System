<?php
    class Support{
        public static function getLogo($logo,$file){
            if($logo["logo"] == "custom"){
                return $file["logo_file"]["tmp_name"];
            }
            if($logo["logo"] != "custom" && $logo["logo"] != "none"){
                return $logo["logo"];
            }
        }
    }