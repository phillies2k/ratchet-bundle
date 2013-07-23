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
            try {
                var data = JSON.parse(e.data);
                if (data.event && data.data) {
                    invokeEventHandlers.call(self, data.event, data.data, e);
                }
            } catch (e) {
                if (Ratchet.debug) {
                    console.error(e);
                }
            }
        };

        socket.onopen = function(e) {
            invokeEventHandlers.call(self, 'socket.open', null, e);
            self.emit('socket.auth.request', window.p2_ratchet_access_token);
        };

        socket.onclose = this.onClose;
        socket.onerror = this.onError;
    };

    Ratchet.debug = false;
    Ratchet.prototype.authenticated = false;

    Ratchet.prototype.onClose = function() {};
    Ratchet.prototype.onError = function() {};

    Ratchet.prototype.emit = function(event, data) {
        try {
            var encoded = JSON.stringify({ event: event, data: data });
            socket.send(encoded);

            return true;
        } catch (e) {
            if (Ratchet.debug) {
                console.error(e);
            }
        }

        return false;
    };

    Ratchet.registerEventHandler = function(event, handler) {
        var eventHandlers = socketEventHandler[event];

        if (! eventHandlers) {
            eventHandlers = socketEventHandler[event] = [];
        }

        eventHandlers[eventHandlers.length] = handler;
        Ratchet.prototype.on.call(this, event, handler);
    };

    Ratchet.prototype.on = Ratchet.registerEventHandler;

    Ratchet.registerEventHandler('socket.auth.success', function(client) {
        this.authenticated = true;
        this.client = client;
    });

    window.Ratchet = Ratchet;

    function invokeEventHandlers(event, data, e) {
        var handlers = socketEventHandler[event];
        if (handlers && handlers.length) {
            for (var i = 0, len = handlers.length; i < len; i++) {
                handlers[i].call(this, data, event, e);
            }
        }
    }

}).call(window);