<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DteFolios Model
 *
 * @property \App\Model\Table\DteTipoDocumentosTable|\Cake\ORM\Association\BelongsTo $DteTipoDocumentos
 *
 * @method \App\Model\Entity\DteFolio get($primaryKey, $options = [])
 * @method \App\Model\Entity\DteFolio newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DteFolio[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DteFolio|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DteFolio saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DteFolio patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DteFolio[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DteFolio findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DteFoliosTable extends Table
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

        $this->setTable('dte_folios');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('DteTipoDocumentos', [
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
            ->scalar('rut_emisor')
            ->maxLength('rut_emisor', 45)
            ->allowEmptyString('rut_emisor');

        $validator
            ->scalar('inicio')
            ->maxLength('inicio', 45)
            ->allowEmptyString('inicio');

        $validator
            ->scalar('fin')
            ->maxLength('fin', 45)
            ->allowEmptyString('fin');

        $validator
            ->scalar('ultimo_disponible')
            ->maxLength('ultimo_disponible', 45)
            ->allowEmptyString('ultimo_disponible');

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
