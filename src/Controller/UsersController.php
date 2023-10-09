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
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Users->find();
        $users = $this->paginate($query);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, contain: ['Rooms']);
        $this->set(compact('user'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $rooms = $this->Users->Rooms->find('list', limit: 200)->all();
        $this->set(compact('user', 'rooms'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, contain: ['Rooms']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $rooms = $this->Users->Rooms->find('list', limit: 200)->all();
        $this->set(compact('user', 'rooms'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
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
        if (!$this->getRequest()->is('options') && !AuthUtil::getUser()) {
            return throw new UnauthorizedException('You must be logged in to access this page');
        }
        $this->loadComponent('FormProtection');
        $this->FormProtection->setConfig('validate', false);
        parent::beforeFilter($event);
    }
}
