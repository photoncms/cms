<template>
    <div class="form-horizontal">
        <div class="form-group">
            <label for="permissionTemplatePicker" class="control-label col-lg-12">
            Select Permission Template
            <i data-toggle="tooltip" data-placement="top" title="" class="fa fa-info-circle uses-tooltip" data-original-title="Permission templates are used to automatically generate permission rules."></i></label>
            <div class="col-lg-12">
                <div>
                    <permission-template-picker
                        field-type="ManyToMany"
                        id="permissionTemplatePicker"
                        name="permissionTemplatePicker"
                        :options-data="permissionTemplateOptions"
                        :required="true"
                        :value="null"
                        >
                    </permission-template-picker>
                </div>
            </div>
        </div>
        <div
            class="form-group"
            v-for="field in permissionNameGeneratorFields"
            v-if="isComponentVisible(field.id)">
            <label :for="field.id" class="control-label col-lg-4">
            {{field.label}}
            <i data-toggle="tooltip" data-placement="top" title="" class="fa fa-info-circle uses-tooltip" :data-original-title="field.tooltip"></i></label>
            <div class="col-lg-8">
                <div>
                    <component
                        :disabled="field.disabled"
                        :field-type="field.vueComponent"
                        :id="field.id"
                        :is="`${field.vueComponent}Field`"
                        :name="field.name"
                        :options-data="field.optionsData"
                        :preload-data-config="field.preloadDataConfig"
                        :preselect-first="field.preselectFirst"
                        :refresh-fields="field.refreshFields"
                        :related-table-name="field.relatedTableName"
                        :required="field.required"
                        :value="field.value"
                        ref="id">
                    </component>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PermissionNameGenerator from './PermissionNameGenerator.js';
    export default PermissionNameGenerator;
</script>
