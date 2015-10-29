<!DOCTYPE html>
<?php
    session_start();
?>
<html lang="en">
    <head>
        
    
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/styles/default.min.css">
        <link rel="stylesheet" href="css/prism.css">
        
        <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
        <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/prism.js"></script>
        
        <style>
            body {
                padding-top: 40px;
                padding-bottom: 50px;
            }
            .lpa-sdk {
                padding: 40px 15px;
                text-align: center;
            }
            .input-group {
                margin-bottom:10px;
            }
            #go-home {
                cursor:pointer;
            }
            pre code {
                overflow:scroll;
                word-wrap:normal;
                white-space:pre;
            }
            .jumbotroncolor {
                background:rgba(0, 153, 153, 0.3);
            }
            .jumbotroncodecolor {
                background:rgba(255, 204, 153, 0.4);
            }
        </style>

        <script type='text/javascript'>
        $(document).ready(function() {
            $('.start-over').on('click', function() {
                amazon.Login.logout();
                document.cookie = "amazon_Login_accessToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
                window.location = 'index.php';
            });
            $('#place-order').on('click', function() {
                $(this).hide();
                $('#ajax-loader').show();
            });
        });
        </script>
  
    
    <script type='text/javascript'>
    $.post("Apicalls/ConfirmAndAuthorize.php", {}).done(function(data) {
        var obj = jQuery.parseJSON(data);
        $.each(obj, function(key, value) {
            if (key == 'confirm') {
                var str = JSON.stringify(value, null, 2);
                $("#confirm").html(str);
            } else if (key == 'authorize') {
                var str = JSON.stringify(value, null, 2);
                alert(str);
                $("#authorize").html(str);
            }
        });
    });
</script>
    
    </head>
    <body>
        <div class="container">

            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand start-over" href="#">Pay with Amazon PHP SDK Simple Checkout</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li><a class="start-over" href="#">Start Over</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="jumbotron jumbotroncolor" style="padding-top:25px;" id="api-content">
                <div id="section-content">
                
    <h2>Confirm</h2>
    <p>Congratulations! You are now a proud owner of the official Pay with Amazon 
    PHP Software Development Kit!</p>
    <p>At this point we will make the <em>Confirm</em> API call to confirm the order 
    reference and a subsequesnt <em>Authorize</em> and <em>Capture</em> API call. 
    If you used a test account associated with your email address you should receive 
    an email.</p>

                </div>
            </div>
            <div class="jumbotron jumbotroncodecolor" style="padding-top:25px;" id="api-calls">
            <h3>Code</h3>
            

<p>The <em>Confirm</em> API call does not return any special values. If it were 
unsuccessful you would see an error response.</p>
<pre id="confirm"><code class="json"></code></pre>

<p>The <em>Authorize</em> API call will authorize the order reference. Instead 
of making a separate <em>Capture</em> API call we can set the <strong>CaptureNow</strong> 
parameter to <strong>True</strong> and the funds will be captured in the same call.</p>
<pre id="authorize"><div class="text-center"></div></pre>

            </div>            
        
        </div>
    </body>
</html>