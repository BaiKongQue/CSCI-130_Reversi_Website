<?php

/*******************
 * Database Config *
 *******************/
deifne("DB_HOST", "127.0.0.1");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "0Bdragon8712`");
define("DB_SCHEME", "csci-130-project1");

/*****************
 * Login Options *
 *****************/
define("LOGIN_ALLOWED", true);      // if login system is on
define("LOGIN_BRUTE_HOUR", 2);      // number of hours to wait if brute force
define("LOGIN_BRUTE_ATTMEMPT", 5);  // number of tries before account lock (0 for off)

/******************
 * Session Config *
 ******************/
define("SESSION_LIFE", 0);          // lifetime of the session (hrs)
define("SESSION_SECURE", false);    // wether the session is secure or not (false for debug)

?>