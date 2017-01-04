<?php
namespace Search\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Search\Controller\AppController;
use Search\Model\Entity\Widget;

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
        $query = $this->Dashboards->getUserDashboards($this->Auth->user());
        $dashboards = $query->all();

        if (0 < $dashboards->count()) {
            return $this->redirect(['action' => 'view', $dashboards->first()->id]);
        }
    }

    /**
     * View method
     *
     * @param string|null $id Dashboard id.
     * @return void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $dashboard = $this->Dashboards->get($id, [
            'contain' => [
                'Roles',
                'Widgets' => [
                    'sort' => [
                        'Widgets.row' => 'ASC',
                        'Widgets.column' => 'ASC'
                    ]
                ]
            ]
        ]);

        if (method_exists($this, '_checkRoleAccess')) {
            $this->_checkRoleAccess($dashboard->role_id);
        }

        $widgets = [];
        if (!empty($dashboard->widgets)) {
            foreach ($dashboard->widgets as $w) {
                $widgetObject = WidgetFactory::create(
                    $w,
                    $this->request,
                    $this->response,
                    $this->eventManager()
                );
                // skip widgets without data
                if (empty($widgetObject->widgetData)) {
                    continue;
                }

                $widgets[] = $widgetObject;
            }
        }

        $widgetsTable = TableRegistry::get('Search.Widgets');
        $this->set('columns', Configure::readOrFail('Search.dashboard.columns'));
        $this->set('widgets', $widgets);
        $this->set('user', $this->Auth->user());
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

        $widgetsTable = TableRegistry::get('Search.Widgets');
        $widgets = $widgetsTable->getWidgets();

        if ($this->request->is('post')) {
            $data = $this->request->data;

            $widgets = [];

            $dashboard = $this->Dashboards->patchEntity($dashboard, [
                'name' => $data['name'],
                'role_id' => $data['role_id']
            ]);

            $resultedDashboard = $this->Dashboards->save($dashboard);

            if ($resultedDashboard) {
                $this->Flash->success(__('The dashboard has been saved.'));

                $dashboardId = $resultedDashboard->id;

                if (!empty($data['widgets'])) {
                    $count = count($data['widgets']['widget_id']);
                    for ($i = 0; $i < $count; $i++) {
                        array_push($widgets, [
                            'dashboard_id' => $dashboardId,
                            'widget_id' => $data['widgets']['widget_id'][$i],
                            'widget_type' => $data['widgets']['widget_type'][$i],
                            'widget_options' => null,
                            'column' => $data['widgets']['column'][$i],
                            'row' => $data['widgets']['row'][$i],
                        ]);
                    }

                    $widgetTable = TableRegistry::get('Widgets');
                    foreach ($widgets as $w) {
                        $widget = $widgetTable->newEntity();
                        $widget = $widgetTable->patchEntity($widget, $w);
                        $widgetTable->save($widget);
                    }
                }

                return $this->redirect(['action' => 'view', $dashboard->id]);
            } else {
                $this->Flash->error(__('The dashboard could not be saved. Please, try again.'));
            }
        }

        $roles = $this->Dashboards->Roles->find('list', ['limit' => 200]);

        $this->set(compact('dashboard', 'roles', 'widgets'));
        $this->set('columns', Configure::readOrFail('Search.dashboard.columns'));
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
            'contain' => [
                'Widgets' => [
                    'sort' => [
                        'Widgets.row' => 'ASC',
                        'Widgets.column' => 'ASC'
                    ]
                ]
            ]
        ]);

        $dashboardWidgets = $dashboard->widgets;
        unset($dashboard->widgets);

        $widgetsTable = TableRegistry::get('Search.Widgets');

        $widgets = $widgetsTable->getWidgets();
        $savedWidgetData = [];
        foreach ($dashboardWidgets as $dashboardWidget) {
            foreach ($widgets as $k => $widget) {
                if ($dashboardWidget->widget_id !== $widget['data']['id']) {
                    continue;
                }
                $widget['data']['column'] = $dashboardWidget->column;
                $widget['data']['row'] = $dashboardWidget->row;
                array_push($savedWidgetData, $widget);
                unset($widgets[$k]);
            }
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->data;
            $widgets = [];

            if (!empty($data['widgets'])) {
                $count = count($data['widgets']['widget_id']);
                for ($i = 0; $i < $count; $i++) {
                    array_push($widgets, [
                        'dashboard_id' => $dashboard->id,
                        'widget_id' => $data['widgets']['widget_id'][$i],
                        'widget_type' => $data['widgets']['widget_type'][$i],
                        'widget_options' => null,
                        'column' => $data['widgets']['column'][$i],
                        'row' => $data['widgets']['row'][$i],
                    ]);
                }
            }

            unset($dashboard->widgets);

            $dashboard = $this->Dashboards->patchEntity($dashboard, [
                'name' => $data['name'],
                'role_id' => $data['role_id']
            ]);

            if ($this->Dashboards->save($dashboard)) {
                $this->Flash->success(__('The dashboard has been saved.'));

                $widgetTable = TableRegistry::get('Widgets');
                $widgetTable->deleteAll([
                    'dashboard_id' => $dashboard->id
                ]);

                if (!empty($widgets)) {
                    foreach ($widgets as $w) {
                        $widget = $widgetTable->newEntity();
                        $widget = $widgetTable->patchEntity($widget, $w);
                        $resultedWidgets = $widgetTable->save($widget);
                    }
                }

                return $this->redirect(['action' => 'view', $id]);
            } else {
                $this->Flash->error(__('The dashboard could not be saved. Please, try again.'));
            }
        }

        $roles = $this->Dashboards->Roles->find('list', ['limit' => 200]);

        $this->set(compact('dashboard', 'roles', 'widgets', 'savedWidgetData'));
        $this->set('columns', Configure::readOrFail('Search.dashboard.columns'));
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
            $widgetTable = TableRegistry::get('Widgets');
            $widgetTable->deleteAll([
                'dashboard_id' => $id
            ]);


            $this->Flash->success(__('The dashboard has been deleted.'));
        } else {
            $this->Flash->error(__('The dashboard could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
