import Vue from 'vue';

// App root component
export const App = Vue.component(
        'App',
        require('_/components/App/App.vue')
    );

// Assets component
export const Assets = Vue.component(
        'Assets',
        require('_/components/Assets/Assets.vue')
    );

// User interface components
export const MainMenu = Vue.component(
        'MainMenu',
        require('_/components/UserInterface/MainMenu/MainMenu.vue')
    );

export const LicenseExpiringNotification = Vue.component(
        'LicenseExpiringNotification',
        require('_/components/UserInterface/LicenseExpiringNotification/LicenseExpiringNotification.vue')
    );

export const UserMenu = Vue.component(
        'UserMenu',
        require('_/components/UserInterface/UserMenu/UserMenu.vue')
    );

// Common components
export const Breadcrumb = Vue.component(
        'Breadcrumb',
        require('_/components/UserInterface/Breadcrumb/Breadcrumb.vue')
    );

export const HelpBlock = Vue.component(
        'HelpBlock',
        require('_/components/UserInterface/HelpBlock/HelpBlock.vue')
    );

export const Instructions = Vue.component(
        'Instructions',
        require('_/components/UserInterface/Instructions/Instructions.vue')
    );

export const Sidebar = Vue.component(
        'Sidebar',
        require('_/components/UserInterface/Sidebar/Sidebar.vue')
    );

export const TitleBlock = Vue.component(
        'TitleBlock',
        require('_/components/TitleBlock/TitleBlock.vue')
    );

export const TitleBar = Vue.component(
        'TitleBar',
        require('_/components/Dashboard/TitleBar/TitleBar.vue')
    );

export const QuickLaunch = Vue.component(
        'QuickLaunch',
        require('_/components/Dashboard/QuickLaunch/QuickLaunch.vue')
    );
// export const DashboardWidgets = require('_/components/Dashboard/DashboardWidgets/DashboardWidgets.vue');

// Admin components
export const EntryForm = Vue.component(
        'EntryForm',
        require('_/components/UserInterface/EntryForm/EntryForm.vue')
    );

export const InfoPanel = Vue.component(
        'InfoPanel',
        require('_/components/UserInterface/InfoPanel/InfoPanel.vue')
    );
