<?php

namespace DduiTuto;

use Dcp\AttributeIdentifiers\Cvdoc as CvdocAttributes;
use Dcp\Family\Cvdoc as CvdocFamily;

trait TContactWizardRenderConfigEdit
{

    protected $wizardInfos;

    /**
     * @param \Doc $document the document object
     * @param bool $force    recompute wizard infos even if they are already cached
     *
     * compute and cache informations about views
     * from the viewrender associated with the document
     */
    public function initWizardInfos(\Doc $document, $force = false)
    {
        if ($force || is_null($this->wizardInfos)) {
            $wizardSteps = [];
            $wizardCurrentStepKey = 0;
            $wizardNextStep = null;
            $goto = null;

            // get the list of wizard views
            $wizardViews = $this->getWizardViews($document);
            $nbSteps = count($wizardViews);

            // get informations on the wizard stored in a tag of the document
            // it contains last visited step
            $wizardTags = $document->getUTag('wizard');
            if (false === $wizardTags) {
                $wizardTags = [];
            } else {
                $wizardTags = json_decode($wizardTags->comment, true);
            }

            // get informations transmitted by the client
            // it contains
            // - (optional)currentWizardStepName: the name of the step currently displayed
            // - (optional)goto: where the wizard wants to go
            //    - wizard.targetStep: the name of the step requested is in targetStep
            //    - wizard.previous: the wizard is asking the previous viewable step
            //    - wizard.next: the wizard is asking the next viewable step
            // - (required if goto = wizard.targetStep)targetStep:
            //      the name of the next step the wizard is asking to display
            $customClientData = \Dcp\Ui\Utils::getCustomClientData();

            if (isset($customClientData['goto'])
                && 'wizard.targetStep' === $customClientData['goto']
            ) {
                // a specific step has been asked
                $currentWizardStepName = $customClientData['targetStep'];
            } elseif (isset($customClientData['currentWizardStepName'])) {
                // stay on the current step
                $currentWizardStepName
                    = $customClientData['currentWizardStepName'];
            } else {
                // the wizard did not gave current step name
                // (probably because we are displaying the document from consultation or from application menu)
                if (isset($wizardTags['currentWizardStepName'])) {
                    // but the document has already been displayed and the last visited step has been stored
                    // it will be the default step
                    $currentWizardStepName
                        = $wizardTags['currentWizardStepName'];
                } else {
                    // first visit to this document with the wizard
                    $currentWizardStepName = null;
                }
            }

            foreach ($wizardViews as $key => $wizardView) {
                // inject attributes informations in the step informations
                $wizardView['attributes'] = $this->getWizardViewAttributes(
                    $document,
                    $wizardView[CvdocAttributes::cv_mskid]
                );

                // add the step to the list of steps
                $wizardSteps[] = $wizardView;

                if ($wizardView[CvdocAttributes::cv_idview]
                    === $currentWizardStepName
                ) {
                    // keep the index of this step as the current one
                    $wizardCurrentStepKey = $key;
                }
            }

            // get the index of the step to display:
            // - current (by default),
            // - next or previous relative to the current
            $wizardTargetStepKey = $wizardCurrentStepKey;
            if (isset($customClientData['goto'])) {
                switch ($customClientData['goto']) {
                case 'wizard.previous':
                    // display the step before
                    if (0 < $wizardCurrentStepKey) {
                        $wizardTargetStepKey = $wizardCurrentStepKey - 1;
                    }
                    break;
                case 'wizard.next':
                    if ($nbSteps - 1 > $wizardCurrentStepKey) {
                        $wizardTargetStepKey = $wizardCurrentStepKey + 1;
                    }
                    break;
                default:
                    break;
                }
            }

            // mark the target step as current
            $wizardSteps[$wizardTargetStepKey]['current'] = true;

            // register the current step informations as a user tag
            $wizardTags['currentWizardStepName']
                = $wizardSteps[$wizardTargetStepKey][CvdocAttributes::cv_idview];
            $wizardTags['currentWizardStepLabel']
                = $wizardSteps[$wizardTargetStepKey][CvdocAttributes::cv_lview];
            $document->addUTag(
                $document->getSystemUserId(), 'wizard', json_encode($wizardTags)
            );

            // apply the mask defined by the view/step
            $document->setMask(
                $wizardSteps[$wizardTargetStepKey][CvdocAttributes::cv_mskid]
            );

            // cache computed informations so that we do not need to compute them again
            $this->wizardInfos = [
                "steps" => $wizardSteps,
                "previousStep" => (isset($wizardSteps[$wizardTargetStepKey - 1])
                    ? $wizardSteps[$wizardTargetStepKey - 1]
                    : null),
                "currentStep" => $wizardSteps[$wizardTargetStepKey],
                "nextStep" => (isset($wizardSteps[$wizardTargetStepKey + 1])
                    ? $wizardSteps[$wizardTargetStepKey + 1]
                    : null),
                "nbSteps" => $nbSteps
            ];
        }
    }

