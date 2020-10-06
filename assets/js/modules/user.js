import axios from 'axios'

export default {
    state: {
        token: localStorage.getItem('token') || ''
    },
    getters: {
        GET_TOKEN: state => {
            return !!state.token
        }
    },
    mutations: {
        SET_TOKEN: (state, payload) => {
            state.token = payload
        },
        LOGOUT: state => {
            state.token = ''
        }
    },
    actions: {
        LOGIN: (context, payload) => {
            return new Promise((resolve, reject) => {
                axios
                    .post(`login_check`, payload)
                    .then(response => {
                        if (response && response.hasOwnProperty('status') && response.status === 200) {
                            localStorage.setItem('token', response.data.token)
                            axios.defaults.headers.common['Authorization'] = response.data.token
                            context.commit('SET_TOKEN', response.data.token)
                            resolve(response)
                        }
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        REGISTER: (context, payload) => {
            return new Promise((resolve, reject) => {
                axios.post(`register`, payload)
                    .then(response => {
                        if (response.status === 200) {
                            resolve(response)
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        LOGOUT: (context) => {
            return new Promise((resolve, reject) => {
                localStorage.removeItem('token')
                context.commit('LOGOUT')
                delete axios.defaults.headers.common['Authorization']
                resolve()

            })
        },
        CHANGE_PASSWORD: (context, payload) => {
            return new Promise((resolve, reject) => {
                axios
                    .patch(`change-password`, {
                        password: payload.password,
                        newPassword: payload.newPassword,
                        repeatNewPassword: payload.repeatNewPassword
                    })
                    .then(response => {
                        if(response.status===200) {
                            resolve(response)
                        }
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },
        CHANGE_EMAIL: (context, email) => {
            return new Promise((resolve, reject) => {
                axios
                    .patch(`change-email`, {
                        email: email
                    })
                    .then(response => {
                        resolve(response)
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        PASSWORD_RESET: (context, email) => {
            return new Promise((resolve, reject) => {
                axios
                    .post(`password-reset`, {
                        email: email
                    })
                    .then(response => {
                        if(response.status===200) {
                            resolve(response)
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        }
    },
}
