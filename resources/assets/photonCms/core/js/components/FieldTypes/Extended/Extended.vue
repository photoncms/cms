<template>
    <div>
        <div v-for="option in options" :class="{'active': option.id == editedOptionId}" class="col-md-12 extended-relation-listing-entry" >
            <div class="extended-option pull-left">{{ option.anchor_text }}</div>
            <div class="button-container" v-show="!isConfirmEntryVisible(option.id)">
                <button
                    @click="editOption(option.id)"
                    class="btn btn-xs btn-default"
                    type="button">
                    <i class="fa fa-pencil"></i>
                </button>
                <button
                    @click="confirmDeleteEntry(option.id)"
                    class="btn btn-xs btn-default"
                    type="button">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            <div class="button-container confirm-delete" v-show="isConfirmEntryVisible(option.id)">
                <button
                    @click="confirmDeleteEntry(option.id)"
                    class="btn btn-xs btn-default"
                    type="button">
                    <i class="fa fa-ban"></i>
                </button>
                <button
                    @click="removeOption(option.id)"
                    class="btn btn-xs btn-default"
                    type="button">
                    <i class="fa fa-trash"></i>
                </button>
                <span class="text-danger pull-right">{{ $t('admin.confirmDelete') }}:</span>
            </div>
        </div>
        <div
            v-if="formVisible"
                class="col-md-12 extended-relation-listing-entry">
            <entry-form
                :disable-create-another="true"
                :disable-delete="true"
                :extendedFieldEditing="true"
                :fields="fields"
                :form-fields-reset-prop="formFieldsReset"
                :inSidebar="true"
                :name="name"
                :vuexModule="registeredModuleName"
                v-on:submitSuccess="submitSuccess">
            </entry-form>
        </div>

        <div
            class="btn-group pull-right"
            style="margin-top: 10px;">
            <button
                :disabled="!formVisible"
                @click="hideForm"
                class="btn btn-default"
                type="button">
                {{ $t('admin.cancelEditing') }}
            </button>
            <button
                :disabled="formVisible || isOneToMany"
                class="btn btn-primary"
                @click="showForm"
                type="button">
                {{ $t('admin.addOption') }}
            </button>
        </div>
    </div>
</template>

<script>
    import Extended from './Extended.js';
    export default Extended;
</script>
