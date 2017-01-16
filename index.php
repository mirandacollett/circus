<!doctype html>
<html lang="en">
    <?php include $_SERVER["DOCUMENT_ROOT"].'/cookies.php'; ?>
<head>
    <?php include $_SERVER["DOCUMENT_ROOT"].'/client/head.php'; ?>
    <?php include $_SERVER["DOCUMENT_ROOT"].'/client/ga.php'; ?>
</head>
<body class='hide'>
    <div id="fb-root"></div>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header">
            <div class="mdl-layout__header-row">
                <a href="/index.php"><img src="/images/logo.png" alt="Logo"></a>
                <span class="mdl-layout-spacer"></span>
                <div class="mdl-navigation-container">
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="/client/about.php">About Us</a>
                        <a class="mdl-navigation__link" href="/index.php">Home</a>
                    </nav>
                </div>
            </div>
        </header>
        <main class="content-wrapper">
            <div class="logobar bg" id="logobar-left"></div>
            <div class="mdl-layout__content">
                <div class="mdl-grid">
                    <div class="mdl-cell--12-col pageCol">
                        <div class="mdl-typography--headline fw" id="dWhatsOn"><span class="showname"></span> - What's On</div>
                        <div class="calendar fw" id="calendar"></div>
                        <div class="calendar hide fw" id="dEA">
                            <div class="mdl-typography--headline" name="News">Equine Affairs Calendar</div>
                        </div>
                        <div class="mdl-grid fw" id="dEventCards"></div>
                        <div class="mdl-grid hide fw" id="dNews">
                            <div class="mdl-cell--12-col mdl-typography--headline" name="News">Latest News</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="logobar bg" id="logobar-right"></div>
        </main>
        <?php include $_SERVER["DOCUMENT_ROOT"].'/client/footer.php'; ?>
    </div>
    <script>
        thisPage='index.php';
        $(document).ready(function (){
            $('.showname').html(SiteName);
            $.when(applySettings())
             .then(function(settings){
                $.cloudinary.responsive();   
                $.post("/circushandler.php"
                    , {Action: "getEventCards"}
                    , function (data) {
                        $('#dEventCards').html(data.cards);
                        if(data.news) $('#dNews').removeClass('hide').append(data.news);
                        $.cloudinary.responsive();
                        setBackground(data.background);
                        var sb = getFromArray(data,'sidebarLsettings',false);
                        if(sb){
                            if(getFromArray(sb,'sidebar','')=='fb' && getFromArray(sb,'facebook','') > ""){
                                getFacebook($('#logobar-left')
                                           ,getFromArray(sb,'facebook','')
                                           ,getFromArray(settings,'facebook_app','')
                                            );
                            }
                            if(getFromArray(sb,'sidebar','')=='logos') {setSidebar(getFromArray(data,'sidebarL',false),$('#logobar-left'));}                            
                        }
                        var sb = getFromArray(data,'sidebarRsettings',false);
                        if(sb){
                            if(getFromArray(sb,'sidebar','')=='fb' && getFromArray(sb,'facebook','') > ""){
                                getFacebook($('#logobar-right')
                                           ,getFromArray(sb,'facebook','')
                                           ,getFromArray(settings,'facebook_app','')
                                            );
                            }
                            if(getFromArray(sb,'sidebar','')=='logos') {setSidebar(getFromArray(data,'sidebarR',false),$('#logobar-right'));}
                        }
                        componentHandler.upgradeDom();
                        $('body').removeClass('hide');
                        $.cloudinary.responsive();
                },'json');
                if (getFromArray(settings,'Calendar','N') == "Y") {
                    getCalendar(getFromArray(settings,'Google API Key',false)
                               ,getFromArray(settings,'Google Calendar ID',false));
                }
                if (getFromArray(settings,'Equine Affairs','N') == 'Y'){
                    $('#dEA').removeClass('hide');
                    var frame = $('<iframe />').attr({
                                                    src: getFromArray(settings,'EA_events',null),
                                                    width: "900",
                                                    height: "800",
                                                    frameborder: "0"});
                    frame.appendTo($('#dEA'))
                }
                if (getFromArray(settings,'Show Results','N') == 'Y') {writeToMenu('results.php','Results');}
                if (getFromArray(settings,'Show Times','N') == 'Y') {writeToMenu('times.php','Times');}
            });
        });
        function getFacebook(vObject,vURL,vAppID){
            var s = document.createElement("script");
            s.type = "text/javascript";
            s.text = '(function(d, s, id) { '
                     + 'var js, fjs = d.getElementsByTagName(s)[0]; '
                     + 'if (d.getElementById(id)) return; '
                     + 'js = d.createElement(s); js.id = id; '
                     + 'js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.8&appId=' + vAppID + '"; '
                     + 'fjs.parentNode.insertBefore(js, fjs); '
                     + '}(document, "script", "facebook-jssdk"));';
            $("body").prepend(s);
            var ht=vObject.height()-150;
            if(ht<400) ht=500;
            var wd = Math.floor($('body').width()/4);
            if (wd<100) wd = Math.floor($('body').width());
            vObject.addClass('fb');
            var fb = $('<div />').addClass('fb-page')
                                 .attr({'data-href': vURL
                                        ,'data-tabs': 'timeline'
                                        ,'data-width': wd
                                        ,'data-height': ht
                                        ,'data-small-header': 'true'
                                        ,'data-adapt-container-width': 'true'
                                        ,'data-hide-cover': 'false'
                                        ,'data-show-facepile': 'false'
                                    });
            vObject.append(fb);
        }
        function getCalendar(APIKey,CalendarID) {
            // https://fullcalendar.io/support/
            $('#calendar').fullCalendar({
                googleCalendarApiKey: APIKey,
                events: {googleCalendarId: CalendarID},
                header:{left:'prev,next today',
                    center:'title',
                    right:'month,listYear'
                },
                eventRender: function(event,element){
                    if(-1 != event.title.indexOf("arena hire")) {
                        /* Arena hire is teal */
                        element.css('background-color', '#019FDE'); 
                        element.css('border-color', '#019FDE'); 
                    }
                    if(-1 != event.title.indexOf("Club")) {
                        /* Club events are pink */
                        element.css('background-color', '#E98B7F'); 
                        element.css('border-color', '#E98B7F'); 
                    }
                    if (-1 != event.title.indexOf("BS") ||
                        -1 != event.title.indexOf("SJ")) {
                        /* BS events are red */
                        element.css('background-color', 'rgb(212,24,0)'); 
                        element.css('border-color', 'rgb(212,24,0)'); 
                    }
                },
                eventClick: function(event){
                    if(ValidURL(event.description)){
                        window.open(event.description,'Show');
                    }
                    else{
                        window.open(event.url,'Show');
                    }
                    return false;
                },
                firstDay:1
            })
        }
        function setSidebar(vImages,vObject){
            if(vImages){
                $.each(vImages,function(a,b){
                    var href = $('<a />').attr({ href: b.url, target: "_blank"});
                    if(b.url==''||b.url==null) href = $('<a>').attr({ href: '#'});
                    var image = $('<img />').attr({ src: b.image_url, alt: 'Logo'});
                    var img = new Image();
                    img.onload = function() {
                        if(this.width>180 ) {image.attr({width:180});}
                        else if(this.height>180 ) {image.attr({height:180});}
                    }
                    img.src = b.image_url;
                    image.attr({style: 'margin:5px auto;'});
                    href.append(image).appendTo(vObject);
                });
                vObject.addClass('logos')
            }
            else{
                vObject.addClass('hide');
            }
        }
        function ValidURL(str) {
            var regex = /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[\-;:&=\+\$,\w]+@)?[A-Za-z0-9\.\-]+|(?:www\.|[\-;:&=\+\$,\w]+@)[A-Za-z0-9\.\-]+)((?:\/[\+~%\/\.\w\-_]*)?\??(?:[\-\+=&;%@\.\w_]*)#?(?:[\.\!\/\\\w]*))?)/;
            if(regex.test(str)) return true;
            return false;
        }
    </script>
  </body>
</html>
