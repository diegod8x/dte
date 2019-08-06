<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DteBoletasTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DteBoletasTable Test Case
 */
class DteBoletasTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DteBoletasTable
     */
    public $DteBoletas;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.DteBoletas',
        'app.DteDocumentos'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('DteBoletas') ? [] : ['className' => DteBoletasTable::class];
        $this->DteBoletas = TableRegistry::getTableLocator()->get('DteBoletas', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DteBoletas);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
