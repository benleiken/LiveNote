<?php session_start();
require_once("lib/autoload.php");
require_once("lib/Thrift.php");
require_once("lib/transport/TTransport.php");
require_once("lib/transport/THttpClient.php");
require_once("lib/protocol/TProtocol.php");
require_once("lib/protocol/TBinaryProtocol.php");
require_once("lib/packages/Errors/Errors_types.php");
require_once("lib/packages/Types/Types_types.php");
require_once("lib/packages/UserStore/UserStore.php");
require_once("lib/packages/UserStore/UserStore_constants.php");
require_once("lib/packages/NoteStore/NoteStore.php");
require_once("lib/packages/Limits/Limits_constants.php");
require_once("evlogin.php");
    if(!empty($_POST['username']) && !empty($_POST['password']))
    {
       $_SESSION['user1'] = trim($_POST['username']);
       $_SESSION['pass1'] = trim($_POST['password']);
        Login();
       $_SESSION['isset']= true;
    }
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>LiveNote</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<script src="jquery-1.6.4.min.js" type="text/javascript"></script>
	<script src="js/jquery.flipCounter.1.2.js" type="text/javascript"></script>
	<script src='sprintf.js'></script>
	<link rel="stylesheet" type="text/css" href="css/styles.css">
	 
</head>
<body>
	
<header>
	<div id="header">
		<img id="canister" src="img/logo.jpg" alt="Film Canister" />
	</div>
<div id="app-form">
	<img id="evlogo" src="img/evlogo.jpg" />
        <? if (!(isset($_SESSION['isset'])) && !$_SESSION['isset']){ ?>
	<form action="" method="post">
	<font color="#93CA6B">Username: </font> <input type = "text" name="username" style="width:145px">
	<br />
	<br />
	<font color="#93CA6B">Password: </font> <input type ="password" name="password" style="width:150px"/>
	<br />
	<br />
	<input id="submit" type="submit" value="Login" />
	</form>
</div>
<? } else{ ?>
        <div id="login">
	
	<font color="#93CA6B">Logged in as: <? print $_SESSION['user1'] ?> </font>

        </div>
<? } ?>
</header>

<div id="total-app">
	<div id="app-header">
		<button class="btn success" id="start">Start</button>
		<button class="btn info" id="pause">Pause</button>
		<button class="btn danger" id="stop">Stop</button>
		<button class ="btn danger" id="youtube">YouTube</button>
		<br />
		<br />
		<div id="counter-hrs"><input type="hidden" name="counter-hrs-value" value="0" /></div>
		<div id="counter-mins"><input type="hidden" name="counter-mins-value" value="0" /></div>
		<div id="counter-secs"><input type="hidden" name="counter-secs-value" value="0" /></div>
	</div>

<section id="responsive">
	<p>Screen size below 960px!</p>
</section>	
	<!--<form action="" method="post">-->
	<input type="text" name="title" id="title" placeholder="Title" />
	<br />
	<br />
	<section id="app" name="input" contenteditable="true">
		<p>Start typing here!</p>
	</section>
	<br />
	<br />
	<input id="save" type="button" name="Save to Evernote" value="Save to Evernote" /> 
	<!--</form>-->
	<div style="text-align:center"> 
		<br/>
		<br/>
	</div>

	
	<div style="text-align:center" id="youtubetools"> 
		<br/>
		<br/>
		<br/>
		<div id="youtubevideo">
		<div id="videoDiv">Loading...</div>
		<br/>
		<div id="videoInfo">
        
        <br/>
        <p>Current Time: <span id="videoCurrentTime">--:--</span> | Duration: <span id="videoDuration">--:--</span></p>
        <br/>
        <p>Bytes Total: <span id="bytesTotal">--</span> | Start Bytes: <span id="startBytes">--</span> | Bytes Loaded: <span id="bytesLoaded">--</span></p>
        <br/>
        <p>Controls: <a href="javascript:void(0);" onclick="playVideo();">Play</a> | <a href="javascript:void(0);" onclick="pauseVideo();">Pause</a> | <a href="javascript:void(0);" onclick="muteVideo();">Mute</a> | <a href="javascript:void(0);" onclick="unMuteVideo();">Unmute</a></p>
        <br/>
        <p><input id="volumeSetting" type="text" size="3" />&nbsp;<a href="javascript:void(0)" onclick="setVideoVolume();">&lt;- Set Volume</a> | Volume: <span id="volume">--</span></p>
        <br/>
        </div>
        <br/>
        <button class ="btn danger" id="add-url">Add This YouTube Video's URL To The Document</button>
        <br/>
        <br/>
        <section id="paste-url" contenteditable="true">
			Paste a Youtube URL here to view the video
		</section>
		<br/>
        <button class ="btn danger" id="load-url">Load This Youtube Video</button>
      </div> 
	</div> 

