(function() {

    'use strict';

    if (! 'WebSocket' in window) {
        throw new Error('WebSockets are not supported by your browser.');
    }

    if (! 'JSON' in window) {
        throw new Error('No JSON support available by your browser.');
    }

    /**
     * @type Object
     */
    var socketEventHandler = {};

    /**
     * @type WebSocket
     */
    var socket;

    /**
     *
     * @param {string} uri
     * @constructor
     */
    var Ratchet = function(uri) {

        socket = new WebSocket(uri);
        var self = this;

        socket.onmessage = function(e) {
            var data = JSON.parse(e.data);
            if (data.event && data.data) {
                var handlers = socketEventHandler[data.event];
                if (handlers && handlers.length) {
                    for (var i = 0, len = handlers.length; i < len; i++) {
                        handlers[i].call(self, data.data);
                    }
                }
            }
        };

        socket.onopen = function(e) {
            self.emit('socket.auth.request', window.p2_ratchet_access_token);
        };

        socket.onclose = this.onClose;
        socket.onerror = this.onError;
    };

    Ratchet.prototype.authenticated = false;

    Ratchet.prototype.onClose = function() {};
    Ratchet.prototype.onError = function() {};

    Ratchet.prototype.emit = function(event, data) {
        try {
            var encoded = JSON.stringify({ event: event, data: data });
            socket.send(encoded);

            return true;
        } catch (e) {
            console.error(e.message);
        }

        return false;
    };

    Ratchet.registerEventHandler = function(event, handler) {
        Ratchet.prototype.on.call(this, event, handler);
    };

    Ratchet.prototype.on = function(event, handler) {
        var eventHandlers = socketEventHandler[event];

        if (! eventHandlers) {
            eventHandlers = socketEventHandler[event] = [];
        }

        eventHandlers[eventHandlers.length] = handler;
    };

    Ratchet.registerEventHandler('socket.auth.success', function(client) {
        this.client = client;
    });

    window.Ratchet = Ratchet;

}).call(window);