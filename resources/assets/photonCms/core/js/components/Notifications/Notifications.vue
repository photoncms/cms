<template>
    <div class="notifications-wrapper">
        <license-expiring-notification></license-expiring-notification>

        <main-menu></main-menu>

        <section class="wrapper scrollable">

            <user-menu></user-menu>

            <breadcrumb></breadcrumb>

            <div id="top" class="panel panel-default panel-block panel-title-block">
                <div class="panel-heading">
                    <div>
                        <i class="fa fa-comment"></i>
                        <h1 style="margin-top: 5px;">{{ $t('notifications.notifications') }}<span class="page-title"></span></h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div>
                    <div class="col-md-6 col-lg-12">
                        <div class="panel-group">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="panel panel-block text-right" style="margin-bottom: 10px;">
                                        <a href="javascript:;" v-if="notifications.allNotifications.length > 0" @click="markAllNotificationsAsRead()">{{ $t('notifications.markAllAsRead') }}</a>
                                        <span v-else class="text-muted">{{ $t('notifications.markAllAsRead') }}</span>
                                    </div>
                                    <table v-if="notifications.allNotifications.length > 0" class="table notifications-table">
                                        <tbody>
                                            <tr v-for="notification in notifications.allNotifications" :class="{'notification-read': notification.read_at}" @click="readNotification(notification)">
                                                <td>
                                                    <div>
                                                        <strong>{{ notification.subject }}</strong>
                                                        <span class="pull-right text-muted">{{ fromNow(notification.created_at) }}</span>
                                                    </div>
                                                    {{ notification.compiled_message }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div v-else class="text-muted text-center">{{ $t('notifications.noRecentNotificationsFound') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <assets></assets>
    </div>
</template>

<script>
    import Notifications from './Notifications.js';
    export default Notifications;
</script>
