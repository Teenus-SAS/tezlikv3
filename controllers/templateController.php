<?php

class templateController
{
    public function ctrTemplateOperations()
    {
        include __DIR__ . '../../cost/views/templateOperations.php';
    }

    public function ctrTemplatePlanning()
    {
        include __DIR__ . '../../planning/views/templatePlanning.php';
    }

    public function ctrTemplateAdmin()
    {
        include __DIR__ . '../../admin/views/templateAdmin.php';
    }

    public function ctrTemplateGlobal()
    {
        include __DIR__ . '../../selector/views/templateSelector.php';
    }
}
