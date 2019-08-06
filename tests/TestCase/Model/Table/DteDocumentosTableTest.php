<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DteDocumentosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DteDocumentosTable Test Case
 */
class DteDocumentosTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DteDocumentosTable
     */
    public $DteDocumentos;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.DteDocumentos',
        'app.DteTipoDocumentos',
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
        $config = TableRegistry::getTableLocator()->exists('DteDocumentos') ? [] : ['className' => DteDocumentosTable::class];
        $this->DteDocumentos = TableRegistry::getTableLocator()->get('DteDocumentos', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DteDocumentos);

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
