<template>
    <div>
        <form id="registration-form" role="form">
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
                                {{ $t('register.createYourAccount') }}<br>
                            </span>
                            <div>
                                <span class="member">
                                    <router-link to="/login">{{ $t('register.alreadyHaveAnAccount') }}</router-link>
                                </span>
                            </div>
                        </li>
                        <li class="list-group-item form-login-inputs">
                            <div class="form-group">
                                <input name="first_name"
                                    type="text"
                                    v-model="credentials.first_name"
                                    class="form-control input-lg"
                                    id="first_name"
                                    :placeholder="$t('register.firstName')">
                                    <div id="first_name-error" class="form-field-error"></div>
                            </div>
                            <div class="form-group">
                                <input name="last_name"
                                    type="text"
                                    v-model="credentials.last_name"
                                    class="form-control input-lg"
                                    id="last_name"
                                    :placeholder="$t('register.lastName')">
                                    <div id="last_name-error" class="form-field-error"></div>
                            </div>
                            <div v-if="!invitationToken" class="form-group">
                                <input name="email"
                                    type="email"
                                    v-model="credentials.email"
                                    class="form-control input-lg"
                                    id="email"
                                    :placeholder="$t('register.email')">
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
                            </div>
                            <br>
                        </li>
                    </ul>
                    <div class="panel-footer">
                        <div class="server-error" v-if="serverError">{{serverError}}</div>
                        <button type="submit" class="btn btn-lg btn-success">{{ $t('register.registerYourAccount') }}</button>
                        <br>
                        <br>
                    </div>
                </section>
            </section>
        </form>
    </div>
</template>

<script>
    import Register from './Register.js';
    export default Register;
</script>
