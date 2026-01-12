<?php
    class Identifier {
        public static function main($input) {
            $input = str_replace(",","",$input);
            $input = str_replace(":","",$input);
            $models = [
                "ipaddress" => new IP_Address(),
                "equipment" => new Equipment(),
                "entry" => new Equipment_Entry(),
                "network" => new IP_Network(),
                "router" => new Routers(),
                "isp" => new ISP(),
                "map" => new CCTV_Location(),
                "cctv" => new CCTV_Camera(),
                "mac" => new MAC_Address(),
                "wifi" => new Wifi(),
                "consumable" => new Consumables()
            ];

            $ignore = ["en", "ent","nt","me"];  // Add any more short tokens here

            // $input_temp = strtolower(trim($input));
            // $in = explode("in", $input);
            // $input = count($in) == 2 ? $in[1] : $input;

            foreach ($models as $field => $model) {
                $words = preg_split('/\s+/', strtolower($input));

                foreach ($words as $word) {
                    if (strlen($word) < 2) continue;
                    if (in_array($word, $ignore)) continue;  // Skip ignored words

                    if (stripos($field, $word) !== false) {
                        $input = str_replace($word,"",$input);
                        return Identifier::generateFromModel($model, $input);
                    }
                }
            }
            return "No matching data found.";
        }

        public static function generateFromModel($model, $input) {
            $reply_type = "string";
            $reply_file = "";
            $findID = preg_match('/\bid\b/i', $input) === 1;
            $input = str_replace("id","",$input);
            $table = $model->table;
            $fillable = $model->fillable;
            $ignore = $model->ignore;
            $column = [];
            $filteredWords = [];

            // Extract all the data in the database
            $data = DB::all($model);
            $values = [];
            foreach ($data as $record) {
                foreach ($record as $key => $value) {
                    // Skip nulls or unnecessary fields if needed
                    if (!is_null($value) && $value != "-" && !in_array($key, ['created_at', 'updated_at'])) {
                        $values[] = strtolower(trim($value)); // Normalize for matching
                    }
                }
            }

            // Get all matching data from database that are in the $input
            $input = strtolower(trim($input));
            $words = preg_split('/\s+/', $input);
            $ignored = ["table","or","column","from","someone","some","which","something","everyone","everything","every","log","can","provide","their","you","please","find","of","is","me","and","equal","value", "give","also", "all", "in", "on","=","than","less","greater","at", "to", "the", "show", "list", "data","who","have","has","been", "using", "with", "for","map","that"];
            $results = [];
            foreach ($words as $word) {
                if (strlen($word) < 2 || in_array($word, $ignored)) continue;
                foreach ($values as $value) {
                    if (stripos($value, $word) !== false) {
                        $results[] = $value;
                    }
                }
            }
            $results = array_unique($results); // Removes duplicates


            if(!$findID){
                $input = strtolower(trim($input));
                $wor = preg_split('/\s+/', $input);
                // Filter out ignored words and store the cleaned input back into $input
                $filwor = array_filter($wor, function ($wo) use ($ignored) {
                    return strlen($wo) >= 2 && !in_array($wo, $ignored);
                });
                $input = implode(' ', $filwor);    
            }
            

            // get columns to be used
            $words = preg_split('/\s+/', strtolower($input));
            foreach ($words as $word) {
                if ((!ctype_digit($word) && strlen($word) < 2) || (!$findID && ctype_digit($word))) continue;
                if (in_array($word, $ignore)) continue;

                $isColumnMatch = false;

                foreach ($fillable as $fill) {
                    if (strlen($word) > 1 && stripos($fill, $word) !== false) {
                        $column[] = $fill;
                        $isColumnMatch = true;
                        break;
                    }
                }
                // Keep word only if it's not matched to any column
                if (!$isColumnMatch) {
                    $filteredWords[] = $word;
                }
            }

            
            $cleanedInput = implode(' ', $filteredWords);
            $cleanedInput = str_replace($model->table,"",$cleanedInput);
            foreach ($ignored as $i) {
                $cleanedInput = str_replace($i,"",$cleanedInput);
            }
            $column = array_unique($column); // Removes duplicates
            // $cleanedInput = str_replace(" ","",$cleanedInput);

            // remove all character from the remaining string in &input except numeric and use only the first occurence or numeric value
            if($findID){
                preg_match_all('/\d+/', $cleanedInput, $matches);
                if(count($matches[0])){
                    $numbers = $matches[0];
                    $cleanedInput = $numbers[0];    
                }
                
            }

            // Get row in database where value is in $results
            $data = DB::all($model);
            $row = [];
            foreach ($data as $d) {
                foreach ($column as $c) {
                    in_array(strtolower($d[$c]),array_map('strtolower',$results)) ? $row[] = $d : null;
                }
                in_array(strtolower($d[$model->main]),array_map('strtolower',$results)) ? $row[] = $d : null;
            }


            // Removes duplicates for multidimensional array
            $id = [];
            $temp_row = [];
            foreach ($row as $r) {
                if(!in_array($r["id"],$id)){
                    array_push($id,$r["id"]);
                    array_push($temp_row,$r);
                }
            }
            $row = $temp_row;


            // in_array("ip",$column) ? $column = array_diff($column,["ip"]): null;
            // return ["string",count($column),""];
            // return ["string",$findID];

            if(count($column)){
                count($row) ? $reply = "" : $reply = "No matching data found.1";
                if(!count($row) && count($column)){
                    $data = DB::all($model);
                    count($data) ? $reply = "" : $reply = "No matching data found.2";
                    foreach ($data as $d) {
                        $reply .= "<b>--- ".$d[$model->main]."</b>br|";
                        $reply .= "[<b>ID: </b><i>".$d["id"]."</i>br|";

                        foreach ($column as $c) {
                            $reply .= "[<b>".$c.": </b><i>".$d[$c]."</i>br|";
                        }
                        $reply .= "br|";
                    }
                }
                if(count($row) && count($column)){
                    for ($i = 0; $i < count($row); $i++) {
                        $reply .= "<b>--- ".$row[$i][$model->main]."</b>br|";
                        foreach ($column as $c) {
                            for ($j = 0; $j < count($row); $j++) {
                                if($row[$j][$model->main] == $row[$i][$model->main]){
                                    $reply .= "<b>".$c.": </b><i>".($row[$j][$c] != "-" ? $row[$j][$c] : "")."</i>br|";
                                }
                            }
                        }
                        $reply .= "br|";
                    }
                }
                return [$reply_type,Identifier::label_replace($reply,$model),$reply_file];
                // return Identifier::breaker($reply);
            }else{
                count($row) ? $reply = "" : $reply = "No matching data found.3";
                if(!$findID && $cleanedInput){
                    for ($i = 0; $i < count($row); $i++) {
                        $reply .= "<b>--- ".$row[$i][$model->main]."</b>br|";
                        $reply .= "[<b>ID: </b><i>".$row[$i]["id"]."</i>br|";
                        $reply .= "[<b>".$model->main.": </b><i>".$row[$i][$model->main]."</i>br|";
                        $model->table == "ip_address" ? $reply .= "[<b>IP: </b><i>".$row[$i]["ip"]."</i>br|" : null;

                        count($column) ? $reply .= "br|" : null;
                    }
                }
                
                if(!count($row) && count($results) && !$findID){
                    $data = DB::all($model);
                    $row = [];
                    $fillable = [];

                    foreach ($data as $d) {
                        foreach ($model->fillable as $fill) {
                            if(in_array(strtolower($d[$fill]),array_map('strtolower',$results))){
                                array_push($row,$d);
                                !in_array($fill,$fillable) ? array_push($fillable,$fill) : null;
                            }
                        }
                    }

                    // Removes duplicates for multidimensional array
                    $id = [];
                    $temp_row = [];
                    foreach ($row as $r) {
                        if(!in_array($r["id"],$id)){
                            array_push($id,$r["id"]);
                            array_push($temp_row,$r);
                        }
                    }
                    $row = $temp_row;
                    count($row) ? $reply = "" : $reply = "No matching data found.4";
                    foreach ($row as $r) {
                        $reply .= "<b>--- ".$r[$model->main]."</b>br|";
                        $reply .= "[<b>ID: </b><i>".$r["id"]."</i>br|";
                        foreach ($fillable as $f) {
                            $reply .= "[<b>".$f.": </b><i>".$d[$f]."</i>br|";
                        }
                        $reply .= "br|";
                    }
                }

                if(!count($row) && !count($results) && !$findID & !$cleanedInput){
                    $data = DB::all($model);
                    count($data) ? $reply = "" : $reply = "No matching data found.5";
                    foreach ($data as $d) {
                        $reply .= "<b>--- ".$d[$model->main]."</b>br|";
                        $reply .= "[<b>ID: </b><i>".$d["id"]."</i>br|";
                        $reply .= "[<b>".$model->main.": </b><i>".$d[$model->main]."</i>br|";
                        $reply .= "br|";
                    }
                }

                if($findID && $cleanedInput){
                    $data = DB::find($model,$cleanedInput);
                    count($data) ? $reply = "" : $reply = "No matching data found.6";
                    foreach ($data as $d) {
                        $reply .= "<b>--- ".$d[$model->main]."</b>br|";
                        $reply .= "[<b>ID: </b><i>".$d["id"]."</i>br|";
                        foreach ($model->fillable as $fill) {
                            $fill != "floorplan" ? $reply .= "[<b>".$fill.": </b><i>".$d[$fill]."</i>br|" : null;
                            if($fill == "floorplan"){
                                $reply_type = "file";
                                $reply_file = $d[$fill];
                                $reply_file = str_replace("maps","maps_output",$reply_file);
                                $reply_file = str_replace("../../","../",$reply_file);
                            }
                        }
                        $reply .= "br|";
                    }
                }

                return [$reply_type,Identifier::label_replace($reply,$model),$reply_file];
                // return Identifier::breaker($reply);
            }

            // return ["table to be used","columns or fillable to be used","matching value in database using the query","row of from database which match the value of model->main"]
            return [$table,$column,$results,$row];
        }

        public static function label_replace($reply,$model){
            foreach ($model->label as $fillable => $label) {
                $reply = str_replace($fillable,$label,$reply);
            }
            return $reply;
        }

        public static function breaker($reply){
            $response = [];
            $temp = explode("br|",$reply);
            $temp_text = "";
            foreach ($temp as $t) {
                if(strlen($temp_text) > 3500 && strlen($temp_text) < 3600){
                    $response[] = $temp_text;
                    $temp_text = "";
                }else{
                    $temp_text .= $t . "br|";
                }
            }
            if($temp_text != ""){
                $response[] = $temp_text;
            }
            return strlen($response[0]);
        }
    }
?>