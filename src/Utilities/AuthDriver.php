<?php
    /**
     * Define common function for authentication
     *
     * @author: Huynh
     */
    namespace App\Utilities;

    use Cake\Core\Configure;

class AuthDriver
{
    /**
     * Default password hash method, source: FUEL\Auth_Login_Driver
     * @param string $password Password input to be hashed
     * @return string
     */
    public function hashPassword($password)
    {
        return base64_encode(hash_pbkdf2('sha256', $password, Configure::read('auth.salt'), Configure::read('auth.iterations', 10000), 32, true));
    }
}
