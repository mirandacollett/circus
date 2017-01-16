<!doctype html>
<html lang="en">
    <?php include $_SERVER["DOCUMENT_ROOT"].'/cookies.php'; ?>
<head>
    <?php include $_SERVER["DOCUMENT_ROOT"].'/client/head.php'; ?>
    <link href="uploadifive.css" rel="stylesheet"/>
</head>
<body class='hide'>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header mdl-layout__header--waterfall">
            <div class="mdl-layout__header-row">
                <a href="/index.php"><img src="/images/logo.png" alt="Logo"></a>
                <span class="mdl-layout-title mdl-layout-spacer">Manage Links</span>
                <div class="mdl-navigation-container">
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="/admin.php">Admin</a>
                    </nav>
                </div>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="mdl-grid" id="dContent"></div>
        </main>
    </div>
    <script src="jquery.uploadifive.js"></script>
    <script>var updateHandler="updateAttachment";</script>
    <script>
        $(document).ready(function () {
            getStarted();
        });
        $(document).on('click','#btSelectFiles', function(){
            $('#file_upload').click();
            return false;
        });
        $(document).on('submit','#fmQueue', function(e){
            e.PreventDefault;
            return false;
        });
        $(document).on('click','#btUploadFile', function(){
            });
        function getStarted(){
            $('#dContent').html();
            $.when(applySettings())
             .then(function(mySettings){
                $('html').addClass('wait');
                $.post("/circushandler.php"
                    , {Action: "getfmLogon"}
                    , function (data) {
                        if(data>""){
                            activateLogin(data,$('#dContent'));
                        }
                        else {
                            $.post("/circushandler.php"
                                , {Action: "getfmAttachments",id: sessionStorage.getItem("Circus-Admin-Event")}
                                , function(card) {
                                    $('#dContent').html(card);
                                    componentHandler.upgradeDom();
                                    $('html').removeClass('wait');
                                });
                        }
                    });
            });
        }
        function goBack(){
            window.location='admin.php';
        }
        function getNewLink(id){
            $.post("/circushandler.php"
                , {Action: "getNewLink",id: id}
                , function(card) {
                        $('#dContent').html(card);
                        componentHandler.upgradeDom();
                    });
        }
        function createNewLink(id){
            if($('#new_url').val()=="") {
                return false;
            } else {
                $.post("/circushandler.php"
                    , {Action: "createNewLink",eventid: id,url:$('#new_url').val()}
                    , function() {
                        $('html').addClass('wait');
                        getStarted(id);
                    });
            }
        }
        function getUploader(id){
            $.post("/circushandler.php"
                , {Action: "getUploader",id: id}
                , function(card) {
                        $('#dContent').html(card);
                        componentHandler.upgradeDom();
                        $('#file_upload').uploadifive({
                            'onUploadComplete': function (file, data) {
                                var eventid=$('#file_upload').data('id');
                                $('html').addClass('wait');
                                $.post("/circushandler.php"
                                       , {Action: "createNewLink",eventid: eventid,url:"uploads/"+file.name}
                                       , function() {
                                            getStarted();
                                    });
                            }
                        });
                    });
        }
        function browseFiles(){
            $('#file_upload').click();
            return false;
        }
        function uploadFile(){$('#file_upload').uploadifive('upload');            }
        function getID(field){
            return field.closest('tr').data('id');
        }
        function deleteLink(id){
            $.post("/circushandler.php",{Action: "deleteLink",id:id}
                ,function() {
                      $('table tr[data-id="'+id+'"]').remove();
            });
        }
    </script>
  </body>
</html>
