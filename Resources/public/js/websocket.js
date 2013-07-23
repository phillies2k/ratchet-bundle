(function() {

    'use strict';

    if (! 'WebSocket' in window) {
        throw new Error('WebSockets are not supported by your browser.');
    }

    if (! 'JSON' in window) {
        throw new Error('No JSON support by your browser.');
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
            return onMessage.call(self, e, this);
        };

        socket.onopen = function(e) {
            return onOpen.call(self, e, this);
        };

        socket.onclose = function(e) {
            return onClose.call(self, e, this);
        };

        socket.onerror = function(e) {
            return onError.call(self, e, this);
        };
    };

    Ratchet.prototype.authenticated = false;

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
        var eventHandlers = handlers[event];

        if (! eventHandlers) {
            eventHandlers = handlers[event] = [];
        }

        eventHandlers[eventHandlers.length] = handler;
    };

    /**
     * private methods
     */

    function onMessage(e, sock) {
        var data = JSON.parse(e.data);
        if (data.event && data.data) {
            var handlers = socketEventHandler[data.event];
            if (handlers && handlers.length) {
                for (var i = 0, len = handlers.length; i < len; i++) {
                    handlers[i].call(this, data.data);
                }
            }
        }
    }

    function onOpen(e) {}
    function onClose(e) {}
    function onError(e) {}

    Ratchet.registerEventHandler('socket.open', function() {
        var token = window.wsAccessToken;
        if (token && token.length) {
            this.emit('socket.auth.request', token);
        }
    });

    window.Ratchet = Ratchet;

}).call(window);