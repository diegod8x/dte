<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Boletas Model
 *
 * @method \App\Model\Entity\Boleta get($primaryKey, $options = [])
 * @method \App\Model\Entity\Boleta newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Boleta[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Boleta|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Boleta saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Boleta patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Boleta[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Boleta findOrCreate($search, callable $callback = null, $options = [])
 */
class BoletasTable extends Table
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

        $this->setTable('boletas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('folio')
            ->allowEmptyString('folio');

        $validator
            ->scalar('xml')
            ->maxLength('xml', 4294967295)
            ->allowEmptyString('xml');

        return $validator;
    }
}
