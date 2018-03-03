<template>
    <div>
        <form id="reset-password-form" role="form">
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
                        <li class="list-group-item form-login-inputs">
                            <span class="login-text">
                                {{ $t('forgotPassword.pleaseSelectNewPassword') }}
                            </span>
                            <div class="form-group">
                                <input name="email"
                                    type="email"
                                    v-model="credentials.email"
                                    class="form-control input-lg"
                                    :placeholder="$t('register.email')"
                                    id="email">
                                    <div id="email-error" class="form-field-error"></div>
                            </div>
                            <div class="form-group">
                                <input name="password"
                                    type="password"
                                    v-model="credentials.password"
                                    class="form-control input-lg"
                                    id="password"
                                    :placeholder="$t('register.password')">
                                    <div id="password-error" class="form-field-error"></div>
                            </div>
                            <div class="form-group">
                                <input name="confirm_password"
                                    type="password"
                                    v-model="credentials.confirmPassword"
                                    class="form-control input-lg"
                                    id="confirm_password"
                                    :placeholder="$t('register.confirmPassword')"
                                    data-parsley-required="true"
                                    data-parsley-equalto="#password"
                                    :data-parsley-required-message="$t('register.confirmPasswordRequiredMessage')"
                                    data-parsley-equalto-message="Passwords do not match.">
                                <br>
                            </div>
                        </li>
                    </ul>
                    <div class="panel-footer">
                        <div class="server-error" v-if="serverError">{{serverError}}</div>
                        <button type="submit" class="btn btn-lg btn-success">{{ $t('forgotPassword.resetPassword') }}</button>
                        <div class="log-in-muted">
                            <router-link to="/login" class="text-muted">
                                <i style="font-size: 35px;" class="fa fa-home"></i><br>
                                <span>{{ $t('forgotPassword.logIn') }}</span>
                            </router-link>
                        </div>
                    </div>
                </section>
            </section>
        </form>
    </div>
</template>

<script>
    import SetNewPassword from './SetNewPassword.js';
    export default SetNewPassword;
</script>
