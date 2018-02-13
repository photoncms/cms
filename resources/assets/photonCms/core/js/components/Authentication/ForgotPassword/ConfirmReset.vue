<template>
    <div>
        <section class="wrapper scrollable">
            <section class="panel panel-default">
                <div class="panel-heading">
                    <div>
                        <img src="/images/logo.png" alt="proton-logo">
                        <h1>
                            <span class="title">
                                {{ ui.photonConfig.companyTitle }}
                            </span>
                            <span class="subtitle">
                                {{ ui.photonConfig.companySubtitle }}
                            </span>
                        </h1>
                    </div>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <span class="welcome-text">
                            {{ $t('forgotPassword.passwordResetRequested') }}<br>
                            <br>
                        </span>
                        <div>
                            <span class="member">
                                {{ $t('forgotPassword.confirmPasswordReset') }}
                            </span><br>
                        </div>
                    </li>
                </ul>
                <div class="panel-body" style="text-align: center;">
                    <router-link to="/login" style="float: none;">
                        <i style="font-size: 35px;" class="fa fa-home"></i><br>
                        <span>{{ $t('forgotPassword.logIn') }}</span>
                    </router-link>
                </div>
            </section>
        </section>
    </div>
</template>

<script>
    import ConfirmReset from './ConfirmReset.js';
    export default ConfirmReset;
</script>
