<template>
    <form id="registration-form" role="form">
        <section class="wrapper scrollable">
            <section class="panel panel-default">
                <div class="panel-heading">
                    <div>
                        <img src="/images/logo.png" alt="proton-logo">
                        <h1>
                            <span class="title">
                                {{ui.photonConfig.companyTitle}}
                            </span>
                            <span class="subtitle">
                                {{ui.photonConfig.companySubtitle}}
                            </span>
                        </h1>
                    </div>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <span class="welcome-text">
                            {{ $t('forgotPassword.passwordResetSuccess') }}<br>
                        </span>
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
    </form>
</template>

<script>
    import PasswordReset from './PasswordReset.js';
    export default PasswordReset;
</script>
