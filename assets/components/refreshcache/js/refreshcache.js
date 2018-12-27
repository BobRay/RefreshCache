
/* This function is triggered when the start button is clicked. 
   Placeholders below are set in the snippet properties
*/

window.jsonParse=function(){var r="(?:-?\\b(?:0|[1-9][0-9]*)(?:\\.[0-9]+)?(?:[eE][+-]?[0-9]+)?\\b)",k='(?:[^\\0-\\x08\\x0a-\\x1f"\\\\]|\\\\(?:["/\\\\bfnrt]|u[0-9A-Fa-f]{4}))';k='(?:"'+k+'*")';var s=new RegExp("(?:false|true|null|[\\{\\}\\[\\]]|"+r+"|"+k+")","g"),t=new RegExp("\\\\(?:([^u])|u(.{4}))","g"),u={'"':'"',"/":"/","\\":"\\",b:"\u0008",f:"\u000c",n:"\n",r:"\r",t:"\t"};function v(h,j,e){return j?u[j]:String.fromCharCode(parseInt(e,16))}var w=new String(""),x=Object.hasOwnProperty;return function(h,
j){h=h.match(s);var e,c=h[0],l=false;if("{"===c)e={};else if("["===c)e=[];else{e=[];l=true}for(var b,d=[e],m=1-l,y=h.length;m<y;++m){c=h[m];var a;switch(c.charCodeAt(0)){default:a=d[0];a[b||a.length]=+c;b=void 0;break;case 34:c=c.substring(1,c.length-1);if(c.indexOf("\\")!==-1)c=c.replace(t,v);a=d[0];if(!b)if(a instanceof Array)b=a.length;else{b=c||w;break}a[b]=c;b=void 0;break;case 91:a=d[0];d.unshift(a[b||a.length]=[]);b=void 0;break;case 93:d.shift();break;case 102:a=d[0];a[b||a.length]=false;
b=void 0;break;case 110:a=d[0];a[b||a.length]=null;b=void 0;break;case 116:a=d[0];a[b||a.length]=true;b=void 0;break;case 123:a=d[0];d.unshift(a[b||a.length]={});b=void 0;break;case 125:d.shift();break}}if(l){if(d.length!==1)throw new Error;e=e[0]}else if(d.length)throw new Error;if(j){var p=function(n,o){var f=n[o];if(f&&typeof f==="object"){var i=null;for(var g in f)if(x.call(f,g)&&f!==n){var q=p(f,g);if(q!==void 0)f[g]=q;else{i||(i=[]);i.push(g)}}if(i)for(g=i.length;--g>=0;)delete f[i[g]]}return j.call(n,
o,f)};e=p({"":e},"")}return e}}();

