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
        <script type="text/javascript" src="js/jquery.knob.min.js"></script>
        <script type="text/javascript" src="js/notify.min.js"></script>
        
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
                background:rgba(153, 255, 204, 0.4);
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
        });
        </script>
        
    </head>
    <body>
        <div class="container">

            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand start-over" href="#">Pay with Amazon PHP SDK Simple Recurring Payment</a>
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
                
    <h2>Recurring Simulation</h2>
    <p style="margin-top:40px;">This will make authorizations on the billing agreement every <strong>10</strong> seconds. We will 
    authorize 1.99 to simulate the recurring charge.</p>
    <div class="text-center" style="margin-top:10px;">
        <button id="pause-cycle" class="btn btn-danger">Pause</button>
    </div>
    <div class="text-center" style="height:140px; padding-top:20px;">
        <input type="text" data-width="100" value="50" class="dial" data-fgColor="#222222" data-bgColor="#fafafa" style="padding:0; margin:0;" />
    </div>
    <div id="authorize-response"><pre><code>Waiting for authorization...</code></pre></div>
    <div id="confirm-response"><pre><code></code></pre></div>
    <script type="text/javascript">
       function confirm()
        {
            $.post("Apicalls/ConfirmAndAuthorize.php", {
                    action:'confirm'
                }).done(function (data) {
                    $("#confirm-response").html('<pre><code>'+data+'</pre></code>');

                });
        }
        confirm();
    </script>
    <script type="text/javascript">
        var countdown = 0;
        $(function() {
            $('.dial').knob({
                'min': 0,
                'max': 10,
                'val': 0,
                'readOnly': true
            });
        });
        tick();
        function tick() {
            $('.dial').val(countdown).trigger('change');
            timeout = setTimeout('tick()', 1000);
            countdown++;
            if(countdown > 10) {
                $.notify('Authorizing', {
                    className: 'success',
                    autoHide: true,
                    globalPosition: 'top center',
                    autoHideDelay: 3000
                });
                clearTimeout(timeout);
                $.post("Apicalls/ConfirmAndAuthorize.php", {
                    action:'authorize',
                }).done(function (data) {
                    $("#authorize-response").html('<pre><code>'+data+'</pre></code>');
                    timeout = setTimeout('tick()', 1000);

                });
                countdown = 0;
            }
        }
        $('#pause-cycle').on('click', function () {
            if($(this).hasClass('btn-danger')) {
                $(this).removeClass('btn-danger').addClass('btn-success');
                $(this).text('Continue');
                clearTimeout(timeout);
            } else {
                $(this).removeClass('btn-success').addClass('btn-danger');
                $(this).text('Pause');
                timeout = setTimeout('tick()', 1000);
            }
        });
    </script>

                </div>
            </div>           
        
        </div>
    </body>
</html>