<!DOCTYPE html>
<html>
	
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Recording File Play</title>

        <link href="data:image/gif;" rel="icon" type="image/x-icon" />

        <!-- Bootstrap -->
        <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">

        <link rel="stylesheet" href="example/css/style.css" />
        <link rel="stylesheet" href="example/css/ribbon.css" />

        <link rel="screenshot" itemprop="screenshot" href="http://katspaugh.github.io/wavesurfer.js/example/screenshot.png" />

        <!-- wavesurfer.js -->
        <script src="src/wavesurfer.js"></script>
        <script src="src/util.js"></script>
        <script src="src/webaudio.js"></script>
        <script src="src/mediaelement.js"></script>
        <script src="src/drawer.js"></script>
        <script src="src/drawer.canvas.js"></script>

        <!-- regions plugin -->
<!--        <script src="plugin/wavesurfer.regions.js"></script>-->

        <!-- Demo -->
	<script type="text/javascript" src="example/main.js?file_name=<?php echo $_GET['file_name'];?>"></script>
	<script type="text/javascript">
		var file_name = "<?php echo $_GET['file_name']?>";
	</script>
        <script src="example/trivia.js"></script>
    </head>

    <body itemscope itemtype="http://schema.org/WebApplication">
        <div class="container">
            <div class="header">
                <noindex>
                <ul class="nav nav-pills pull-right">
                    <li><a href="?fill&file_name=<?=$_GET['file_name']?>">Fill</a></li>
                    <li><a href="?scroll&file_name=<?=$_GET['file_name']?>">Scroll</a></li>
                </ul>
                </noindex>

                <h1 itemprop="name"><?php echo $_GET['file_name']?></h1>
            </div>

            <div id="demo">
                <div id="waveform">
                    <div class="progress progress-striped active" id="progress-bar">
                        <div class="progress-bar progress-bar-info"></div>
                    </div>

                    <!-- Here be the waveform -->
                </div>

                <div class="controls">
                    <button class="btn btn-primary" data-action="back">
                        <i class="glyphicon glyphicon-step-backward"></i>
                        Backward
                    </button>

                    <button class="btn btn-primary" data-action="play">
                        <i class="glyphicon glyphicon-play"></i>
                        Play
                        /
                        <i class="glyphicon glyphicon-pause"></i>
                        Pause
                    </button>

                    <button class="btn btn-primary" data-action="forth">
                        <i class="glyphicon glyphicon-step-forward"></i>
                        Forward
                    </button>

                    <button class="btn btn-primary" data-action="toggle-mute">
                        <i class="glyphicon glyphicon-volume-off"></i>
                        Toggle Mute
                    </button>
                </div>
            </div>
        </div>

 <!--       <div class="github-fork-ribbon-wrapper right">
            <div class="github-fork-ribbon">
                <a itemprop="isBasedOnUrl" href="https://github.com/katspaugh/wavesurfer.js">Fork me on GitHub</a>
            </div>
        </div> -->

    </body>
</html>
