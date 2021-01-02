<?php
namespace App\Helper;

use DateTime;

class General{
    public static function newDT(){

        return new DateTime('now');
    }
}