<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>WebcamJS Test Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
	<!-- CSS -->
    <style>
    #my_camera{
        width: 240px;
        height: 320px;
        border: 1px solid black;
    }
	</style>
    <script src="{{ URL::asset('js/jquery.js') }}"></script>
	<script src="{{ URL::asset('js/webcam2.min.js') }}"></script>
</head>
<body>
<!-- -->
<div id="my_camera"></div>
 <input type=button value="Open Camera" onClick="configure()">
<input type=button value="Reset" onClick="reset()">

 <h2 id="found"></h2>
 <!-- Script -->
 <script language="JavaScript">
 function set_interval() {
  //the interval 'timer' is set as soon as the page loads  
  var autocapture = 1000 * 1 * 3; // 3 seconds
  window.setTimeout(function () { auto_send(); }, autocapture);
  //itimer=setInterval("auto_send()",autocapture);
}
 // Configure a few settings and attach camera
 function configure(){
  Webcam.set({
   width: 240,
   height: 320,
   image_format: 'jpeg',
   jpeg_quality: 90
  });
  Webcam.attach( '#my_camera' );
  set_interval();
 }
 function reset(){
    Webcam.reset();
    document.getElementById('found').innerHTML = '';
 }
 // preload shutter audio clip
 var shutter = new Audio();
 shutter.autoplay = false;
 shutter.src = navigator.userAgent.match(/Firefox/) ? 'raw/shutter.ogg' : 'raw/shutter.mp3';

function auto_send(){
     // play sound effect
  shutter.play();
  // take snapshot and get image data
  Webcam.snap( function(data_uri) {
 // Get base64 value from <img id='imageprev'> source
 //var base64image = document.getElementById("imageprev").src;
 let d = data_uri;
        let token = '{{ csrf_token() }}';
 let formData = new FormData();
        formData.append('image',d);
        formData.append('_token',token);
        $.ajax({
            url: '{{ route('snap')}}',
            type : 'POST',
            enctype: 'multipart/form-data',
            data : formData,
            processData : false,
            contentType : false,
            cache: false,
            timeout: 5000,
            success : function(data) {
                console.log(data);
                if (typeof data.response !== 'undefined') 
                {
                    if (data.response == 'false') 
                    {
                        alert(data.message)
                        return;                        
                    }
                    document.getElementById('found').innerHTML = data.message;
                }   
            
            }
        });
  });
}
</script>
</body>
</html>