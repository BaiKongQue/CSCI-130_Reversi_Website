<?php
include_once "cred.php";

/*******************
 * Database Config *
 *******************/
// define("DB_HOST", DB_HOST);
// define("DB_USERNAME", DB_USERNAME);
// define("DB_PASSWORD", DB_PASSWORD);
// define("DB_SCHEME", DB_SCHEME);

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
define("SESSION_NAME", "sec_session_id");   // session name
define("SESSION_LIFE", 0);                  // lifetime of the session (hrs)
define("SESSION_SECURE", false);            // wether the session is secure or not (false for debug)
define("SESSION_HTTP_ONLY", true);          // only http protocol
define("SESSION_KEYS", ['player_id', 'username', 'first_name', 'last_name', 'age', 'gender', 'location', 'icon', 'login_string']);

/***************
 * Enum String *
 ***************/
define("ENUM_GENDER", ['boy', 'girl', 'other']);    // Gender: enum to string

/***********************
 * File Handle Options *
 ***********************/
define("FILE_UPLOAD_DIR", $_SERVER['DOCUMENT_ROOT'] . "/images/uploads/users/");


/**********************
 * Game Configuration *
 **********************/
define("GAME_TILE_NONE", 0);
define("GAME_TILE_PLAYER1", 1);
define("GAME_TILE_PLAYER2", 2);

/*********************
 * AI Configurations *
 *********************/
define("AI_EASY_ID", -1);
define("AI_HARD_ID", -2);
define("AI_DIFFICULTY_ID", ['easy' => AI_EASY_ID, 'hard' => AI_HARD_ID]);
?>