    /**
     * @param \Doc $document the document
     *
     * @return array
     *
     * get all wizard views from the cvdoc,
     * and return them sorted by order
     */
    protected function getWizardViews(\Doc $document)
    {
        $wizardViews = [];

        $cvId = $document->getPropertyValue('cvid');
        /** @var CvdocFamily $cvDoc */
        $cvDoc = new_Doc('', $cvId, true);
        if ($cvDoc->isAlive()) {
            $cvDoc->Set($document);

            // get all the views
            $wizardViews = $cvDoc->getViews();

            // only keep wizard views
            // that the user is allowed to see
            $wizardViews = array_filter(
                $wizardViews,
                function ($view) use ($cvDoc) {
                    $idView = $view[CvdocAttributes::cv_idview];
                    $mode = $view[CvdocAttributes::cv_kview];
                    return (("WIZARD_" === substr($idView, 0, 7))
                        && ("VEDIT" === $mode)
                        && ("" === $cvDoc->control($idView)));
                }
            );

            // order the views
            usort(
                $wizardViews,
                function ($view1, $view2) {
                    if ($view1[CvdocAttributes::cv_order]
                        === $view2[CvdocAttributes::cv_order]
                    ) {
                        // if they have the same order, sort them by id
                        return ($view1[CvdocAttributes::cv_idview]
                            < $view2[CvdocAttributes::cv_idview]) ? -1 : 1;
                    }
                    return ($view1[CvdocAttributes::cv_order]
                        < $view2[CvdocAttributes::cv_order]) ? -1 : 1;
                }
            );
        }

        return $wizardViews;
    }

    /**
     * @param \Doc $contact
     * @param integer $mskId
     *
     * @return array
     *
     * return information on the attributes to display for this view
     * according to the mask:
     * - frames: a list of frames
     * - fields: a list of attributes
     * - nbFields: the number of fields
     */
    protected function getWizardViewAttributes(\Doc $contact, $mskId)
    {
        $fields = [];
        $frames = [];
        // keep the currently applied mask id
        $initMid = $contact->getPropertyValue('mid');
        if ($mskId !== $initMid) {
            $contact->setMask($mskId);
        }

        // get fields attributes
        foreach ($contact->getNormalAttributes() as $attribute) {
            // only keep visible attributes
            if ('W' === $attribute->mvisibility
                || 'O' === $attribute->mvisibility
            ) {
                // add the attribute to the list of fieldset attributes
                $fields[$attribute->ordered] = [
                    'attrid' => $attribute->id,
                    'required' => $attribute->needed,
                    'label' => $attribute->getLabel(),
                    'type' => $attribute->type,
                    'order' => $attribute->ordered,
                    'filled' => trim($contact->getRawValue($attribute->id))
                        !== ""
                ];
                // get the corresponding frame
                do {
                    $fieldset = $attribute->fieldSet;
                } while ($fieldset->type !== 'frame');
                // remember the lowest order of attributes visible in this frame
                if (!isset($frames[$fieldset->id])) {
                    $frames[$fieldset->id] = [
                        'attrid' => $fieldset->id,
                        'label' => $fieldset->getLabel(),
                        'order' => $attribute->ordered
                    ];
                } else {
                    $frames[$fieldset->id]['order'] = min(
                        $frames[$fieldset->id]['order'],
                        $attribute->ordered
                    );
                }
            }
        }

        // order fieldset attributes
        usort(
            $frames, function ($a, $b) {
            return $a['order'] - $b['order'];
        }
        );

        if ($mskId !== $initMid) {
            // restore initial mask
            $contact->setMask($initMid);
        }

        return [
            'frames' => array_values($frames),
            'fields' => array_values($fields),
            'nbFields' => count($fields)
        ];
    }
}
