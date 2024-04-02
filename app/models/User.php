<?php
/*
** Users Model
*
*/

declare(strict_types=1);

namespace app\models;

use app\core\Model;
use DateTime;

class User extends Model
{

    protected $table = 'users';

    protected $allowedColumns = [
        'user_id',
        'full_name',
        'email',
        'password',
        'token',
        'token_expires_at',
        'created_at',
    ];

    protected $beforeInsert = [
        'user_id',
        'password_hash',
    ];

    protected $beforeUpdate = [
        'password_hash',
    ];

    protected $afterSelect = [];


    //? method to validate user datas 
    public function validate(array $data): bool
    {
        //? Validate full Name
        if (isset($data['first_name'])) {
            if (empty($data['first_name'])) {
                $this->errors['first_name'] = 'Pls fill in this field';
            } elseif (!preg_match('/^[a-zA-Z ]+$/', $data['first_name'])) {
                $this->errors['first_name'] = 'Only letters allowed in first name';
            }
        }

        //? validate last name
        if (isset($data['last_name'])) {
            if (empty($data['last_name'])) {
                $this->errors['last_name'] = 'Pls fill in this field';
            } elseif (!preg_match('/^[a-zA-Z ]+$/', $data['last_name'])) {
                $this->errors['last_name'] = 'Only letters allowed in last name';
            }
        }


        //? Validate Email 
        if (isset($data['email'])) {
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->errors['email'] = 'Pls fill in this field';
            } elseif (!empty($data['email'])) {

                //? check if email already exist
                $this->where('email', $data['email']);

                if ($this->rowCount() > 0) {
                    $this->errors['email'] = 'Email already exists';
                }
            }
        }


        //? Validate Password
        if (isset($data['password'])) {

            if (empty($data['password'])) {
                $this->errors['password'] = 'Pls fill in this field';
            } elseif (strlen($data['password']) < 6) {
                $this->errors['password'] = 'Password must be atleast 6 characters long ';
            } elseif ($data['password'] !== $data['confirm_password']) {
                $this->errors['password'] = 'The password do not match';
            } else {
                echo '';
            }
        }

        //? Checking if errors are empty
        if (empty($this->errors)) {
            return true;
        }

        return false;
    }


    //? genearate a user ID 
    public function user_id(array $data)
    {
        $data['user_id'] = random_string(30);

        while ($this->where('user_id', $data['user_id'])) {
            $data['user_id'] .= rand(10, 1000);
        }


        return $data;
    }


    //? hash password before inserting
    public function password_hash(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}
