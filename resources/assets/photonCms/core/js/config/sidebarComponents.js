import Vue from 'vue';

export const AdminSidebar = Vue.component(
        'AdminSidebar',
        require('_/components/UserInterface/Sidebar/AdminSidebar/AdminSidebar.vue')
    );

export const GeneratorSidebar = Vue.component(
        'GeneratorSidebar',
        require('_/components/UserInterface/Sidebar/GeneratorSidebar/GeneratorSidebar.vue')
    );

export const MenuEditorSidebar = Vue.component(
        'MenuEditorSidebar',
        require('_/components/UserInterface/Sidebar/MenuEditorSidebar/MenuEditorSidebar.vue')
    );

export const MenuItemsEditorSidebar = Vue.component(
        'MenuItemsEditorSidebar',
        require('_/components/UserInterface/Sidebar/MenuItemsEditorSidebar/MenuItemsEditorSidebar.vue')
    );

export const NotificationsSidebar = Vue.component(
        'NotificationsSidebar',
        require('_/components/UserInterface/Sidebar/NotificationsSidebar/NotificationsSidebar.vue')
    );
