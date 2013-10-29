P2RatchetBundle
===============

Version: **1.0.6**


### Installation

    "require": {
        "p2/ratchet-bundle": "dev-master"
    }


### Configuration

    p2_ratchet:
        provider: ~             # The client provider to use, null for default
        address: 0.0.0.0        # The address to receive sockets on (0.0.0.0 means receive from any)
        port: 8080              # The port the socket server will listen on


### Usage

* Implement the [ClientInterface](WebSocket/Client/ClientInterface.php) in your applications user model or document.
* Implement the [ClientProviderInterface](WebSocket/Client/ClientProviderInterface.php) in your applications user provider or managing repository.
* Set the `provider` setting to the service id of your applications client provider implementation or leave blank for the default anonymous provider.
* Implement the [ApplicationInterface](WebSocket/Server/ApplicationInterface.php) to listen on your own socket events ([Getting started](#getting-started)).
* Use the `{{ websocket_client(token, debug) }}` macro within your templates to enable the frontend websocket client.
* Write your client side event handler scripts. See the [Javascript API](#javascript-api) section for more detail.
* Open a terminal and start the server `app/console socket:server:start`. By default it will accept connection from *:8080 (see [Command Line Tool](#command-line-tool))


### Getting started

The [ApplicationInterface](WebSocket/Server/ApplicationInterface.php) acts only as an alias for symfony`s EventSubscriberInterface. Its used to detect websocket event subscribers explicitly.

Write your application as you would write a common event subscriber. The event handler methods will receive exactly one argument: a [ConnectionEvent](WebSocket/ConnectionEvent.php) instance, containing information about the socket connection and the payload (see [ConnectionInterface](WebSocket/Connection/ConnectionInterface.php) and [Payload](WebSocket/Payload.php) for more details).

```php
# src/Acme/Bundle/ChatBundle/WebSocket/Application.php
<?php

namespace Acme\Bundle\ChatBundle\WebSocket;

use P2\Bundle\RatchetBundle\WebSocket\Server\ApplicationInterface;

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

Create a service definition for your websocket application. Tag your service definition with `kernel.event_subscriber` and `p2_ratchet.application` to register the application to the server.

The service definition may look like this:
```yaml
# src/Acme/Bundle/ChatBundle/Resources/config/services.yml
services:

    # websocket chat application
    websocket_chat:
        class: Acme\Bundle\ChatBundle\WebSocket\ChatApplication
        tags:
            - { name: kernel.event_subscriber }
            - { name: p2_ratchet.application }
```


### Command Line Tool

```bash
php app/console socket:server:start [port] [address]
```


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


### Hook-in Points

The bundle allows you to hook into the react event loop to add your own periodic timers. All you have to do is to create a class implementing [PeriodicTimerInterface](WebSocket/Server/Loop/PeriodicTimerInterface.php) and to tag it as "p2_ratchet.periodic_timer".
Then the timers will be added to the loop on server startup.

##### Example:
```php
# src/Acme/Bundle/ChatBundle/WebSocket/Loop/CustomTimer.php
<?php

namespace Acme\Bundle\ChatBundle\WebSocket\Loop;

use P2\Bundle\RatchetBundle\WebSocket\Server\Loop\PeriodicTimerInterface;

class CustomTimer implements PeriodicTimerInterface
{
    /**
     * Returns the interval for this timer
     *
     * @return int
     */
    public function getInterval()
    {
        return 60; // execute this timer once per minute
    }

    /**
     * Returns the callback.
     *
     * @return callable
     */
    public function getCallback()
    {
        return function() {
            // do something
        };
    }

    /**
     * Returns a unique name for this timer.
     *
     * @return string
     */
    public function getName()
    {
        return 'custom_timer';
    }
}
```

##### Service
```
    # my custom timer
    acme_chat.websocket.loop.custom_timer:
        class: %acme_chat.websocket.loop.custom_timer%
        tags:
            - { name: p2_ratchet.periodic_timer }
```


### Javascript API

The api represents just a simple wrapper for the native javascript WebSocket to ease developers life. It basically implements the basic communication logic with the socket server.

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


### Simple chat application example

The application code:
```php
# src/Acme/Bundle/ChatBundle/WebSocket/ChatApplication.php
<?php

namespace Acme\Bundle\ChatBundle\WebSocket;

use P2\Bundle\RatchetBundle\WebSocket\ConnectionEvent;
use P2\Bundle\RatchetBundle\WebSocket\Payload;
use P2\Bundle\RatchetBundle\WebSocket\Server\ApplicationInterface;

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

{% import 'P2RatchetBundle::client.html.twig' as p2_ratchet %}

{% block stylesheets %}

    <style type="text/css">
        #chat { width: 760px; margin: 0 auto; }
        #chat_frame { overflow: hidden; position: relative; height: 320px; line-height: 16px; font-size: 12px; font-family: monospace; border: 1px solid #a7a7a7; margin-bottom: 10px; border-radius: 10px; }
        #chat_buffer { line-height: 16px; font-size: 12px; font-family: monospace; min-height: 300px; position: absolute; bottom: 0; left: 0; padding: 10px 20px; width: 720px; }
        #chat_buffer > p { margin: 0; padding: 0; font-size: inherit; line-height: inherit; color: dimgray; }
        #chat_buffer > p > em { font-weight: bold; color: deepskyblue; }
        #chat_buffer > p > span { color: dimgray; }
        #send_message { background: #f5f5f5; border: 1px solid darkgray; border-radius: 10px; padding: 20px; }
        #message { padding: 8px 5px; border: 1px solid darkgray; font-size: 14px; line-height: 16px; width: 100%; box-sizing: border-box; }
    </style>

{% endblock %}

{% block body %}

    <section id="chat">
        <div id="chat_frame">
            <div id="chat_buffer"></div>
        </div>

        <form id="send_message" method="post" action="">
            <input type="text" id="message" name="message" placeholder="...">
        </form>

    </section>

{% endblock %}

{% block javascripts %}

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

    {{ p2_ratchet.websocket_client(app.user.accessToken|default(''), app.debug) }}

    <script type="text/javascript">
        $(function() {

            function appendChatMessage(response) {
                $('#chat_buffer').append(
                        $('<p>[<em>' + response.client.username + '</em>]: <span>' + response.message + '</span></p>')
                );
            }

            var server = new Ratchet('ws://localhost:8080');

            // bind listeners
            server.on('chat.message.sent', appendChatMessage);
            server.on('chat.message', appendChatMessage);

            $('#send_message').submit(function(e) {
                e.preventDefault();

                var message = $('#message');
                var value = message.val();

                if (value.length) {
                    server.emit('chat.send', value);
                    message.val("");
                }

                return false;
            });
        });
    </script>

{% endblock %}


```
