<?php

namespace DduiTuto;

use Dcp\Ui\IRenderConfig;
use Dcp\Ui\IRenderConfigAccess;
use Dcp\Ui\RenderConfigManager;

class ContactCvdocRenderConfigAccess implements IRenderConfigAccess
{
    /**
     * @param string $mode
     * @param \Doc   $document
     *
     * @return IRenderConfig
     */
    public function getRenderConfig($mode, \Doc $document)
    {
        switch($mode) {
        case RenderConfigManager::ViewMode:
            //let the CV RENDER do the job
            return null;
        default:
            $wizardRenderConfig = new ContactWizardRenderConfigEdit();
            $wizardRenderConfig->initWizardInfos($document);
            return $wizardRenderConfig;
        }
    }
}
