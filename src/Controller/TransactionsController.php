<?php
declare(strict_types=1);

namespace App\Controller;

use App\Utils\AuthUtil;
use Cake\Event\EventInterface;
use Cake\Http\Exception\UnauthorizedException;
use Cake\View\JsonView;

/**
 * Transactions Controller
 *
 * @property \App\Model\Table\TransactionsTable $Transactions
 */
class TransactionsController extends AppController
{
    /**
     * @return array<string>
     */
    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $response = ['status' => 'success'];
        if ($this->getRequest()->is('post')) {
            $this->Transactions->createTransactions($this->getRequest()->getData());
        }
        $this->set(compact('response'));
        $this->viewBuilder()->setOption('serialize', 'response');
    }

    /**
     * Delete method
     *
     * @param string|null $id Transaction id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null): void
    {
        $response = ['status' => 'error'];
        if ($this->getRequest()->is(['post', 'delete'])) {
            $transaction = $this->Transactions->get($id);
            if ($this->Transactions->delete($transaction)) {
                $response['status'] = 'success';
            }
        }
        $this->set(compact('response'));
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
