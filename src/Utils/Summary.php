<?php
declare(strict_types=1);

namespace App\Utils;

use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

class Summary
{
    private $transactions;
    private $users;

    /**
     * @param $roomId
     */
    public function __construct($roomId)
    {
        $this->transactions = TableRegistry::getTableLocator()->get('Transactions')->find()
            ->contain(['ByUsers', 'ForUsers'])
            ->where(['room_id' => $roomId])->toArray();
        $this->users = TableRegistry::getTableLocator()->get('Users')->find()
            ->matching('Rooms', function ($q) use ($roomId) {
                return $q->where(['Rooms.id' => $roomId]);
            })->toArray();
    }

    /** $2y$10$zBUH2hoE.3h74ocVIui0q.PqiJtW6ZpEdDjuzqj3ke2yQevhDQ//.
     * ["",{"from":"07:00:00","to":"14:00:00"},{"from":"07:00:00","to":"14:00:00"},{"from":"07:00:00","to":"14:00:00"},{"from":"07:00:00","to":"14:00:00"},{"from":"07:00:00","to":"14:00:00"},""]
     * @return array
     */
    public function calculateSummary(): array
    {
        $usersSummary = [];
        foreach ($this->users as $user) {
            $usersSummary[$user->id] = $this->getSummaryForUser($user->id);
        }

        foreach($usersSummary as $byUser => &$userSummary) {
            foreach($userSummary as $forUser => $value) {
                if(isset($usersSummary[$forUser][$byUser])) {
                    if($value >= $usersSummary[$forUser][$byUser]) {
                        $userSummary[$forUser] = $value - $usersSummary[$forUser][$byUser];
                        unset($usersSummary[$forUser][$byUser]);
                    }
                }
            }
        }

        $formatted = [];
        $users = [];
        foreach($this->users as $user) {
            unset($user['_matchingData']);

            $users[$user->id] = $user->toArray();
        }

        foreach($usersSummary as $toUserId => $summary) {
            foreach($summary as $fromUserId => $value) {
                $formatted[] = [
                    'from' => $users[$fromUserId],
                    'to' => $users[$toUserId],
                    'value' => $value
                ];
            }
        }

        return $formatted;
    }

    /**
     * @param $userId
     * @return array
     */
    public function getSummaryForUser($userId): array
    {
        $userSummary = [];
        /** @var \App\Model\Entity\Transaction $transaction */
        foreach ($this->transactions as $transaction) {
            if ($transaction->by_user_id == $userId && $transaction->for_user_id != $userId) {

                if (!isset($userSummary[$transaction->for_user_id])) {
                    $userSummary[$transaction->for_user_id] = 0;
                }
                $userSummary[$transaction->for_user_id] += $transaction->value;
            }
        }

        return $userSummary;
    }
}
