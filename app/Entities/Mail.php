<?php namespace App\Entities;

use CodeIgniter\Entity;

class Mail extends Entity
{
    protected $username;
    protected $password;
    protected $name;
    protected $name_to;
    protected $email_to;
    protected $subject;
    protected $body;
}