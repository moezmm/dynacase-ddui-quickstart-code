<?php

namespace DduiTuto;

use Dcp\Ui\ElementMenu;
use Dcp\Ui\ItemMenu;

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

        $contentTemplate = "";
        foreach($this->wizardInfos['currentStep']['attributes']['frames'] as $frame) {
            $contentTemplate .= "{{{document.attributes." . $frame['attrid'] . ".htmlView}}}";
        }

        $templates["sections"]["content"]["content"] = $contentTemplate;

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

    public function getMenu(\Doc $document)
    {
        $this->initWizardInfos($document);

        $menu = parent::getMenu($document);

        //Hide some menus
        foreach(['save', 'create', 'createAndClose', 'close', 'workflow'] as $elementId) {
            $element = $menu->getElement($elementId);
            if (!is_null($element)) {
                $element->setVisibility(ElementMenu::VisibilityHidden);
            }
        }

        //add custom menus
        $item = new ItemMenu(
            "wizard_end", ___("End creation", "ddui_tuto:wizard"),
            "#action/wizard.end"
        );
        $item->setBeforeContent('<div class="fa fa-check" />');
        $item->setHtmlAttribute("class", "menu--right");
        if (\DduiTuto\DDUI_TUTO_CONTACT__WFL::e_up_to_date === $document->getState()
            || !empty($this->wizardInfos['nextStep'])) {
            $item->setVisibility($item::VisibilityHidden);
        }
        $item->useConfirm(___("Confirm end creation", "ddui_tuto:wizard"));
        $menu->appendElement($item);

        $item = new ItemMenu(
            "wizard_next", ___("Next step", "ddui_tuto:wizard"),
            "#action/wizard.next"
        );
        $item->setBeforeContent('<div class="fa fa-step-forward" />');
        $item->setHtmlAttribute("class", "menu--right");
        if (empty($this->wizardInfos['nextStep'])) {
            $item->setVisibility($item::VisibilityHidden);
        }
        $menu->appendElement($item);

        $item = new ItemMenu(
            "wizard_previous", ___("Previous step", "ddui_tuto:wizard"),
            "#action/wizard.previous"
        );
        $item->setBeforeContent('<div class="fa fa-step-backward" />');
        $item->setHtmlAttribute("class", "menu--right");
        if (empty($this->wizardInfos['previousStep'])) {
            $item->setVisibility($item::VisibilityDisabled);
        }
        $menu->appendElement($item);

        $item = new ItemMenu(
            "wizard_cancel", ___("Cancel", "ddui_tuto:wizard"),
            "#action/wizard.cancel"
        );
        $item->setBeforeContent('<div class="fa fa-undo" />');
        $item->setHtmlAttribute("class", "menu--left");
        $menu->appendElement($item);

        return $menu;
    }

    public function getVisibilities(\Doc $document)
    {
        $this->initWizardInfos($document);
        return parent::getVisibilities($document);
    }
}
