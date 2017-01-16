<!doctype html>
<html lang="en">
    <?php include $_SERVER["DOCUMENT_ROOT"].'/cookies.php'; ?>
<head>
    <?php include $_SERVER["DOCUMENT_ROOT"].'/client/head.php'; ?>
    <style>
        #dImages{
            max-width: 280px;
        }
        #dImages .mdl-card__media{
            max-width: 180px;
            margin-top: 10px;
            margin-left: 50px;
            margin-right: 50px;
        }
    </style>
</head>
<body class='hide'>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header mdl-layout__header--waterfall">
            <div class="mdl-layout__header-row">
                <a href="/index.php"><img src="/images/logo.png" alt="Logo"></a>
                <span class="mdl-layout-title mdl-layout-spacer">Manage Right Sidebar</span>
                <div class="mdl-navigation-container">
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="/admin.php">Admin</a>
                    </nav>
                </div>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="mdl-grid">
                <div class="mdl-cell mdl-cell--12-col">
                    <div class="mdl-typography--headline">Right Sidebar</div>
                </div>
                <div class="mdl-cell mdl-cell--6-col mdl-shadow--3dp">
                    <div class="mdl-card__title"><h4 class="mdl-card__title-text">Options</h4></div>
                    <div class="mdl-card__supporting-text">
                        <form id="fmRSB">
                            <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect">
                                <input type="radio" class="mdl-radio__button" name="rsb" value="none">
                                <span class="mdl-radio__label">No Right Sidebar</span>
                            </label><br>
                            <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect">
                                <input type="radio" class="mdl-radio__button" name="rsb" value="logos">
                                <span class="mdl-radio__label">Logos or Photos</span>
                            </label><br>
                            <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect">
                                <input type="radio" class="mdl-radio__button" name="rsb" value="fb">
                                <span class="mdl-radio__label">Facebook Panel</span>
                            </label><br>
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="text" id="rsb_fb">
                                <label class="mdl-textfield__label" for="rsb_fb">Facebook URL</label>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="mdl-cell mdl-cell--6-col mdl-shadow--3dp">
                    <div class="mdl-card__title"><h4 class="mdl-card__title-text">Information</h4></div>
                    <div class="mdl-card__supporting-text">
                        <span class="mdl-typography--font-light mdl-typography--subhead">Any images that you want to put onto the sidebar, such as logos or photos, need to be uploaded using the Photos page. Give them a sensible name that explains the photo or logo to you. On this page, click the New Image button to create a new image. It will select a photo that you can alter. You can also optionally enter a URL e.g. http://www.bbc.co.uk so that clicking on the image takes them to that website. If you leave URL blank then there will be no click action. Images will be displayed at a maximum height or width of 180px, respecting their original shape.</span>
                    </div>
                </div>
            </div>
            <div class="mdl-grid" id="dImages"></div>
        </main>
    </div>
    <script>
        var updateHandler="updateSidebar";
        $(document).ready(function () {
            getStarted();
        });
        $(document).on('change','select',function(e){
            getStarted();
        });
        $(document).on('change','#fmRSB input.mdl-radio__button',function(e){
            $.post("/circushandler.php",{Action:'setSetting',description:'RightSidebar',text_value:$(this).val(),date_value:null,numeric_value:null});
        });
        $(document).on('change','#fmRSB input.mdl-textfield__input',function(e){
            $.post("/circushandler.php",{Action:'setSetting',description:'RightFacebook',text_value:$(this).val(),date_value:null,numeric_value:null});
        });
        function getStarted(){
            $.when(applySettings())
             .then(function(mySettings){
                $('html').addClass('wait');
                $.post("/circushandler.php"
                    , {Action: "getfmLogon"}
                    , function (logonData) {
                        if(logonData > ""){
                            activateLogin(logonData,$('#dContent'));
                            $('html').removeClass('wait');
                        }
                        else {
                            $.post("/circushandler.php"
                                , {Action: "getSidebar",location:'R'}
                                , function(data) {
                                    $('#dImages').html(data.cards);
                                    $("#fmRSB input[name=rsb][value=" + getFromArray(data.settings,'sidebar','#') + "]").prop('checked', true).parent().addClass('is-checked');
                                    if(getFromArray(data.settings,'facebook','') > "") {
                                        $('#fmRSB input.mdl-textfield__input').val(getFromArray(data.settings,'facebook',''))
                                                                              .parent().addClass('is-dirty');
                                    }
                                    componentHandler.upgradeDom();
                                    $.cloudinary.responsive();
                                    $('html').removeClass('wait');
                                },'json');
                        }
                    });
            });
        }
        function getID(field){
            console.log('getID says '+field.data('id'));
            return field.data('id');
        }
        function newSidebarLeft(){
            $.post("/circushandler.php",{Action: "createSidebarImage",location:"L"}
                  ,function() {getStarted();});
        }
        function newSidebarRight(){
            $.post("/circushandler.php",{Action: "createSidebarImage",location:"R"}
                  ,function() {getStarted();});
        }
        function deleteSidebarImage(vID){
            $.post("/circushandler.php",{Action: "deleteSidebarImage",id:vID}
                  ,function() {getStarted();});
        }
        function cloneSidebarRL(){
            $.post("/circushandler.php",{Action: "cloneSidebar",from:'R',to:'L'}
                  ,function() {getStarted();});
        }
        function cloneSidebarLR(){
            $.post("/circushandler.php",{Action: "cloneSidebar",from:'L',to:'R'}
                  ,function() {getStarted();});
        }
    </script>
  </body>
</html>
