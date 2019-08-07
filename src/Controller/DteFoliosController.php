<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * DteFolios Controller
 *
 * @property \App\Model\Table\DteFoliosTable $DteFolios
 *
 * @method \App\Model\Entity\DteFolio[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DteFoliosController extends AppController
{

    public function getFolio($rut, $docId){

        $folioValue = $this->DteFolios->find()
                        ->select(['id','ultimo_disponible'])
                        ->where(['rut_emisor' => $rut, 'dte_tipo_documento_id' => $docId])
                        ->enableHydration(false)
                        ->first();

        $folio = $this->DteFolios->get($folioValue["id"]); 
        $folio->ultimo_disponible = $folioValue["ultimo_disponible"] + 1;
        $this->DteFolios->save($folio);

        return $folioValue["ultimo_disponible"];
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
        $dteFolios = $this->paginate($this->DteFolios);

        $this->set(compact('dteFolios'));
    }

    /**
     * View method
     *
     * @param string|null $id Dte Folio id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $dteFolio = $this->DteFolios->get($id, [
            'contain' => ['DteTipoDocumentos']
        ]);

        $this->set('dteFolio', $dteFolio);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $dteFolio = $this->DteFolios->newEntity();
        if ($this->request->is('post')) {
            $dteFolio = $this->DteFolios->patchEntity($dteFolio, $this->request->getData());
            if ($this->DteFolios->save($dteFolio)) {
                $this->Flash->success(__('The dte folio has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dte folio could not be saved. Please, try again.'));
        }
        $dteTipoDocumentos = $this->DteFolios->DteTipoDocumentos->find('list', ['limit' => 200]);
        $this->set(compact('dteFolio', 'dteTipoDocumentos'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Dte Folio id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $dteFolio = $this->DteFolios->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dteFolio = $this->DteFolios->patchEntity($dteFolio, $this->request->getData());
            if ($this->DteFolios->save($dteFolio)) {
                $this->Flash->success(__('The dte folio has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dte folio could not be saved. Please, try again.'));
        }
        $dteTipoDocumentos = $this->DteFolios->DteTipoDocumentos->find('list', ['limit' => 200]);
        $this->set(compact('dteFolio', 'dteTipoDocumentos'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Dte Folio id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dteFolio = $this->DteFolios->get($id);
        if ($this->DteFolios->delete($dteFolio)) {
            $this->Flash->success(__('The dte folio has been deleted.'));
        } else {
            $this->Flash->error(__('The dte folio could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
