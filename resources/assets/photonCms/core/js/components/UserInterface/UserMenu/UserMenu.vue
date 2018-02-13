<template>
    <div>
        <nav class="user-menu">
            <a href="javascript:;" class="main-menu-access">
                <i class="fa-proton-logo"></i>
                <i class="fa fa-reorder"></i>
            </a>
            <section class="user-menu-wrapper">
                <a href="javascript:;" @click="toggleNotificationsPanel()"
                    class="notifications-access"
                    :class="{ 'unread': notifications.notificationsBadge }">
                    <i class="fa fa-comment-o"></i>
                    <div v-if="notifications.notificationsBadge" class="menu-counter">{{ notifications.notificationsBadge }}</div>
                </a>
                <router-link :to="`/admin/users/${user.meta.id}`">
                    <span class="user-name">{{user.meta.first_name}} {{user.meta.last_name}}</span>
                    <i v-if="!user.impersonating" class="fa fa-user-circle"></i>
                    <i v-else class="fa fa-exclamation-triangle"></i>
                </router-link>
            </section>
            <div class="panel panel-default nav-view notifications-view" :class="{ active: notifications.notificationsPanelVisible }">
                <div class="arrow user-menu-arrow"></div>
                <div class="panel-heading">
                    <i class="fa fa-comment-o"></i>
                    <span>Notifications {{notifications.notificationsPanelVisible}}</span>
                </div>
                <ul v-if="notifications.notificationsBadge > 0" class="list-group">
                    <li v-for="notification in notifications.unreadNotifications" class="list-group-item" @click="readNotification(notification)">
                        <div class="text-holder">
                            <span class="title-text">{{ notification.subject }}</span>
                            <span class="description-text">{{ notification.compiled_message }}</span>
                        </div>
                        <span class="time-ago">
                            {{ fromNow(notification.created_at) }}
                        </span>
                    </li>
                </ul>
                <div v-else class="panel-no-unread">
                    {{ $t('admin.noUnreadNotifications') }}
                </div>
                <div class="panel-footer">
                    <a href="javascript:;" class="see-all" @click="seeAll()">{{ $t('admin.seeAll') }}</a>
                    <a href="javascript:;" class="mark-all-as-read" @click="markAsRead()">{{ $t('notifications.markAllAsRead') }}</a>
                </div>
            </div>
        </nav>
        <notifications-menu></notifications-menu>
    </div>
</template>

<script>
    import UserMenu from './UserMenu.js';
    export default UserMenu;
</script>
