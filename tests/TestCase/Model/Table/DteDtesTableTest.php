<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DteDtesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DteDtesTable Test Case
 */
class DteDtesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DteDtesTable
     */
    public $DteDtes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.DteDtes',
        'app.DteTipoDtes',
        'app.DteBoletas'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('DteDtes') ? [] : ['className' => DteDtesTable::class];
        $this->DteDtes = TableRegistry::getTableLocator()->get('DteDtes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DteDtes);

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
