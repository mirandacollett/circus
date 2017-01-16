<!doctype html>
<html lang="en">
    <?php include $_SERVER["DOCUMENT_ROOT"].'/cookies.php'; ?>
<head>
    <?php include $_SERVER["DOCUMENT_ROOT"].'/client/head.php'; ?>
    <title>Admin</title>
    <meta name="description" content="For our site administrators">
    <style>
        .mdl-layout{overflow: auto;}
    </style>
</head>
<body class='hide'>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header mdl-layout__header--waterfall">
            <div class="mdl-layout__header-row">
                <a href="/index.php"><img src="/images/logo.png" alt="Logo"></a>
                <span class="mdl-layout-title mdl-layout-spacer">The Admin Page</span>
                <div class="mdl-navigation-container">
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="/index.php">Home</a>
                    </nav>
                </div>
            </div>
        </header>
        <div class="mdl-layout__content">
            <div class="hide">
                <div class="mdl-typography--headline">Current Events</div>
                <div class="mdl-grid" id="gLiveEvents"></div>
            </div>
            <div class="hide">
                <div class="mdl-typography--headline">Future Hidden Events</div>
                <div class="mdl-grid" id="gFutureEvents"></div>
            </div>
            <div class="hide">
                <div class="mdl-typography--headline">Past Hidden Events</div>
                <div class="mdl-grid" id="gPastEvents"></div>
            </div>
            <div class="hide">
                <div class="mdl-typography--headline">More Admin</div>
                <div class="mdl-grid" id="gMoreAdmin"></div>
            </div>
        </div>
    </div>
    <script>
        var updateHandler='';
        $(document).ready(function () {
            getStarted();
        });
        $(document).on('click','.btEditEvent', function(){
            var clickedButton = $(this);
            window.location.href='admin_events.php?eventid='+clickedButton.data('id');
        });
        $(document).on('click','.btGetAttachments', function(){
            var clickedButton = $(this);
            sessionStorage.setItem("Circus-Admin-Event",clickedButton.data('id'));
            window.location.href='admin_links.php';
        });
        $(document).on('click','#btCreateEvent', function(){
            $.post("/circushandler.php"
                , {Action: "createEvent"}
                , function () {getStarted();});
        });
        function getStarted(){
            $('html').addClass('wait');
            $.when(applySettings())
             .then(function(mySettings){
                getAdminCards();
            });
        }
        function clearScreen(){
            $('.mdl-grid').html('')
                          .parent().addClass("hide");
        }
        function getID(){
            return 0;
        }
        function reportHeight(){
            console.log('Window height is '+$(window).height());
            console.log('Document height is '+$(document).height());
            console.log('body height is '+$('body').height());
            console.log('mdl-layout__container height is '+$('.mdl-layout__container').height());
            console.log('mdl-layout height is '+$('.mdl-layout').height());
            console.log('mdl-layout__content height is '+$('.mdl-layout__content').height());
        };
    </script>
  </body>
</html>
