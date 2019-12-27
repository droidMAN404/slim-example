<?php

namespace Slime;

class Validator
{
    public function validate($user)
    {
        $errors = [];

        if (empty($user['nickname'])) {
            $errors['nickname'] = 'Enter your nickname';
        }

        if (empty($user['email'])) {
            $errors['email'] = 'Enter your email';
        }

        return $errors;
    }
}