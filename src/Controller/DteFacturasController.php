<?php
namespace App\Controller;

use App\Controller\AppController;

\sasco\LibreDTE\Sii::setAmbiente(\sasco\LibreDTE\Sii::CERTIFICACION);
define("CERT_BOLETAS", ROOT . DS . 'files' . DS . 'certificacion' . DS);
define("FILE_BOLETAS", 'EnvioBOLETAS');

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
        
        if ($this->request->is('post')) {
                        
            /*if (!empty($this->request->data["data"]) && !empty($this->request->data["file"])) {
                $this->request->data["data"] = json_decode($this->request->data["data"], true);
            }
            else {
                echo json_encode(["message" => "Debe completar todos los campos antes de enviar la solicitud", "data" => []]); 
                exit;
            }*/

            if (!empty($this->request->data["data"]["caratula"]) && !empty($this->request->data["data"]["emisor"]) && !empty($this->request->data["data"]["receptor"]) && !empty($this->request->data["data"]["dataPruebas"])){

                $caratula = $this->request->data["data"]["caratula"];
                $Emisor = $this->request->data["data"]["emisor"];
                $Receptor = $this->request->data["data"]["receptor"];
                $documentos = $this->request->data["data"]["dataPruebas"];
                
                $file = $this->request->data["file"];                
                $pathXML = CERT_BOLETAS . $Emisor["RUTEmisor"] . DS . 'xml' . DS . FILE_BOLETAS . '.xml';
                $pathCAF = CERT_BOLETAS . $Emisor["RUTEmisor"] . DS . 'folios' . DS . basename($file['name']);
                move_uploaded_file($file['tmp_name'], $pathCAF);
                
                $foliosTipo = [ 39 => 1 ];

                $boleta["xml"] = $this->setBoleta($foliosTipo, $caratula, $Emisor, $Receptor, $documentos);
                
                $dom = new \DOMDocument;
                $dom->preserveWhiteSpace = TRUE;
                $dom->loadXML(trim($boleta["xml"]));
                $dom->save($pathXML);

                header('Content-type: text/xml');
                header('Content-Disposition: attachment; filename='.FILE_BOLETAS.'.xml');

                echo $dom->saveXML() . "\n";

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
