<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DteFacturas Model
 *
 * @property \App\Model\Table\DteDocumentosTable|\Cake\ORM\Association\BelongsTo $DteDocumentos
 *
 * @method \App\Model\Entity\DteFactura get($primaryKey, $options = [])
 * @method \App\Model\Entity\DteFactura newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DteFactura[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DteFactura|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DteFactura saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DteFactura patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DteFactura[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DteFactura findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DteFacturasTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('dte_facturas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('DteDocumentos', [
            'foreignKey' => 'det_documento_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create')
            ->add('id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('folio')
            ->maxLength('folio', 45)
            ->allowEmptyString('folio');

        $validator
            ->scalar('estado')
            ->maxLength('estado', 45)
            ->allowEmptyString('estado');

        $validator
            ->scalar('xml_preliminar')
            ->maxLength('xml_preliminar', 45)
            ->allowEmptyString('xml_preliminar');

        $validator
            ->scalar('xml_final')
            ->maxLength('xml_final', 45)
            ->allowEmptyString('xml_final');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['id']));
        $rules->add($rules->existsIn(['det_documento_id'], 'DteDocumentos'));

        return $rules;
    }
}
