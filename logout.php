<?php

session_start();

if (!isset($_SESSION["IsLogin"])) {
    $_SESSION["IsLogin"] = 0; // chưa đăng nhập
}

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once './utils/Context.php';
require_once './utils/Utils.php';


if (Context::IsLogged()) {
    Context::destroy();
} else {
    
}

Utils::Redirect("index.php");
?>