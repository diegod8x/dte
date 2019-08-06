<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DteFacturasTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DteFacturasTable Test Case
 */
class DteFacturasTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DteFacturasTable
     */
    public $DteFacturas;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.DteFacturas',
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
        $config = TableRegistry::getTableLocator()->exists('DteFacturas') ? [] : ['className' => DteFacturasTable::class];
        $this->DteFacturas = TableRegistry::getTableLocator()->get('DteFacturas', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DteFacturas);

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
