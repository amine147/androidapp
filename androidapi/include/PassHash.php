<?php
/**
 * Created by PhpStorm.
 * User: moncifbounif
 * Date: 15/03/2016
 * Time: 15:07
 */

class PassHash {

    // blowfish
    private static $algo = '$2a';
    // cost parameter
    private static $cost = '$10';

    // mainly for internal use
    public static function unique_salt() {
        return substr(sha1(mt_rand()), 0, 22);
    }

    // this will be used to generate a hash
    public static function hash($password) {

       /* return crypt($password, self::$algo .
            self::$cost .
            '$' . self::unique_salt());*/
            return md5($password);
    }

    // this will be used to compare a password against a hash
    public static function check_password($hash, $password) {
        //$full_salt = substr($hash, 0, 29);
        //$new_hash = crypt($password, $full_salt);
        if(md5($password) == $hash) return true;
    }

}