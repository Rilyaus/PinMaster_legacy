<?php

$connect = mysqli_connect("localhost", "rilyaus", "rudeh123", "rilyaus");

if(mysqli_connect_errno()) {
    echo "Connect failed : ";
    echo mysqli_connect_error();
    exit();
}

mysqli_query($connect, "set names utf8");

?>