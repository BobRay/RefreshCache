
/* This function is triggered when the submit button is clicked.
   It gets the pagetitle and uri from the getlist.class.php
   processor and makes an Ajax call to the refresh.cache.php
   processor to update the cache for each published resource.
*/

/* Function to update progress bar width and percent */
function progress(percent, $element) {
    percent = (percent < 5) ? 5 : percent;
    var progressBarWidth = percent * $element.width() / 100;
    $element.find('div').animate({width: progressBarWidth}, 500, 'linear').html( "&nbsp;"+ percent + "%");
    if (percent >= 100) {
        setTimeout(function () {
            $('#refreshcache_submit').removeAttr("disabled");
        }, 1000);

    }
}

$(document).ready(function (event) {
    $('#refreshcache_submit').click(function () {
        var connectorUrl = "http://localhost/addons/assets/mycomponents/refreshcache/assets/components/refreshcache/connectors/connector.php";
        var pBar = $('#progressBar');
        var text = $('.pbar_text');
        var pageTitleDiv = $('.refresh_cache_pagetitle');

        pageTitleDiv.css('visibility', 'visible');
        $('#refreshcache_submit').attr("disabled", "disabled");

        $.ajax({
            type: "get",
            data: {
                'action': 'getlist'
            },
            dataType: "json",
            cache: false,
            url: connectorUrl,
            success: function (data) {
                /* Recursive function to make Ajax calls */
                var sendToServer = function (lines, index) {
                    var length = lines.length;
                    var percent = Math.round((index/length) * 100);

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
                                text.text(data.results[index].pagetitle);
                                progress(percent, pBar);
                                if (index < lines.length) {
                                    sendToServer(lines, index + 1);
                                }
                            },
                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                console.log("Inner Ajax Error: " + errorThrown);
                            }
                        });
                    } else {
                        progress(100, pBar);
                    }
                };

                sendToServer(data.results, 0);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
               console.log("Outer Ajax Error: " + errorThrown);
            }
        });  // end outer Ajax

        return false;
    }) // end click function

}); // end onready
