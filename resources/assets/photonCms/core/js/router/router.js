import { config } from '_/config/config';

import { customRoutes } from '~/router/router';

import Vue from 'vue';

import VueRouter from 'vue-router';

import Echo from 'laravel-echo';

import _ from 'lodash';

import Pusher from 'pusher-js';

import {
    pLog,
    pWarn,
} from '_/helpers/logger';

import { store } from '_/vuex/store';

import { storage } from '_/services/storage';

import {
    Admin,
    ConfirmReset,
    Dashboard,
    EmailConfirmation,
    EmailChangeConfirmation,
    ErrorPage,
    ForgotPassword,
    Generator,
    InvalidLicense,
    Login,
    MenuEditor,
    MenuItemsEditor,
    Notifications,
    PasswordReset,
    Register,
    Registered,
    SetNewPassword
} from '@/route-components';

Vue.use(VueRouter);

const routes = [
    ...customRoutes,
    {
        path: '/',
        component: Dashboard,
        meta: {
            onlyIfAuthenticated: true,
            sidebarWidthGroup: 'default',
        }
    },
    {
        path: '/login',
        component: Login,
        meta: {
            noLicenseCheckRequired: false,
            onlyIfNotAuthenticated: true,
            sidebarWidthGroup: 'default',
        }
    },
    {
        path: '/register',
        component: Register,
        meta: {
            noLicenseCheckRequired: true,
            onlyIfNotAuthenticated: true,
            sidebarWidthGroup: 'default',
        }
    },
    {
        path: '/register/:invitationToken',
        component: Register,
        meta: {
            noLicenseCheckRequired: true,
            onlyIfNotAuthenticated: true,
            sidebarWidthGroup: 'default',
        }
    },
    {
        path: '/registered',
        component: Registered,
        meta: {
            noLicenseCheckRequired: true,
            onlyIfNotAuthenticated: true,
            sidebarWidthGroup: 'default',
        },
    },
    {
        path: '/confirm-email/:emailToken',
        component: EmailConfirmation,
        meta: {
            noLicenseCheckRequired: true,
        },
    },
    {
        path: '/confirm-email-change/:emailToken/:userId',
        component: EmailChangeConfirmation,
        meta: {
            onlyIfAuthenticated: true,
            noLicenseCheckRequired: true,
        },
    },
    {
        path: '/forgot-password',
        component: ForgotPassword,
        meta: {
            noLicenseCheckRequired: true,
            onlyIfNotAuthenticated: true,
            sidebarWidthGroup: 'default',
        }
    },
    {
        path: '/confirm-password-reset',
        component: ConfirmReset,
        meta: {
            noLicenseCheckRequired: true,
            onlyIfNotAuthenticated: true,
            sidebarWidthGroup: 'default',
        }
    },
    {
        path: '/reset-password/:emailToken',
        component: SetNewPassword,
        meta: {
            noLicenseCheckRequired: true,
            onlyIfNotAuthenticated: true,
            sidebarWidthGroup: 'default',
        }
    },
    {
        path: '/password-reset-success',
        component: PasswordReset,
        meta: {
            noLicenseCheckRequired: true,
            onlyIfNotAuthenticated: true,
            sidebarWidthGroup: 'default',
        }
    },
    {
        path: '/notifications',
        component: Notifications,
        meta: {
            onlyIfAuthenticated: true,
            sidebarWidthGroup: 'default',
        }
    },
    {
        path: '/generator',
        component: Generator,
        meta: {
            onlyIfAuthenticated: true,
            sidebarWidthGroup: 'generator',
        }
    },
    {
        path: '/generator/:moduleTableName',
        component: Generator,
        meta: {
            onlyIfAuthenticated: true,
            sidebarWidthGroup: 'generator',
        }
    },
    {
        path: '/menu-editor',
        component: MenuEditor,
        meta: {
            onlyIfAuthenticated: true,
            sidebarWidthGroup: 'menu-editor',
        }
    },
    {
        path: '/menu-editor/:menuId',
        component: MenuEditor,
        meta: {
            onlyIfAuthenticated: true,
            sidebarWidthGroup: 'menu-editor',
        }
    },
    {
        path: '/menu-items-editor',
        component: MenuItemsEditor,
        meta: {
            onlyIfAuthenticated: true,
            sidebarWidthGroup: 'menu-items-editor',
        }
    },
    {
        path: '/menu-items-editor/:menuId',
        component: MenuItemsEditor,
        meta: {
            onlyIfAuthenticated: true,
            sidebarWidthGroup: 'menu-items-editor',
        }
    },
    {
        path: '/menu-items-editor/:menuId/:menuItemId',
        component: MenuItemsEditor,
        meta: {
            onlyIfAuthenticated: true,
            sidebarWidthGroup: 'menu-items-editor',
        }
    },
    {
        path: '/admin/:moduleTableName',
        component: Admin,
        meta: {
            onlyIfAuthenticated: true,
            sidebarWidthGroup: 'admin',
        }
    },
    {
        path: '/admin/:moduleTableName/:moduleEntryId',
        component: Admin,
        meta: {
            onlyIfAuthenticated: true,
            sidebarWidthGroup: 'admin',
        }
    },
    {
        path: '/error/:errorName',
        component: ErrorPage
    },
    {
        path: '/invalid-license/:errorType',
        component: InvalidLicense,
        meta: {
            noLicenseCheckRequired: true,
        }
    },
    {
        path: '/invalid-license',
        component: InvalidLicense,
        meta: {
            noLicenseCheckRequired: true,
        }
    },
    {
        path: '*',
        component: ErrorPage
    }
];

