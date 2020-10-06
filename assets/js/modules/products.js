import axios from 'axios'

export default {
    state: {
        products: [],
        userProducts: [],
        singleProduct: null,
    },
    getters: {
        GET_PRODUCT: state => index => {
            return state.userProducts.products.find(element => element.id === index)
        },
        TEMPLATE_PRODUCTS: state => {
            return state.products
        },
        USER_PRODUCTS: state => {
            return state.userProducts
        },
        GET_SINGLE_PRODUCT: state => {
            return state.singleProduct
        }

    },
    mutations: {
        SET_PRODUCTS: (state, payload) => {
            state.products = payload.data;
        },
        SET_USER_PRODUCTS: (state, payload) => {
            state.userProducts = payload.data
        },
        SET_SINGLE_PRODUCT: (state, payload) => {
            state.singleProduct = payload.data
        }
    },
    actions: {
        GET_TEMPLATE_PRODUCTS: async (context, payload) => {
            let response = await axios.get(`templates/${payload}/products`)
            context.commit('SET_PRODUCTS', response)
        },
        GET_USER_PRODUCTS: async (context, payload = 1) => {
            let response = await axios.get(`products?page=${payload}`)
            context.commit('SET_USER_PRODUCTS', response)
        },
        GET_SINGLE_PRODUCT: async (context, payload) => {
            let response = await axios.get(`products/${payload}`)
            console.log(response)
            context.commit('SET_SINGLE_PRODUCT', response)
        },
        DELETE_PRODUCT: (context, id) => {
            return new Promise((resolve, reject) => {
                axios
                    .delete(`products/${id}`)
                    .then(response => {
                        resolve(response)
                    })
                    .catch(error => {
                        reject(error)
                    })
            })

        },
        EDIT_PRODUCT: (context, payload) => {
            return new Promise((resolve, reject) => {
                axios
                    .patch(`products/${payload.id}`, {
                        name: payload.name,
                        kcal: payload.kcal,
                        weight: payload.weight,
                        protein: payload.protein,
                        carbs: payload.carbs,
                        fat: payload.fat,

                    })
                    .then(response => {
                        resolve(response)
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },
        POST_TEMPLATE_PRODUCT: (context, payload) => {
            return new Promise((resolve, reject) => {
                axios
                    .post(`templates/${payload.templateId}/products`, {
                        name: payload.productName,
                        kcal: payload.productKcal,
                        protein: payload.productProtein,
                        carbs: payload.productCarbs,
                        fat: payload.productFat,

                    })
                    .then(response => {
                        resolve(response)
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },
        POST_USER_PRODUCT: (context, payload) => {
            return new Promise((resolve, reject) => {
                axios
                    .post(`products`, {
                        name: payload.productName,
                        kcal: payload.productKcal,
                        protein: payload.productProtein,
                        carbs: payload.productCarbs,
                        fat: payload.productFat,
                    })
                    .then(response => {
                        resolve(response)
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },
        SEARCH_USER_PRODUCTS: (context, payload) => {
            return new Promise((resolve, reject) => {
                axios
                    .get(`products?filter=${payload}`)
                    .then(response => {
                        if(response.status === 200) {
                            context.commit('SET_USER_PRODUCTS', response)
                            resolve(response)
                        }
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },

    }
}