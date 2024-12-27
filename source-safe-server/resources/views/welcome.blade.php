<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- scripts -->
    @php
    $PUSHER_APP_KEY = env('PUSHER_APP_KEY');
    @endphp
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        Pusher.logToConsole = true;

        var pusher = new Pusher("{{$PUSHER_APP_KEY}}", {
            cluster: 'eu'
        });

        var channel = pusher.subscribe('group.' + "1");
        channel.bind('SendNotification', function(data) {
            // console.log(data);
            alert(JSON.stringify(data.message));
        });
    </script>
</head>

<body class="antialiased">
    <h1>home page</h1>
</body>

</html>