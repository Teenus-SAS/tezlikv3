<?php

class templateController
{
    public function ctrTemplateOperations()
    {
        include __DIR__ . '../../views/templateOperations.php';
    }

    public function ctrTemplatePlanning()
    {
        include __DIR__ . '../views/templatePlanning.php';
    }

    public function ctrTemplateAdmin()
    {
        include __DIR__ . '../views/templateAdmin.php';
    }
}
