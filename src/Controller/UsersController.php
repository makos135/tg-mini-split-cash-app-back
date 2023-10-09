<?php
declare(strict_types=1);

namespace App\Controller;

use App\Utils\AuthUtil;
use Cake\Event\EventInterface;
use Cake\Http\Exception\UnauthorizedException;
use Cake\View\JsonView;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    /**
     * @throws \Exception
     */
    public function init()
    {
        $token = '';
        if ($this->getRequest()->is('post')) {

            $user = $this->Users->find()->where(['telegram_id' => $this->getRequest()->getData('id')])->first();
            if (empty($user)) {
                $user = $this->Users->newEmptyEntity();
                $user->telegram_id = $this->getRequest()->getData('id');
                $user->name = $this->getRequest()->getData('name');
                $user->token = AuthUtil::generateToken();
                $this->Users->save($user);
            } else {
                if (empty($user->token)) {
                    $user->token = AuthUtil::generateToken();
                    $this->Users->save($user);
                }
            }
            $token = $user->token;
        }
        $this->set(['response' => ['status' => 'success', 'token' => $token]]);
        $this->viewBuilder()->setOption('serialize', 'response');

    }

    public function beforeFilter(EventInterface $event)
    {
        $this->loadComponent('FormProtection');
        $this->FormProtection->setConfig('validate', false);
        parent::beforeFilter($event);
    }
}
