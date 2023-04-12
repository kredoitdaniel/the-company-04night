<?php

include "../classes/User.php";

// Create an object of the class User
$user = new User;

// Call the method using the object $user
$user->store($_POST);
// $_POST holds all the data from the form in views/register.php 
