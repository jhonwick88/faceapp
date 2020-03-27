<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                <div>
        <video id="live" width="360" height="480" autoplay style="display: inline;"></video>
        <canvas width="500" id="canvas" height="480" style="display: inline;"></canvas>
         <p><label id="recognise_name" style="display: none"></label>  <ul><li id="timer" style="display: none"></li></ul></p>
    </div>
                </div>

                <div class="links">
                    <a href="#">Docs</a>
                    <a href="#">Laracasts</a>
                </div>
            </div>
        </div>
        <script type="text/javascript">


     var hour,minute,second;//时 分 秒
    hour=minute=second=0;//初始化
    var millisecond=0;//毫秒
    var int;
    function Reset()//重置
    {
      window.clearInterval(int);
      millisecond=hour=minute=second=0;

    }

    function start()//开始
    {
      int=setInterval(timer,50);
    }

    function timer()//计时
    {
      millisecond=millisecond+50;
      if(millisecond>=1000)
      {
        millisecond=0;
        second=second+1;
      }
      if(second>=60)
      {
        second=0;
        minute=minute+1;
      }

      if(minute>=60)
      {
        minute=0;
        hour=hour+1;
      }
      $('#timer').text(hour+'时'+minute+'分'+second+'秒'+millisecond+'毫秒');

    }

    function stop()//暂停
    {
      window.clearInterval(int);
      $('#timer').show()
    }



    var ws = null;
    var video = $("#live").get()[0];
    var canvas = $("#canvas");
    var ctx = canvas.get()[0].getContext('2d');


    if (window.location.protocol === "https:") {
       wsproto = "wss";
    } else {
       wsproto = "ws";
    }


    if (window.location.protocol === "file:") {
       wsuri = wsproto + "://127.0.0.1:8080/ws";
    } else {
       wsuri = wsproto + "://" + window.location.hostname + ":9001/ws";
    }


    if ("WebSocket" in window) {
       ws = new WebSocket(wsuri);
    } else if ("MozWebSocket" in window) {
       ws = new MozWebSocket(wsuri);
    } else {
       console.log("Browser does not support WebSocket!");
    }

    if (ws) {

        ws.onopen = function () {
            console.log("Openened connection to websocket");
        }


        // 收到WebSocket服务器数据
        ws.onmessage = function(e) {
            var name = eval('('+e.data+')');
            var labelName = $("#recognise_name");
            // e.data contains received string.
            console.log("onmessage: " + name["face_found_in_image"]);
            if(name["face_found_in_image"]!=""&&name["face_found_in_image"]!=null){
                stop();
                labelName.text("Hi dear " + name["face_found_in_image"] + " we find you");
                labelName.show();
                ws.close();
            }
        };

        // 关闭WebSocket连接
        ws.onclose = function(e) {
            console.log("Connection closed (wasClean = " + e.wasClean + ", code = " + e.code + ", reason = '" + e.reason + "')");
            ws = null;
        };

    }




    navigator.webkitGetUserMedia({video:true, audio:true},
          function(stream) {
            video.src = window.webkitURL.createObjectURL(stream);
          },
            function(err) {
                console.log("Unable to get video stream!")
            }
      );


     var dataURI = function dataURIToBlob(dataurl){
        var arr = dataurl.split(',');
        var mime = arr[0].match(/:(.*?);/)[1];
        var bstr = atob(arr[1]);
        var n = bstr.length;
        var u8arr = new Uint8Array(n);
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new Blob([u8arr], {type:mime});
    }

    start();


        timer = setInterval(
            function () {
                ctx.drawImage(video, 0, 0, 480, 360);
                var data = canvas.get()[0].toDataURL('image/png', 1.0);
                newblob = dataURI(data);
                if (ws.readyState===1) {
                    ws.send(newblob);
                    console.log(newblob)
                }else{
                    //do something
                }
            }, 200);
</script>
    </body>
</html>
