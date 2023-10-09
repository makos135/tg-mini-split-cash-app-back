<?php
declare(strict_types=1);

namespace App\Utils;

use App\Model\Entity\User;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

class AuthUtil
{
    /**
     * @return \App\Model\Entity\User
     */
    public static function getUser(): User
    {
        $token = Router::getRequest()->getHeader('X-TOKEN');
        $UsersTable = TableRegistry::getTableLocator()->get('Users');

        return $UsersTable->find()->where(['token' => current($token)])->first();
    }

    /**
     * @throws \Exception
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
