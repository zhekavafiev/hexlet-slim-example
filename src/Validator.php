<?php

namespace App;

class Validator
{
    public function validate($user)
    {
        $errors = [];
        if ($user['name'] == '') {
            $error['name'] = "Can't be blank";
        }

        if ($user['email'] == '') {
            $error['email'] = "Can't be blank";
        }

        if ($user['password'] == '') {
            $error['password'] = "Can't be blank";
        }

        if ($user['passwordConfirmation'] == '') {
            $error['passwordConfirmation'] = "Can't be blank";
        }

        if ($user['passwordConfirmation'] !== $user['password']) {
            $error['passwordDoNotMatch'] = "password do not match";
        }
        return $error;
    }
}
