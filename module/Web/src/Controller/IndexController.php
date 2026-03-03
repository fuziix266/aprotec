<?php

namespace Web\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function navidadAction()
    {
        return new ViewModel();
    }

    public function noticiasAction()
    {
        return new ViewModel();
    }

    public function importanteAction()
    {
        return new ViewModel();
    }

    public function conveniosAction()
    {
        return new ViewModel();
    }
}
