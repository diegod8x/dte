<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DteTipoDte Entity
 *
 * @property int $id
 * @property string|null $codigo
 * @property string|null $nombre
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\DteDte[] $dte_dtes
 * @property \App\Model\Entity\DteFolio[] $dte_folios
 */
class DteTipoDte extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'codigo' => true,
        'nombre' => true,
        'created' => true,
        'modified' => true,
        'dte_dtes' => true,
        'dte_folios' => true
    ];
}
