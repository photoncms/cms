<template>
    <div class="panel panel-default panel-block">
        <div class="list-group">
            <div class="list-group-item">
                <div class="form-group">
                    <label>{{ $t('admin.fetchAModuleIdByModuleNameHelper') }}</label>
                    <div>
                        <ManyToOneField
                            v-on:change="hydrateModuleField"
                            :disabled="admin.editorMode === 'edit'"
                            :field-type="fieldOption.fieldType"
                            :id="fieldOption.id"
                            :name="fieldOption.name"
                            :refresh-fields="fieldOption.refreshFields"
                            :options-data="fieldOption.optionsData"
                            :preselect-first="fieldOption.preselectFirst"
                            :required="fieldOption.required"
                            :tooltip="fieldOption.tooltip"
                            :value="fieldOption.value"
                            ref="fieldOption.id">
                        </ManyToOneField>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import FieldGroupsHelper from './FieldGroupsHelper.js';
    export default FieldGroupsHelper;
</script>
