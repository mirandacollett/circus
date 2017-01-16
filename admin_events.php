<!doctype html>
<html lang="en">
    <?php include $_SERVER["DOCUMENT_ROOT"].'/cookies.php'; ?>
<head>
    <?php include $_SERVER["DOCUMENT_ROOT"].'/client/head.php'; ?>
    <link href="/uploadifive.css" rel="stylesheet"/> 
</head>
<body class="hide">
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header mdl-layout__header--waterfall">
            <div class="mdl-layout__header-row">
                <a href="/index.php"><img src="/images/logo.png" alt="Logo"></a>
                <span class="mdl-layout-title mdl-layout-spacer">Manage Events</span>
                <div class="mdl-navigation-container">
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="/admin.php">Admin</a>
                    </nav>
                </div>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="mdl-grid" id="dLiveEvents"></div>
        </main>
    </div>
    <script>
        var updateHandler="updateEvent";
        $(document).ready(function () {
            getStarted();
        });
        function getStarted(){
            $('html').addClass('wait');
            $.when(applySettings())
             .then(function(mySettings){
                $.post("/circushandler.php"
                    , {Action: "getfmLogon"}
                    , function (data) {
                        if(data>""){
                            activateLogin(data,$('#dLiveEvents'));
                        }
                        else {
                    console.log('About to getfmEditEvent')
                    var eventid = getParameters('eventid',0);
                    if(eventid>0) {
                        $.post("/circushandler.php"
                            , {Action: "getfmEditEvent",id: eventid}
                            , function(card) {
                                $('#dLiveEvents').html(card);
                                componentHandler.upgradeDom();
                                $('#fmEditEvent input').each(function() {$(this).focus();});
                                $('#fmEditEvent textarea').each(function() {$(this).focus();});
                                $('#fmEditEvent #title').focus();
                                resizeTextAreas();
                                if($('html').hasClass('noEventPhotos')){
                                    $('#fmEditEvent p').append('NB Event Images are currently disabled in settings')
                                }
                            });
                    }
                    else {
                        $('#dLiveEvents').html('eventid not found - please return to Admin using the link above.');
                    }
                        }
                    });
            });
        }
        $(document).on('click','#btCancel', function(){
            window.location="admin.php";
        });
        $(document).on('submit','#fmEditEvent', function(e){
            e.PreventDefault;
            return false;
        });
        $(document).on('click','.btEditEvent', function(){
            var clickedButton = $(this);
            $.post("/circushandler.php"
                , {Action: "getfmEditEvent",id: clickedButton.data('id')}
                , function(card) {
                    clearScreen();
                    $('#dLive').html(card);
                    componentHandler.upgradeDom();
                    $('#fmEditEvent input').each(function() {$(this).focus();});
                    $('#fmEditEvent textarea').each(function() {$(this).focus();});
                    $('#fmEditEvent #title').focus();
                });
        });
        function deleteEvent(id){
            console.log('Delete event ' + id)
            $.post("/circushandler.php",{Action: "deleteEvent",id:id}
                  ,function(data) {
                      console.log(data)
                      console.log('Should be deleted - now open admin.php')
                      location.href='/admin.php';
                  });
        }
        function getID(field){
            return field.closest('form').data('id');
        }  
    </script>
</body>
</html>
