<?php

/*******************
 * Database Config *
 *******************/
define("DB_HOST", "localhost:3306");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "0Bdragon8712`");
define("DB_SCHEME", "csci-130_project1");

/*****************
 * Login Options *
 *****************/
define("LOGIN_ALLOWED", true);      // if login system is on
define("LOGIN_BRUTE_HOURS", 2);     // number of hours to wait if brute force
define("LOGIN_BRUTE_ATTMEMPT", 5);  // number of tries before account lock (0 for off)

/********************
 * Register Options *
 ********************/
define("REGISTER_ALLOWED", true);           // if registration is allowed
define("REGISTER_USERNAME_MAX_LENGTH", 45); // Max length of username
define("REGISTER_PASSWORD_MIN_LENGTH", 6);  // Min length of password

/******************
 * Session Config *
 ******************/
define("SESSION_LIFE", 0);          // lifetime of the session (hrs)
define("SESSION_SECURE", false);    // wether the session is secure or not (false for debug)
define("SESSION_KEYS", ['user_id', 'username', 'first_name', 'last_name', 'age', 'gender', 'location', 'icon', 'login_string']);

/***************
 * Enum String *
 ***************/
define("ENUM_GENDER", ['boy', 'girl', 'other']);    // Gender: enum to string

/***********************
 * File Handle Options *
 ***********************/
define("FILE_UPLOAD_DIR", $_SERVER['DOCUMENT_ROOT'] . "/images/upload/users/");

?>