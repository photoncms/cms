<template>
    <div>
        <form id="login-form" role="form">
            <section class="wrapper scrollable">
                <section class="panel panel-default">
                    <div class="panel-heading">
                        <div>
                            <img src="/images/logo.png" alt="proton-logo">
                            <h1>
                                <span class="title pull-left">
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
                                {{ $t('login.welcomeText', { 'companyTitle': ui.photonConfig.companyTitle }) }}<br>
                            </span>
                            <div v-if="signUpEnabled">
                                <span class="member">
                                    <router-link to="/register">{{ $t('login.notAMember') }}</router-link>
                                </span>
                            </div>
                        </li>
                        <li class="list-group-item form-login-inputs">
                            <div class="form-group">
                                <input name="email"
                                    type="email"
                                    v-model="credentials.email"
                                    class="form-control input-lg"
                                    id="email"
                                    :placeholder="$t('login.email')">
                                    <div id="email-error" class="form-field-error"></div>
                            </div>
                            <div class="form-group">
                                <input name="password"
                                    type="password"
                                    v-model="credentials.password"
                                    class="form-control input-lg"
                                    id="password"
                                    :placeholder="$t('login.password')">
                                    <div id="password-error" class="form-field-error"></div>
                            </div>
                        </li>
                    </ul>
                    <div class="panel-footer">
                        <div class="server-error" v-if="serverError">{{serverError}}</div>
                        <button type="submit" class="btn btn-lg btn-success">{{ $t('login.loginToYourAccount') }}</button>
                        <br>
                        <router-link class="forgot" to="/forgot-password">{{ $t('login.forgotYourPassword') }}</router-link>
                    </div>
                </section>
            </section>
        </form>
    </div>
</template>

<script>
    import Login from './Login.js';
    export default Login;
</script>
