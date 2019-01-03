
/* This function is triggered when the submit button is clicked.
   It gets the pagetitle and uri from the getlist.class.php
   processor and makes an Ajax call to the refresh.cache.php
   processor to update the cache for each published resource.
*/

/* Function to update progress bar width and percent */
function progress(percent, $element, index) {
    var pageTitleDiv = $('#refresh_cache_pagetitle');
    var progressBarWidth = percent * $element.width() / 100;
    if (percent >= 8) {
        $element.find('div').animate({width: progressBarWidth}, 300, 'linear').html("&nbsp;" + percent + "%");
    } else {
        $element.find('div').animate({width: progressBarWidth}, 300, 'linear');
    }



    if (percent >= 100) {
        setTimeout(function () {
            pageTitleDiv.fadeOut('slow',function () {
                $(this).text(_('rc_refreshed') + ' ' + index + ' ' + _('rc_resources'))
            }).fadeIn('slow');
        }, 1000);

    }
}

$(document).ready(function (event) {
    $('#refreshcache_submit').click(function () {

        var connectorUrl = "http://localhost/addons/assets/mycomponents/refreshcache/assets/components/refreshcache/connectors/connector.php";
        var pBar = $('#progressBar');
        var text = $('.pbar_text');
        var pageTitleDiv = $('.refresh_cache_pagetitle');

        text.text('Getting Data');

        $("#refreshcache_submit").fadeOut("slow", function () {
            $('#refreshcache_results').fadeIn('slow');
            $('#refresh_cache_pagetitle').fadeIn('slow');
        });

        $.ajax({
            type: "get",
            data: {
                'action': 'getlist'
            },
            dataType: "json",
            cache: false,
            url: connectorUrl,
            success: function (data) {
                var lines = data.results;
                var length = lines.length;
                var percent = 0;
                var index = 0;
                /* Recursive function to make Ajax calls */
                var sendToServer = function (index) {
                    percent = Math.round((index/length) * 100);

                    /* Recurse if not at end of data */
                    if (index < length) {

                        var item = lines[index];
                        $.ajax({
                            type: 'GET',
                            url: connectorUrl,
                            dataType: 'json',
                            data: {
                                'uri' : item.uri,
                                'action': "refresh"
                            },
                            /* Update progress bar and text */
                            success: function (msg) {
                                text.text(lines[index].pagetitle);
                                progress(percent, pBar, index);
                                if (index < lines.length +1 ) {
                                        sendToServer(index + 1);
                                }
                            },
                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                console.log("Inner Ajax Error: " + errorThrown);
                            }
                        });
                    } else {
                        progress(100, pBar, index);
                    }
                };
                sendToServer(0);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
               console.log("Outer Ajax Error: " + errorThrown);
            }
        });  // end outer Ajax
        return false;
    }) // end click function

}); // end onready
