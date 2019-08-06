<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DteFactura Entity
 *
 * @property int $id
 * @property string|null $folio
 * @property string|null $estado
 * @property string|null $xml_preliminar
 * @property string|null $xml_final
 * @property int|null $det_documento_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\DteDocumento $dte_documento
 */
class DteFactura extends Entity
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
        'folio' => true,
        'estado' => true,
        'xml_preliminar' => true,
        'xml_final' => true,
        'det_documento_id' => true,
        'created' => true,
        'modified' => true,
        'dte_documento' => true
    ];
}
