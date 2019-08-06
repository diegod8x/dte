<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DteFolio Entity
 *
 * @property int $id
 * @property string|null $rut_emisor
 * @property int|null $dte_tipo_documento_id
 * @property string|null $inicio
 * @property string|null $fin
 * @property string|null $ultimo_disponible
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\DteTipoDocumento $dte_tipo_documento
 */
class DteFolio extends Entity
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
        'rut_emisor' => true,
        'dte_tipo_documento_id' => true,
        'inicio' => true,
        'fin' => true,
        'ultimo_disponible' => true,
        'created' => true,
        'modified' => true,
        'dte_tipo_documento' => true
    ];
}