$(document).ready(function (event) {
    $('#refreshcache_submit').click(function () {
      // alert("Submit Clicked");
        /* If no action selected, submit (reload) form */
       /* if (($('#nf_notify').prop('checked') == false)
                && ($('#nf_send_test_email').prop('checked') == false)
                && ($('#nf_send_tweet').prop('checked') == false)) {
           $('#nf_form').submit();
            return true;
        }

        $('#nf_results').find('span').remove();
        $('#nf_results').find('br').remove();
        $('#nf_results').hide();*/


        var connectorUrl = "http://localhost/addons/assets/mycomponents/refreshcache/assets/components/refreshcache/connectors/connector.php";


        /* One or more actions selected */

        /* IF send_tweet is checked, call sendTweet processor */
        if (true) {
            $.ajax({
                type: "get",
                data: {
                    'action': 'getlist'
                },
                dataType: "json",
                cache: false,
                url: connectorUrl,
                success: function (data) {
                  console.log(data);
                  //alert("To Here");
                  // var parsedData = JSON.parse(data);
                  //alert("success: " + data['success']);
                  //alert("Parsed");
                  /* if (parsedData['success'] == true) {
                       alert('parse OK');
                   }*/
                  console.log(data.results);
                   //$("<ul>").appendTo(".refresh_cache_inner");
                   $.each(data.results, function (i, item) {
                        console.log(data.results[i].pagetitle);
                       console.log(data.results[i].uri);
                       $("<span class='rc_pagetitle' style='display:block'>" + data.results[i].pagetitle +
                           "</span>").appendTo(".refresh_cache_inner");

                    });


                   /*if (data['errors'] !== null) {
                       data['errors'].forEach(function (err, i) {
                           console.log("Error: " + err);
                           $('<span class="nf_error">' + err + '</span><br />').appendTo("#nf_results")


                       });
                   }
                   if (data['successMessages'] !== null) {
                       data['successMessages'].forEach(function (msg, i) {
                           console.log("Success: " + msg);
                           $('<span class="nf_success">' + msg + '</span><br />').appendTo("#nf_results")
                       });
                   }
                   $("#nf_results").slideDown("slow");
                    var $target = $('html,body');
                    $target.animate({scrollTop: $target.height()}, 1000);*/
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                   console.log("Submit Error: " + errorThrown);
                }


            });
        }

        /* Send single Email */
        if ($("#nf_send_test_email").prop('checked') == true) {
            $.ajax({
                type: "POST",
                data: {
                   'action': 'mgr/nfsendemail',
                   'send_bulk': false,
                   'single_id': $("#nf_test_email_address").val(),
                   'email_subject': $("#nf_email_subject").val(),
                   'email_text': $("#nf_email_text").val(),
                   'single': true
                },
                dataType: "json",
                cache: false,
                url: connectorUrl,
                success: function (data) {
                    if (data['errors'] !== null) {
                       data['errors'].forEach(function (err, i) {
                           $('<span class="nf_error">' + err + '</span><br />').appendTo("#nf_results")
                       });
                    }

                    if (data['successMessages'] !== null) {
                        data['successMessages'].forEach(function (msg, i) {
                            $('<span class="nf_success">' + msg + '</span><br />').appendTo("#nf_results")
                        });
                    }

                   $("#nf_results").slideDown("slow");
                   var $target = $('html,body');
                   $target.animate({scrollTop: $target.height()}, 1000);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                   console.log("Send Test Email Error: " + errorThrown);
                }
                });
        }

            /* Send bulk email (with progress bar)
             *
             * Start the notify-process snippet, ignore the return value
             * this needs to be at the top so the process snippet
             * can write to the file and this ajax call can complete
             * before the second ajax call tries to read the file
             */
        if ($("#nf_notify").prop('checked') == true) {

            $.ajax({
                type: "POST",
                data: {
                    'action': 'mgr/nfsendemail',
                    'send_bulk' : true,
                    'email_subject': $("#nf_email_subject").val(),
                    'email_text': $("#nf_email_text").val(),
                    'groups': $("#nf_groups").val(),
                    'tags': $("#nf_tags").val(),
                    'require_all_tags': $("#nf_require_all_tags").prop('checked') == true,
                    'page_alias': $("#nf_page_alias").val(),
                    'single': false
                },
                dataType: "json",
                cache: false,
                url: connectorUrl,
                success: function(data) {
                    if (data['errors'] !== null) {
                       data['errors'].forEach(function (err, i) {
                            console.log("Error: " + err);
                           $('<span class="nf_error">' + err + '</span><br />').appendTo("#nf_results")
                        });
                    }
                    if (data['successMessages'] !== null) {
                       data['successMessages'].forEach(function (msg, i) {
                           console.log("Success: " + msg);
                           $('<span class="nf_success">' + msg + '</span><br />').appendTo("#nf_results")
                       });
                    }

                   $("#nf_results").slideDown("slow");
                    var $target = $('html,body');
                    $target.animate({scrollTop: $target.height()}, 1000);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                   console.log("Batch send Error: " + errorThrown);
                }
            });


            var url = "[[+nf_status_url]]";

            $("#pb_progressbar").progressbar({
                value: 0,
                max: 100,
                complete: function(event, ui) {


                }
            });

            $("#pb_button").slideUp("slow");
            $("#pb_progressOuter").slideDown("slow");
            var $target = $('html,body');
            $target.animate({scrollTop: $target.height()}, 1000);




            /* set the timer that checks the status.php file for progress reports */
            var timer = setInterval(function(){
                $.ajax({
                    type: "POST",
                    url: url,
                    cache: false,
                    data: {},
                    dataType:"json",
                    //crossDomain: true,

                    /* update the progress bar and text messages if the file changes */
                    success: function(data){
                        if (data.percent >= 100) {
                            clearInterval(timer);
                            $("#pb_progressbar").progressbar("value", 100);
                            $("#pb_progressOuter").slideUp("0");
                            $("#pb_button").slideDown("slow");
                            /* Clear for future runs */
                            data.percent = 100;
                            data.text1="Finished";
                            data.text2="";
                            $("#pb_progressbar").progressbar("value", 0);

                        } else {
                            $("#pb_progressbar").progressbar("value",data.percent);
                            $("#pb_percent").text(data.percent);
                            $("#pb_text2").text(data.text2);
                            $("#pb_text1").text(data.text1);
                        }

                    },
                error : function (x, d, e) {
                      if (x.status == -5) {
                          alert("You are offline!! Please Check Your Network.");
                      } else {
                          if (x.status == 404) {
                             /* alert("Requested URL not found"); */
                          } else {
                              if (x.status == 500) {
                                  alert("Internal Server Error.");
                              } else {
                                  if (e == "parsererror") {
                                      alert("Error: Parsing JSON Request failed.");
                                  } else {
                                      if (e == "timeout") {
                                          alert("Request Time out.");
                                      } else {
                                          console.log("Unknown Error: " + x.responseText);
                                      }
                                  }
                              }
                          }
                      }
                  }
               });
           },[[+nf_set_interval]]);
        }
        return false;
        })

});
