<template>
    <div class="col-md-6 col-lg-8">
        <form
            id="generator-form"
            data-parsley-excluded="input[type=button], input[type=submit], input[type=reset]">
            <div class="panel panel-default panel-block">
                <div class="list-group">
                    <div class="list-group-item">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <div class="btn-group pull-right" style="margin-bottom: 10px;">
                                        <button
                                            :disabled="!moduleHasFields"
                                            class="btn btn-default dropdown-toggle"
                                            data-toggle="dropdown"
                                            type="button">
                                            {{ $t('generator.options') }} <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-arrow pull-right" role="menu">
                                            <li><a href="javascript:;" @click="toggleAdditionalOptions({ show: true })">
                                                {{ $t('generator.showAllAdditionalOptions') }}
                                            </a></li>
                                            <li><a href="javascript:;" @click="toggleAdditionalOptions({ show: false })">
                                                {{ $t('generator.hideAllAdditionalOptions') }}

                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <form-field
                                :disabled="field.disabled"
                                :field-type="field.vueComponent"
                                :form-fields-reset-prop="formFieldsReset"
                                :id="field.id"
                                :inline="true"
                                :key="field.id"
                                :label="field.label"
                                :mutation="field.mutation"
                                :name="field.name"
                                :options-data="field.optionsData"
                                :placeholder="field.placeholder"
                                :preselect-first="field.preselectFirst"
                                :related-table-name="field.relatedTableName"
                                :required="field.required"
                                :tooltip="field.tooltip"
                                :value="field.value"
                                v-for="field in moduleOptions"
                                vuexModule="generator"
                                >
                            </form-field>
                        </div>
                    </div>
                </div>
            </div>
            <input name="fields" type="hidden">
            <div class="sortable-fields" v-if="moduleHasFields">
                <div class="panel panel-default panel-block panel-title-block">
                    <div class="panel-heading">
                        <div>
                            <h1>Module Fields
                                <small>The following items represent the fields beloging to this module.</small>
                            </h1>
                        </div>
                    </div>
                </div>
                <module-field-configurator
                    :form-fields-reset="formFieldsReset"
                    :key="moduleField.id"
                    :module-field="moduleField"
                    v-for="moduleField in generator.selectedModule.fields">
                </module-field-configurator>
            </div>
            <div id="fields-error" class="form-field-error"></div>
            <button
                style="margin-bottom: 20px;"
                @click="createNewField"
                type="button"
                class="btn btn-primary btn-block">
                {{ $t('generator.addNewField') }}
            </button>
            <div class="panel panel-default panel-block">
                <div class="list-group">
                    <div class="list-group-item" style="height: auto; overflow: hidden">
                        <div class="form-group pull-left no-bottom-margin">
                            <button type="button"
                                :disabled="generator.newModule"
                                @click="navigateToModule(generator.selectedModule.table_name)"
                                class="btn btn-default">
                                <i class="fa fa-eye"></i> {{ $t('generator.viewModule') }}
                            </button>
                        </div>
                        <div v-if="serverError" class="form-group clearfix">
                            <div class="server-error pull-right" >{{serverError}}</div>
                        </div>
                        <div class="form-group pull-right">
                            <button type="button" v-if="generator.selectedModule && !generator.newModule" @click="deleteModule({ reporting: true })" class="btn btn-danger">{{ $t('generator.delete') }}</button>
                            <button type="submit" v-if="generator.selectedModule && !generator.newModule" class="btn btn-success">{{ $t('generator.saveChanges') }}</button>
                            <button type="submit" v-if="generator.selectedModule && generator.newModule" class="btn btn-success">{{ $t('generator.createModule') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- Module reporter -->
        <module-reporter></module-reporter>
    </div>
</template>

<script>
import ModuleConfigurator from './ModuleConfigurator.js';
export default ModuleConfigurator;
</script>
