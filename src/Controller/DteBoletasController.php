<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * DteBoletas Controller
 *
 * @property \App\Model\Table\DteBoletasTable $DteBoletas
 *
 * @method \App\Model\Entity\DteBoleta[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DteBoletasController extends AppController
{
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
