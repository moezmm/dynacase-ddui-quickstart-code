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
            // update the header summary according to attributes values
            updateSummaryStatus();
        }
    );
})(window, $);