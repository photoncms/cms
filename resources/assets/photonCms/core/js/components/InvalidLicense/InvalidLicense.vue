<template>
    <section class="wrapper scrollable">
        <div class="panel panel-default panel-block panel-error-block invalid-license">
            <div class="panel-heading">
                <div>
                    <h1>
                        <i class="fa fa-exclamation-triangle"></i>
                        {{error.title}}
                        <div class="license-error-info">
                            {{error.info}}
                        </div>
                    </h1>
                    <div class="refresh-status">
                        <a href="javascript://" @click="refreshStatus">
                            <i class="fa fa-refresh" :class="{ 'fa-spin': isLoading }"></i>
                            <span class="cta-text">{{ $t('license.refreshStatus') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="panel-body error-footer">
                <a :href="getActionButtonLink" class="btn btn-lg btn-primary">
                    {{ $t('license.resolve') }}
                    <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </section>
</template>

<script>
    import InvalidLicense from './InvalidLicense.js';
    export default InvalidLicense;
</script>
