<?php

namespace DduiTuto;

class ContactRenderConfigView extends \Dcp\Ui\DefaultView
{
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