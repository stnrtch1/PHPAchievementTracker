<?php

    //database info
    $config = array(
        'DB_HOST'=>'localhost',
        'DB_USERNAME'=>'root',
        'DB_PASSWORD'=>'',
        'DB_DATABASE'=>'achievementtracker',
        'DB_GAMETABLE'=>'games',
        'DB_USERTABLE'=>'users',
    );

    function getSortKey($key){
        if($key == "nameA"){
            return "gameName ASC";
        }else if($key == "nameD"){
            return "gameName DESC";
        }else if($key == "countA"){
            return "gameAchievementCount ASC";
        }else if($key == "countD"){
            return "gameAchievementCount DESC";
        }else if($key == "maxA"){
            return "gameAchievementMax ASC";
        }else if($key == "maxD"){
            return "gameAchievementMax DESC";
        }else if($key == "percentA"){
            return "gamePercentageEarned ASC";
        }else if($key == "percentD"){
            return "gamePercentageEarned DESC";
        }
    }

    
?>