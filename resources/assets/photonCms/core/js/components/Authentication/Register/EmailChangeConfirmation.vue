<template>
    <div>
        <section class="wrapper scrollable">
            <section class="panel panel-default">
                <div class="panel-heading">
                    <div>
                        <img src="/images/logo.png" alt="proton-logo">
                        <h1>
                            <span class="title pull-left">
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
                            <div class="server-error" v-if="confirmationError">{{confirmationError}}</div>
                            <div v-if="emailConfirmed">{{ $t('login.emailSuccessfullyConfirmed') }}</div>
                        </span>
                        <div v-if="emailConfirmed" class="text-center">
                            <span class="member">
                                {{ $t('login.loginWithNewEmailAddress') }}
                            </span><br>
                        </div>
                    </li>
                </ul>

                <div class="panel-footer">
                    <div class="log-in-muted">
                        <router-link to="/login" class="homepage-link">
                            <i class="fa fa-home" style="font-size: 35px;"></i><br>
                            <span>{{ $t('forgotPassword.logIn') }}</span>
                        </router-link>
                    </div>
                </div>
            </section>
        </section>
    </div>
</template>

<script>
    import EmailChangeConfirmation from './EmailChangeConfirmation.js';
    export default EmailChangeConfirmation;
</script>
