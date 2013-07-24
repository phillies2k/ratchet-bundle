P2RatchetBundle
===============

Version: **1.0.3**


### Installation

    "require": {
        "p2/ratchet-bundle": "dev-master"
    }

### Configuration

    p2_ratchet:
        provider: my_provider   # The ClientProviderInterface implementation
        address: 0.0.0.0        # The address to receive sockets on (0.0.0.0 means receive from any)
        port: 8080              # The port the socket server will listen on

### Usage

* Implement the [ClientInterface](Socket/ClientInterface.php) in your applications user model or document.
* Implement the [ClientProviderInterface](Socket/ClientProviderInterface.php) in your applications user provider or managing repository.
* Set the `provider` setting to the service id of your applications client provider implementation.
* Implement your custom event subscribers to listen on your own socket events ([Getting started](#getting-started)).
* Use the `{{ p2_ratchet_client }}` tag within your templates to enable the frontend websocket client.
* Write your client side event handler scripts. See the [Javascript API](#javascript-api) section for more detail.
* Open a terminal and start the server `app/console ratchet:start`

### Socket Events

#### Client:
| Event                 | Payload            | Description           |
| --------------------- | ------------------ | ----------------------|
| `socket.auth.request` | `{ token }`        | This event is dispatched by the javascript client directly after the socket.open event occurred at the client socket |

#### Server:
| Event                 | Payload            | Description           |
| --------------------- | ------------------ | ----------------------|
| `socket.auth.success` | `{ client }`       | Fired on a successful authentication request. The payload contains the public user data returned by ClientInterface::jsonSerialize() |
| `socket.auth.failure` | `{ errors }`       | Fired when an error occurred during the authentication process. The payload contains the errors returned. |


### Javascript API

```javascript

// create the websocket
var socket = new Ratchet('ws://localhost:8080');

// implement your custom event handlers
socket.on('my.custom.event', function(data) {
    // ...
});

```

### Example

This is an example console chat application demonstrating the usage of this bundle.

```php
<?php

namespace Acme\Bundle\ChatBundle\WebSocket;

use P2\Bundle\RatchetBundle\Event\MessageEvent;
use P2\Bundle\RatchetBundle\Socket\Payload\EventPayload;

class ConsoleChat implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            ChatEvents::CHAT_SEND => 'onSendMessage'
        );
    }

    public function onSendMessage(MessageEvent $event)
    {
        $event->getConnection()->broadcast($event->getPayload());

        $event->getConnection()->emit(
            new EventPayload(
                ChatEvents::CHAT_MESSAGE_SEND,
                $event->getPayload()->getData()
            )
        );
    }
}

```

The respective twig template may look like this:
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Console Chat</title>
</head>
<body>
    <form id="send_message" method="post" action="">
        <input type="text" name="message">
        <button type="submit" name="send">send</button>
    </form>
    {{ p2_ratchet_client }}
</body>
</html>
```
