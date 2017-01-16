<!doctype html>
<html lang="en">
    <?php include $_SERVER["DOCUMENT_ROOT"].'/cookies.php'; ?>
<head>
    <?php include $_SERVER["DOCUMENT_ROOT"].'/client/head.php';?>
</head>
<body class='hide'>
    <script type='text/javascript' src='/cloudinary/jquery.ui.widget.js'></script>
    <script type='text/javascript' src='/cloudinary/jquery.iframe-transport.js'></script>
    <script type='text/javascript' src='/cloudinary/jquery.fileupload.js'></script>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header mdl-layout__header--waterfall">
            <div class="mdl-layout__header-row">
                <a href="/index.php"><img src="/images/logo.png" alt="Logo"></a>
                <span class="mdl-layout-title mdl-layout-spacer">Manage Photos</span>
                <div class="mdl-navigation-container">
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="/admin.php">Admin</a>
                    </nav>
                </div>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="mdl-grid" id="dContent"></div>
            <div class="mdl-grid">
                <div class="mdl-cell mdl-cell--12-col mdl-shadow--3dp" id='direct_upload'>
                    <div class="mdl-card__title">Photo upload to the cloud</div>
                    <div class="mdl-card__supporting-text">
                        <form>
                            <input type="file" name="file" class="cloudinary_fileupload">
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>var updateHandler="updatePhoto";</script>
    <script>
        $(document).ready(function () {            
            getStarted();
        });
        function getStarted(){
            $('#dContent').html('');
            $('html').addClass('wait');
            $.when(applySettings())
            .then(function(mySettings){
                prepareUpload(mySettings);
                $.when(getLogOn())
                .then(function(data){
                    $.post("/circushandler.php"
                        , {Action: "getPhotos"}
                        , function(data) {
                            $('#dContent').html(data.cards);
                            $('.fSelect').addClass('fSelect2').removeClass('fSelect');
                            componentHandler.upgradeDom();
                            $.cloudinary.responsive();
                            $('html').removeClass('wait noEventPhotos');
                    },'json');
                },function(data){
                    activateLogin(data,$('#dContent'));
                });
            });
        }
        function prepareUpload(mySettings){
            var myCloudName = getFromArray(mySettings,'Cloudinary Cloud Name','None');
            var myPreset = getFromArray(mySettings,'Cloudinary Preset','None');
            $('.cloudinary_fileupload').unsigned_cloudinary_upload(
                myPreset, 
                { cloud_name: myCloudName, tags: 'WebUpload' }, 
                { multiple: true }
               ).on('fileuploadfail', function (e, data) {
                    $('#status_update').html('FAILED');
                    console.log(data);
                })
               .on('cloudinarydone', function (e, data) {
                    $('#status_update').html('Done - do not forget to parse the response to save the filename etc locally');
                    $.post("/circushandler.php"
                    , {Action: "createNewPhoto"
                      ,url:'https://res.cloudinary.com/' + myCloudName + '/image/upload/w_auto/'+data.result.path
                      ,title:data.result.original_filename}
                    , function () {
                        getStarted();
                        $('html').removeClass('wait');
                    });
                });
        }
        function getID(field){
            return field.data('id');
        }
        $(document).on('click','.deletePhoto',function(){
            var photoID=$(this).attr('data-id');
            var thisCard = $(this).parent().parent();
            $.post("/circushandler.php"
                , {Action: "deletePhoto",id:photoID}
                , function(data) {
                    if(data.deleted){
                        thisCard.remove();
                    }
                    else {
                        alert(data.message);
                    }
            },'json');
        });
    </script>
  </body>
</html>
