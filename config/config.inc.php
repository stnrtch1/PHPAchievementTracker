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
        }
    }

    
?>