<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DteFoliosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DteFoliosTable Test Case
 */
class DteFoliosTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DteFoliosTable
     */
    public $DteFolios;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.DteFolios',
        'app.DteTipoDocumentos'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('DteFolios') ? [] : ['className' => DteFoliosTable::class];
        $this->DteFolios = TableRegistry::getTableLocator()->get('DteFolios', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DteFolios);

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
