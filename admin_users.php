<!doctype html>
<html lang="en">
    <?php include $_SERVER["DOCUMENT_ROOT"].'/cookies.php'; ?>
<head>
    <?php include $_SERVER["DOCUMENT_ROOT"].'/client/head.php'; ?>
</head>
<body class='hide'>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header mdl-layout__header--waterfall">
            <div class="mdl-layout__header-row">
                <a href="/index.php"><img src="/images/logo.png" alt="Logo"></a>
                <span class="mdl-layout-title mdl-layout-spacer">Manage Users</span>
                <div class="mdl-navigation-container">
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="/admin.php">Admin</a>
                    </nav>
                </div>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="mdl-grid" id="dContent"></div>
            <div class="mdl-grid" id="dMessage"></div>
        </main>
    </div>
    <script>var updateHandler="updateUser";</script>
    <script>
        $(document).ready(function () {
            getStarted();
        });
        $(document).on('click','.send', function(){
            var clickedButton = $(this);
            var id = clickedButton.data('id');
            var vEmail = $('#fmUserEmail_'+id).val();
            $.post("/circushandler.php"
                , {Action: "writeResetEmail",userid: id,url:window.location.host}
                , function(email) {
                    var html = writeEmail(email.message);
                    sendMail(vEmail,SiteName,html);
                    $('html').removeClass('wait');
                },'json');
        });
        function getStarted(){
            $('#dContent').html();
            $('html').addClass('wait');
            $.when(applySettings())
             .then(function(mySettings){
                $.post("/circushandler.php"
                    , {Action: "getfmLogon"}
                    , function (data) {
                        if(data>""){
                            activateLogin(data,$('#dContent'));
                        }
                        else {
                            $.post("/circushandler.php"
                                , {Action: "getUsers"}
                                , function(cards) {
                                    $('#dContent').html(cards);
                                    componentHandler.upgradeDom();
                                    $('html').removeClass('wait');
                                });
                        }
                    });
            });
        }
        function getID(field){
            return field.data('id');
        }
        function createNewUser(){
            var un=$('#fmNewUserName');
            var em=$('#fmNewUserEmail');
            if(un.val()>""&&em.val()>""&&!un.parent().hasClass('is-invalid')&&!em.parent().hasClass('is-invalid')){
                $.post("/circushandler.php"
                    , {Action:"createExpiredUser",username:un.val(),email:em.val(),url:window.location.host}
                    , function(data) {
                        if(data.faulty){
                            $('#dResponse').html(data.message);
                        }
                        else{
                            sendMail(em.val()
                                    ,'Accessing the ' + SiteName + ' website'
                                    ,'<p>Welcome to the ' + SiteName + ' website. Your username is '
                                            + un.val()
                                            + ' and please click on the link below to set a password.</p>'
                                            + writeEmail(data.message));
                        }
                    },'json');
            }
            return false;
        }
        function resendNewUser(id){
            $('html').addClass('wait');
            $.post("/circushandler.php"
                   , {Action: "getWelcomeEmail",userid: id,url:window.location.host}
                   , function(email) {
                       sendMail($('#fmUserEmail_'+id).val()
                               ,SiteName
                               ,writeEmail(email.message));
                       $('html').removeClass('wait');
                   },'json');
        }
    </script>
</body>
</html>
