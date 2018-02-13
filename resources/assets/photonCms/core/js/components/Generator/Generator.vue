<template>
    <div>
        <license-expiring-notification></license-expiring-notification>

        <main-menu></main-menu>

        <sidebar></sidebar>

        <section class="wrapper scrollable" :class="{ retracted: ui.sidebarExtended, extended: !ui.sidebarExtended }">
            <user-menu></user-menu>
            <breadcrumb></breadcrumb>
            <title-block></title-block>
            <div class="row">
                <module-configurator></module-configurator>
                <div class="col-md-6 col-lg-4 info-panel">
                    <generator-info-panel></generator-info-panel>

                    <generator-help-block></generator-help-block>
                </div>
            </div>
        </section>

        <assets></assets>
    </div>
</template>

<script>
import Generator from './Generator.js';
export default Generator;
</script>
