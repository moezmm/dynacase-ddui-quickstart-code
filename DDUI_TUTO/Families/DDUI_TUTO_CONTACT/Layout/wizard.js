(function wizardContact(window, $){
    "use strict";

    var updateSummaryStatus = function updateSummaryStatus() {
        // iterate over each step
        $('.wizard_summary__step').each(function updateSummaryStatus_stepIteratee() {
            var $this = $(this),
                // attributes not having the "filled" class
                $unfilledStepAttributes = $('.wizard_summary__step__attribute', $this)
                    .not('.wizard_summary__step__attribute_filled');

            $this.toggleClass(
                // add class "required_unfilled" if some of the unfilled attributes have the "required" class
                'wizard_summary__step_required_unfilled',
                0 < $unfilledStepAttributes.filter('.wizard_summary__step__attribute_required').length
            ).toggleClass(
                // add class "optional_unfilled" if some of the unfilled attributes does not have the "required" class
                'wizard_summary__step_optional_unfilled',
                0 < $unfilledStepAttributes.not('.wizard_summary__step__attribute_required').length
            );
        });
    };

    /**
     * inject events once the widget is ready
     */
    window.dcp.document.documentController(
        "addEventListener",
        "ready",
        {
            "name": "addWizardEvents",
            "documentCheck": function addWizardEventsDocumentCheck(documentObject)
            {
                return documentObject.family.name === "DDUI_TUTO_CONTACT" &&
                    documentObject.renderMode === "edit";
            }
        },
        function addWizardEvents(/*event, documentObject*/)
        {
            // react to actionClick events from the menus
            this.documentController(
                "addEventListener",
                "actionClick",
                {
                    "name": "actionClick.wizard.contact",
                    "documentCheck": function actionClickWizardContactDocumentCheck(documentObject)
                    {
                        return documentObject.family.name === "DDUI_TUTO_CONTACT" &&
                            documentObject.renderMode === "edit";
                    }
                },
                function actionClickWizardContact(event, documentObject, options)
                {
                    var customServerData = this.documentController("getCustomServerData"),
                        currentWizardStepName = null;

                    if(customServerData && customServerData.wizardInfos && customServerData.wizardInfos.currentStep) {
                        currentWizardStepName = customServerData.wizardInfos.currentStep.cv_idview;
                    }

                    switch (options.eventId) {
                        case 'wizard.cancel':
                            // reinit document to server state
                            this.documentController(
                                "reinitDocument",
                                {
                                    viewId: '!defaultConsultation'
                                }
                            );
                            break;
                        case 'wizard.previous':
                        case 'wizard.next':
                            // save document and send customClientData with goto and currentStepName
                            this.documentController(
                                "saveDocument",
                                {
                                    customClientData: {
                                        goto: options.eventId,
                                        currentWizardStepName: currentWizardStepName
                                    }
                                }
                            ).then(function actionClickWizardContact_success(successInfos)
                            {
                                successInfos.element.documentController(
                                    "showMessage",
                                    "Document has been saved"
                                );
                            }, function actionClickWizardContact_error(errorInfos)
                            {
                                errorInfos.element.documentController(
                                    "showMessage",
                                    {
                                        type: 'error',
                                        message: 'an error occured: ' + errorInfos.errorMessage.contentText
                                    }
                                );
                            });
                            break;
                        case 'wizard.end':
                            this.documentController(
                                "saveDocument",
                                {
                                    customClientData: {
                                        currentWizardStepName: currentWizardStepName
                                    }
                                }
                            ).then(function wizardEnd_changeState() {
                                window.dcp.document.documentController(
                                    "changeStateDocument",
                                    {
                                        "nextState": "ctc_e2",
                                        "transition": "ctc_t_e1__e2"
                                    },
                                    {
                                        viewId: '!defaultConsultation',
                                        revision: -1
                                    }
                                );
                            });
                            break;
                        default:
                            return;
                    }
                    event.preventDefault();
                }
            );

            // react to custom:wizardgotostep events from a click on a step
            this.documentController(
                "addEventListener",
                "custom:wizardgotostep",
                {
                    "name": "gotoStep.wizard.contact",
                    "documentCheck": function gotoStepWizardContactDocumentCheck(documentObject)
                    {
                        return documentObject.family.name === "DDUI_TUTO_CONTACT" &&
                            documentObject.renderMode === "edit";
                    }
                },
                function gotoStepWizardContact(event, targetStep)
                {
                    var customServerData = this.documentController("getCustomServerData"),
                        currentWizardStepName = null;

                    // do not let the browser follow the link
                    event.preventDefault();

                    if (customServerData && customServerData.wizardInfos && customServerData.wizardInfos.currentStep) {
                        currentWizardStepName = customServerData.wizardInfos.currentStep.cv_idview;
                    }

                    // reload document from server
                    // add customClientData indicating the target step
                    this.documentController(
                        "reinitDocument",
                        {
                            customClientData: {
                                goto: 'wizard.targetStep',
                                currentWizardStepName: currentWizardStepName,
                                targetStep: targetStep
                            }
                        }
                    );
                }
            );

            // react to change events (when an attribute value is updated)
            this.documentController(
                "addEventListener",
                "change",
                {
                    "name": "change.wizard.contact",
                    "documentCheck": function changeWizardContactDocumentCheck(documentObject)
                    {
                        return documentObject.family.name === "DDUI_TUTO_CONTACT" &&
                            documentObject.renderMode === "edit";
                    }
                },
                function changeWizardContact(event, documentObject, attributeObject, values)
                {
                    // find the summary field corresponding to this attribute
                    $('.wizard_summary__step__attribute[data-attrid=' + attributeObject.id + ']')
                        .toggleClass(
                            // add the class "filled" if the value is not empty
                            'wizard_summary__step__attribute_filled',
                            values.current.value !== ''
                        );

                    // update the header summary according to new attributes values
                    updateSummaryStatus();
                }
            );

            // trigger a custom:wizardgotostep when the user click on a step
            $('.wizard_summary').on('click', '.wizard_summary__step', function stepLabelClick()
            {
                window.dcp.document.documentController(
                    "triggerEvent",
                    "custom:wizardgotostep",
                    $(this).data('viewid')
                );
            });

            // update the header summary according to attributes values
            updateSummaryStatus();
        }
    );
})(window, $);
