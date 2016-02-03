(function mainElement($, _) {

    "use strict";

    var getDocumentElementCard = _.template(
            '<a class="documentElement list-group-item"' +
            '   href="?app=DOCUMENT&id=<% print(encodeURIComponent( initid )) %>"' +
            '   data-initid="<%= initid %>">' +
            '    <div class="documentElement__title"><%- title %></div>' +
            '</a>'
        ),
        getListElements = function getListElements($listPartElements, callback)
        {
            $.get(
                "api/v1/searches/" + window.encodeURIComponent(window.conf.searchName) + "/documents/"
            ).always(function initList(response)
            {
                var listElements = "";

                if (!response.success || !response.data || !response.data.documents || !$.isArray(response.data.documents)) {
                    alert("Unable to init the list ! Please reload the page");
                    return;
                }

                $.each(response.data.documents, function documentsIterator()
                {
                    listElements += getDocumentElementCard(this.properties);
                });

                callback(listElements);
            });
        };

    $(window).ready(function windowReady() {

        //Application not configured
        if (!window.conf.searchName) {
            $(".notConfigurated").show();
            $(".mainWrapper").hide();
            return;
        }

        var $window = $(window),
            $listPartElements = $(".listPart__elements");

        // initialize the list
        getListElements($listPartElements, function getListElementsCallback(listElements)
        {
            $listPartElements
                .empty()
                .append(listElements);
        });

        $window.on('documentOpened', function documentOpened(event, documentObject)
        {
            console.log("documentOpened", documentObject);

            // documentElement for this document, updated with fres info from the server
            var $updatedDocumentElementcard = $(getDocumentElementCard(documentObject));

            // mark as opened
            $updatedDocumentElementcard.addClass("active");

            $listPartElements
                // ensure all active flags are removed
                .find(".documentElement")
                .removeClass("active");
            $listPartElements
                // get the documentElement object for this document
                .find('[data-initid=' + documentObject.initid + ']')
                // replace with updated document infos from server
                .replaceWith($updatedDocumentElementcard);
        });

        $window.on('documentChanged', function documentChanged(event, documentObject)
        {
            console.log("documentChanged", documentObject);

            var $oldDocumentCard = $listPartElements
                    .find('[data-initid=' + documentObject.initid + ']'),
                $newDocumentCard = $(getDocumentElementCard(documentObject))
                    .toggleClass('active', $oldDocumentCard.hasClass('active'))
                    .toggleClass('modified', documentObject.isModified);

                // replace with updated document infos from the document itself
                $oldDocumentCard.replaceWith($newDocumentCard);
        });

        $window.on('documentSaved', function documentSaved(event, documentObject)
        {
            console.log("documentSaved", documentObject);

            $listPartElements
                // get the documentElement object for this document
                .find('[data-initid=' + documentObject.initid + ']')
                // replace with updated document infos from the document itself
                .replaceWith(getDocumentElementCard(documentObject));
        });

        $window.on('documentCreated', function documentCreated(event, documentObject)
        {
            console.log("documentCreated", documentObject);

            //we reload the entire list so that the document is in the good order
            getListElements($listPartElements, function getListElementsCallback(listElements)
            {
                $listPartElements
                    // remove all previous cards
                    .empty()
                    // and add the new cards
                    .append(listElements);
                $listPartElements
                    // get the documentElement object for this document
                    .find('[data-initid=' + documentObject.initid + ']')
                    // mark as opened
                    .addClass("active");
            });
        });

        $window.on('documentDeleted', function documentDeleted(event, documentObject)
        {
            console.log("documentDeleted", documentObject);

            $listPartElements
                // ensure all active flags are removed
                .find(".documentElement")
                .removeClass("active");
            $listPartElements
                // get the documentElement object for this document
                .find('[data-initid=' + documentObject.initid + ']')
                // remove it
                .remove();
        });

        $window.on('documentRestored', function documentRestored(event, documentObject)
        {
            console.log("documentRestored", documentObject);

            //we reload the entire list so that the document is in the good order
            getListElements($listPartElements, function getListElementsCallback(listElements)
            {
                $listPartElements
                // remove all previous cards
                    .empty()
                    // and add the new cards
                    .append(listElements);
                $listPartElements
                // get the documentElement object for this document
                    .find('[data-initid=' + documentObject.initid + ']')
                    // mark as opened
                    .addClass("active");
            });
        });

        $listPartElements.on("click", ".documentElement", function clickOnDocumentElement(event) {
            event.preventDefault();
            $window.trigger('documentElementClicked', {
                "initid": $(this).data("initid")
            });
        });

        $('.button_create').on("click", function clickOnButtonCreate(event) {
            event.preventDefault();
            $window.trigger('buttonCreateClicked', {
                "famid": $(this).data("famid")
            });
        });
    });

})($, _);