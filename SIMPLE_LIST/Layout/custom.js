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

            // propagate that this document has changed
            $documentWrapper.document(
                "addEventListener",
                "change",
                {
                    "name": "change.simple_list"
                },
                function simpleList_propagateChange(event, documentObject)
                {
                    $window.trigger('documentChanged', documentObject);
                }
            );

            // propagate that this document has been saved
            $documentWrapper.document(
                "addEventListener",
                "afterSave",
                {
                    "name": "afterSave.simple_list"
                },
                function simpleList_propagateAfterSave(event, currentDocumentObject, previousDocumentObject)
                {
                    if(previousDocumentObject.initid === 0) {
                        // if previous document had no initid, it is a creation
                        $window.trigger('documentCreated', currentDocumentObject);
                    } else {
                        // otherwise, it is a simple save
                        $window.trigger('documentSaved', currentDocumentObject);
                    }
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

    $window.on('buttonCreateClicked', function onButtonCreateClicked(event, options)
    {
        var fetchOptions = {
            "initid": options.famid,
            "viewId": "!defaultCreation"
        };
        loadDocument(fetchOptions);
    });
})($, _);
