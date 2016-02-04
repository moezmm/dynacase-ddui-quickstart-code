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
}