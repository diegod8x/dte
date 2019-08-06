<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DteTipoDocumentos Model
 *
 * @property \App\Model\Table\DteDocumentosTable|\Cake\ORM\Association\HasMany $DteDocumentos
 * @property \App\Model\Table\DteFoliosTable|\Cake\ORM\Association\HasMany $DteFolios
 *
 * @method \App\Model\Entity\DteTipoDocumento get($primaryKey, $options = [])
 * @method \App\Model\Entity\DteTipoDocumento newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DteTipoDocumento[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DteTipoDocumento|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DteTipoDocumento saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DteTipoDocumento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DteTipoDocumento[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DteTipoDocumento findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DteTipoDocumentosTable extends Table
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

        $this->setTable('dte_tipo_documentos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('DteDocumentos', [
            'foreignKey' => 'dte_tipo_documento_id'
        ]);
        $this->hasMany('DteFolios', [
            'foreignKey' => 'dte_tipo_documento_id'
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
            ->scalar('codigo')
            ->maxLength('codigo', 45)
            ->allowEmptyString('codigo');

        $validator
            ->scalar('nombre')
            ->maxLength('nombre', 100)
            ->allowEmptyString('nombre');

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

        return $rules;
    }
}
