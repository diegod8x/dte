<?php
namespace App\Controller;

use App\Controller\AppController;
define("BOLETA_ELECTRONICA", 39);

/**
 * Boletas Controller
 *
 * @property \App\Model\Table\BoletasTable $Boletas
 *
 * @method \App\Model\Entity\Boleta[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BoletasController extends AppController
{
    public function generar()
    {        
        $boleta = $this->Boletas->newEntity();
        if ($this->request->is('post')) {

            // primer folio a usar para envio de set de pruebas
            $folios = [
                39 => 1 // boleta electrónica
            ];
            // caratula para el envío de los dte
            $caratula = $this->request->data["caratula"];
            $Emisor = $this->request->data["emisor"];
            $Receptor = $this->request->data["receptor"];            
            // datos de las boletas (cada elemento del arreglo $set_pruebas es una boleta)
            $set_pruebas = $this->request->data["dataPruebas"];
            
            $respuesta = $this->setBoleta($folios, $caratula, $Emisor, $Receptor, $set_pruebas);
            pr($respuesta);exit;
            echo $respuesta;exit;

            $boleta = $this->Boletas->patchEntity($boleta, $this->request->getData());

            if ($this->Boletas->save($boleta)) {
 

                $this->Flash->success(__('The boleta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The boleta could not be saved. Please, try again.'));
        }
        $this->set(compact('boleta'));
    }

    public function setBoleta($folios, $caratula, $Emisor, $Receptor, $set_pruebas){

        $config = AppController::config();
        // Objetos de Firma y Folios
        $Firma = new \sasco\LibreDTE\FirmaElectronica($config['firma']);
        $Folios = [];
        $rutaXml = ROOT.DS.'files'.DS.'xml'.DS.'boletas'.DS;
        foreach ($folios as $tipo => $cantidad)
            $Folios[$tipo] = new \sasco\LibreDTE\Sii\Folios(file_get_contents($rutaXml.$tipo.'.xml'));           

        // generar cada DTE, timbrar, firmar y agregar al sobre de EnvioBOLETA
        $EnvioDTE = new \sasco\LibreDTE\Sii\EnvioDte();
        foreach ($set_pruebas as $documento) {
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
                $errorMsg.=$error."\n";
            }
            return $errorMsg;
        }        
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $boletas = $this->paginate($this->Boletas);

        $this->set(compact('boletas'));
    }

    /**
     * View method
     *
     * @param string|null $id Boleta id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $boleta = $this->Boletas->get($id, [
            'contain' => []
        ]);

        $this->set('boleta', $boleta);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $boleta = $this->Boletas->newEntity();
        if ($this->request->is('post')) {
            $boleta = $this->Boletas->patchEntity($boleta, $this->request->getData());
            if ($this->Boletas->save($boleta)) {
                $this->Flash->success(__('The boleta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The boleta could not be saved. Please, try again.'));
        }
        $this->set(compact('boleta'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Boleta id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $boleta = $this->Boletas->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $boleta = $this->Boletas->patchEntity($boleta, $this->request->getData());
            if ($this->Boletas->save($boleta)) {
                $this->Flash->success(__('The boleta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The boleta could not be saved. Please, try again.'));
        }
        $this->set(compact('boleta'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Boleta id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $boleta = $this->Boletas->get($id);
        if ($this->Boletas->delete($boleta)) {
            $this->Flash->success(__('The boleta has been deleted.'));
        } else {
            $this->Flash->error(__('The boleta could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
