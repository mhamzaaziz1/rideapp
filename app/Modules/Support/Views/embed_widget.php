<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RideApp Support</title>
    <!-- FontAwesome for standard icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Base styles for standalone iframe */
        body {
            margin: 0;
            padding: 0;
            background: transparent;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            overflow: hidden;
        }

        /* Override the widget container to fill the iframe */
        #rideapp-chat-widget {
            position: fixed !important;
            bottom: 0 !important;
            right: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            border-radius: 0 !important;
            display: flex !important;
            box-shadow: none !important;
            margin: 0 !important;
        }

        /* Hide the FAB (Floating Action Button) since the iframe wrapper will handle toggling if needed */
        #chat-fab {
            display: none !important;
        }

        /* Hide the internal close button since the parent site wrapper should handle closing, 
           or leave it if we want the iframe to trigger a postMessage to close itself */
        #chat-close {
            display: none !important;
        }
    </style>
</head>
<body>
    <?= view('App\Modules\Support\Views\chat_widget') ?>
</body>
</html>
