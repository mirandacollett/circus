<!doctype html>
<html lang="en">
<head>
    <?php include $_SERVER["DOCUMENT_ROOT"].'/head.php'; ?>
    <script src="https://apis.google.com/js/client.js?onload=doNowt"></script>
    <style>
        .embed-wrapper{
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }
        .mdl-typography--headline.mdl-typography--headline{
            color: rgb(212,24,0);
            font-weight: 600;
            padding-bottom: 20px;
        }
        .mdl-grid{
            max-width: 1900px!important;
        }
        .mdl-layout__content {
            overflow-y: auto!important;
        }
        .matched{
            background-color: lightgreen;
        }
        .created{
            background-color: yellow;
        }
    </style>
</head>
<body class='hide'>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
    <header class="mdl-layout__header mdl-layout__header--waterfall">
        <div class="mdl-layout__header-row">
            <a href="index.php"><img src="images/logo.png" alt="Logo"></a>
            <span class="mdl-layout-title mdl-layout-spacer"></span>
            <div class="mdl-navigation-container">
                <nav class="mdl-navigation">
                    <a class="mdl-navigation__link" href="index.php">Home</a>
                </nav>
            </div>
        </div>
    </header>
    <div class="mdl-layout__content">
        <div class="mdl-grid">
            <div id="authorize-div" style="display: none">
                <span>Authorize access to Google Calendar API</span>
                <button id="authorize-button" onclick="handleAuthClick(event)">Authorize</button>
            </div>
            <div class="mdl-card mdl-cell mdl-cell--6-col mdl-shadow--2dp">
                <div class="mdl-typography--headline">Equine Affairs</div>
                <div class="mdl-card__supporting-text" id="dEA">
                    <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="gatherEA();">Fetch</button>
                </div>
            </div>
            <div class="mdl-card mdl-cell mdl-cell--6-col mdl-shadow--2dp">
                <div class="mdl-typography--headline">Google Calendar</div>
                <div class="mdl-card__supporting-text" id="dG">
                    <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="checkAuth();">Fetch</button>
                </div>
            </div>
            <div class="mdl-card mdl-cell mdl-cell--12-col mdl-shadow--2dp">
                <div class="mdl-typography--headline">Go compare...</div>
                <div class="mdl-card__supporting-text" id="dC"></div>
                <div class="mdl-card__supporting-text" id="dCompare">Fetch both first</div>
                <div class="mdl-card__supporting-text">
                    <!-- <iframe src="https://www.equineaffairs.com/RemoteLocationEventList.aspx?LocationID=1854&from=rl" 
                            width="900" height="600" frameborder="0" id="dEA">
                    </iframe> -->
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function (){
        applySettings();
    });
    // ==================================================== Fetch Equine Affairs
    function gatherEA(){
        $('#dEA').html('Wait for it...')
        $.ajax({
            url: "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D'https%3A%2F%2Fwww.equineaffairs.com%2FRemoteLocationEventList.aspx%3FLocationID%3D1854%26from%3Drl'&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys",
            type: 'GET',
            success: function(res) {
                $('#dEA').html('')
                var form = $(res.query.results.body.form);
                var y = form[0]['div'];
                var tab = y[2]['table'][0];
                var tbody = tab['tbody'];
                var eID = 0;
                $(tbody['tr']).each(function(b,c){
                    if(c.class=="visible-sm visible-md visible-lg") {
                        //console.log(c);
                        eID++;
                        var row = c['td'];
                        var event = {}
                        event.dateFrom=parseDate(row[1]);
                        event.dateTo=parseDate(row[2]);
                        event.title=row[3]['a']['content'];
                        if(event.title.substring(0,17)=='Dorset Showground') event.title=event.title.substring(18);
                        event.url='https://www.equineaffairs.com' + row[3]['a']['href'];
                        writeResult($('#dEA'),event,eID);
                    }
                })
                activateComparison();
            }
        });
    }
    // =============================================================== Utilities
    function parseDate(iString){
        var now = new Date();
        var parts =iString.split(' ');
        var months = {}
        months["Jan"]=0;
        months["Feb"]=1;
        months["Mar"]=2;
        months["Apr"]=3;
        months["May"]=4;
        months["Jun"]=5;
        months["Jul"]=6;
        months["Aug"]=7;
        months["Sep"]=8;
        months["Oct"]=9;
        months["Nov"]=10;
        months["Dec"]=11;
        var oString = new Date(now.getFullYear(),months[parts[2]],parts[1]);
        if(oString<now) oString = new Date((now.getFullYear())+1,months[parts[2]],parts[1]);
        oString=getGMTDate(oString);
        return oString;
    }
    function writeResult(domObject,event,ID){
        var div = $('<div />');
        div.attr('data-id',ID);
        div.attr('data-event',JSON.stringify(event));
        div.append(event.dateFrom);
        if(event.dateFrom!==event.dateTo){
            div.append('<br>')
               .append(event.dateTo)
        }
        div.append('<br>')
           .append(event.title)
           .append('<br>')
           .append(event.url)
           .append('<br><br>');
        domObject.append(div);
        domObject.attr('data-loaded','Y');
     }
    function doNowt(){
    }
    function activateComparison(){
        if($('#dEA').attr('data-loaded')=='Y' &&
           $('#dG').attr('data-loaded')=='Y'){
            var b = $('<button />').html('Compare')
                                   .addClass("mdl-button mdl-js-button mdl-button--raised mdl-button--colored")
                                   .attr({'onclick':'goCompare();'});
            $('#dC').append(b);
            $('#dCompare').html('');
        }
    }
    function strToDate(iDate){
        try{
            if(iDate==''||iDate==null) return null;
            var parts = iDate.split('-');
            if(parts[2].length=='26T17:00:00+01:00'.length){
                parts[2] = (parts[2]).substring(0,2);
            }
            if(parts[2].length=='2T17:00:00+01:00'.length){
                parts[2] = (parts[2]).substring(0,1);
            }
            var d = new Date(parts[0], parts[1]-1, parts[2]);
            return d;
        } catch(err){
            console.log('Error in strToDate using ' + iDate + ': ' + err.message);
            return null;
        }
    }
    function dateOnly(iDate){
        if(iDate==''||iDate==null) return iDate;
        var d = getGMTDate(strToDate(iDate));
        return d;
    }
    function alterDateTo(iFrom,iTo){
        try {
            if(iTo==''||iTo==null) return iFrom;
            var vMessage='';
            var dFrom = strToDate(iFrom);
            var dTo = strToDate(iTo);
            if(getGMTDate(dFrom)==getGMTDate(dTo)){
                return dateOnly(iTo);
            }
            else{
                var dTo2 = dTo.setDate(dTo.getDate() - 1);
                dTo = new Date(dTo2);
                dTo = getGMTDate(dTo);
                return dTo;
            }
        }
        catch(err) {
            console.log(err.message);
            console.log('iFrom and iTo are '+iFrom + ' ===> ' + iTo)
            console.log('dTo is ' + dTo)
            console.log(vMessage)
            return iTo;
        }
    }
    function alterDate(iDate,iDays){
        var dDate = strToDate(iDate);
        var dDate2 = dDate.setDate(dDate.getDate() + iDays);
        dDate = new Date(dDate2);
        return getGMTDate(dDate);
    }
    function getGMTDate(iDate){
        function pad(s) { return (s < 10) ? '0' + s : s; }
        return [iDate.getFullYear()
               ,pad(iDate.getMonth()+1)
               ,pad(iDate.getDate())
               ].join('-');
    }
    
    // =================================================== Fetch Google Calendar
    function checkAuth() {
        gapi.auth.authorize({'client_id': settings.google_client_id
                            ,'scope': settings.scopes.join(' ')
                            ,'immediate': true
                            }, handleAuthResult);
    }
    function handleAuthResult(authResult) {
        var authorizeDiv = document.getElementById('authorize-div');
        if (authResult && !authResult.error) {
          // Hide auth UI, then load client library.
          authorizeDiv.style.display = 'none';
          loadCalendarApi();
        } else {
          // Show auth UI, allowing the user to initiate authorization by
          // clicking authorize button.
          authorizeDiv.style.display = 'inline';
        }
    }
    function handleAuthClick(event) {
        gapi.auth.authorize(
          {client_id: settings.google_client_id, scope: settings.scopes, immediate: false},
          handleAuthResult);
        return false;
      }
    function loadCalendarApi() {
        $('#dG').html('');
        gapi.client.load('calendar', 'v3', listUpcomingEvents);
      }
    function listUpcomingEvents() {
        var request = gapi.client.calendar.events.list({
          'calendarId': settings.GoogleCalendarID,
          'timeMin': (new Date()).toISOString(),
          'showDeleted': false,
          'singleEvents': true,
          'maxResults': 30,
          'orderBy': 'startTime'
        });
        var eID = 0;
        request.execute(function(resp) {
            var events = resp.items;
            var vURL = 'https://www.equineaffairs.com/eventdetails.aspx?id=230791';
            if (events.length > 0) {
                for (i = 0; i < events.length; i++) {
                    var event = events[i];
                    eID++;
                    var oEvent={}
                    oEvent.title=event.summary;
                    oEvent.url = event.description;
                    oEvent.dateFrom = event.start.dateTime;
                    if (!oEvent.dateFrom) {oEvent.dateFrom = event.start.date;}
                    oEvent.dateFrom=dateOnly(oEvent.dateFrom);
                    oEvent.dateTo = event.end.dateTime;
                    if (!oEvent.dateTo) {oEvent.dateTo = event.end.date;}
                    oEvent.dateTo=alterDateTo(oEvent.dateFrom,oEvent.dateTo);
                    oEvent.title=event.summary;
                    oEvent.url = event.description;
                    writeResult($('#dG'),oEvent,eID);
                }
                activateComparison();
            }
            else {
                $('#dG').append('No upcoming events found.');
            }

        });
    }
    // ===================================================== Compare and resolve
    function goCompare(){
        $('#dCompare').html('Getting EA IDs');
        var eEvents = [];
        $('#dEA div').each(function(a,b){
            eEvents[$(this).attr('data-id')]=JSON.parse($(this).attr('data-event'));
        })
        var gEvents = [];
        $('#dG div').each(function(a,b){
            gEvents[$(this).attr('data-id')]=JSON.parse($(this).attr('data-event'));
        })
        $('#dCompare').append('<br>Got the arrays. Now compare');
        $.each(eEvents,function(a,eEvent){
            if(eEvent){
                var matched=false,gID;
                $.each(gEvents,function(x,gEvent){
                    if(gEvent && gEvent.dateFrom==eEvent.dateFrom & gEvent.url==eEvent.url){
                        matched=true;
                        $('#dEA div[data-id="' + a + '"]').addClass('matched');
                        $('#dG div[data-id="' + x + '"]').addClass('matched');
                    }
                })
                if(!matched) {
                    if(eEvent.title=="Unaffilated Dressage & Combined Training") eEvent.title="Dressage/CT";
                    if(eEvent.title=="Senior British Show Jumping & Unaffiliated Show Jumping") eEvent.title="SJ";
                    if(eEvent.title==" British Show Jumping Club & Unaffiliated Show Jumping") eEvent.title="Club & Unaffiliated";
                    eEvent.dateTo=alterDate(eEvent.dateTo,1);
                    $('#dCompare').append('<br>Event to be created on ' + eEvent.dateFrom + ' to ' + eEvent.dateTo + ' called ' + eEvent.title);
                //    createGoogleEvent(eEvent,a);
                }
            }
        })
    }
    function createGoogleEvent(eEvent,eID){
        var event = {
          'summary': eEvent.title,
          'description':eEvent.url,
          'start': {
            'date': eEvent.dateFrom,
            'timeZone': 'Europe/London'
          },
          'end': {
            'date': eEvent.dateTo,
            'timeZone': 'Europe/London'
          },
        };
        var request = gapi.client.calendar.events.insert({
            'calendarId': settings.GoogleCalendarID,
            'resource': event
        });
        request.execute(function(event) {
            $('#dCompare').append('<br>Event created in the Google Calendar');
            $('#dEA div[data-id="' + eID + '"]').addClass('created');
        });
    }
</script>
</body>
</html>
