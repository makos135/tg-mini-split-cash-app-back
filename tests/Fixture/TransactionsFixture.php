<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TransactionsFixture
 */
class TransactionsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'room_id' => 1,
                'by_user_id' => 1,
                'for_user_id' => 1,
                'value' => 1.5,
                'currency' => 'Lor',
                'created' => 1696274699,
            ],
        ];
        parent::init();
    }
}
