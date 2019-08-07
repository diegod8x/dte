<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Controller\DteFoliosController;

/**
 * DteBoletas Controller
 *
 * @property \App\Model\Table\DteBoletasTable $DteBoletas
 *
 * @method \App\Model\Entity\DteBoleta[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DteBoletasController extends AppController
{

    public function certificar()
    {        
        //$dteBoleta = $this->DteBoletas->newEntity();
        //$foliosObj = new DteFoliosController(); 
        if ($this->request->is('post')) {
            /*$this->loadModel('DteTipoDocumentos');
            $tiposDocs = $this->DteTipoDocumentos->find()->select(["codigo","id"])->enableHydration(false)->toList();
            foreach($tiposDocs as $tipoDoc)
                $tipoDocumento[$tipoDoc["codigo"]] = $tipoDoc["id"];*/            
            $caratula = $this->request->data["caratula"];
            $Emisor = $this->request->data["emisor"];
            $Receptor = $this->request->data["receptor"];
            $documentos = $this->request->data["dataPruebas"];
            $foliosTipo = [ 39 => 1 ];
            //foreach($documentos as $documento) {
                //if(!isset($documento["Encabezado"]["IdDoc"]["Folio"]))
                //    $documento["Encabezado"]["IdDoc"]["Folio"] = $foliosObj->getFolio($documento["Encabezado"]["Emisor"]["RUTEmisor"], $tipoDocumento[$documento["Encabezado"]["IdDoc"]["TipoDTE"]]);                
                //if(array_key_exists($tipoDocumento[$documento["Encabezado"]["IdDoc"]["TipoDTE"]], $search_array))
                //    $foliosTipo[$tipoDocumento[$documento["Encabezado"]["IdDoc"]["TipoDTE"]]] = 1;
                //$documentosConFolio[] = $documento;
            //}

            $boleta["xml"] = $this->setBoleta($foliosTipo, $caratula, $Emisor, $Receptor, $documentos);

            $dom = new \DOMDocument;
            $dom->preserveWhiteSpace = TRUE;
            $dom->loadXML(trim($boleta["xml"]));
            $dom->save(ROOT . DS . 'files' . DS . 'certificacion' . DS . $Emisor["RUTEmisor"] .DS . 'EnvioBOLETAS.xml');
            echo json_encode(["message" => "OK", "EnvioBOLETAS" => $boleta["xml"] ]);
            exit;
        }
        $this->set(compact('boleta'));
    }

    public function setBoleta($folios, $caratula, $Emisor, $Receptor, $documentos){

        $config = AppController::config();
        // Objetos de Firma y Folios
        $Firma = new \sasco\LibreDTE\FirmaElectronica($config['firma']);
        
        $Folios = [];
        $rutaXml = ROOT.DS.'files'.DS.'xml'.DS.'folios'.DS;

        foreach ($folios as $tipo => $cantidad)
            $Folios[$tipo] = new \sasco\LibreDTE\Sii\Folios(file_get_contents($rutaXml.$tipo.'.xml'));

        // generar cada DTE, timbrar, firmar y agregar al sobre de EnvioBOLETA
        $EnvioDTE = new \sasco\LibreDTE\Sii\EnvioDte();
        
        foreach ($documentos as $documento) {
            $DTE = new \sasco\LibreDTE\Sii\Dte($documento);                        
            if (!$DTE->timbrar($Folios[$DTE->getTipo()]))
                break;
            if (!$DTE->firmar($Firma))
                break;
            $EnvioDTE->agregar($DTE);
        }

        $EnvioDTE->setFirma($Firma);
        $EnvioDTE->setCaratula($caratula);
        $EnvioDTE->generar();

        if ($EnvioDTE->schemaValidate()) {            
            return $EnvioDTE->generar();
        } else {
            // si hubo errores mostrar            
            $errorMsg = '';
            foreach (\sasco\LibreDTE\Log::readAll() as $error) {

                return $error."\n";
            }
            echo $errorMsg;
        }        
    }






    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['DteDocumentos']
        ];
        $dteBoletas = $this->paginate($this->DteBoletas);

        $this->set(compact('dteBoletas'));
    }

    /**
     * View method
     *
     * @param string|null $id Dte Boleta id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $dteBoleta = $this->DteBoletas->get($id, [
            'contain' => ['DteDocumentos']
        ]);

        $this->set('dteBoleta', $dteBoleta);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $dteBoleta = $this->DteBoletas->newEntity();
        if ($this->request->is('post')) {
            $dteBoleta = $this->DteBoletas->patchEntity($dteBoleta, $this->request->getData());
            if ($this->DteBoletas->save($dteBoleta)) {
                $this->Flash->success(__('The dte boleta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dte boleta could not be saved. Please, try again.'));
        }
        $dteDocumentos = $this->DteBoletas->DteDocumentos->find('list', ['limit' => 200]);
        $this->set(compact('dteBoleta', 'dteDocumentos'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Dte Boleta id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $dteBoleta = $this->DteBoletas->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dteBoleta = $this->DteBoletas->patchEntity($dteBoleta, $this->request->getData());
            if ($this->DteBoletas->save($dteBoleta)) {
                $this->Flash->success(__('The dte boleta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dte boleta could not be saved. Please, try again.'));
        }
        $dteDocumentos = $this->DteBoletas->DteDocumentos->find('list', ['limit' => 200]);
        $this->set(compact('dteBoleta', 'dteDocumentos'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Dte Boleta id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dteBoleta = $this->DteBoletas->get($id);
        if ($this->DteBoletas->delete($dteBoleta)) {
            $this->Flash->success(__('The dte boleta has been deleted.'));
        } else {
            $this->Flash->error(__('The dte boleta could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
