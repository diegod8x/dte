<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DteDocumentos Model
 *
 * @property \App\Model\Table\DteTipoDocumentosTable|\Cake\ORM\Association\BelongsTo $DteTipoDocumentos
 * @property \App\Model\Table\DteBoletasTable|\Cake\ORM\Association\HasMany $DteBoletas
 *
 * @method \App\Model\Entity\DteDocumento get($primaryKey, $options = [])
 * @method \App\Model\Entity\DteDocumento newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DteDocumento[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DteDocumento|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DteDocumento saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DteDocumento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DteDocumento[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DteDocumento findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DteDocumentosTable extends Table
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

        $this->setTable('dte_documentos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('DteTipoDocumentos', [
            'foreignKey' => 'dte_tipo_documento_id'
        ]);
        $this->hasMany('DteBoletas', [
            'foreignKey' => 'dte_documento_id'
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
            ->scalar('rut_emisor')
            ->maxLength('rut_emisor', 45)
            ->allowEmptyString('rut_emisor');

        $validator
            ->scalar('rut_receptor')
            ->maxLength('rut_receptor', 45)
            ->allowEmptyString('rut_receptor');

        $validator
            ->scalar('estado')
            ->maxLength('estado', 45)
            ->allowEmptyString('estado');

        $validator
            ->scalar('fecha_emision')
            ->maxLength('fecha_emision', 45)
            ->allowEmptyString('fecha_emision');

        $validator
            ->scalar('fecha_envio')
            ->maxLength('fecha_envio', 45)
            ->allowEmptyString('fecha_envio');

        $validator
            ->scalar('trackid')
            ->maxLength('trackid', 45)
            ->allowEmptyString('trackid');

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
        $rules->add($rules->existsIn(['dte_tipo_documento_id'], 'DteTipoDocumentos'));

        return $rules;
    }
}
