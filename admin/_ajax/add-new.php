<?php
include("../_include/_models/db.php");
$db = new Database("localhost", "root", "", "projekt");
    if(isset($_POST) && !empty($_POST)){
        $flattenedInput = flattenArray($_POST["input"]);
        if($_POST["inputType"] == "cities"){
            $sql = "insert into city (name) values ";
        }elseif($_POST["inputType"] == "hashtags"){
            $sql = "insert into hashtaglist (name) values ";
        }elseif($_POST["inputType"] == "admins"){
            $sql = "insert into admin (firstName, lastName, email, password, adminPrivilege) values ";
        }elseif($_POST["inputType"] == "locations"){
            $sql = "insert into location (name, description, address, isGym, cityID) values ";
        }
        if($_POST["columns"] != 1){
            $basePos = 0;
            for($rows = 0; $rows < $_POST["rows"]; $rows++){
                $sql = $sql."(";
                    for($cols = 0; $cols < ($_POST["columns"]); $cols++){
                        if($cols < ($_POST["columns"]-1)){
                            $sql = $sql."'".$flattenedInput[$cols+$basePos]."',";
                        }else {
                            if(($_POST["inputType"] == "admins") && ($cols == 3)){
                                $pwd = $flattenedInput[$cols+$basePos];
                                $salt = md5($pwd);
                                $checkForPWD = hash("sha256", $salt.$pwd.$salt);
                                $sql = $sql."'".$checkForPWD."', 1";// 1 is the pre-defiened value of admin priveliege
                            }else{
                                $sql = $sql."'".$flattenedInput[$cols+$basePos]."'";//this is for locations
                            }
                            
                            
                        }
                    }
                    $basePos = $cols+$basePos;
                if($rows < ($_POST["rows"]-1)){
                    $sql = $sql."),";
                }
                else {
                    $sql = $sql.")";
                }
            }
        }else {
            for($rows = 0; $rows < $_POST["rows"]; $rows++){
                if($rows < ($_POST["rows"]-1))
                    $sql = $sql."('".$flattenedInput[$rows]."'),";
                else
                    $sql = $sql."('".$flattenedInput[$rows]."')";
            }
        }
        
        if($db->q($sql))
            echo 1;
        else
            echo mysqli_error($db->db)." ".$sql;
        
        // echo $sql;
        // var_dump($sql);
    }else{
        echo "Error";
    }

    function flattenArray(array $array) {
        $return = array();
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }
        
?>