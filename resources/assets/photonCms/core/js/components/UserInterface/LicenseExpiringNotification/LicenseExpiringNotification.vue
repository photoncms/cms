<template>
    <div v-if="user.licenseExpiring" class="ui-pnotify " style="width: 300px; opacity: 1; right: 36px; top: 70px; cursor: auto;">
        <div class="alert ui-pnotify-container alert-error ui-pnotify-shadow" style="min-height: 16px;">
            <div class="ui-pnotify-icon">
                <span class="fa fa-exclamation-triangle"></span>
            </div>
            <h4 class="ui-pnotify-title">{{ $t('license.licenseKeyDomainInactive') }}</h4>
            <div class="ui-pnotify-text">{{ $t('license.licenseKeyDomainInactiveExplanation') }}
                <br>
                <a href="javascript://" @click="refreshStatus" class="btn btn-sm btn-primary btn-refresh-status pull-left">
                    {{ $t('license.refreshStatus') }}&nbsp;<i class="fa fa-refresh" :class="{ 'fa-spin': isLoading }"></i>
                </a>
                <a href="https://photoncms.com/pricing" target="_blank" class="btn btn-sm btn-primary btn-resolve">
                    {{ $t('license.resolve') }}&nbsp;<i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
    import LicenseExpiringNotification from './LicenseExpiringNotification.js';
    export default LicenseExpiringNotification;
</script>
