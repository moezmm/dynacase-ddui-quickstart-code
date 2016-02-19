<?php

namespace DduiTuto;

class ContactRenderConfigView extends \Dcp\Ui\DefaultView
{
    public function getTemplates(\Doc $document = null)
    {
        $templates = parent::getTemplates($document);

        $templates["sections"]["content"]["file"]
            = "DDUI_TUTO/Families/DDUI_TUTO_CONTACT/Layout/contactContent.mustache";
        return $templates;
    }

    public function getJsReferences(\Doc $document = null)
    {
        $version = \ApplicationParameterManager::getParameterValue(
            "CORE", "WVERSION"
        );

        $jsReferences = parent::getJsReferences();

        $jsReferences["bootstrap_collapse"]
            = "lib/bootstrap/3/js/collapse.js?ws="
            . $version;

        $jsReferences["jsqr"]
            = "lib/jsqr/jsqr-1.0.2-min.js?ws="
            . $version;

        return $jsReferences;
    }

    public function getCssReferences(\Doc $document = null)
    {
        $version = \ApplicationParameterManager::getParameterValue(
            "CORE", "WVERSION"
        );

        $cssReferences = parent::getCssReferences($document);

        $cssReferences['DDUI_TUTO_CONTACT_view']
            = "DDUI_TUTO/Families/DDUI_TUTO_CONTACT/Layout/view.css?ws="
            . $version;

        return $cssReferences;
    }

    public function getMenu(\Doc $document)
    {
        $menu = parent::getMenu($document);

        $modifLabel = ___("Modify", "UiMenu");

        // get wizard informations stored on the document
        $wizardTags = $document->getUTag('wizard');
        if (false === $wizardTags) {
            $wizardTags = [];
        } else {
            $wizardTags = json_decode($wizardTags->comment, true);
        }
        // append the name of the last displayed step to the label
        if (isset($wizardTags['currentWizardStepName'])) {
            $modifLabel .= " (" . $wizardTags['currentWizardStepLabel'] . ")";
        }

        // get the "modify" button
        $modifMenu = $menu->getElement("modify");
        // update its label
        if (!is_null($modifMenu)) {
            $modifMenu->setTextLabel($modifLabel);
        }

        return $menu;
    }
}