<?php
    class Data{
        public static function load($name,$data){
            $_SESSION[$name] = $data;
            return $data;
        }
        public static function unload($name){
            $data = $_SESSION[$name];
            return $data;
        }
        public static function push($name,$data){
            if(is_array($_SESSION[$name])){
                array_push($_SESSION[$name],$data);
            }
            $data = $_SESSION[$name];
            return $data;
        }
        public static function pop($name){
            if(is_array($_SESSION[$name])){
                array_pop($_SESSION[$name]);
            }
            $data = $_SESSION[$name];
            return $data;
        }
        public static function trash($name){
            if(is_array($_SESSION[$name])){
                $_SESSION[$name] = [];    
            }else{
                $_SESSION[$name] = "";    
            }
            $data = $_SESSION[$name];
            return $data;
        }
        public static function json_response($data){
            print_r(json_encode($data));
        }
        public static function reverse($arr){
            return array_reverse($arr);
        }
        public static function shuffle($str){
            return str_shuffle($str);
        }
        public static function pick($arr){
            return $arr[rand(0,count($arr)-1)];
        }
        public static function pp($data = null){
            echo "<style>
                    .sole-dataprint{
                        font-family: arial;
                        font-size: 16px;
                        margin: 8px;
                    }
                </style>";
            echo('<div style="background-color: black; padding: 5px;">');
            if(is_object($data)){
                echo('<h6 class="sole-dataprint" style="color: red;">Note: This is a work in progress function, array keys and instance of an object will not be displayed.</h6>');
            }
            Data::pp_start($data);
            echo('</div>');
        }
        public static function pp_start($data = null){
            if(is_object($data)){
                $datatemp = [];
                foreach($data as $d){
                    array_push($datatemp,$d);
                }
                $data = $datatemp;
            }
            if(is_array($data)){
                echo('<div style="margin-left: 10px;">');
                echo('<h6 class="sole-dataprint" style="color: greenyellow;"><i>Array('.count($data).')</i></h6>');
                Data::pp_assist($data);
            echo('</div>');
            }else{
                echo('<span style="color: brown;">'.$data.'</span></h6>');
            }
        }
        public static function pp_assist($data){
            $keys = array_keys($data);
            foreach($keys as $k){
                if(is_array($data) || is_object($data)){
                    echo('<div style="margin-left: 10px;">');
                    echo('<h6 class="sole-dataprint" style="color: brown;"><span style="color: orange;">'.$k.'</span> '.': ');
                    Data::pp_start($data[$k]);
                    echo('</div>');
                }else{
                    echo('<span style="color: brown;">'.$data.'</span></h6>');
                }
            }
        }
        public static function generate($n,$type){
            $key = "";
            $case = 0;
            $letter = [
                "a","b","c","d","e","f","g","h","i","j","k","l","m",
                "n","o","p","q","r","s","t","u","v","w","x","y","z"
            ];
            $number = [
                "1","2","3","4","5","6","7","8","9"
            ];
            $symbol = [
                "`","~","!","@","#","$","%","^","&","*","(",")","-","_","=","+","[","]","{","}",";",":","'",'"',",",".","<",">","/","|","?",
            ];
            if($type == "alpha"){
                for ($i=0; $i < $n; $i++) { 
                    $case = rand(0,1);
                    if($case){
                        $key .= strtoupper($letter[rand(0,count($letter)-1)]);
                    }else{
                        $key .= strtolower($letter[rand(0,count($letter)-1)]);
                    }
                }
            }elseif($type == "numeric"){
                for ($i=0; $i < $n; $i++) { 
                    $key .= $number[rand(0,count($number)-1)];
                }
            }elseif($type == "symbol"){
                for ($i=0; $i < $n; $i++) { 
                    $key .= $symbol[rand(0,count($symbol)-1)];
                }
            }elseif($type == "alphanumeric"){
                for ($i=0; $i < $n; $i++) { 
                    $key_type = rand(0,1);
                    if($key_type){
                        $case = rand(0,1);
                        if($case){
                            $key .= strtoupper($letter[rand(0,count($letter)-1)]);
                        }else{
                            $key .= strtolower($letter[rand(0,count($letter)-1)]);
                        }
                    }else{
                        $key .= $number[rand(0,count($number)-1)];
                    }    
                }
            }elseif($type == "alphasymbol"){
                for ($i=0; $i < $n; $i++) { 
                    $key_type = rand(0,1);
                    if($key_type){
                        $case = rand(0,1);
                        if($case){
                            $key .= strtoupper($letter[rand(0,count($letter)-1)]);
                        }else{
                            $key .= strtolower($letter[rand(0,count($letter)-1)]);
                        }
                    }else{
                        $key .= $symbol[rand(0,count($symbol)-1)];
                    }    
                }
            }
            elseif($type == "numericsymbol"){
                for ($i=0; $i < $n; $i++) { 
                    $key_type = rand(0,1);
                    if($key_type){
                        $key .= $number[rand(0,count($number)-1)];
                    }else{
                        $key .= $symbol[rand(0,count($symbol)-1)];
                    }    
                }
            }
            elseif($type == "alphanumericsymbol"){
                for ($i=0; $i < $n; $i++) { 
                    $key_type = rand(0,2);
                    if($key_type == 0){
                        $case = rand(0,1);
                        if($case){
                            $key .= strtoupper($letter[rand(0,count($letter)-1)]);
                        }else{
                            $key .= strtolower($letter[rand(0,count($letter)-1)]);
                        }
                    }elseif($key_type == 1){
                        $key .= $number[rand(0,count($number)-1)];
                    }
                    elseif($key_type == 2){
                        $key .= $symbol[rand(0,count($symbol)-1)];
                    }    
                }
            }
            else{
                $key = uniqid();
            }
            return $key;
        }
    }
?>