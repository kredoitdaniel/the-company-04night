<?php

include "../classes/User.php";

// create object
$user = new User;

// call the method
$user->login($_POST);
// $_POST holds all the data from the form.