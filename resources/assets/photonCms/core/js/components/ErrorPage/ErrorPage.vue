<template>
    <section class="wrapper scrollable">
        <div class="panel panel-default panel-block panel-error-block">
            <div class="panel-heading">
                <div>
                    <i :class="error.errorIcon"></i>
                    <h1>
                        <small>{{ $t('errors.sorryThereSBeenAnError') }}</small>{{error.errorText}}
                    </h1>
                    <div class="error-code">
                        {{error.errorCode}}
                    </div>
                </div>
            </div>
            <div class="panel-body error-footer">
                <router-link v-if="invalidToken" to="/login" class="homepage-link">
                    <i class="fa fa-home"></i><br>
                    <span>{{ $t('forgotPassword.logIn') }}</span>
                </router-link>

                <router-link v-else to="/" class="homepage-link">
                    <i class="fa fa-home"></i><br>
                    <span>{{ $t('errors.dashboard') }}</span>
                </router-link>
            </div>
        </div>
    </section>
</template>

<script>
    import ErrorPage from './ErrorPage.js';
    export default ErrorPage;
</script>