export const router = new VueRouter({
    base: config.ENV.controlPanelBase,
    mode: 'history',
    routes
});

const checkRouteAvailability = (to, from, next) => {
    if (store.state.user.loggedIn) {
        if (to.matched.some(record => record.meta.onlyIfNotAuthenticated)) {
            return next({
                path: '/'
            });
        }

        store.dispatch('notification/getUnreadNotifications');

        // Unsubscribe a user from entry subscriptions
        store.dispatch('subscription/unsubscribe', {
            entryId: from.params.moduleEntryId,
            moduleTableName: from.params.moduleTableName
        });

        return next();
    }

    // Else if non-authenticated user
    if (to.matched.some(record => record.meta.onlyIfAuthenticated)) {
        return next({ // Redirect to login
            path: '/login',
            query: { redirect: to.fullPath }
        });
    }

    next();
};

const runLaravelEcho = function(apiToken, userId) {
    if(_.has(window, 'Echo') && !_.isEmpty(window.Echo)) {
        return;
    }

    const Authorization = 'Bearer ' + apiToken;

    window.Echo = new Echo({
        auth: {
            headers: { Authorization }
        },
        authEndpoint: config.ENV.apiBasePath.replace(new RegExp('api$'), '') + 'broadcasting/auth',
        broadcaster: 'pusher',
        cluster: config.ENV.pusher.cluster,
        encrypted: true,
        key: config.ENV.pusher.key,
    });

    window.Echo.private(`Photon.PhotonCms.Dependencies.DynamicModels.User.${userId}`)
        .notification((notification) => {
            pWarn('New Echo notification received.', notification);

            store.dispatch('notification/getUnreadNotifications', { notify: true });
        });
};

/**
 * Middleware to check auth before each route
 *
 * @return  {void}
 */
router.beforeEach((to, from, next) => {
    if(to.meta.noLicenseCheckRequired) {
        handleLogin(to, from, next);

        return;
    }

    store.dispatch('user/checkLicense')
        .then((response) => {
            pWarn('Haven responded.', response);

            handleLogin(to, from, next);
        })
        .catch((response) =>{
            pWarn('Haven rejected.', response);

            if (response === 'PHOTON_LICENSE_KEY_INACTIVE') {
                return next({
                    path: '/invalid-license/license-key-inactive'
                });
            }

            if (response === 'PHOTON_LICENSE_KEY_DOMAIN_INACTIVE') {
                return next({
                    path: '/invalid-license/license-key-domain-inactive'
                });
            }

            return next({
                path: '/invalid-license'
            });
        });
});

const handleLogin = function handleLogin (to, from, next) {
    pLog('Attepmting transition to path', to.fullPath);

    // Gets API token from storage if any
    const apiToken = storage.get('apiToken');

    // If there's a stored API token and user is not logged in check '/me' route to see if token is still valid
    if (!store.state.user.loggedIn && apiToken) {

        store.dispatch('user/checkMe', { apiToken })
            .then(() => {
                init(to, from, next, apiToken);
            });

        return;
    }

    // If a user is logged in and has an apiToken stored
    if (store.state.user.loggedIn && apiToken) {
        init(to, from, next, apiToken);
    }

    // Else if no token was stored
    checkRouteAvailability(to, from, next);
};

const init = function (to, from, next, apiToken) {
    Promise.all([
        store.dispatch('photonModule/getPhotonModules', {}),
        store.dispatch('photonField/getPhotonFields', {}),
    ]).then(() => {
        runLaravelEcho(apiToken, store.state.user.meta.id);

        checkRouteAvailability(to, from, next);
    });
};
