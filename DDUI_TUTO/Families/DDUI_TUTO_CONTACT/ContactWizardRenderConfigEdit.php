<?php

namespace DduiTuto;

class ContactWizardRenderConfigEdit extends \Dcp\Ui\DefaultEdit
{
    use TContactWizardRenderConfigEdit;

    public function getContextController(\Doc $document)
    {
        $this->initWizardInfos($document);

        $controller = parent::getContextController($document);
        $controller["wizardInfos"] = $this->wizardInfos;
        return $controller;
    }

    public function getTemplates(\Doc $document = null)
    {
        $this->initWizardInfos($document);

        $templates = parent::getTemplates($document);

        $templates["sections"]["menu"]["file"]
            = "DDUI_TUTO/Families/DDUI_TUTO_CONTACT/Layout/contactWizardHeader.mustache";

        return $templates;
    }

    public function getCssReferences(\Doc $document = null)
    {
        $version = \ApplicationParameterManager::getParameterValue(
            "CORE", "WVERSION"
        );

        $cssReferences = parent::getCssReferences($document);

        $cssReferences['DDUI_TUTO_CONTACT_WIZARD']
            = "DDUI_TUTO/Families/DDUI_TUTO_CONTACT/Layout/wizard.css?ws="
            . $version;

        return $cssReferences;
    }
}
