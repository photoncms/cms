<template>
    <nav class="quick-launch-bar">
        <div class="quick-wrapper quick-wrapper-selected">
            <ul class="quick-selected">
                <li v-for="menuListItem in ui.menus.quickLaunchMenu">
                    <router-link v-if="menuListItem.menu_link_type_name === 'admin_panel_module_link'" :to="`/admin/${menuListItem.resource_data.table_name}`">
                        <i class="nav-icon" :class="menuListItem.icon ? menuListItem.icon : menuListItem.resource_data.icon"></i>
                        <span class="nav-text">{{menuListItem.title}}</span>
                    </router-link>
                    <router-link v-if="menuListItem.menu_link_type_name === 'admin_panel_single_entry'" :to="`/admin/${menuListItem.resource_data.table_name}/${menuListItem.entry_data.id}`">
                        <i class="nav-icon" :class="menuListItem.icon ? menuListItem.icon : menuListItem.resource_data.icon"></i>
                        <span class="nav-text">{{menuListItem.title}}</span>
                    </router-link>
                    <router-link v-if="menuListItem.menu_link_type_name === 'static_link' && !/^(f|ht)tps?:\/\//i.test(menuListItem.resource_data)" :to="menuListItem.resource_data">
                        <i class="nav-icon" :class="menuListItem.icon"></i>
                        <span class="nav-text">{{menuListItem.title}}</span>
                    </router-link>
                    <a v-if="menuListItem.menu_link_type_name === 'static_link' && /^(f|ht)tps?:\/\//i.test(menuListItem.resource_data)" :href="menuListItem.resource_data" target="_blank">
                        <i class="nav-icon" :class="menuListItem.icon"></i>
                        <span class="nav-text">{{menuListItem.title}}</span>
                    </a>
                </li>
                <li v-if="userHasRole('super_administrator')">
                    <router-link :to="`/menu-items-editor/2`">
                        <i class="nav-icon fa fa-pencil-square-o"></i>
                        <span class="nav-text">{{ $t('dashboard.addShortcut') }}</span>
                    </router-link>
                </li>
                <li v-if="!userHasRole('super_administrator') && ui.menus.quickLaunchMenu.length == 0">
                    <a>
                        <span class="nav-text">{{ $t('dashboard.noShortcutsSet') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
    import QuickLaunch from './QuickLaunch.js';
    export default QuickLaunch;
</script>
