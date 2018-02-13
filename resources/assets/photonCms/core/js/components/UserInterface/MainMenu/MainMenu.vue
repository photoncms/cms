<template>
    <nav class="main-menu" data-step='2' data-intro='This is the extendable Main Navigation Menu.' data-position='right'>
        <ul>
            <li>
                <router-link to="/">
                    <i class="fa fa-home nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </router-link>
            </li>
            <li v-for="menuListItem in ui.menus.mainMenu" :class="{'has-subnav': menuListItem.has_children}">
                <router-link v-if="menuListItem.clickable && menuListItem.menu_link_type_name !== 'static_link'" :to="'/admin/' + menuListItem.resource_data.table_name">
                    <i class="nav-icon" :class="menuListItem.icon ? menuListItem.icon : menuListItem.resource_data.icon"></i>
                    <span class="nav-text">{{menuListItem.title}}</span>
                </router-link>
                <router-link v-if="menuListItem.clickable && menuListItem.menu_link_type_name === 'static_link'" :to=" menuListItem.resource_data">
                    <i class="nav-icon" :class="menuListItem.icon"></i>
                    <span class="nav-text">{{menuListItem.title}}</span>
                </router-link>
                <span class="clickable" v-if="!menuListItem.clickable">
                    <i class="nav-icon" :class="menuListItem.icon"></i>
                    <span class="nav-text">{{menuListItem.title}}</span>
                    <i class="fa fa-angle-right"></i>
                </span>
                <ul v-if="menuListItem.has_children">
                    <li v-for="children in menuListItem.children">
                        <router-link v-if="children.clickable && children.menu_link_type_name !== 'static_link'" :to="'/admin/' + children.resource_data.table_name">
                            <i class="nav-icon" :class="children.icon ? children.icon : children.resource_data.icon"></i>
                            <span class="nav-text">{{children.title}}</span>
                        </router-link>
                        <router-link v-if="children.clickable && children.menu_link_type_name === 'static_link'" :to=" children.resource_data">
                            <i class="nav-icon" :class="children.icon"></i>
                            <span class="nav-text">{{children.title}}</span>
                        </router-link>
                    </li>
                </ul>
            </li>
        </ul>
        <ul class="logout">
            <li v-if="userHasRole('super_administrator')">
                <router-link to="/generator">
                    <i class="fa fa-cogs nav-icon"></i>
                    <span class="nav-text">Generator</span>
                </router-link>
            </li>
            <li
                v-if="(userHasRole('super_administrator'))
                    && (licenseStatus.domainType === 1 || licenseStatus.licenseType === 4)"
                >
                <span class="clickable">
                    <i class="nav-icon fa fa-folder-o"></i>
                    <span class="nav-text">User Settings</span>
                    <i class="fa fa-angle-right"></i>
                </span>
                <ul>
                    <li>
                        <router-link to="/admin/permissions">
                            <i class="fa fa-user-plus nav-icon"></i>
                            <span class="nav-text">Permissions</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/admin/roles">
                            <i class="fa fa-users nav-icon"></i>
                            <span class="nav-text">Roles</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/admin/invitations">
                            <i class="fa fa-envelope-o nav-icon"></i>
                            <span class="nav-text">Invite Users</span>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li v-if="userHasRole('super_administrator')">
                <span class="clickable">
                    <i class="nav-icon fa fa-folder-o"></i>
                    <span class="nav-text">Menu Editor</span>
                    <i class="fa fa-angle-right"></i>
                </span>
                <ul>
                    <li>
                        <router-link to="/menu-editor">
                            <i class="fa fa-bars nav-icon"></i>
                            <span class="nav-text">Menu Editor</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/menu-items-editor">
                            <i class="fa fa-list nav-icon"></i>
                            <span class="nav-text">Menu Items Editor</span>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li>
                <span @click="openAssetsManager" class="clickable">
                    <i class="nav-icon fa fa-image"></i>
                    <span class="nav-text">{{ $t('assetsManager.assetsManager') }}</span>
                </span>
            </li>
            <li v-if="user.impersonating">
                <span @click="impersonateUser({ id: null })" class="clickable">
                    <i class="fa fa-stop-circle nav-icon"></i>
                    <span class="nav-text">{{ $t('admin.stopImpersonating', { firstName: user.meta.first_name, lastName: user.meta.last_name }) }}</span>
                </span>
            </li>
            <li v-if="!user.impersonating">
                <span @click="logout" class="clickable">
                    <i class="fa fa-power-off nav-icon"></i>
                    <span class="nav-text">{{ $t('admin.logout') }}</span>
                </span>
            </li>
        </ul>
    </nav>
</template>

<script>
    import MainMenu from './MainMenu.js';
    export default MainMenu;
</script>
