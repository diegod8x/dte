<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DteDocumento Entity
 *
 * @property int $id
 * @property string|null $rut_emisor
 * @property string|null $rut_receptor
 * @property int|null $dte_tipo_documento_id
 * @property string|null $estado
 * @property string|null $fecha_emision
 * @property string|null $fecha_envio
 * @property string|null $trackid
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\DteTipoDocumento $dte_tipo_documento
 * @property \App\Model\Entity\DteBoleta[] $dte_boletas
 */
class DteDocumento extends Entity
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
        'rut_receptor' => true,
        'dte_tipo_documento_id' => true,
        'estado' => true,
        'fecha_emision' => true,
        'fecha_envio' => true,
        'trackid' => true,
        'created' => true,
        'modified' => true,
        'dte_tipo_documento' => true,
        'dte_boletas' => true
    ];
}
