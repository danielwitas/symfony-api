import axios from 'axios'

export default {
    state: {
        templates: [],
    },
    getters: {
        USER_TEMPLATES: state => {
            return state.templates
        },
        TEMPLATE_TITLE: state => index => {
            return state.templates.templates.find(element => element.id === index).name
        },

    },
    mutations: {
        SET_TEMPLATES: (state, payload) => {
            state.templates = payload
        },
    },
    actions: {
        GET_TEMPLATES: async ({commit}, payload = 1) => {
            let {data} = await axios.get(`templates?page=${payload}`)
            commit('SET_TEMPLATES', data)
        },
        POST_TEMPLATE: ({commit}, payload) => {
            return new Promise((resolve, reject) => {
                axios
                    .post('templates', payload)
                    .then(response => {
                        if (response.status === 201) {
                            resolve(response)
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        DELETE_TEMPLATE: (context, id) => {
            return new Promise((resolve, reject) => {
                axios
                    .delete(`templates/${id}`)
                    .then(response => {
                        resolve(response)
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },
        EDIT_TEMPLATE: (context, payload) => {
            return new Promise((resolve, reject) => {
                axios
                    .patch(`templates/${payload.id}`, {
                        name: payload.name
                    })
                    .then(response => {
                        resolve(response)
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },
        SEARCH_TEMPLATES: (context, payload) => {
            return new Promise((resolve, reject) => {
                axios
                    .get(`templates?filter=${payload}`)
                    .then(response => {
                        if(response.status === 200) {
                            context.commit('SET_TEMPLATES', response.data)
                            resolve(response)
                        }
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        }

    }
}