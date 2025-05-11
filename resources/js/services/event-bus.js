// Simple event bus for Vue 3 using mitt
import mitt from 'mitt';

const emitter = mitt();

export default {
    // Toast notifications
    toast: {
        success: (message) => emitter.emit('toast', { type: 'success', message }),
        error: (message) => emitter.emit('toast', { type: 'error', message }),
        info: (message) => emitter.emit('toast', { type: 'info', message }),
        warning: (message) => emitter.emit('toast', { type: 'warning', message }),
    },
    
    // Listen for events
    on: (event, callback) => emitter.on(event, callback),
    
    // Remove event listener
    off: (event, callback) => emitter.off(event, callback)
}; 