<?php defined("INCLUDED") or die("Denied"); 

class Bank {
    protected $bank_name = "default";
    protected $user = "";
    protected $pass = "";
    protected $curl = null;

    function __construct($user, $pass) {
        $this->user = $user;
        $this->pass = $pass;

        $this->curl = init_curl();
    }

    function login() {
        throw new \Exception("Not implemented");
    }

    function getAccounts() {
        throw new \Exception("Not implemented");
    }

    function logout() {
        throw new \Exception("Not implemented");
    }

    function getBankName() {
        return $this->bank_name;
    }
}

// Usado para reintentar
class TemporalError extends Exception { }