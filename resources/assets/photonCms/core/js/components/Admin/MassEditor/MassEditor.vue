<template>
    <div class="mass-editor">
        <form
            id="mass-editor-form"
            data-parsley-excluded="input[type=button], input[type=submit], input[type=reset]"
            v-show="advancedSearch.showMassEditor">

            <div class="form-horizontal">
                <h4 class="section-title">{{ $t('admin.massEditor') }}</h4>

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

            <div class="panel panel-default panel-block">
                <div class="list-group">
                    <div class="list-group-item form-footer">
                        <div v-if="serverError" class="form-group clearfix">
                            <div class="server-error pull-right">{{serverError}}</div>
                        </div>

                        <div class="btn-group pull-right no-bottom-margin" style="margin-bottom: 0;">
                            <button
                                :disabled="admin.submitInProgress"
                                type="button"
                                @click="hideMassEditor" class="btn btn-default">
                                {{ $t('admin.cancel') }}
                            </button>

                            <button
                                @click="massEdit"
                                :disabled="admin.submitInProgress"
                                type="button"
                                class="btn btn-danger">
                                {{ $t('admin.applyChanges') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import MassEditor from './MassEditor.js';
    export default MassEditor;
</script>
