<?php

$app_desc = array(
    "name" => "SIMPLE_LIST",
    "short_name" => N_("SIMPLE_LIST:SIMPLE_LIST"),
    "description" => N_("SIMPLE_LIST:SIMPLE_LIST"),
    "icon" => "SIMPLE_LIST.png",
    "with_frame" =>"Y",
    "displayable" => "N",
    "childof" => ""
);


// ACLs for this application
$app_acl = array(
    array(
        "name" => "BASIC",
        "description" => N_("SIMPLE_LIST:Basic ACL"),
        "group_default" => "Y"
    )
);
// Actions for this application
$action_desc = array(
    array(
        "name" => "MAIN",
        "short_name" => N_("MAIN"),
        "layout" => "main.html",
        "script" => "action.main.php",
        "function" => "main",
        "root" => "Y",
        "acl" => "BASIC"
    )
);