</div>
<script src="http://www.google.com/jsapi" type="text/javascript"></script>
    <script type="text/javascript">
      google.load("swfobject", "2.1");
    </script>
    <script type="text/javascript"> 
    	var startcounter = 0;
    	var ispaused = false;
     /*
       * Chromeless player has no controls.
       */
      
      // Update a particular HTML element with a new value
      function updateHTML(elmId, value) {
        document.getElementById(elmId).innerHTML = value;
      }
      
      // This function is called when an error is thrown by the player
      function onPlayerError(errorCode) {
        alert("An error occured of type:" + errorCode);
      }
      
      // This function is called when the player changes state
      function onPlayerStateChange(newState) {
      	if (newState == 1)
        {
        	$("#start").trigger("click");
        }
        if (newState == 2)
        {
        	$("#pause").trigger("click");
        	console.log("paused");
        }
        updateHTML("playerState", newState);
      }
      
      // Display information about the current state of the player
      function updatePlayerInfo() {
        // Also check that at least one function exists since when IE unloads the
        // page, it will destroy the SWF before clearing the interval.
        if(ytplayer && ytplayer.getDuration) {
          updateHTML("videoDuration", ytplayer.getDuration());
          updateHTML("videoCurrentTime", ytplayer.getCurrentTime());
          updateHTML("bytesTotal", ytplayer.getVideoBytesTotal());
          updateHTML("startBytes", ytplayer.getVideoStartBytes());
          updateHTML("bytesLoaded", ytplayer.getVideoBytesLoaded());
          updateHTML("volume", ytplayer.getVolume());
        }
      }
      
      // Allow the user to set the volume from 0-100
      function setVideoVolume() {
        var volume = parseInt(document.getElementById("volumeSetting").value);
        if(isNaN(volume) || volume < 0 || volume > 100) {
          alert("Please enter a valid volume between 0 and 100.");
        }
        else if(ytplayer){
          ytplayer.setVolume(volume);
        }
      }
      
      function playVideo() {
        if (ytplayer) {
          ytplayer.playVideo();
        }
      }
      
      function pauseVideo() {
        if (ytplayer) {
          ytplayer.pauseVideo();
        }
      }
      
      function muteVideo() {
        if(ytplayer) {
          ytplayer.mute();
        }
      }
      
      function unMuteVideo() {
        if(ytplayer) {
          ytplayer.unMute();
        }
      }
      
      function addURL() {
      	if(ytplayer){
      		ytplayer.getVideoUrl();
      	}
      }
      
      var finalurl;
      
      $("#load-url").click(function(){
		var input_url = $("#paste-url").html();
		var part1 = "http://www.youtube.com/v/";
		console.log(part1);
		var part2 = input_url.substring(35,46);
		console.log(part2);
		var part3 = "?version=3";
		console.log(part3);
		finalurl = part1 + part2 + part3;
		console.log(finalurl);
		if (ytplayer){
			ytplayer.loadVideoByUrl(finalurl);
		}
	});
      
      
      // This function is automatically called by the player once it loads
      function onYouTubePlayerReady(playerId) {
        ytplayer = document.getElementById("ytplayer");
        // This causes the updatePlayerInfo function to be called every 250ms to
        // get fresh data from the player
        setInterval(updatePlayerInfo, 250);
        updatePlayerInfo();
        ytplayer.addEventListener("onStateChange", "onPlayerStateChange");
        ytplayer.addEventListener("onError", "onPlayerError");
        //Load an initial video into the player
        ytplayer.cueVideoById("ylLzyHk54Z0");
      }
      
      // The "main method" of this sample. Called when someone clicks "Run".
      function loadPlayer() {
        // Lets Flash from another domain call JavaScript
        var params = { allowScriptAccess: "always" };
        // The element id of the Flash embed
        var atts = { id: "ytplayer" };
        // All of the magic handled by SWFObject (http://code.google.com/p/swfobject/)
        swfobject.embedSWF("http://www.youtube.com/apiplayer?" +
                           "version=3&enablejsapi=1&playerapiid=player1", 
                           "videoDiv", "480", "295", "9", null, null, params, atts);
      }
      function _run() {
        loadPlayer();
      }
      google.setOnLoadCallback(_run);
	</script>


