<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Transaction Entity
 *
 * @property int $id
 * @property int $room_id
 * @property int $by_user_id
 * @property int $for_user_id
 * @property string|null $value
 * @property string|null $currency
 * @property string|null $description
 * @property \Cake\I18n\DateTime|null $created
 *
 * @property \App\Model\Entity\Room $room
 * @property \App\Model\Entity\User $user
 */
class Transaction extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'room_id' => true,
        'by_user_id' => true,
        'for_user_id' => true,
        'value' => true,
        'currency' => true,
        'created' => true,
        'description' => true,
        'room' => true,
        'user' => true,
    ];
}
