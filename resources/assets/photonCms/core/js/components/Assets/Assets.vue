<template>
    <div>
        <modal
            v-if="entryEditorVisible"
            :show="entryEditorVisible"
            class="modal-entry-editor"
            large>

            <div slot="header">
                <a type="button" class="close" @click="closeEntryEditor">&times;</a>
                <h4 class="modal-title">
                    <slot name="title"><i class="fa fa-th"></i>{{ $t('admin.entryEditor') }}</slot>
                </h4>
            </div>

            <div v-if="admin.editorMode === 'edit' || admin.editorMode === 'create'" class="modal-entry-form">
                <entry-form
                    :fields="fields"
                    :form-fields-reset-prop="formFieldsReset"
                    :inSidebar="true"
                    vuexModule="adminModal">
                </entry-form>
            </div>

            <div slot="footer">
                <div class="close-footer clearfix">
                    <button
                        @click="closeEntryEditor"
                        class="btn btn-default"
                        type="button">
                        {{ $t('admin.close') }}
                    </button>
                </div>
            </div>
        </modal>

        <!-- Asset manager modal -->
        <modal
            :show="assetsManagerVisible"
            class="photon-assets"
            large>

            <div slot="header">
                <a type="button" class="close" @click="closeAssetsManager">&times;</a>
                <h4 class="modal-title">
                    <slot name="title"><i class="fa fa-th"></i>{{ $t('assetsManager.assetsManager') }}</slot>
                </h4>
            </div>

            <div class="file-picker-container">
                <file-picker
                    v-if="assetsManagerVisible"
                    v-on:selectedAsset="selectAssetAction"
                    :name="name"
                    :assets-manager="assetsManager">
                </file-picker>
            </div>

            <div slot="footer">
                <div class="close-footer clearfix">
                    <div class="pull-left">
                        <button
                            class="btn btn-primary"
                            :disabled="assetsManager.editorMode=='create'"
                            type="button"
                            @click="prepareForNewUploadAction">
                            <i class="fa fa-upload"></i>
                            {{ $t('assetsManager.uploadFile') }}
                        </button>
                    </div>
                    <button
                        @click="closeAssetsManager"
                        class="btn btn-default"
                        type="button">
                        {{ $t('assetsManager.close') }}
                    </button>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
    import Assets from './Assets.js';
    export default Assets;
</script>
