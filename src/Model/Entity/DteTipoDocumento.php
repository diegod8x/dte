<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DteTipoDocumento Entity
 *
 * @property int $id
 * @property string|null $codigo
 * @property string|null $nombre
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\DteDocumento[] $dte_documentos
 * @property \App\Model\Entity\DteFolio[] $dte_folios
 */
class DteTipoDocumento extends Entity
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
        'dte_documentos' => true,
        'dte_folios' => true
    ];
}
