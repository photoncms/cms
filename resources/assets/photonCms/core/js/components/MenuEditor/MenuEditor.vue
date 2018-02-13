<template>
    <div>
        <license-expiring-notification></license-expiring-notification>

        <main-menu></main-menu>

        <sidebar></sidebar>

        <section
            class="wrapper scrollable"
            :class="{
                    extended: !ui.sidebarExtended,
                    retracted: ui.sidebarExtended
                }">

            <user-menu></user-menu>

            <breadcrumb></breadcrumb>

            <title-block></title-block>

            <div class="row" v-show="!ui.searchMode">
                <instructions
                    :text="$t('admin.pleaseSelectAParentEntryFromTheTreeMenu')"
                    :title="$t('admin.rootEntryCreationIsDisabledForThisModule')"
                    v-if="menuEditor.editorMode === 'default'">
                </instructions>

                <div v-if="menuEditor.editorMode === 'edit' || menuEditor.editorMode === 'create'">
                    <entry-form
                        :fields="fields"
                        :form-fields-reset-prop="formFieldsReset"
                        :should-router-push="false"
                        vuexModule="menuEditor">
                    </entry-form>
                </div>

                <div class="col-md-6 col-lg-4 info-panel">
                    <info-panel vuexModule="menuEditor"></info-panel>

                    <menu-editor-help-block></menu-editor-help-block>
                </div>
            </div>
        </section>
        <assets></assets>
    </div>
</template>

<script>
    import MenuEditor from './MenuEditor.js';
    export default MenuEditor;
</script>
