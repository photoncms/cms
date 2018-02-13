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
                    v-if="menuItemsEditor.editorMode === 'default'">
                </instructions>

                <entry-form
                    :fields="fields"
                    :form-fields-reset-prop="formFieldsReset"
                    :should-router-push="false"
                    v-if="menuItemsEditor.editorMode === 'edit' || menuItemsEditor.editorMode === 'create'"
                    vuexModule="menuItemsEditor"
                    >
                </entry-form>

                <div class="col-md-6 col-lg-4 info-panel">
                    <info-panel vuexModule="menuItemsEditor"></info-panel>
                </div>
            </div>
        </section>
        <assets></assets>
    </div>
</template>

<script>
    import MenuItemsEditor from './MenuItemsEditor.js';
    export default MenuItemsEditor;
</script>