<script type="text/javascript">

$(function() {				
	var enter_counter = 0;
	var currenthrs = 0;
	var currentmins = 0;
	var currentsecs = 0;
	
	var minutes_holder = 0;
	var hours_holder = 0;
	
	var hrs_paused_holder = 0;
	var mins_paused_holder = 0;
	var secs_paused_holder = 0;
	
	var is_paused = false;
	
	var start_counter = 0;
	
	var time_left_after_pause = 0;
	
	$("#videoInfo").hide();
	$("#youtubetools").hide();
    $("#youtube").show();
 
    $('#youtube').click(function(){
    	$("#youtubetools").slideToggle();
    });

	$("#add-url").click(function(){
		$("#app > p:last-child").prepend(ytplayer.getVideoUrl());
	})
	
	function incrementMinutesAndHours()
	{
			console.log("increment minutes and hours")
			minutes_holder++;
			$("#counter-mins").flipCounter("setNumber",minutes_holder);
			$("#counter-secs").flipCounter("setNumber",0);
			$("#counter-secs").flipCounter("startAnimation",
        		{
            		number: 5, // the number we want to scroll from
                	end_number: 60, // the number we want the counter to scroll to
                	duration: 60000, // number of ms animation should take to complete
                	easing: false
            	});
        	if (minutes_holder == 60)
        	{
        		minutes_holder = 0;
        		hours_holder++;
        		$("#counter-hrs").flipCounter("setNumber", hours_holder);
        	}	
	}
	
	$("#counter-secs").flipCounter({
    	number:0, // the initial number the counter should display, overrides the hidden field
    	numIntegralDigits:2, // number of places left of the decimal point to maintain
      	numFractionalDigits:0, // number of places right of the decimal point to maintain
        digitClass:"counter-digit", // class of the counter digits
        counterFieldName:"counter-value", // name of the hidden field
        digitHeight:40, // the height of each digit in the flipCounter-medium.png sprite image
        digitWidth:30, // the width of each digit in the flipCounter-medium.png sprite image
        imagePath:"img/flipCounter-medium.png", // the path to the sprite image relative to your html document
       	easing: false, // the easing function to apply to animations, you can override this with a jQuery.easing method
       	duration: 1000,
        onAnimationStarted:false, // call back for animation upon starting
        onAnimationStopped:incrementMinutesAndHours, // call back for animation upon stopping
        onAnimationPaused:false, // call back for animation upon pausing
        onAnimationResumed:false // call back for animation upon resuming from pause
	});
	
	$("#counter-mins").flipCounter({
    	number:0, // the initial number the counter should display, overrides the hidden field
        numIntegralDigits:2, // number of places left of the decimal point to maintain
       	numFractionalDigits:0, // number of places right of the decimal point to maintain
        digitClass:"counter-digit", // class of the counter digits
        counterFieldName:"counter-value", // name of the hidden field
        digitHeight:40, // the height of each digit in the flipCounter-medium.png sprite image
        digitWidth:30, // the width of each digit in the flipCounter-medium.png sprite image
        imagePath:"img/flipCounter-medium.png", // the path to the sprite image relative to your html document
       	easing: false, // the easing function to apply to animations, you can override this with a jQuery.easing method
        onAnimationStarted:false, // call back for animation upon starting
        duration: 1000,
        onAnimationStopped:false, // call back for animation upon stopping
        onAnimationPaused:false, // call back for animation upon pausing
        onAnimationResumed:false // call back for animation upon resuming from pause
	});
			
	$("#counter-hrs").flipCounter({
    	number:0, // the initial number the counter should display, overrides the hidden field
        numIntegralDigits:2, // number of places left of the decimal point to maintain
       	numFractionalDigits:0, // number of places right of the decimal point to maintain
        digitClass:"counter-digit", // class of the counter digits
        counterFieldName:"counter-value", // name of the hidden field
        digitHeight:40, // the height of each digit in the flipCounter-medium.png sprite image
        digitWidth:30, // the width of each digit in the flipCounter-medium.png sprite image
        imagePath:"img/flipCounter-medium.png", // the path to the sprite image relative to your html document
       	easing: false, // the easing function to apply to animations, you can override this with a jQuery.easing method
       	duration: 1000,
        onAnimationStarted:false, // call back for animation upon starting
        onAnimationStopped:false, // call back for animation upon stopping
        onAnimationPaused:false, // call back for animation upon pausing
        onAnimationResumed:false // call back for animation upon resuming from pause
	});
	
	$('#app').keydown(function(event){
		if (event.which == '13') {
			currenthrs = $("#counter-hrs").flipCounter("getNumber");
			currentmins = $("#counter-mins").flipCounter("getNumber");
			currentsecs = $("#counter-secs").flipCounter("getNumber");
			$("#app > p:last-child").prepend("<span class='timestamp'>" + sprintf("%02d:%02d:%02d", currenthrs, currentmins, currentsecs) + ": " + "</span>");
			enter_counter++;
		}
	});
	
	$("#start").click(function(){
		if (start_counter == 0) {
			console.log("started");
			start_counter++;
			$("#counter-secs").flipCounter("startAnimation",
        		{
            		number: 0, // the number we want to scroll from
                	end_number: 60, // the number we want the counter to scroll to
                	easing: false, // this easing function to apply to the scroll.
                	duration: 60000 // number of ms animation should take to complete
               });
        	$(this).html("Going");
        	playVideo();
		} else {
			if (is_paused == true) {
        		$(this).html("Going");
        		$("#counter-secs").flipCounter("resumeAnimation");
        		is_paused = false;
        		playVideo();
        	}
		}
    });
    
    $("#pause").click(function(){
    	//hrs_paused_holder = $("#counter-hrs").flipCounter("getNumber");
		//mins_paused_holder = $("#counter-mins").flipCounter("getNumber");
		//secs_paused_holder = $("#counter-secs").flipCounter("getNumber");
		//console.log("paused time: " + sprintf("%02d:%02d:%02d", hrs_paused_holder, mins_paused_holder, secs_paused_holder));
		//console.log(secs_paused_holder);
		//time_left_after_pause = 60000 - (secs_paused_holder*1000);
		//console.log("seconds left: " + time_left_after_pause);
		$("#counter-secs").flipCounter("pauseAnimation"); // pause animation, can be resumed by calling startAnimation
		pauseVideo();
		if (is_paused == false)
		{
			$("#start").html("Resume");
			is_paused = true;
		}
    });
$("#save").click(function(){
  var input = '';
  $('#app p').each(function() {
    input = $(this).children('span').text() + $(this).text();
  });

console.log(input);

  $.post('evlogin.php', {
      title : $('#title').val(),
      input : $('#app').html(),
      doSave : true
  });
});
						
});

</script>


<footer>
	<p>Designed and Constructed by Sam Daniel and Ben Leiken</p>
</footer>

</body>
</html>

		
