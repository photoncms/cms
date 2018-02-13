<template>
    <div
        class="panel-group sortable-item"
        :data-order="moduleField.order"
        :data-id="moduleField.id">
        <div class="panel panel-default">
            <div class="panel-heading model-field-heading">
                <h4 class="panel-title model-field-title">
                    <a @click="deleteField({ fieldToDelete: moduleField})" class="remove-field" tabindex="-1" style="float:right">
                        <i class="fa fa-minus-circle"></i>
                    </a>
                        {{ moduleFieldName }}<br>
                        <small>{{ $t('generator.fieldConfigurationOptions') }}</small>
                </h4>
                <div class="sort-handle">
                    <i class="fa fa-ellipsis-v"></i>
                </div>
            </div>
            <div :id="'collapse_' + moduleField.order" class="panel-collapse collapse in">
                <div class="panel-body">
                    <div class="form-horizontal" role="form">
                        <form-field
                            :default-value="field.defaultValue"
                            :disabled="field.disabled"
                            :field-type="field.vueComponent"
                            :id="field.id"
                            :inline="true"
                            :label="field.label"
                            :key="field.id"
                            :mutation="field.mutation"
                            :name="field.name"
                            :options-data="field.optionsData"
                            :placeholder="field.placeholder"
                            :preselect-first="field.preselectFirst"
                            :refresh-fields="field.refreshFields"
                            :related-table-name="field.relatedTableName"
                            :ref="field.ref"
                            :form-fields-reset-prop="formFieldsReset"
                            :tooltip="field.tooltip"
                            :value="field.value"
                            v-if="field.optionGroup === 0"
                            v-for="field in moduleFieldOptions"
                            vuexModule="generator"
                            >
                        </form-field>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a
                        class="accordion-toggle"
                        data-toggle="collapse"
                        :href="'#collapse_' + moduleField.order + '_additional'"
                        style="font-size:14px">
                        {{ $t('generator.additionalOptions') }} <i class="fa fa-angle-double-right"></i>
                    </a>
                </h4>
            </div>
            <div :id="'collapse_' + moduleField.order + '_additional'" class="additional-options panel-collapse collapse">
                <div class="panel-body">
                    <div class="form-horizontal" role="form">
                        <form-field
                            :default-value="field.defaultValue"
                            :disabled="field.disabled"
                            :field-type="field.vueComponent"
                            :form-fields-reset-prop="formFieldsReset"
                            :id="field.id"
                            :inline="true"
                            :label="field.label"
                            :key="field.id"
                            :mutation="field.mutation"
                            :name="field.name"
                            :options-data="field.optionsData"
                            :placeholder="field.placeholder"
                            :preselect-first="field.preselectFirst"
                            :related-table-name="field.relatedTableName"
                            :tooltip="field.tooltip"
                            :value="field.value"
                            v-if="field.optionGroup === 1"
                            v-for="field in moduleFieldOptions"
                            vuexModule="generator"
                            >
                        </form-field>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ModuleFieldConfigurator from './ModuleFieldConfigurator.js';
export default ModuleFieldConfigurator;
</script>
