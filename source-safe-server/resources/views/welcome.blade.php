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
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // // Enable Pusher logging for development (disable in production)
        // Pusher.logToConsole = true;
        // // Initialize Pusher
        // var pusher = new Pusher('24b84ba7291470020271', {
        //     cluster: 'eu'
        // });
        // // Subscribe to the private channel
        // var channel = pusher.subscribe('group.1');
        // // Bind to the event
        // channel.bind('SendNotification', function(data) {
        //     console.log('Notification received:', data);
        //     alert(`File "${data.file.fileName}" was checked in.`);
        // });
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('24b84ba7291470020271', {
            cluster: 'eu'
        });

        var channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            alert(JSON.stringify(data));
        });
    </script>
</head>

<body class="antialiased">
    <h1>home page</h1>
</body>

</html>