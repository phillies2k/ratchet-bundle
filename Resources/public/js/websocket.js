(function() {

    if (! 'WebSocket' in window) {
        throw new Error('WebSockets are not supported by your browser.');
    }

    var handlers = {};

    var socket;

    var Ratchet = function(uri) {
        socket = new WebSocket(uri);
        var self = this;

        socket.onmessage = function(e) {
            var data = JSON.parse(e.data);
            if (data.event && data.data) {
                var eventHandlers = handlers[data.event];
                if (eventHandlers && eventHandlers.length) {
                    for (var i = 0, len = eventHandlers.length; i < len; i++) {
                        eventHandlers[i].call(self, data.data);
                    }
                }
            }
        };
    };

    Ratchet.prototype.emit = function(event, data) {
        socket.send(JSON.stringify({ event: event, data: data }));
    };

    Ratchet.prototype.addListener = function(event, handler) {
        var eventHandlers = handlers[event];

        if (! eventHandlers) {
            eventHandlers = handlers[event] = [];
        }

        eventHandlers[eventHandlers.length] = handler;
    };

    window.Ratchet = new Ratchet('ws://localhost:8080');

}).call(window);