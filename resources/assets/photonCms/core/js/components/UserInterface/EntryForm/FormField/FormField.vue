<template>
    <div class="form-group" v-if="!(hidden)">
        <label class="control-label"
            :class="inline ? 'col-lg-4' : 'col-lg-12'"
            :for="id">
            {{fieldLabel}}
            <i
                v-if="tooltip"
                class="fa fa-info-circle uses-tooltip"
                data-toggle="tooltip"
                data-placement="top"
                :title="tooltip">
            </i>
        </label>
        <div :class="inline ? 'col-lg-8' : 'col-lg-12'">
            <component
                v-on:change="onChange"
                :can-create-search-choice="canCreateSearchChoice"
                :default-value="defaultValue"
                :disabled="disabled"
                :field-type="fieldType"
                :flatten-to-optgroups="flattenToOptgroups"
                :id="id"
                :is="`${fieldType}Field`"
                :is-active-entry-filter="isActiveEntryFilter"
                :lazy-loading="lazyLoading"
                :multiple="multiple"
                :mutation="mutation"
                :name="name"
                :options-data="optionsData"
                :preload-data-config="preloadDataConfig"
                :placeholder="placeholder"
                :preselect-first="preselectFirst"
                :related-table-name="relatedTableName"
                :refresh-fields="refreshFields"
                :required="required"
                :tooltip="tooltip"
                :value="value"
                :vuexModule="vuexModule"
                ref="id">
            </component>
            <div :id="`${name}-error`" class="form-field-error"></div>
        </div>
    </div>
</template>

<script>
    import FormField from './FormField.js';
    export default FormField;
</script>
