/// <reference path="/Includes/jquery-2.0.2-vsdoc.js" />
thisPage='index.php';
$(document).ready(function (){
    $.when(applySettings())
     .then(function(x){
        $.cloudinary.responsive();   
        $.post("/circushandler.php"
            , {Action: "getEventCards"}
            , function (data) {
                $('#dEventCards').html(data.cards);
                if(data.news) $('#dNews').removeClass('hide').append(data.news);
                $.cloudinary.responsive();
                setBackground(data.background);
                setSidebar(data.sidebarL,$('#logobar-left'));
                setSidebar(data.sidebarR,$('#logobar-right'));
                componentHandler.upgradeDom();
                $.cloudinary.responsive();
        },'json');
        if(settings.use_calendar=="Y") {getCalendar();}
        if(settings.use_EA=='Y'){
            $('#dEA').removeClass('hide');
            var frame = $('<iframe />').attr({
                                            src: settings.EA_events,
                                            width: "900",
                                            height: "800",
                                            frameborder: "0"});
            frame.appendTo($('#dEA'))
        }
        if(settings.show_results=='Y'){writeToMenu('results.php','Results');}
        if(settings.show_times=='Y'){writeToMenu('times.php','Times');}
        if(settings.facebook_url>""||1==1){
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.8&appId=118532961969150";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        }
    });
});
function getCalendar() {
    // https://fullcalendar.io/support/
    $('#calendar').fullCalendar({
        googleCalendarApiKey: settings.GoogleAPIKey,
        events: {googleCalendarId: settings.GoogleCalendarID
                },
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
                window.open(event.description,'Show','width=' + (window.innerWidth - 50) + ',height=' + window.innerHeight);                        
            }
            else{
                window.open(event.url,'Show','width=' + (window.innerWidth - 50) + ',height=' + window.innerHeight);
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
    }
    else{
        vObject.css({'display':'none'});
    }
}
function ValidURL(str) {
    var regex = /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[\-;:&=\+\$,\w]+@)?[A-Za-z0-9\.\-]+|(?:www\.|[\-;:&=\+\$,\w]+@)[A-Za-z0-9\.\-]+)((?:\/[\+~%\/\.\w\-_]*)?\??(?:[\-\+=&;%@\.\w_]*)#?(?:[\.\!\/\\\w]*))?)/;
    if(regex.test(str)) return true;
    return false;
}
