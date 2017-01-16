<!doctype html>
<html lang="en">
    <?php include $_SERVER["DOCUMENT_ROOT"].'/cookies.php'; ?>
<head>
    <?php include $_SERVER["DOCUMENT_ROOT"].'/client/head.php'; ?>
    <?php include $_SERVER["DOCUMENT_ROOT"].'/client/ga.php'; ?>
    <style>
        .embed-wrapper{
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }
    </style>
</head>
<body class='hide'>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
    <header class="mdl-layout__header mdl-layout__header--waterfall">
        <div class="mdl-layout__header-row">
            <a href="/index.php"><img src="/images/logo.png" alt="Logo"></a>
            <span class="mdl-layout-title mdl-layout-spacer"></span>
            <div class="mdl-navigation-container">
                <nav class="mdl-navigation">
                    <a class="mdl-navigation__link" href="/index.php">Home</a>
                </nav>
            </div>
        </div>
    </header>
    <div class="mdl-layout__content">
        <div class="mdl-typography--headline">Results</div>
        <div class="mdl-grid">
            <div class="embed-wrapper"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function (){
        thisPage='results.php';
        $.when(applySettings())
         .then(function(x){
            var frame = $('<iframe />').attr({
                src: getFromArray(mySettings,'Results URL','#'),
                width: "900",
                height: "600",
                frameborder: "0"});
            frame.appendTo($('.embed-wrapper'));
            if(getFromArray(mySettings,'Show Times','N')=='Y'){writeToMenu('times.php','Times');}
        });
    });
</script>
</body>
</html>
