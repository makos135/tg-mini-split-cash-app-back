<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Utils\AuthUtil;
use Cake\Log\Log;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Transactions Model
 *
 * @property \App\Model\Table\RoomsTable&\Cake\ORM\Association\BelongsTo $Rooms
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Transaction newEmptyEntity()
 * @method \App\Model\Entity\Transaction newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Transaction[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Transaction get($primaryKey, $options = [])
 * @method \App\Model\Entity\Transaction findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Transaction patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Transaction[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Transaction|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Transaction saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TransactionsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('transactions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Rooms', [
            'foreignKey' => 'room_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ByUsers', [
            'className' => 'Users',
            'foreignKey' => 'by_user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ForUsers', [
            'className' => 'Users',
            'foreignKey' => 'for_user_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('room_id')
            ->notEmptyString('room_id');

        $validator
            ->integer('by_user_id')
            ->notEmptyString('by_user_id');

        $validator
            ->integer('for_user_id')
            ->notEmptyString('for_user_id');

        $validator
            ->decimal('value')
            ->allowEmptyString('value');

        $validator
            ->scalar('currency')
            ->maxLength('currency', 5)
            ->allowEmptyString('currency');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('room_id', 'Rooms'), ['errorField' => 'room_id']);
        $rules->add($rules->existsIn('by_user_id', 'ByUsers'), ['errorField' => 'by_user_id']);
        $rules->add($rules->existsIn('for_user_id', 'ForUsers'), ['errorField' => 'for_user_id']);

        return $rules;
    }

    /**
     * @param array $data
     * @return void
     */
    public function createTransactions(array $data): void
    {
        $users = $data['users'];
        $room = $this->Rooms->get($data['room_id']);
        foreach ($users as $user) {
            $transaction = $this->newEmptyEntity();
            $transaction->room_id = $data['room_id'];
            $transaction->by_user_id = $data['by_user']['id'];
            $transaction->for_user_id = $user['id'];
            $transaction->value = $user['value'];
            $transaction->description = $data['description'];
            $transaction->currency = $room->currency;
            $this->saveOrFail($transaction);
        }
    }
}
