P2RatchetBundle
===============

Version: **1.0.0**


### Installation

    "require": {
        "p2/ratchet-bundle": "dev-master"
    }

### Configuration

    p2_ratchet:
        provider: my_custom_client_provider         # The ClientProviderInterface implementation the service should use
        address: 0.0.0.0                            # The address to receive sockets on (0.0.0.0 means receive from any)
        port: 8080                                  # The port the socket server will listen on

### Usage

* Implement the [ClientInterface](Socket/ClientInterface.php) in your applications user model or document.
* Implement the [ClientProviderInterface](Socket/ClientProviderInterface.php) in your applications user provider or managing repository.
* Set the `provider` setting to the service id of your applications ClientProviderInterface implementation.
* Use `{% include '@P2RatchetBundle::client.html.twig' %}` within your templates to enable the frontend websocket client.
* Open a terminal and start the server `app/console ratchet:start`

### Socket Events

#### Client:
| Event                 | Payload            | Description           |
| --------------------- | ------------------ | ----------------------|
| `socket.auth.request` | `{ accessToken }`  | This event is dispatched by the javascript client directly after the socket.open event occurred at the client socket |

#### Server:
| Event                 | Payload            | Description           |
| --------------------- | ------------------ | ----------------------|
| `socket.auth.success` | `{ client }`       | Fired on a successful authentication request. The payload contains the public user data returned by ClientInterface::jsonSerialize() |
| `socket.auth.failure` | `{ error: "..." }` | Fired when an error occurred during the authentication process. The payload contains the error returned. |


### Javascript API

```javascript

// create the websocket
var socket = new Ratchet('ws://localhost:8080');

// implement your custom event handlers
socket.on('my.custom.event', function(data) {
    console.log();
});

```