<?php

namespace App;


use Faker\Factory;

class Email
{
    private $email;

    public function email($email)
    {
        $this->email = $email;
    }
    public static function filter($emails)
    {
        return filter_var_array($email = str_split($emails), FILTER_VALIDATE_EMAIL);
    }
    public static function sort($email)
    {
        return sort($email, SORT_STRING);
    }
    public function send()
    {
        $faker = Factory::create('pt_BR');
        return $faker->boolean();
    }
}
