<!doctype html>
<html lang="en">
    <?php include $_SERVER["DOCUMENT_ROOT"].'/cookies.php'; ?>
<head>
    <?php include $_SERVER["DOCUMENT_ROOT"].'/client/head.php'; ?>
    <style>
        .mdl-grid{
            -webkit-justify-content: center;
            -ms-flex-pack: center;
            justify-content: center;
        }
        .pseudoRow{
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-flex-direction: row;
            -ms-flex-direction: row;
            flex-direction: row;
            -webkit-flex-wrap: wrap;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            -webkit-justify-content: space-between;
            -ms-flex-pack: justify;
            justify-content: space-between;
            -webkit-align-content: stretch;
            -ms-flex-line-pack: stretch;
            align-content: stretch;
            -webkit-align-items: flex-start;
            -ms-flex-align: start;
            align-items: flex-start;
            padding: 10px;
            border: 1px solid grey;
            margin: 5px;
            height: auto;
            line-height: normal;
        }
        .lfNumber{
            background-color: rgb(250, 255, 189);
        }
    </style>
</head>
<body class='hide'>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header mdl-layout__header--waterfall">
            <div class="mdl-layout__header-row">
                <a href="/index.php"><img src="/images/logo.png" alt="Logo"></a>
                <span class="mdl-layout-title mdl-layout-spacer">Site Settings</span>
                <div class="mdl-navigation-container">
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="/admin.php">Admin</a>
                    </nav>
                </div>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="mdl-grid" id="dDetails">
                <div class="mdl-cell mdl-cell--12-col mdl-shadow--3dp">
                    <div class="mdl-card__supporting-text">
                        <form id="fmSettings">    
                            <div class="pseudoRow">
                                <fieldset>
                                    <div class="pseudoRow">
                                        <span class="mdl-card__title-text">Background Colour or Image</span>
                                        <select id="fBackground" class="lfSelect" data-fieldname="text_value" data-desc="Background">
                                            <option value="None">Plain pale background</option>
                                        </select>
                                    </div>
                                    <div class="pseudoRow">
                                        <span class="mdl-card__title-text">Primary Colour</span>
                                        <select id="fPrimary" class="lfSelect" data-fieldname="text_value" data-desc="Primary Colour">
                                            <option value="deep_orange">Deep Orange</option>
                                            <option value="red">Red</option>
                                            <option value="pink">Pink</option>
                                            <option value="purple">Purple</option>
                                            <option value="deep_purple">Deep Purple</option>
                                            <option value="indigo">Indigo</option>
                                            <option value="blue">Blue</option>
                                            <option value="light_blue">Light Blue</option>
                                            <option value="cyan">Cyan</option>
                                            <option value="teal">Teal</option>
                                            <option value="green">Green</option>
                                            <option value="light_green">Light Green</option>
                                            <option value="lime">Lime</option>
                                            <option value="yellow">Yellow</option>
                                            <option value="amber">Amber</option>
                                            <option value="orange">Orange</option>
                                            <option value="brown">Brown</option>
                                            <option value="blue_grey">Blue Grey</option>
                                            <option value="grey">Grey</option>
                                        </select>
                                    </div>
                                    <div class="pseudoRow">
                                        <span class="mdl-card__title-text">Secondary Colour</span>
                                        <select id="fSecondary" class="lfSelect" data-fieldname="text_value" data-desc="Secondary Colour">
                                            <option value="deep_orange">Deep Orange</option>
                                            <option value="red">Red</option>
                                            <option value="pink">Pink</option>
                                            <option value="purple">Purple</option>
                                            <option value="deep_purple">Deep Purple</option>
                                            <option value="indigo">Indigo</option>
                                            <option value="blue">Blue</option>
                                            <option value="light_blue">Light Blue</option>
                                            <option value="cyan">Cyan</option>
                                            <option value="teal">Teal</option>
                                            <option value="green">Green</option>
                                            <option value="light_green">Light Green</option>
                                            <option value="lime">Lime</option>
                                            <option value="yellow">Yellow</option>
                                            <option value="amber">Amber</option>
                                            <option value="orange">Orange</option>
                                        </select>
                                    </div>
                                    <div class="pseudoRow">
                                        <span class="mdl-card__title-text">Headline Colour</span>
                                        <select id="fHeadline" class="lfSelect" data-fieldname="text_value" data-desc="Headline Colour">
                                            <option value="black">Black</option>
                                            <option value="white">White</option>
                                            <option value="darkred">Dark Red</option>
                                            <option value="deep_orange">Deep Orange</option>
                                            <option value="red">Red</option>
                                            <option value="pink">Pink</option>
                                            <option value="purple">Purple</option>
                                            <option value="deep_purple">Deep Purple</option>
                                            <option value="indigo">Indigo</option>
                                            <option value="blue">Blue</option>
                                            <option value="light_blue">Light Blue</option>
                                            <option value="cyan">Cyan</option>
                                            <option value="teal">Teal</option>
                                            <option value="green">Green</option>
                                            <option value="light_green">Light Green</option>
                                            <option value="lime">Lime</option>
                                            <option value="yellow">Yellow</option>
                                            <option value="amber">Amber</option>
                                            <option value="orange">Orange</option>
                                        </select>
                                    </div>
                                    <div class="pseudoRow">
                                        <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="fEventCardPhotos">
                                            <input type="checkbox" 
                                                   id="fEventCardPhotos"
                                                   class="lfCheckbox mdl-checkbox__input"
                                                   data-fieldname="text_value"
                                                   data-desc="Event Card Photographs">
                                            <span class="mdl-checkbox__label">Use Photos on Event Cards</span>
                                        </label>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <table>
                                        <tr><th colspan="2">Change the order of the Homepage elements</th></tr>
                                        <tr><td>What's On Headline</td><td><input type="number" class="lfNumber" data-fieldname="numeric_value" data-desc="Order - Whats On"></input></td></tr>
                                        <tr><td>Google Calendar</td><td><input type="number" class="lfNumber" data-fieldname="numeric_value" data-desc="Order - Google Calendar"></input></td></tr>
                                        <tr><td>Equine Affairs Calendar</td><td><input type="number" class="lfNumber" data-fieldname="numeric_value" data-desc="Order - EA Calendar"></input></td></tr>
                                        <tr><td>Event Cards</td><td><input type="number" class="lfNumber" data-fieldname="numeric_value" data-desc="Order - Event Cards"></input></td></tr>
                                        <tr><td>News Headline and Cards</td><td><input type="number" class="lfNumber" data-fieldname="numeric_value" data-desc="Order - News"></input></td></tr>
                                    </table>
                                </fieldset>
                            </div>
                            <div class="pseudoRow">
                                <fieldset>
                                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="fCalendar">
                                        <input type="checkbox" 
                                               id="fCalendar"
                                               class="lfCheckbox mdl-checkbox__input"
                                               data-fieldname="text_value"
                                               data-desc="Calendar">
                                        <span class="mdl-checkbox__label" id="fCalendarLabel">Use Calendar on Home Page (needs Calendar ID and API Key)</span>
                                    </label>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input lfText"
                                               type="text"
                                               id="fGcalID"
                                               data-fieldname="text_value"
                                               data-desc="Google Calendar ID">
                                        <label class="mdl-textfield__label" for="fGcalID">Google Calendar ID</label>
                                    </div>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input lfText"
                                               type="text"
                                               id="fGoogleAPIKey"
                                               data-fieldname="text_value"
                                               data-desc="Google API Key">
                                        <label class="mdl-textfield__label" for="fGoogleAPIKey">Google API Key</label>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input lfText"
                                               type="text"
                                               data-fieldname="text_value"
                                               data-desc="Cloudinary Cloud Name">
                                        <label class="mdl-textfield__label">Cloudinary Cloud Name</label>
                                    </div>
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                        <input class="mdl-textfield__input lfText"
                                               type="text"
                                               data-fieldname="text_value"
                                               data-desc="Cloudinary Preset">
                                        <label class="mdl-textfield__label">Cloudinary Preset</label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="pseudoRow">
                                <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="fEA">
                                    <input type="checkbox" 
                                           id="fEA"
                                           class="lfCheckbox mdl-checkbox__input"
                                           data-fieldname="text_value"
                                           data-desc="Equine Affairs">
                                    <span class="mdl-checkbox__label">Use Equine Affairs frame on Home Page (needs URL)</span>
                                </label><br>
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input lfText"
                                           type="text"
                                           id="fEA_src"
                                           data-fieldname="text_value"
                                           data-desc="Equine Affairs List">
                                    <label class="mdl-textfield__label" for="fEA_src">Equine Affairs URL</label>
                                </div>
                            </div>
                            <div class="pseudoRow">
                                <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="fResults">
                                    <input type="checkbox" 
                                           id="fResults"
                                           class="lfCheckbox mdl-checkbox__input"
                                           data-fieldname="text_value"
                                           data-desc="Show Results">
                                    <span class="mdl-checkbox__label">Show Results page (needs results URL)</span>
                                </label>
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input lfText"
                                           type="text"
                                           id="fResultsURL"
                                           data-fieldname="text_value"
                                           data-desc="Results URL">
                                    <label class="mdl-textfield__label" for="fResultsURL">Results URL</label>
                                </div>
                            </div>
                            <div class="pseudoRow">
                                <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="fTimes">
                                    <input type="checkbox" 
                                           id="fTimes"
                                           class="lfCheckbox mdl-checkbox__input"
                                           data-fieldname="text_value"
                                           data-desc="Show Times">
                                    <span class="mdl-checkbox__label">Show Times page (needs times URL)</span>
                                </label>
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input lfText"
                                           type="text"
                                           id="fTimesURL"
                                           data-fieldname="text_value"
                                           data-desc="Times URL">
                                    <label class="mdl-textfield__label" for="fTimesURL">Times URL</label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="mdl-cell mdl-cell--12-col mdl-shadow--3dp">
                    <div class="mdl-card__title">Technical Settings - meddle at your peril</div>
                    <div class="mdl-card__supporting-text">
                        <div class="pseudoRow">
                            <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="fEA_link">
                                <input type="checkbox" 
                                       id="fEA_link"
                                       class="lfCheckbox mdl-checkbox__input"
                                       data-fieldname="text_value"
                                       data-desc="Link Equine Affairs">
                                <span class="mdl-checkbox__label">Link Equine Affairs dates to Google Calendar (needs Client ID)</span>
                            </label><br>
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input lfText"
                                       type="text"
                                       id="fGoogleClientID"
                                       data-fieldname="text_value"
                                       data-desc="Google Client ID">
                                <label class="mdl-textfield__label" for="fGoogleClientID">Google Client ID</label>
                            </div>
                        </div>
                        <div class="pseudoRow">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input lfText"
                                       type="text"
                                       id="fFacebookApp"
                                       data-fieldname="text_value"
                                       data-desc="Facebook App ID">
                                <label class="mdl-textfield__label" for="fFacebookApp">Facebook App ID (required to embed a Facebook feed)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mdl-cell mdl-cell--6-col mdl-shadow--3dp">
                    <div class="mdl-card__title mdl-typography--headline">Google integration</div>
                    <div class="mdl-card__supporting-text">
                        <h5>Sign up first</h5>
                        <p>You need to become a Google Developer. Create your Developer Account in the Google Dev Console and create a project. You need a Google API key for some of the features to work properly. This key needs to be saved in the relevant field in admin - settings. To create a Google API key, log into Google as a suitable identity. Go to Google API Manager (Google it!). Create a project and then create credentials (API Key - copy this into admin - settings. Do also set a key restriction to your URL plus the relevant localhost for testing.). </p>
                        <h5>Analytics</h5>
                        <p>Google it, create the relevant account, put the code snippet into your copy of ga.php</p>
                        <h5>Map</h5>
                        <p>This is displayed on the About page. You must have saved a Google API Key and also set up the following in the Google API Manager Library. Choose:</p>
                        <ul><li>Google Calendar API</li><li>Google Maps Javascript API</li><li>Google Maps Geocoding API</li><li>Google Maps Directions API</li></ul>
                        <h5>Calendar</h5>
                        <p>You can display a nicely-formatted calendar on the home page - see www.dorsetshowground.co.uk for an example. This is based on a Google Calendar. You will need to add and edit calendar items using the normal Google Calendar. If you put just a URL in the description field, the users will be able to click through to this URL on your homepage. You need to put your Google Calendar ID into admin - settings. You can find this by opening your Google Calendar in the Google website - on the left is My Calendars. Hover over your calendar and click the drop-down arrow. Go to Calendar Settings. Half way down the page is Calendar ID - a long string of letters ending calendar.google.com. Copy all of this into Google Calendar ID on admin - settings. It is possible to integrate this Google Calendar with Equine Affairs.</p>
                        <h5>Equine Affairs integration</h5>
                        <p>First, you need to become an Equine Affairs client. Talk to them about it. When you have signed up with Equine Affairs, they will send you three URLs: calendar, times and results. Put these into the three relevant fields on the admin -settings page: Equine Affairs Calendar URL, Results URL, Times URL. Now, you can choose whether to display an Equine Affairs calendar on your home page (just click the Use Equine Affairs frame on Home page) or a better formatted version (click use Calendar on Home Page.</p>
                        <p>You also need to extend your Google API Manager credentials to add an OAuth 2.0 client ID. Follow the instructions on the screen.</p>
                        <p>Now you need to copy your Equine Affairs dates into your Google Calendar. There is an app for that! Just go back to Admin and then click on the Calendar Sync button. Instructions are on the screen. If it goes wrong, open Google Calendar to delete the calendar items and start again. It will only add calendar dates, not alter any.</p>
                        <h5>Equo Calendar integration</h5>
                        <p>Just joking - I haven't written this (yet).</p>
                    </div>
                </div>
                <div class="mdl-cell mdl-cell--6-col mdl-shadow--3dp">
                    <div class="mdl-card__title mdl-typography--headline">Facebook Integration</div>
                    <div class="mdl-card__supporting-text">
                        <h5>Facebook app ID</h5>
                        <p>Go to Facebook for Developers and set up an app ID. Use it in head.php and also admin - settings.</p>
                        <h5>Facebook feed in a sidebar</h5>
                        <p>Go to Admin - Sidebar Left or Admin - Sidebar Right. Use the radio buttons to say that you want a Facebook panel instead of logos or images. Put the URL of the relevant Facebook page into the field. Test.</p>
                        <h5>Your website logo in FB posts</h5>
                        <p>The og settings need to be put into the head.php file. You can test it <a href='https://developers.facebook.com/tools/debug/sharing/'>here</a>.</p>
                    </div>
                    <div class="mdl-card__title mdl-typography--headline">Personalising your website</div>
                    <div class="mdl-card__supporting-text">
                        <h5>First step - much of this is done by Miranda</h5>
                        <p>The relevant steps are:</p>
                        <ul>
                            <li>You decide on a colour scheme and a plan for the home page (calendars, cards, photos, etc).</li>
                            <li>You provide any photos that are needed</li>
                            <li>You write the words for the About page</li>
                            <li>You provide any logos needed for sidebars</li>
                            <li>Miranda will create you 2 versions of a logo if you don't already have one. The letterbox shaped logo will appear in the page header and the square logo on Facebook posts.</li>
                            <li>Miranda will set up the background information that is needed for Facebook/Twitter/Google integration</li>
                        </ul>
                        <h5>Create news stories and events</h5>
                        <p>Use the Admin page to write news stories, create events, add attachments or photos and to tweak the site generally.</p>
                    </div>
                    <div class="mdl-card__title mdl-typography--headline">Updates</div>
                    <div class="mdl-card__supporting-text">
                        <h5>New Features</h5>
                        <p>Whenever a client asks for (and pays for) a new feature on this family of websites, that new feature will be made available to all the websites in the family. These new features will generally be made available in the winter after thorough testing. You will be contacted when a new feature becomes available.</p>
                        <ul>
                            <li>2016 - basic website with the ability to create events and new stories and with all the admin pages to maintain these.</li>
                            <li>2017 - with thanks to Dorset Showground - a new calendar for the Home Page</li>
                            <li>2017 - with thanks to Bicton Arena - integration with Equine Affairs calendars</li>
                            <li>2017 - with thanks to Dorset Showground - Facebook integration</li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        var UseEventPhotos;
        $(document).ready(function () {
            getStarted();
        });
        function getStarted(){
            $.when(applySettings())
             .then(function(mySettings){
                $.post("/circushandler.php"
                    , {Action: "getfmLogon"}
                    , function (data) {
                        if(data>""){activateLogin(data,$('#dDetails'));}
                        else {
                            $.post("/circushandler.php"
                                    ,{Action:'startSettingsPage'}
                                    ,function(data){
                                        $.each(data.photos,function(index,photo){
                                            $('#fBackground').append($('<option />',{ value : photo['id'] }).text(photo['title']));
                                        });
                                        $.each(data.settings,function(index,setting){
                                            var fieldname = setting.description;
                                            var inputField = getInput(fieldname);
                                            if($(inputField).attr('type')=='checkbox'){
                                                populateCheckbox(inputField,setting.text_value);
                                            }
                                            else if($(inputField).attr('type')=='text'){
                                                populateTextbox(inputField,setting.text_value);
                                            }
                                            else if($(inputField).attr('type')=='number'){
                                                if(inputField&&setting.numeric_value){
                                                    $(inputField).data('original',setting.numeric_value);
                                                    $(inputField).val(setting.numeric_value);
                                                }
                                            }
                                            else if($(inputField).attr('type')=='select'){
                                                populateSelect(inputField,setting.text_value);
                                            }
                                            else if($(inputField).attr('type')=='textarea'){
                                                populateSelect(inputField,setting.text_value);
                                            }
                                        });
                                        componentHandler.upgradeDom();
                                    },'json');
                                    }
                        });
            });
        }
        function getInput(fieldname){   
            var vReturn = false;
            $('input').each(function(a,b){
                if(!vReturn && $(b).attr('data-desc')==fieldname){vReturn = b;}
            })
            if(!vReturn){
                $('select').each(function(a,b){
                    if(!vReturn){
                        if($(b).attr('data-desc')==fieldname){
                            $(b).attr('type','select');
                            vReturn = b;
                        }
                    }
                })
            }
            return vReturn;
        }
        function populateCheckbox(inputField,value){
            if(inputField&&value){
                $(inputField).data('original',value);
                if(value=="Y"){
                    $(inputField).attr({checked:true})
                                 .parent().addClass('is-checked');
                }
            }
        }
        function populateTextbox(inputField,value){
            if(inputField){
                $(inputField).parent().css({width:"100%"});
            }
            if(inputField&&value){
                $(inputField).data('original',value);
                $(inputField).val(value);
                $(inputField).parent().addClass('is-dirty');
            }
        }
        function populateSelect(inputField,value){
            if(inputField&&value){
                $(inputField).data('original',value);
                $.each($('option',inputField),function(i,option){
                    if(option.value==value){option.setAttribute('selected',true);}
                });
            }
        }
        function populateTextarea(inputField,value){
            if(inputField&&value){
                $(inputField).data('original',value);
                $(inputField).html(value);
                $(inputField).parent().addClass('is-dirty');
                inputField.trigger('input');    
            }
        }
        $(document).on('change','.lfSelect',function(){
            var field = $(this);
            var description = field.data('desc');
            var val = field.val();
            if(field.data('fieldname')=='text_value'&&val!==field.data('original')){
                $.post("/circushandler.php"
                      ,{Action:'setSetting',description:description,text_value:val,numeric_value:null,date_value:null}
                      ,function(data){
                          field.data('original',val);
                          location.reload();
                      });
            }
        }); 
        $(document).on('change','.lfCheckbox',function(){
            var field = $(this);
            var val = 'N';
            if(field.is(':checked'))val = 'Y';
            $.post("/circushandler.php"
                    ,{Action:'setSetting',description:field.data('desc'),text_value:val,numeric_value:null,date_value:null}
                    ,function(){field.data('original',val);});
        });
        $(document).on('change','.lfText,.lfNumber',function(){
            var field = $(this);
            var datatype = field.attr('data-fieldname'); 
            var values={
                text_value: (datatype=='text_value' ? field.val() : null),
                numeric_value: (datatype=='numeric_value' ? field.val() : null),
                date_value: (datatype=='date_value' ? field.val() : null),
                final: field.val()
            }
            $.post("/circushandler.php"
                    ,{Action: 'setSetting'
                    , description: field.data('desc')
                    , text_value: values.text_value
                    , numeric_value: values.numeric_value
                    , date_value: values.date_value}
                    ,function(){field.data('original',values.final);});
        });
        $(document).on('change','.lfTextarea',function(){
            var field = $(this);
            var val = field.html();
            $.post("/circushandler.php"
                    ,{Action:'setSetting',description:field.data('desc'),text_value:val,numeric_value:null,date_value:null}
                    ,function(){field.data('original',val);});
        });
    </script>
  </body>
<!--
 15/12/2015 Created
-->
</html>
