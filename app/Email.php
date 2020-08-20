<?php

namespace App;


use Faker\Factory;

class Email
{
    private $email;

    public function __construct($email)
    {
        $this->email = $email;
    }
    public static function filter($emails)
    {
        return array_filter(filter_var_array($emails, FILTER_VALIDATE_EMAIL));
    }
    public static function sort($emails)
    {
        sort($emails, SORT_STRING);
        return $emails;
    }
    public function send()
    {
        $faker = Factory::create('pt_BR');
        return $faker->boolean();
    }
}
