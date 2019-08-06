<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DteTipoDtesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DteTipoDtesTable Test Case
 */
class DteTipoDtesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DteTipoDtesTable
     */
    public $DteTipoDtes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.DteTipoDtes',
        'app.DteDtes',
        'app.DteFolios'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('DteTipoDtes') ? [] : ['className' => DteTipoDtesTable::class];
        $this->DteTipoDtes = TableRegistry::getTableLocator()->get('DteTipoDtes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DteTipoDtes);

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
