<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BoletasTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BoletasTable Test Case
 */
class BoletasTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BoletasTable
     */
    public $Boletas;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Boletas'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Boletas') ? [] : ['className' => BoletasTable::class];
        $this->Boletas = TableRegistry::getTableLocator()->get('Boletas', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Boletas);

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
