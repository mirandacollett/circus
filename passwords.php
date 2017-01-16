<!doctype html>
<html lang="en">
    <?php include $_SERVER["DOCUMENT_ROOT"].'/cookies.php'; ?>
<head>
    <?php include $_SERVER["DOCUMENT_ROOT"].'/client/head.php'; ?>
</head>
<body class='hide'>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header">
            <div class="mdl-layout__header-row">
                <a href="/index.php"><img src="/images/logo.png" alt="Logo"></a>
                <span class="mdl-layout-title mdl-layout-spacer">Reset Password</span>
                <div class="mdl-navigation-container">
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="/index.php">Home</a>
                    </nav>
                </div>
            </div>
        </header>
        <div class="mdl-layout__content">
            <div class="l-card-container mdl-grid" id="dContent"></div>
        </div>
    </div>
    <script>var updateHandler="updateUser";</script>
    <script>
        $(document).ready(function () {            
            getStarted();
            $(document).on('submit','form', function(e){
                e.PreventDefault;
                return false;
            });
        });
        function getStarted(){
            $('html').addClass('wait');
            $.when(applySettings())
             .done(function(mySettings){
                $.post("/circushandler.php"
                    , {Action: "getfmReset",id: getParameter('id',0)}
                    , function(card) {
                        $('#dContent').html(card);
                        componentHandler.upgradeDom();
                        $('html').removeClass('wait');
                        $('#iPassword').focus();
                    });
            })
        }
        function getID(field){
            return field.data('id');
        }
        function validate(userID){
            var p1=$('#iPassword').val();
            var p2=$('#iPassword2').val();
            if(p1==""){
                $('#iPassword').attr({required: true});
                $('#iPassword').parent().addClass('is-invalid');
                $('#iError').html('Please enter your new password');
                $('#iPassword').focus();
            } 
            else if (p1.length<8){
                $('#iPassword').parent().addClass('is-invalid');
                $('#iError').html('At least 8 characters please');
                $('#iPassword').focus();
            } 
            else if(p2==""){
                $('#iPassword2').attr({required: true});
                $('#iPassword2').parent().addClass('is-invalid');
                $('#iError2').html('Please re-enter your new password');
                $('#iPassword2').focus();
            }
            else if(p1>""&&p2>""&&p1!==p2){
                $('#iPassword2').parent().addClass('is-invalid');
                $('#iPassword2').val('');
                $('#iError2').html('Your new passwords do not match');
                $('#iPassword2').focus();
            } 
            else {
                $.post("/circushandler.php"
                    , {Action: "resetPassword",pw: p1 ,id: userID}
                    , function() {window.location="/index.php";});
            }
            return false;
        }
    </script>
  </body>
</html>
