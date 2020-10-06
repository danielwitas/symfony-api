export default {
    state: {
        notification: {
            display: false,
            text: 'Notification placeholder',
            timeout: 3000,
            class: 'success'
        },
    },
    getters: {
        NOTIFICATION: state => {
            return state.notification
        },
    },
    mutations: {
        SET_NOTIFICATION: (state, notification) => {
            state.notification.display = notification.display
            state.notification.text = notification.text
            state.notification.class = notification.class
        },
        SET_UNKNOWN_ERROR_NOTIFICATION: (state) => {
            state.notification.display = true
            state.notification.text = 'Oops. Something went wrong!'
            state.notification.class = 'error'
        },
        SET_ERROR_NOTIFICATION: (state, message) => {
            state.notification.display = true
            state.notification.text = message
            state.notification.class = 'error'
        },
        SET_SUCCESS_NOTIFICATION: (state, message) => {
            state.notification.display = true
            state.notification.text = message
            state.notification.class = 'success'
        },
    },
    actions: {}
}