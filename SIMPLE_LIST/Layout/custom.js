(function mainElement($, _)
{
    "use strict";

    var $window = $(window),
        $documentWrapper = $('.documentWrapper'),
        loadDocument = function loadDocument(fetchOptions)
        {
            //check if the widget has already been initialised
            if (_.isUndefined($documentWrapper.document("instance"))) {
                //if not, initialize it
                $documentWrapper.document(fetchOptions)
                    //and attach listeners to the newly created widget
                    .on("documentloaded", addDocumentListeners);
            } else {
                //if yes, reuse it
                $documentWrapper.document("fetchDocument", fetchOptions);
            }
        },
        addDocumentListeners = function addDocumentListeners()
        {
            // propagate that this document is opened each time it is reloaded
            $documentWrapper.document(
                "addEventListener",
                "ready",
                {
                    "name": "ready.simple_list"
                },
                function simpleList_propagateReady(/*event, documentObject*/)
                {
                    // refresh the currently selected document
                    $window.trigger('documentOpened', $documentWrapper.document("getProperties"));
                }
            );
        };

    $window.on('documentElementClicked', function onDocumentElementClicked(event, options)
    {
        loadDocument({
            "initid": options.initid,
            "viewId": "!defaultConsultation"
        });
    });
})($, _);
