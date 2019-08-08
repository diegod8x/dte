<?php
namespace App\Controller;

use App\Controller\AppController;

\sasco\LibreDTE\Sii::setAmbiente(\sasco\LibreDTE\Sii::CERTIFICACION);
define("CERT_EMP", ROOT . DS . 'files' . DS . 'certificacion' . DS);
define("FILE_DTE", 'EnvioDTE');

/**
 * DteFacturas Controller
 *
 * @property \App\Model\Table\DteFacturasTable $DteFacturas
 *
 * @method \App\Model\Entity\DteFactura[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DteFacturasController extends AppController
{


    public function certificar()
    {
        $config = AppController::config();
        if ($this->request->is('post')) {
                        
            if (!empty($this->request->data["data"]) && !empty($this->request->data["33"]) && !empty($this->request->data["61"]) && !empty($this->request->data["56"]) ) {
                $this->request->data["data"] = json_decode($this->request->data["data"], true);
            }
            else {
                echo json_encode(["message" => "Debe completar todos los campos antes de enviar la solicitud", "data" => []]); 
                exit;
            }
            
            if (!empty($this->request->data["data"]["caratula"]) && !empty($this->request->data["data"]["emisor"]) && !empty($this->request->data["data"]["receptor"]) && !empty($this->request->data["data"]["dataPruebas"])){

                $caratula = $this->request->data["data"]["caratula"];
                $Emisor = $this->request->data["data"]["emisor"];
                $Receptor = $this->request->data["data"]["receptor"];
                $documentos = $this->request->data["data"]["dataPruebas"];
                $folios = $this->request->data["data"]["folios"];

                $factura = $this->request->data["33"];
                $pathCAF = CERT_EMP . $Emisor["RUTEmisor"] . DS . 'folios' . DS . basename($factura['name']);
                move_uploaded_file($factura['tmp_name'], $pathCAF);

                $notaCredito = $this->request->data["61"];
                $pathCAF = CERT_EMP . $Emisor["RUTEmisor"] . DS . 'folios' . DS . basename($notaCredito['name']);
                move_uploaded_file($notaCredito['tmp_name'], $pathCAF);

                $notaDebito = $this->request->data["56"];
                $pathCAF = CERT_EMP . $Emisor["RUTEmisor"] . DS . 'folios' . DS . basename($notaDebito['name']);
                move_uploaded_file($notaDebito['tmp_name'], $pathCAF);
                
                $Firma = new \sasco\LibreDTE\FirmaElectronica($config['firma']);
                $Folios = [];
                $pathXML = CERT_EMP . $Emisor["RUTEmisor"] . DS . 'folios' . DS;
                foreach ($folios as $tipo => $cantidad)
                    $Folios[$tipo] = new \sasco\LibreDTE\Sii\Folios(file_get_contents($pathXML.$tipo.'.xml'));
                $EnvioDTE = new \sasco\LibreDTE\Sii\EnvioDte();

                // generar cada DTE, timbrar, firmar y agregar al sobre de EnvioDTE
                foreach ($documentos as $documento) {
                    $DTE = new \sasco\LibreDTE\Sii\Dte($documento);
                    if (!$DTE->timbrar($Folios[$DTE->getTipo()]))
                        break;
                    if (!$DTE->firmar($Firma))
                        break;
                    $EnvioDTE->agregar($DTE);
                }

                // enviar dtes y mostrar resultado del envío: track id o bien =false si hubo error
                $EnvioDTE->setCaratula($caratula);
                $EnvioDTE->setFirma($Firma);
                //file_put_contents('xml/EnvioDTE.xml', $EnvioDTE->generar()); // guardar XML en sistema de archivos
                $track_id = $EnvioDTE->enviar();
                $msg = 'Track id ' . $track_id;
                //var_dump($track_id);

                // si hubo errores mostrar
                $obs = '';
                foreach (\sasco\LibreDTE\Log::readAll() as $error)
                    $obs.= $error . ' ';

                echo json_encode(["message"=>  $msg . (($obs != '') ? ". Observaciones: " . $obs : $obs) . '' , "data" => []]);
                
            } else {
                echo json_encode(["message" => "Debe completar todos los campos antes de enviar la solicitud.", "data" => []]);
            }            
        }
        exit;  
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
        $dteFacturas = $this->paginate($this->DteFacturas);

        $this->set(compact('dteFacturas'));
    }

    /**
     * View method
     *
     * @param string|null $id Dte Factura id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $dteFactura = $this->DteFacturas->get($id, [
            'contain' => ['DteDocumentos']
        ]);

        $this->set('dteFactura', $dteFactura);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $dteFactura = $this->DteFacturas->newEntity();
        if ($this->request->is('post')) {
            $dteFactura = $this->DteFacturas->patchEntity($dteFactura, $this->request->getData());
            if ($this->DteFacturas->save($dteFactura)) {
                $this->Flash->success(__('The dte factura has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dte factura could not be saved. Please, try again.'));
        }
        $dteDocumentos = $this->DteFacturas->DteDocumentos->find('list', ['limit' => 200]);
        $this->set(compact('dteFactura', 'dteDocumentos'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Dte Factura id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $dteFactura = $this->DteFacturas->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dteFactura = $this->DteFacturas->patchEntity($dteFactura, $this->request->getData());
            if ($this->DteFacturas->save($dteFactura)) {
                $this->Flash->success(__('The dte factura has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dte factura could not be saved. Please, try again.'));
        }
        $dteDocumentos = $this->DteFacturas->DteDocumentos->find('list', ['limit' => 200]);
        $this->set(compact('dteFactura', 'dteDocumentos'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Dte Factura id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dteFactura = $this->DteFacturas->get($id);
        if ($this->DteFacturas->delete($dteFactura)) {
            $this->Flash->success(__('The dte factura has been deleted.'));
        } else {
            $this->Flash->error(__('The dte factura could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
