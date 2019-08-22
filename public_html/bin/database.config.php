<?php

class Database {
// PRIVATE
    private $host = "127.0.0.1";
    private $username = "root";
    private $password = "0Bdragon8712`";
    private $scheme = "csci-130-project1";

// PUBLIC
    public static $Mysqli;

    public function _constuct() {
        if (empty($this->Mysqli)) {
            $this->Mysqli = new myslqi($host, $username, $password, $scheme);
        }
    }
}

?>