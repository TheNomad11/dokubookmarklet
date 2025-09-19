jQuery(function() {
    // Add a button to the toolbar or create a custom UI element
    jQuery('<a>', {
        id: 'clippings-button',
        href: '#',
        text: 'Save Clipping',
        click: function(e) {
            e.preventDefault();

            // Get the selected text
            var selectedText = window.getSelection().toString().trim();
            if (!selectedText) {
                alert('Please select some text before saving a clipping.');
                return;
            }

            // Get the current page URL and title
            var pageUrl = encodeURIComponent(window.location.href);
            var pageTitle = encodeURIComponent(document.title);

            // Send the data to the DokuWiki plugin
            jQuery.post(
                DOKU_BASE + 'lib/exe/ajax.php',
                {
                    call: 'clippings_save',
                    text: selectedText,
                    url: pageUrl,
                    title: pageTitle
                },
                function(response) {
                    if (response.success) {
                        alert('Clipping saved successfully! You can view it [[' + response.pageId + '|here]].');
                    } else {
                        alert('Error saving clipping: ' + response.message);
                    }
                },
                'json'
            );
        }
    }).appendTo('#dokuwiki__top');
});
