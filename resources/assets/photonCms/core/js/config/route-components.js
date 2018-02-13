import Vue from 'vue';

export const ErrorPage = Vue.component(
        'ErrorPage',
        require('_/components/ErrorPage/ErrorPage.vue')
    );

export const InvalidLicense = Vue.component(
        'InvalidLicense',
        require('_/components/InvalidLicense/InvalidLicense.vue')
    );

export const Login = Vue.component(
        'Login',
        require('_/components/Authentication/Login/Login.vue')
    );

export const Register = Vue.component(
        'Register',
        require('_/components/Authentication/Register/Register.vue')
    );

export const Registered = Vue.component(
        'Registered',
        require('_/components/Authentication/Register/Registered.vue')
    );

export const EmailConfirmation = Vue.component(
        'EmailConfirmation',
        require('_/components/Authentication/Register/EmailConfirmation.vue')
    );

export const EmailChangeConfirmation = Vue.component(
        'EmailChangeConfirmation',
        require('_/components/Authentication/Register/EmailChangeConfirmation.vue')
    );

export const ForgotPassword = Vue.component(
        'ForgotPassword',
        require('_/components/Authentication/ForgotPassword/ForgotPassword.vue')
    );

export const ConfirmReset = Vue.component(
        'ConfirmReset',
        require('_/components/Authentication/ForgotPassword/ConfirmReset.vue')
    );

export const SetNewPassword = Vue.component(
        'SetNewPassword',
        require('_/components/Authentication/ForgotPassword/SetNewPassword.vue')
    );

export const PasswordReset = Vue.component(
        'PasswordReset',
        require('_/components/Authentication/ForgotPassword/PasswordReset.vue')
    );

export const Dashboard = Vue.component(
        'Dashboard',
        require('_/components/Dashboard/Dashboard.vue')
    );

export const Admin = Vue.component(
        'Admin',
        require('_/components/Admin/Admin.vue')
    );

export const Generator = Vue.component(
    'Generator',
    require('_/components/Generator/Generator.vue')
);

export const MenuEditor = Vue.component(
    'MenuEditor',
    require('_/components/MenuEditor/MenuEditor.vue')
);

export const MenuItemsEditor = Vue.component(
    'MenuItemsEditor',
    require('_/components/MenuItemsEditor/MenuItemsEditor.vue')
);

export const Notifications = Vue.component(
        'Notifications',
        require('_/components/Notifications/Notifications.vue')
    );
