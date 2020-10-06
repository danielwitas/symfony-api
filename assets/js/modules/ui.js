export default {
    state: {
        displaySearchList: false,
        displayProductSearchList: false,
        displaySingleTemplateAutocomplete: false
    },
    getters: {
        DISPLAY_SEARCH_LIST: state => {
            return state.displaySearchList
        },
        DISPLAY_PRODUCT_SEARCH_LIST: state => {
          return state.displayProductSearchList
        },
        DISPLAY_SINGLE_TEMPLATE_AUTOCOMPLETE: state => {
            return state.displaySingleTemplateAutocomplete
        }
    },
    mutations: {
        SET_DISPLAY_SEARCH_LIST: (state, payload) => {
            state.displaySearchList = payload
        },
        SET_DISPLAY_PRODUCT_SEARCH_LIST: (state, payload) => {
            state.displayProductSearchList = payload
        },
        SET_DISPLAY_SINGLE_TEMPLATE_AUTOCOMPLETE: (state, payload) => {
            state.displaySingleTemplateAutocomplete = payload
        }
    },
    actions: {}
}