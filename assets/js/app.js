import axios from 'axios'
import gsap from 'gsap/all'
import '../css/app.css';
import Vue from 'vue'
import store from './stores/store'
import VueRouter from "vue-router";
import vuex from 'vuex'
import vuetify from './plugins/vuetify'

import 'material-design-icons/iconfont/material-icons.css';

import Login from "./components/Auth/Login";
import Register from "./components/Auth/Register";
import Logout from "./components/Auth/Logout";
import App from './App'
import ProductsModal from "./components/Template/ProductsModal";
import Templates from "./components/TemplateList/TemplateList";
import singleTemplate from "./components/Template/Template";
import ProductList from "./components/ProductList/ProductList"
import ProductEdit from "./components/ProductList/ProductEdit";
import TemplateCreateModal from "./components/TemplateList/TemplateCreate";
import TemplateEditName from "./components/Template/TemplateEditName";
import Account from "./components/Account/Account";
import PasswordReset from "./components/Auth/PasswordReset";
import ProductAdd from "./components/ProductList/ProductAdd";

Vue.config.productionTip = false
Vue.use(vuex);
Vue.use(VueRouter);

axios.defaults.baseURL = 'https://127.0.0.1:8000/api/'
Vue.prototype.$http = axios;
const token = localStorage.getItem('token')
if (token) {
    Vue.prototype.$http.defaults.headers.common['Authorization'] = token
}

const routes = [

    {
        path: "*",
        redirect: "/templates"
    },
    {
        path: "/",
        redirect: "/login"
    },

    {
        path: '/password-reset',
        component: PasswordReset,
        name: 'passwordReset',


    },
    {
        path: '/logout',
        component: Logout,
        name: 'logout',
        meta: {
            requiresAuth: true
        },
    },
    {
        path: '/login',
        component: Login,
        name: 'login',
    },
    {
        path: '/register',
        component: Register,
        name: 'register',
        meta: {
            requiresVisitor: true
        },
    },
    {
        path: '/account',
        component: Account,
        name: 'account',
        meta: {
            requiresAuth: true
        },
    },
    {
        path: '/templates',
        component: Templates,
        name: 'templates',
        meta: {
            requiresAuth: true
        },
        children: [
            {
                path: '/templates/create',
                components: {templateCreateModal: TemplateCreateModal},
                name: 'templateCreateModal',
                meta: {
                    requiresAuth: true
                },
            },
        ]
    },
    {
        path: '/products',
        component: ProductList,
        name: 'products',
        meta: {
            requiresAuth: true
        },
        children: [
            {
                path: '/products/create',
                components: {ProductAdd: ProductAdd},
                name: 'ProductAdd',
                meta: {
                    requiresAuth: true
                },
            },
            {
                path: '/products/:id',
                components: {ProductEdit: ProductEdit},
                name: 'ProductEdit',
                meta: {
                    requiresAuth: true
                },
            }
        ]
    },
    {
        path: '/templates/:id',
        component: singleTemplate,
        name: 'singleTemplate',
        meta: {
            requiresAuth: true
        },
        children: [
            {
                path: '/templates/:id',
                components: {addTemplateProduct: ProductsModal},
                name: 'addTemplateProduct',
                meta: {
                    requiresAuth: true
                },
            },
            {
                path: '/templates/:id',
                components: {editTemplateName: TemplateEditName},
                name: 'editTemplateName',
                meta: {
                    requiresAuth: true
                },
            }
        ]
    }

]

const router = new VueRouter({
    mode: 'history',
    routes,
    base: '/',

})

axios.interceptors.response.use(
    function (response) {
        return response
    },
    function (error) {
        switch (error.response.status) {
            case 401:
                store.dispatch('LOGOUT')
                break
            case 500:
                store.commit('SET_UNKNOWN_ERROR_NOTIFICATION')
                break;
            default:
                return Promise.reject(error);
        }
    });

router.beforeEach((to, from, next) => {
    if(to.matched.some(record => record.meta.requiresAuth)) {
        if (store.getters.GET_TOKEN) {
            next()
            return
        }
        next('/login')
    } else {
        next()
    }
})

new Vue({
    vuetify,
    store,
    router,
    theme: {dark: true},
    vuex,
    el: '#app',
    components: {App}
})

