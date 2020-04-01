<?php

namespace SousedskaPomoc\Presenters;

use Ublaboo\DataGrid\DataGrid;

class HeadquartersPresenter extends BasePresenter
{
    public function beforeRender()
    {
        if (!$this->user->isInRole('admin')) {
            $this->flashMessage("Do této sekce nemáte mít přístup!");
            $this->redirect("System:profile");
        }
        parent::beforeRender(); // TODO: Change the autogenerated stub
    }

    public function createComponentUsersDataGrid()
    {
        $role = $this->presenter->getParameter('role');

        $grid = new DataGrid();
        $grid->setDataSource($this->userManager->fetchAllUsersInRole($role));
        $grid->addColumnNumber('id', 'ID uživatele');
        $grid->addColumnText('personName', 'Jméno a příjmení')->setFilterText();
        $grid->addColumnText('personEmail', 'E-mail')->setFilterText();
        $grid->addColumnText('personPhone', 'Telefon')->setFilterText();
        $grid->addColumnText('town', 'Město')->setFilterText();
        $grid->addColumnNumber('active', 'Online')->setFilterText();
        $grid->setDefaultPerPage(100);

        return $grid;
    }

    public function createComponentDemandsDataGrid()
    {
        $grid = new DataGrid();
        $grid->setDataSource($this->orderManager->fetchAllWebDemands());
        $grid->addColumnNumber('id', 'ID')->setFilterText();
        $grid->addColumnText('id_volunteers', 'Zadavatel');
        $grid->addColumnText('town', 'Město')->setFilterText();
        $grid->addColumnText('delivery_address', 'Adresa')->setFilterText();
        $grid->addColumnText('delivery_phone', 'Telefon')->setFilterText();
        $grid->addColumnText('order_items', 'Položky obj.')->setFilterText();
        $grid->addFilterSelect('status', 'Stav obj', []);
        $grid->addColumnDateTime('createdAt', 'Datum přidání');
        $grid->addAction('approve', 'Schválit', 'approve!')->setClass("btn btn-success btn-sm");
        $grid->addAction('detail', 'Detail', 'Courier:detail')->setClass("btn btn-primary btn-sm");
        $grid->addAction('delete', 'X', 'deleteDemand!')->setClass("btn btn-danger btn-sm");

        return $grid;
    }

    public function createComponentOrdersDataGrid()
    {
        $grid = new DataGrid();
        $grid->setDataSource($this->orderManager->findAllOrdersData());
        $grid->addColumnNumber('id', 'ID')->setFilterText();
        $grid->addColumnText('id_volunteers', 'Zadavatel');
        $grid->addColumnText('town', 'Město')->setFilterText();
        $grid->addColumnText('delivery_address', 'Adresa')->setFilterText();
        $grid->addColumnText('delivery_phone', 'Telefon')->setFilterText();
        $grid->addColumnText('order_items', 'Položky obj.')->setFilterText();

        $grid->addColumnDateTime('createdAt', 'Datum přidání');
        $grid->addColumnText('status', 'Status')->setFilterText();
        $grid->addAction('reset', 'Resetovat', 'reset!')->setClass("btn btn-danger btn-sm");
        $grid->addAction('detail', 'Detail', 'Courier:detail')->setClass("btn btn-primary btn-sm");
        $grid->addAction('delete', 'X', 'deleteOrder!')->setClass("btn btn-danger btn-sm");

        return $grid;
    }

    public function renderListUsers($id, $role)
    {
        $this->template->role = $role;
    }

    public function renderTowns()
    {
        $this->template->towns = $this->userManager->getTowns();
    }

    public function handleReset($id)
    {
        $this->orderManager->assignOrder(null, $id, null, 'new');
        $this->flashMessage("Objednávka byla obnovena do výchozího stavu.");
        $this->redirect('Headquarters:orders');
    }

    public function handleDeleteDemand($id)
    {
        $this->orderManager->remove($id);
        $this->flashMessage("Poptávka byla smazána.");
        $this->redirect('Headquarters:demands');
    }

    public function handleDeleteOrder($id)
    {
        $this->orderManager->remove($id);
        $this->flashMessage("Objednávka byla smazána.");
        $this->redirect('Headquarters:orders');
    }

    public function handleApprove($id)
    {
        $this->orderManager->changeStatus($id, 'new');
        $this->flashMessage("Objednávka byla schválena.");
        $this->redirect('Headquarters:demands');
    }
}
