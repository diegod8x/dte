<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Controller\DetBoletasController;

/**
 * DteDocumentos Controller
 *
 * @property \App\Model\Table\DteDocumentosTable $DteDocumentos
 *
 * @method \App\Model\Entity\DteDocumento[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DteDocumentosController extends AppController
{

    public function emitir(){

        if ($this->request->is('post')) {

            $data = $this->request->data;
            //pr($data);exit;
            foreach ($data["dte"] as $dte) {
                pr($dte);exit;
                switch ($dte["Encabezado"]["TipoDTE"]) {
                    case 39: DetBoletasController::emitir($data);
                    break;
                    default: return json_encode(["message"=>"Documento no soportado."]);
                }
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
            'contain' => ['DteTipoDocumentos']
        ];
        $dteDocumentos = $this->paginate($this->DteDocumentos);

        $this->set(compact('dteDocumentos'));
    }

    /**
     * View method
     *
     * @param string|null $id Dte Documento id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $dteDocumento = $this->DteDocumentos->get($id, [
            'contain' => ['DteTipoDocumentos', 'DteBoletas']
        ]);

        $this->set('dteDocumento', $dteDocumento);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $dteDocumento = $this->DteDocumentos->newEntity();
        if ($this->request->is('post')) {
            $dteDocumento = $this->DteDocumentos->patchEntity($dteDocumento, $this->request->getData());
            if ($this->DteDocumentos->save($dteDocumento)) {
                $this->Flash->success(__('The dte documento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dte documento could not be saved. Please, try again.'));
        }
        $dteTipoDocumentos = $this->DteDocumentos->DteTipoDocumentos->find('list', ['limit' => 200]);
        $this->set(compact('dteDocumento', 'dteTipoDocumentos'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Dte Documento id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $dteDocumento = $this->DteDocumentos->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dteDocumento = $this->DteDocumentos->patchEntity($dteDocumento, $this->request->getData());
            if ($this->DteDocumentos->save($dteDocumento)) {
                $this->Flash->success(__('The dte documento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dte documento could not be saved. Please, try again.'));
        }
        $dteTipoDocumentos = $this->DteDocumentos->DteTipoDocumentos->find('list', ['limit' => 200]);
        $this->set(compact('dteDocumento', 'dteTipoDocumentos'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Dte Documento id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dteDocumento = $this->DteDocumentos->get($id);
        if ($this->DteDocumentos->delete($dteDocumento)) {
            $this->Flash->success(__('The dte documento has been deleted.'));
        } else {
            $this->Flash->error(__('The dte documento could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
