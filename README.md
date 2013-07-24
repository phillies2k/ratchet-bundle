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
* Set the `provider` setting to the service id of your applications ClientProviderInterface implementation.
* Implement your custom event subscribers to listen on your own socket events ([Getting started][getting-started]).
* Use the `{{ p2_ratchet_client }}` tag within your templates to enable the frontend websocket client.
* Write your client side event handler scripts. See the [Javascript API][javascript-api] section for more detail.
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
| `socket.auth.failure` | `{ error: "..." }` | Fired when an error occurred during the authentication process. The payload contains the error returned. |

### Getting started



```php
<?php

namespace Acme\Bundle\ChatBundle\WebSocket;

use P2\Bundle\RatchetBundle\Event\MessageEvent;

class SimpleChat implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            ChatEvents::CHAT_SEND => 'onSendMessage'
        );
    }

    public function onSendMessage(MessageEvent $event)
    {
        $event->getConnection()->broadcast(ChatEvents::CHAT_MESSAGE, $event->getPayload());
    }
}

```

```javascript
$(function() {

    var socket = new Ratchet('ws://localhost:8080');

    socket.on('chat.message', function(msg, client) {
        console.log('[' + client.username + ']: ' + msg);
    });
});
```

### Javascript API

```javascript

// create the websocket
var socket = new Ratchet('ws://localhost:8080');

// implement your custom event handlers
socket.on('my.custom.event', function(data) {
    console.log();
});

```