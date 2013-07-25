P2RatchetBundle
===============

Version: **1.0.4**


### Installation

    "require": {
        "p2/ratchet-bundle": "dev-master"
    }


### Configuration

    p2_ratchet:
        provider: my_provider   # The ClientProviderInterface implementation
        address: 0.0.0.0        # The address to receive sockets on (0.0.0.0 means receive from any)
        port: 8080              # The port the socket server will listen on

### Command Line Tool

```bash
php app/console ratchet:start [port] [address]
```

### Usage

* Implement the [ClientInterface](Socket/ClientInterface.php) in your applications user model or document.
* Implement the [ClientProviderInterface](Socket/ClientProviderInterface.php) in your applications user provider or managing repository.
* Set the `provider` setting to the service id of your applications client provider implementation.
* Implement the [ApplicationInterface](Socket/ApplicationInterface) to listen on your own socket events ([Getting started](#getting-started)).
* Use the `{{ p2_ratchet_client(debug, user) }}` twig function within your templates to enable the frontend websocket client.
* Write your client side event handler scripts. See the [Javascript API](#javascript-api) section for more detail.
* Open a terminal and start the server `app/console ratchet:start`


### Events

| Event          | Description                                          |
|----------------|----------------------------------------------------- |
| SOCKET_OPEN    | Fired when the server received a new connection.     |
| SOCKET_CLOSE   | Fired when the socket connection was closed.         |
| SOCKET_ERROR   | Fired when an error occurred during transmission.    |
| SOCKET_MESSAGE | Fired when a message was send through a connection.  |


#### WebSocket Events

##### Client:
| Event                 | Payload            | Description           |
| --------------------- | ------------------ | ----------------------|
| `socket.auth.request` | `{ token }`        | This event is dispatched by the javascript client directly after the socket connection was opened. Its attempt is to send the value of `p2_ratchet_access_token` to the server to identify the websocket client within your application. |

##### Server:
| Event                 | Payload            | Description           |
| --------------------- | ------------------ | ----------------------|
| `socket.auth.success` | `{ client }`       | Fired on a successful authentication request. The payload contains the public user data returned by ClientInterface::jsonSerialize() |
| `socket.auth.failure` | `{ errors }`       | Fired when an error occurred during the authentication process. The payload contains the errors returned. |


### Getting started

The [ApplicationInterface](Socket/ApplicationInterface) acts only as an alias for symfony`s EventSubscriberInterface. Its used to detect websocket event subscribers explicitly.

Write your application as you would write a common event subscriber. The event handler methods will receive exactly one argument: a [MessageEvent](Socket/Event/MessageEvent) instance, containing information about the socket connection and the payload (see [ConnectionInterface](Socket/Connection/ConnectionInterface) and [EventPayload](Socket/Payload/EventPayload) for more details).

```php
# src/Acme/Bundle/ChatBundle/WebSocket/Application.php
<?php

namespace Acme\Bundle\ChatBundle\WebSocket;

use P2\Bundle\RatchetBundle\Socket\ApplicationInterface;

class Application implements ApplicationInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'acme.websocket.some.event' => 'onSomeEvent'
            // ...
        );
    }

    // put your event handler code here ...
}

```

#### Service DI Configuration

Create a service definition for your websocket application. Tag your service definition with `p2_ratchet.application` to
register the application at the socket server.

The service definition may look like this:
```yaml
# src/Acme/Bundle/ChatBundle/Resources/config/services.yml
services:

    # websocket chat application
    websocket_chat:
        class: Acme\Bundle\ChatBundle\WebSocket\ChatApplication
        tags:
            - { name: p2_ratchet.application }
```


### Javascript API

The api represents just a simple wrapper for the native javascript WebSocket to ease developers life. It basically
implements the basic communication logic with the socket server.

```javascript

// create the websocket
var socket = new Ratchet('ws://localhost:8080');

// implement your custom event handlers
socket.on('my.custom.event', function(data) {
    // ...
});

// emit an event
socket.emit('some.event', {
    // event data...
});

```


### Simple console chat application example

The application code:
```php
# src/Acme/Bundle/ChatBundle/WebSocket/ChatApplication.php
<?php

namespace Acme\Bundle\ChatBundle\WebSocket;

use P2\Bundle\RatchetBundle\Event\MessageEvent;
use P2\Bundle\RatchetBundle\Socket\ApplicationInterface;
use P2\Bundle\RatchetBundle\Socket\Payload\EventPayload;

class ChatApplication implements ApplicationInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'chat.send' => 'onSendMessage'
        );
    }

    public function onSendMessage(MessageEvent $event)
    {
        $client = $event->getConnection()->getClient()->jsonSerialize();
        $message = $event->getPayload()->getData();

        $event->getConnection()->broadcast(
            new EventPayload(
                'chat.message',
                array(
                    'client' => $client,
                    'message' => $message
                )
            )
        );

        $event->getConnection()->emit(
            new EventPayload(
                'chat.message.sent',
                array(
                    'client' => $client,
                    'message' => $message
                )
            )
        );
    }
}

```

The respective twig template may look like this:
```html
# src/Acme/Bundle/ChatBundle/Resources/views/chat.html.twig
{% extends '::base.html.twig' %}

{% block body %}

    <div id="chat_frame"></div>
    <form id="send_message" method="post" action="">
        <input type="text" id="message" name="message">
        <button type="submit" name="send">send</button>
    </form>

    {{ p2_ratchet_client(app.debug, app.user) }}

    <script type="text/javascript">
        $(function() {

            function appendChatMessage(response) {
                $('#chat_frame').append(
                    $('<p>[<em>%s</em>]: <span>%s</span></p>'.replace(
                        /%s/g,
                        [ response.client.username, response.message ]
                    ))
                );
            }

            var server = new Ratchet('ws://localhost:8080');

            // bind listeners
            server.on('chat.message.sent', appendChatMessage);
            server.on('chat.message', appendChatMessage);

            $('#send_message').submit(function(e) {
                e.preventDefault();

                var message = $('#message').val();

                if (message.length) {
                    server.emit('chat.send', $('#message').val());
                    $('#message').val("");
                }

                return false;
            });
        });
    </script>

{% endblock %}

```
