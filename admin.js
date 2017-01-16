var PrimaryColour,SecondaryColour,thisPage='clueless';
var settings = {};
function getParameters (vName,vDefault){
    var regex = new RegExp("[\\?&]"+vName+"=([^&#]*)");
    var results = regex.exec(window.location.href);
    if (results === null)
        return vDefault;
    else
        return results[1];
}
function getFromArray(mySettings,item,otherwise){
    var rtn = otherwise;
    if(!mySettings) return rtn;
    $.each(mySettings,function(a,b){
        if(a==item) rtn = b;
    })
    return rtn;
}
/* ====================================================== Generic page layout */
function setBackground(background){
    if(background){
        if(background=='None'){
            $.each($('.mdl-layout__content'),function(index){
                var thisObject=$(this);
                var rgb=thisObject.css('background-color');
                rgb='rgba'+rgb.substring(3,rgb.indexOf(')'))+', 0.1)';
                thisObject.css('background-color',rgb)
                          .removeClass('mdl-color--primary');
            });
        }
        else{
            $('body').addClass('imageBackground');
            $('.l-big-photo-section').attr({style:"background-image:url('"+background+"');background-position:top center;background-size:cover;background-repeat:no-repeat;"});
            $('.l-section-title').removeClass('mdl-color-text--primary')
                                 .addClass('l-section-title-white');
        }
    }
}
/* =================================================================== Log On */
function activateLogin(data,iObject){
    iObject.html(data);
    componentHandler.upgradeDom();
    if($('#iUsername').val()==""||$('#iPassword').val()==""){
        $('#btLogon').attr({disabled: true});
    }
    setTimeout(function() {
        $('input').each(function() {
            var inputField = $(this);
            var inputFieldID = inputField.attr('id');
            var hasValue = inputField.val().length > 0; //Normal
            if(!hasValue){
                hasValue = $("#"+inputFieldID+":-webkit-autofill").length > 0;//Chrome
            }
            if (hasValue) {
                inputField.trigger('change');
            }
        })
        $('#iPassword').focus();
        $('#iUsername').focus();
        $('html').removeClass('wait');
    }, 250);
}
$(document).on('submit','#fmLogon', function(e){
    e.PreventDefault;
    return false;
});
$(document).on('click','#btLogon', function(){logOn();});
$(document).on('click','#btRegister', function(){
    return false;
    if($('#btReset').html()=='Cancel'){
        $('#pMessage').html('').addClass('error');
        var validated=true;
        if($('#iUsername').val().length<3||$('#iUsername').val()==null){
            validated=false;
            $('#pMessage').append('<br>The Username is too short');
        }
        if($('#iPassword').val()!==$('#iPassword2').val()){
            validated=false;
            $('#pMessage').append('<p>The Passwords do not match');
        }
        if($('#iEmail').val().length<5||$('#iEmail').val()==null){
            validated=false;
            $('#pMessage').append('<br>The Email address is too short');
        }
        if($('#iEmail').parent().hasClass('is-invalid')){
            validated=false;
            $('#pMessage').append('<br>Please provide a proper email address');
        }
        if(validated){
            $('#pMessage').append('<br>Validated, now saving');
            $.post("/circushandler.php"
                    ,{   Action:'createUser'
                        ,username:$('#iUsername').val()
                        ,password:$('#iPassword').val()
                        ,emailaddress:$('#iEmail').val()}
                    ,function(data){
                        $('#pMessage').html(data);
                    });
        }
    }
    if($('#btReset').html()=='Reset Password'){
        $('#btRegister').closest('.mdl-cell').children('.mdl-card__title').children('h4').html('Register a new user');
        $('#fmLogon .hide').removeClass('hide');
        $('#btLogon').addClass('hide');
        $('#btReset').html('Cancel');
        $('#iPassword').removeClass('fText').val('').parent().removeClass('is-dirty');
        $('#btRegister').removeClass('mdl-button--primary').addClass('mdl-button--raised mdl-button--accent');
        $('#iUsername').val('').parent().removeClass('is-dirty');
        $('#iUsername').removeClass('fText').focus();
    }
});
$(document).on('click','#btReset', function(){
    if($('#btReset').html()=='Cancel'){
        window.location.reload();
    }
});
$(document).on('keyup change paste','#fmLogon input', function(e){
    if($('#iUsername').val()>""&&$('#iPassword').val()>""){
        $('#btLogon').attr({disabled: false});
    }
});
function gettingLogonForm(){
    var dfd = new $.Deferred();
    console.log('2. Starting gettingLogonForm:')
    $.post("/circushandler.php"
        , {Action: "getfmLogon"}
        , function (form) {
        console.log('2.1 Got the form:')
        console.log('2.2 Now resolving it')
        dfd.resolve(form);
    });
    return dfd.promise();
}
/* ============================================================= Page Startup */
function getAdminCards(){
    $.post("/circushandler.php"
        , {Action: "getAdminCards"}
        , function (myAdminCards){
            $('.mdl-grid').html('')
                          .parent().addClass("hide");
            if(myAdminCards.logon){
                console.log('Need to logon')
                activateLogin(myAdminCards.logon,$('#gLiveEvents'));
                $('#gLiveEvents').parent().removeClass('hide');
                console.log('Go for it')
            }
            else {
                if(myAdminCards.cards.current){
                    $('#gLiveEvents').html(myAdminCards.cards.current)
                                     .parent().removeClass('hide');
                }
                if(myAdminCards.cards.future){
                    $('#gFutureEvents').html(myAdminCards.cards.future)
                                       .parent().removeClass('hide');
                }
                if(myAdminCards.cards.past){
                    $('#gPastEvents').html(myAdminCards.cards.past)
                                     .parent().removeClass('hide');
                }
                if(myAdminCards.cards.users){
                    $('#gMoreAdmin').html(myAdminCards.cards.users)
                                    .parent().removeClass('hide');
                }
                $.cloudinary.responsive();
            }
            $('body').removeClass('hide');
            $('html').removeClass('wait');
    },'json');
}
/* ============================================================= Saving Edits */
$(document).on('submit','.l-disregard-form', function(e){
    e.PreventDefault;
    return false;
});
$(document).on('change','.fText',function(){
    var field = $(this);
    var id = getID(field);
    var val = field.val();
    if(val>""){
        $.post("/circushandler.php",{Action:updateHandler,id:id,fieldname:field.data('fieldname'),value:val});
    }
});
$(document).on('change','.fSelect',function(){
    var field = $(this);
    var id = getID(field);
    var val = field.val();
    $.post("/circushandler.php",{Action:updateHandler,id:id,fieldname:field.data('fieldname'),value:val});
});
$(document).on('change','.fCheckbox',function(){
    var field = $(this);
    var id = getID(field);
    var val = 'N';
    if(field.is(':checked'))val = 'Y';
    $.post("/circushandler.php",{Action:updateHandler,id:id,fieldname:field.data('fieldname'),value:val});
});  
function logOn(){
    if($('#iUsername').val()==""||$('#iPassword').val()==""){
        return false;
    }
    $.post("/circushandler.php"
        , {Action: "fmLogonSubmit",username: $('#iUsername').val(),password: $('#iPassword').val()}
        , function() {
            $('#fmLogon').closest('.mdl-cell').remove();
            getStarted();
        },'json');
}
function writeEmail(message){
    var vContent = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
    vContent+= "<html xmlns='http://www.w3.org/1999/xhtml'>";
    vContent+= "<head>";
    vContent+= "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />";
    vContent+= "<title>" + SiteName + "</title>";
    vContent+= "<meta name='viewport' content='width=device-width, initial-scale=1.0'/>";
    vContent+= "</head>";
    vContent+= "<body style='margin: 0; padding: 0;'>";
    //========================================================== Content
    vContent+= message;
    //========================================================== /Content
    vContent+= "</body>";
    return vContent;
}
function sendMail(vEmail,vHeader,vContent) {
    var dfd = new $.Deferred();
    $.post("/sendMail.php"
        , {vEmail:vEmail, vHeader:vHeader, vContent:vContent}
        , function (response) {
            console.log(response);
            dfd.resolve('Sent');
        });
    return dfd.promise();
}
function getParameter(vName,vDefault){
    var regex = new RegExp("[\\?&]"+vName+"=([^&#]*)");
    var results = regex.exec(decodeURI(window.location.href));
    if (results === null) {
        return vDefault;
    }
    else {
        return results[1];
    }
}
function getLogOn(){
    var dfd = new $.Deferred();
    $.post("/circushandler.php", {Action: "getfmLogon"},function (data) {
        if(data=="") dfd.resolve(data);
        else dfd.reject(data);
    });
    return dfd.promise();
}
function applySettings(){
    var dfd = new $.Deferred();
    console.log('1. Starting Apply Settings')
    $('.useSiteName').html(SiteName);
    $.post("/circushandler.php",{Action:'pageSettings'},function(data){
        console.log('1.1 Applying Settings')
        $s=setStandardSettings(data);
        $.each(data,function(index,setting){
            // ============================== Set the colour scheme
            if(setting.description=='Primary Colour'){
                PrimaryColour=setting.text_value;
            }
            if(setting.description=='Secondary Colour'){
                SecondaryColour=setting.text_value;
            }
        });
        // ============================================= Set the mdl style sheet
        $('head').append('<link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.2.1/material.' + PrimaryColour + '-' + SecondaryColour + '.min.css" />');
        $('#dStyleSheet').remove();
        $('head').append('<link rel="stylesheet" href="/styles.css?v=2" />');
        // =============================== More settings now we have the colours
        var allColours=stdColours();
        $.each(data,function(index,setting){
            // ============================== Set the background colour or image
            if(setting.description=='Background'){
                var rgb=allColours[PrimaryColour];
                var rgba='rgba'+rgb.substring(3,rgb.indexOf(')'))+', 0.1)';
                if(setting.text_value=='None'){
                    $('.mdl-layout__content').css('background-color',rgba);
                    $('footer.multipleObjects').css('background-color',rgba);
                    $('head').append('<style>.bg{background-color: '+rgba+';}</style>');
                    $('.mdl-typography--headline span').css('background-color','transparent');
                }
                else{
                    // DIY Responsiveness on the image !!!
                    var ceil = Math.ceil($(window).width()/100)*100;
                    var url = setting.image_url;
                    url = url.replace('w_auto','w_'+ceil);
                    $('.mdl-layout__content').css('background-color',rgba);
                    if($(window).width()>400) {
                        $('.mdl-layout__content').css('backgroundImage', 'url('+url+')');
                        $('.mdl-typography--headline span').removeClass('mdl-color-text--primary')
                                                           .addClass('mdl-color-text--white');
                    }
                }
            }
            if(setting.description=='Event Card Photographs'&&setting.text_value=='N'){
                $('#dLiveEvents').addClass('noEventPhotos');
                $('#dFutureEvents').addClass('noEventPhotos');
                $('#dPastEvents').addClass('noEventPhotos');
            }
            if(setting.description=="Headline Colour"){
                // Set the headline colour
                var headlineColor='black';
                if(setting.text_value=="darkred") headlineColor = 'rgb(212,24,0)';
                else if(setting.text_value=="black") headlineColor = 'black';
                else if(setting.text_value=="white") headlineColor = 'white';
                else if(setting.text_value>"") headlineColor = allColours[setting.text_value];
                $('.mdl-typography--headline').css('color',headlineColor);                
            }
            if(setting.description=='Order - Whats On') $('#dWhatsOn').css({'order':setting.numeric_value});
            if(setting.description=='Order - Google Calendar') $('#calendar').css({'order':setting.numeric_value});
            if(setting.description=='Order - EA Calendar') $('#dEA').css({'order':setting.numeric_value});
            if(setting.description=='Order - Event Cards') $('#dEventCards').css({'order':setting.numeric_value});
            if(setting.description=='Order - News') $('#dNews').css({'order':setting.numeric_value});
        });
        // ================================ Set the navigation menu hover colour
        var rgb,allColours = stdColours();
        rgb=allColours[SecondaryColour];
        var css='.mdl-navigation__link:hover{border-bottom:4px solid '+rgb+';}';
        style = document.createElement('style');
        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }
        document.getElementsByTagName('head')[0].appendChild(style);
        $('body').removeClass('hide');
        // ================================================= Resolve the promise
        console.log('1.2 Resolving Settings')
        dfd.resolve(setStandardSettings(data));
    },'json');
    return dfd.promise();
}
function setStandardSettings(data){
    settings.scopes = ["https://www.googleapis.com/auth/calendar"];
    $.each(data,function(i,setting){
        settings[setting.description]=setting.text_value;
    });
    return settings;
}
function writeToMenu(link,label){
    var a = $('<a />').addClass('mdl-navigation__link')
            .attr({href:link})
            .html(label);
    $('header nav.mdl-navigation').prepend(a);
}
$('textarea').each(function () {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
    })
