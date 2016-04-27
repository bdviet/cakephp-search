<?php
namespace Search\Controller;

use Search\Controller\AppController;

/**
 * Dashboards Controller
 *
 * @property \Search\Model\Table\DashboardsTable $Dashboards
 */
class DashboardsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Roles']
        ];
        $dashboards = $this->paginate($this->Dashboards);

        $this->set(compact('dashboards'));
        $this->set('_serialize', ['dashboards']);
    }

    /**
     * View method
     *
     * @param string|null $id Dashboard id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $dashboard = $this->Dashboards->get($id, [
            'contain' => ['Roles', 'SavedSearches']
        ]);

        $this->set('dashboard', $dashboard);
        $this->set('_serialize', ['dashboard']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $dashboard = $this->Dashboards->newEntity();
        if ($this->request->is('post')) {
            $dashboard = $this->Dashboards->patchEntity($dashboard, $this->request->data);
            if ($this->Dashboards->save($dashboard)) {
                $this->Flash->success(__('The dashboard has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The dashboard could not be saved. Please, try again.'));
            }
        }
        $roles = $this->Dashboards->Roles->find('list', ['limit' => 200]);
        $savedSearches = $this->Dashboards->SavedSearches->find('list')
            ->where(['SavedSearches.name IS NOT' => null])
            ->limit(200);
        $this->set(compact('dashboard', 'roles', 'savedSearches'));
        $this->set('_serialize', ['dashboard']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Dashboard id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $dashboard = $this->Dashboards->get($id, [
            'contain' => ['SavedSearches']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dashboard = $this->Dashboards->patchEntity($dashboard, $this->request->data);
            if ($this->Dashboards->save($dashboard)) {
                $this->Flash->success(__('The dashboard has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The dashboard could not be saved. Please, try again.'));
            }
        }
        $roles = $this->Dashboards->Roles->find('list', ['limit' => 200]);
        $savedSearches = $this->Dashboards->SavedSearches->find('list')
            ->where(['SavedSearches.name IS NOT' => null])
            ->limit(200);
        $this->set(compact('dashboard', 'roles', 'savedSearches'));
        $this->set('_serialize', ['dashboard']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Dashboard id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dashboard = $this->Dashboards->get($id);
        if ($this->Dashboards->delete($dashboard)) {
            $this->Flash->success(__('The dashboard has been deleted.'));
        } else {
            $this->Flash->error(__('The dashboard could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
