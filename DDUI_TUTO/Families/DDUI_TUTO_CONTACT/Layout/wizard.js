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
                            //TODO
                            break;
                        default:
                            return;
                    }
                    event.preventDefault();
                }
            );
            
            // update the header summary according to attributes values
            updateSummaryStatus();
        }
    );
})(window, $);
