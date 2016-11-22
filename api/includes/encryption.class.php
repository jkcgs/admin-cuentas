<?php

require_once "vendor/autoload.php";
use phpseclib\Crypt\TripleDES;
use phpseclib\Crypt\Random;
use phpseclib\Crypt\Base;

class Encryption {
    static function password_hash($password) {
        return password_hash($password, PASSWORD_BCRYPT, ["cost" => 9]);
    }

    static function password_verify($password, $hash) {
        return password_verify($password, $hash);
    }

    static function encrypt($text, $key) {
        $cipher = new TripleDES(Base::MODE_ECB);
        $cipher->setPassword($key);
        return base64_encode($cipher->encrypt($text));
    }

    static function decrypt($encrypted, $key) {
        $cipher = new TripleDES(Base::MODE_ECB);
        $cipher->setPassword($key);

        return $cipher->decrypt(base64_decode($encrypted));
    }

    static function decrypt_user_pass($pass) {
        if(!isset($_SESSION['logged']) || !$_SESSION['logged']) {
            return false;
        }

        if(!function_exists("get_user_data")) {
            require_once dirname(__FILE__) . "/../includes/database.php";
        }

        $user_pass = md5(get_user_data()["password"]);
        return Encryption::decrypt($pass, $user_pass);
    }

    static function encrypt_user_pass($pass) {
        if(!isset($_SESSION['logged']) || !$_SESSION['logged']) {
            return false;
        }

        if(!function_exists("get_user_data")) {
            require_once dirname(__FILE__) . "/../includes/database.php";
        }

        $user_pass = md5(get_user_data()["password"]);
        return Encryption::encrypt($pass, $user_pass);
    }
}