import Vue from 'vue'
import Vuex from 'vuex'

import data from '../modules/templates'
import ui from "../modules/ui"
import user from '../modules/user'
import products from "../modules/products";
import notification from "../modules/notification";
Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        ui: ui,
        data: data,
        user: user,
        products: products,
        notification: notification

    }
});