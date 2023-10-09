<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\User;
use App\Utils\AuthUtil;
use App\Utils\Summary;
use Cake\Event\EventInterface;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Log\Log;
use Cake\View\JsonView;

/**
 * Rooms Controller
 *
 * @property \App\Model\Table\RoomsTable $Rooms
 */
class RoomsController extends AppController
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
        $query = $this->Rooms->find();
        $rooms = $this->paginate($query);

        $this->set(compact('rooms'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $room = $this->Rooms->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->getRequest()->getData();
            $data['users'][] = ['id' => AuthUtil::getUser()->id];
            $room = $this->Rooms->patchEntity($room, $data, ['associated' => ['Users']]);
            $this->Rooms->save($room);
        }
        $this->set(compact('room'));
        $this->viewBuilder()->setOption('serialize', 'room');
    }

    /**
     * all
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function all()
    {
        $rooms = [];
        if ($this->request->is('get')) {
            $rooms = $this->Rooms->find()->contain(['Users'])->matching('Users', function ($query) {
                return $query->where(['Users.id' => AuthUtil::getUser()->id]);
            })->toArray();
        }
        $this->set(compact('rooms'));
        $this->viewBuilder()->setOption('serialize', 'rooms');
    }

    /**
     * Delete method
     *
     * @param string|null $id Room id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $room = $this->Rooms->get($id);
        if ($this->Rooms->delete($room)) {
            $this->Flash->success(__('The room has been deleted.'));
        } else {
            $this->Flash->error(__('The room could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function link()
    {
        $response = ['status' => 'error'];
        if ($this->getRequest()->is('post')) {
            $user = AuthUtil::getUser();
            $room = $this->Rooms->find()->contain(['Users'])
                ->where(['id' => $this->getRequest()->getData('room_id')])->first();
            if ($room) {
                $users = array_map(function (User $user) {
                    return ['id' => $user->id];
                }, $room->users);
                $users[] = ['id' => $user->id];
                $room = $this->Rooms->patchEntity($room, ['users' => $users], ['associated' => ['Users']]);
                Log::debug(json_encode($room));
                if ($this->Rooms->save($room, ['associated' => ['Users']])) {
                    $response = ['status' => 'success'];
                }
            }
        }
        $this->set(compact('response'));
        $this->viewBuilder()->setOption('serialize', 'response');
    }

    /**
     * getTransactions
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function getTransactions($roomId)
    {
        $transactions = $this->Rooms->Transactions->find()->contain(['ByUsers', 'ForUsers'])
            ->where(['room_id' => $roomId])->orderBy(['Transactions.created'])->toArray();
        $this->set(compact('transactions'));
        $this->viewBuilder()->setOption('serialize', 'transactions');
    }

    public function getSummary($roomId) {
        $summary = new Summary($roomId);
        $this->set(['summary' => $summary->calculateSummary()]);
        $this->viewBuilder()->setOption('serialize','summary');
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
