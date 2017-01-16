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
                <span class="mdl-layout-title mdl-layout-spacer">Manage News Stories</span>
                <div class="mdl-navigation-container">
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="/admin.php">Admin</a>
                    </nav>
                </div>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="mdl-typography--headline" id="tLive">News Stories</div>
            <div class="mdl-typography--subhead mdl-color-text--primary">NB - do check the appearance on the Admin page as the text fields displayed on this page are a little different to those on a non-editable card.</div>
            <div class="mdl-grid" id="dLive"></div>
            <div class="mdl-typography--headline hide" id="tHidden">Hidden Stories</div>
            <div class="mdl-grid" id="dHidden"></div>
            <div class="mdl-typography--headline">New Story</div>
            <div class="mdl-grid" id="dNew"></div>
        </main>
    </div>
    <script src="//cdn.ckeditor.com/4.5.1/standard/ckeditor.js"></script>
    <script>
        var updateHandler="updateNews";
        $(document).ready(function () {
            getStarted();
        });
        $(document).on('change','input',function(e){
            var clickedItem=$(this);
            var clickedItemID = clickedItem.attr('id');
            var cell = $(this).closest('div.mdl-cell');
            if(clickedItemID.substring(0,12)=='fmNews_hide_') {
                if($(this).is(':checked')) {
                    cell.detach();
                    cell.appendTo($('#dHidden'));
                    $('#tHidden').removeClass('hide');
                }
                else{
                    cell.detach();
                    cell.appendTo($('#dLive'));
                    if($('#dHidden > div').length==0)$('#tHidden').addClass('hide');
                }
            }
            if(clickedItemID.substring(0,11)=='fmNews_seq_') {
                cell.css('order',$(this).val());
            }
            if(clickedItemID.substring(0,12)=='fmNews_wide_') {
                if($(this).is(':checked')) {
                    cell.removeClass('mdl-cell--3-col').addClass('mdl-cell--6-col');
                }
                else {
                    cell.removeClass('mdl-cell--6-col').addClass('mdl-cell--3-col');
                }
            }
        });
        $(document).on('change','select',function(e){
            var field = $(this);
            var val = field.val();
            if(val==0){
                $('textarea').parent().removeClass('hide');
                resizeTextAreas();
            }
            else{
                $('textarea').parent().addClass('hide');                
            }
        });
        function deleteNews(id){
            $.post("/circushandler.php",{Action: "deleteNews",id:id}
                  ,function() {
                      $('#fmNews_title_'+id).closest('.mdl-cell').remove();
                  });
        }
        function getStarted(){
            $('#dContent').html();
            $.when(applySettings())
             .done(function(mySettings){
                $.post("/circushandler.php"
                    , {Action: "getfmLogon"}
                    , function (form) {
                        
                        if(form>""){
                            activateLogin(form,$('#dContent'));                     
                        }
                        else{
                            $.post("/circushandler.php"
                                , {Action: "getNews"}
                                , function(cards) {
                                    $('#dLive').html(cards.live);
                                    if(cards.hidden) {
                                        $('#dHidden').html(cards.hidden);
                                        $('#tHidden').removeClass('hide');
                                    }
                                    $('#dNew').html(cards.new);
                                    resizeTextAreas();
                                    componentHandler.upgradeDom();
                                    $.cloudinary.responsive();
                                    $('html').removeClass('wait');
                            },'json');
                        }
                    });
            });
        }
        function getStartedDEPRECATED(){
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
                                , {Action: "getNews"}
                                , function(cards) {
                                    $('#dLive').html(cards.live);
                                    if(cards.hidden) {
                                        $('#dHidden').html(cards.hidden);
                                        $('#tHidden').removeClass('hide');
                                    }
                                    $('#dNew').html(cards.new);
                                    resizeTextAreas();
                                    componentHandler.upgradeDom();
                                    $.cloudinary.responsive();
                                    $('html').removeClass('wait');
                                },'json');
                        }
                    });
            });
        }
        function getID(field){
            return field.data('id');
        }
        function newNews(){
            $.post("/circushandler.php",{Action: "createNewNews"}
                  ,function() {getStarted();});
        }
    </script>
  </body>
</html>