.on('input', function () {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});
function stdColours(){
    return  {deep_orange:'rgb(255, 87, 34)'
            ,red:'rgb(244, 67, 54)'
            ,pink:'rgb(255, 64, 129)'
            ,purple:'rgb(156, 39, 176)'
            ,deep_purple:'rgb(124, 77, 255)'
            ,indigo:'rgb(83, 109, 254)'
            ,blue:'rgb(68, 138, 255)'
            ,light_blue:'rgb(64, 196, 255)'
            ,cyan:'rgb(0, 188, 212)'
            ,teal:'rgb(100, 255, 218)'
            ,green:'rgb(76, 175, 80)'
            ,light_green:'rgb(178, 255, 89)'
            ,lime:'rgb(238, 255, 65)'
            ,yellow:'rgb(255, 255, 0)'
            ,amber:'rgb(255, 193, 7)'
            ,orange:'rgb(255, 152, 0)'
            ,brown:'rgb(121, 85, 72)'
            ,blue_grey:'rgb(96, 125, 139)'
            ,grey:'rgb(158, 158, 158)'
            };
}
function resizeTextAreas(){
    $('textarea').each(function () {
         // Handler to extend the textarea fields
         this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
     })
     .on('input', function () {
         this.style.height = 'auto';
         this.style.height = (this.scrollHeight) + 'px';
     });
}
/* ================================================================= Bookings */
$(document).on('submit','#fmBooking', function(e){
    e.PreventDefault;
    if(validatedFmBooking()){
        var vGreeting = 'Thanks' + '<br>' + '<br>';
        var vContent =  SiteName + ' Schooling Day<br>'
                        + '<br>'
                        + 'We have received an online booking<br><br>'
                        + 'Horse: ' + $('#fHorse').val() + '<br>'
                        + 'Rider: ' + $('#fRider').val() + '<br>'
                        + 'Email: ' + $('#fEmail').val() + '<br>'
                        + 'Tel: ' + $('#fTel').val() + '<br>'
                        + 'Voucher: ' + $('#fVoucher').val() + '<br>'
                        + 'Preferred Time: ' + $("#sTime option:selected").text() + '<br>'
                        + 'Notes: ' + $('#fNotes').val() + '<br>' + '<br>'
                        + 'Please put this onto Equo as a manual entry' + '<br>' + '<br>'
                        + vGreeting
                        + 'Your Website';
        sendMail(null,'Helpers Voucher',vContent);
        $('#fHorse').val('');
        $('#fRider').val('');
        $('#fEmail').val('');
        $('#fTel').val('');
        $('#fVoucher').val('');
        $('#fNotes').val('');
        $('#sTime').val('0');
    }
    else alert('Please complete all the fields');
    return false;
});
function validatedFmBooking(){
    var validated = true;
    if($('#fHorse').val()=='') validated=false;
    if($('#fRider').val()=='') validated=false;
    if($('#fEmail').val()=='') validated=false;
    if($('#fTel').val()=='') validated=false;
    if($('#sTime').val()=='0') validated=false;
    return validated;
}
function runTest(){
    console.log('runTest starting')
    $.post("/circushandler.php",{Action:"Test"},function(data){
        console.log('Returning Test data')
        console.log(data)
        console.log('DONE')
    })
    console.log('runTest finished')
}

