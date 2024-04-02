<?php

use app\core\Controller;
use app\models\User;

class Auth extends Controller
{
    public function login()
    {
        //? check request method
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = new User();
            $row  = $user->where('email', $email);

            //? Perform user authentication (validate username/password)


            if (count($row) > 0 && md5($password) == $row[0]->password) {

                //? If authentication is successful, generate a token
                $token = $this->generateToken($row[0]->id);

                //? send json response
                if ($token) {
                    sendJsonResponse(
                        'success',
                        'Logged In Successfully',
                        200,
                        ['token' => $token]
                    );
                } else {
                    sendJsonResponse(
                        'Internal Server Error',
                        'Unable to generate token',
                        500
                    );
                }
            } else {
                sendJsonResponse(
                    'Unauthorized',
                    'Email or Password Incorrect',
                    401
                );
            }
        } else {
            sendJsonResponse(
                'Method Not Allowed',
                'The requested method is not supported for this resource.',
                405
            );
        }
    }


    private function generateToken(int $id): string|bool
    {
        $user = new User();
        $token = random_string(30);

        while ($user->where('token',  $token)) {
            $token .= rand(10, 1000);
        }

        //? update token and expiring time
        $epiring_time = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $updateUser = $user->update($id, [
            'token' => $token,
            'token_expires_at' => $epiring_time,
        ]);

        if ($updateUser) return $token;

        return false;
    }
}