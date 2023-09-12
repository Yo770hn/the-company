<?php

include "../classes/User.php";

$user = new User;

$user->update($_POST, $_FILES);
// $_POST -holds all the data from the form from view.edit-uers.php

/*
$_POST['first_name'];
$_POST['last_name'];
$_POST['username'];
*/

