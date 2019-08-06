<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * DteTipoDocumentos Controller
 *
 * @property \App\Model\Table\DteTipoDocumentosTable $DteTipoDocumentos
 *
 * @method \App\Model\Entity\DteTipoDocumento[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DteTipoDocumentosController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $dteTipoDocumentos = $this->paginate($this->DteTipoDocumentos);

        $this->set(compact('dteTipoDocumentos'));
    }

    /**
     * View method
     *
     * @param string|null $id Dte Tipo Documento id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $dteTipoDocumento = $this->DteTipoDocumentos->get($id, [
            'contain' => ['DteDocumentos', 'DteFolios']
        ]);

        $this->set('dteTipoDocumento', $dteTipoDocumento);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $dteTipoDocumento = $this->DteTipoDocumentos->newEntity();
        if ($this->request->is('post')) {
            $dteTipoDocumento = $this->DteTipoDocumentos->patchEntity($dteTipoDocumento, $this->request->getData());
            if ($this->DteTipoDocumentos->save($dteTipoDocumento)) {
                $this->Flash->success(__('The dte tipo documento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dte tipo documento could not be saved. Please, try again.'));
        }
        $this->set(compact('dteTipoDocumento'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Dte Tipo Documento id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $dteTipoDocumento = $this->DteTipoDocumentos->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dteTipoDocumento = $this->DteTipoDocumentos->patchEntity($dteTipoDocumento, $this->request->getData());
            if ($this->DteTipoDocumentos->save($dteTipoDocumento)) {
                $this->Flash->success(__('The dte tipo documento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dte tipo documento could not be saved. Please, try again.'));
        }
        $this->set(compact('dteTipoDocumento'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Dte Tipo Documento id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dteTipoDocumento = $this->DteTipoDocumentos->get($id);
        if ($this->DteTipoDocumentos->delete($dteTipoDocumento)) {
            $this->Flash->success(__('The dte tipo documento has been deleted.'));
        } else {
            $this->Flash->error(__('The dte tipo documento could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
