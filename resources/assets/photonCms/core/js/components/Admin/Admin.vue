<template>
    <div>
        <license-expiring-notification></license-expiring-notification>

        <main-menu></main-menu>

        <sidebar :fields="fields"></sidebar>

        <section class="wrapper scrollable">

            <user-menu></user-menu>

            <breadcrumb></breadcrumb>

            <title-block></title-block>

            <div class="row" v-if="ui.searchMode">
                <search-results></search-results>
            </div>

            <div class="row" v-show="!ui.searchMode">
                <instructions
                    :text="$t('admin.pleaseSelectAParentEntryFromTheTreeMenu')"
                    :title="$t('admin.rootEntryCreationIsDisabledForThisModule')"
                    v-if="admin.editorMode === 'default'
                        && admin.selectedModule.permission_control.crud.create">
                </instructions>

                <instructions
                    :text="$t('admin.ifYouBelieveThisIsAnErrorPleaseContactTheAdministrator')"
                    :title="$t('admin.youHaveInsufficientPermissionsToCreateNewEntriesForThisModule')"
                    v-if="admin.editorMode === 'default'
                        && !admin.selectedModule.permission_control.crud.create">
                </instructions>

                <admin-slot-above-entry-form></admin-slot-above-entry-form>

                <div v-if="admin.editorMode === 'edit' || admin.editorMode === 'create'">
                    <entry-form
                        :fields="fields"
                        :form-fields-reset-prop="formFieldsReset"
                        vuexModule="admin">
                    </entry-form>
                </div>

                <admin-slot-below-entry-form></admin-slot-below-entry-form>

                <div v-if="admin.editorMode === 'search'">
                    <search
                        :active-module="admin.selectedModule.table_name"
                        :entries="admin.entries">
                    </search>
                </div>

                <div class="col-md-6 col-lg-4 info-panel">
                    <admin-slot-above-info-panel></admin-slot-above-info-panel>

                    <info-panel vuexModule="admin"></info-panel>

                    <admin-slot-below-info-panel></admin-slot-below-info-panel>

                    <help-block></help-block>

                    <admin-slot-below-help-block></admin-slot-below-help-block>
                </div>
            </div>
        </section>
        <assets></assets>
    </div>
</template>

<script>
    import Admin from './Admin.js';
    export default Admin;
</script>
