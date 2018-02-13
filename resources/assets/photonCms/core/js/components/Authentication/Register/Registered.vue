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
                            {{ $t('register.succesfullyRegistered') }}<br>
                            <br>
                        </span>
                        <div class="text-center">
                            <span class="member">
                                {{ $t('register.confirmYourEmail') }}
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
    import Registered from './Registered.js';
    export default Registered;
</script>
