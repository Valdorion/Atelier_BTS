<?php
session_start();
$token = uniqid();
$_SESSION["token"] = $token;