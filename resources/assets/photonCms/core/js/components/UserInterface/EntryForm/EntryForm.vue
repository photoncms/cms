<template>
    <div :class="{'col-md-6 col-lg-8': !inSidebar}">
        <div class="panel-group">

            <form
                :id="`entry-editor-form-${vuexModule}`"
                data-parsley-excluded="input[type=button], input[type=submit], input[type=reset]">
                <div v-if="admin.editedEntry" class="panel panel-default" :class="{'no-border': inSidebar}">
                    <div class="panel-body" :class="{'no-border no-padding': inSidebar}">
                        <div class="form-horizontal">
                            <permission-name-generator-handler v-if="admin.selectedModule.table_name === 'permissions'">
                            </permission-name-generator-handler>

                            <form-field
                                :can-create-search-choice="field.canCreateSearchChoice"
                                :default-value="field.defaultValue"
                                :disabled="field.disabled"
                                :field-type="field.vueComponent"
                                :flatten-to-optgroups="field.flattenToOptgroups"
                                :hidden="field.hidden"
                                :id="field.id"
                                :is-active-entry-filter="field.isActiveEntryFilter"
                                :is-system="field.isSystem"
                                :inline="inlineFieldTypes(field)"
                                :key="field.id"
                                :lazy-loading="field.lazyLoading"
                                :multiple="field.multiple"
                                :mutation="field.mutation"
                                :name="field.name"
                                :placeholder="field.label"
                                :preload-data-config="field.preloadDataConfig"
                                :preselect-first="field.preselectFirst"
                                :related-table-name="field.relatedTableName"
                                :required="field.required"
                                :form-fields-reset-prop="formFieldsReset"
                                :tooltip="field.tooltip"
                                :value="field.value"
                                :vuexModule="vuexModule"
                                v-for="field in fields"
                                >
                            </form-field>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default panel-block" :class="{'no-border': inSidebar}">
                    <div class="list-group">
                        <div class="list-group-item form-footer" :class="{'form-footer-no-padding': inSidebar}">
                            <div v-if="serverError" class="form-group clearfix">
                                <div class="server-error pull-right">{{serverError}}</div>
                            </div>

                            <div
                                v-show="admin.editorMode === 'edit' && admin.confirmDeleteEntry"
                                class="form-group pull-right no-bottom-margin"
                                style="margin-bottom: 0;">
                                <button
                                    :disabled="admin.submitInProgress"
                                    type="button"
                                    @click="deleteEntryConfirmed" class="btn btn-danger">
                                    {{ $t('admin.confirmDelete') }}
                                </button>

                                <button
                                    :disabled="admin.submitInProgress"
                                    type="button"
                                    @click="confirmDeleteEntry" class="btn btn-default">
                                    {{ $t('admin.cancelDelete') }}
                                </button>
                            </div>

                            <div
                                v-show="!admin.confirmDeleteEntry"
                                class="form-group pull-right no-bottom-margin"
                                style="margin-bottom: 0;">
                                <button
                                    :disabled="admin.submitInProgress
                                        || !admin.editedEntry.permission_control.crud.delete"
                                    type="button"
                                    v-if="admin.editorMode === 'edit'
                                        && admin.selectedModule.type != 'single_entry'
                                        && !disableDelete"
                                    @click="confirmDeleteEntry"
                                    class="btn btn-danger">
                                    {{ $t('admin.deleteEntry') }}
                                </button>

                                <button
                                    :disabled="admin.submitInProgress
                                        || !admin.editedEntry.permission_control.crud.update"
                                    type="submit"
                                    v-if="admin.editorMode === 'edit'"
                                    class="btn btn-success">
                                    {{ $t('admin.saveChanges') }}
                                </button>

                                <button
                                    :disabled="admin.submitInProgress
                                    || !admin.selectedModule.permission_control.crud.create"
                                    type="submit"
                                    v-if="admin.editorMode === 'create'"
                                    class="btn btn-success">
                                    {{newEntryButtonText}}
                                </button>
                            </div>

                            <div
                                v-if="admin.editorMode === 'create'
                                    && admin.selectedModule.type != 'single_entry'
                                    && !disableCreateAnother
                                    && admin.selectedModule.permission_control.crud.create"
                                class="form-group pull-right create-another">
                                <input
                                    type="checkbox"
                                    id="createAnother"
                                    value="createAnother"
                                    v-model="createAnother">
                                <label for="createAnother">{{ $t('admin.createAnother') }}</label>
                            </div>

                            <invitations-buttons v-if="admin.selectedModule.table_name === 'invitations'">
                            </invitations-buttons>

                            <impersonate-button
                                :userId="admin.editedEntry.id"
                                v-if="!admin.confirmDeleteEntry
                                && !user.impersonating
                                && admin.editedEntry.id !== user.meta.id
                                && admin.editorMode === 'edit'
                                && admin.selectedModule.table_name === 'users'
                                && userHasRole('super_administrator')">
                            </impersonate-button>

                            <admin-slot-inside-entry-form-footer-bar :vuex-module="vuexModule"></admin-slot-inside-entry-form-footer-bar>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import EntryForm from './EntryForm.js';
    export default EntryForm;
</script>
