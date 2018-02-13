<template>
    <span>
        <textarea
            :disabled="disabled"
            :id="id"
            :name="name"
            :value="value"
            autocapitalize="off"
            autocomplete="off"
            autocorrect="off"
            spellcheck="false">
        </textarea>
        <modal
            v-if="!disabled && assetsManagerVisible"
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
                            type="button"
                            :disabled="assetsManager.editorMode=='create'"
                            @click="prepareForNewUploadAction">
                            <i class="fa fa-upload"></i>
                            {{ $t('assetsManager.uploadFile') }}
                        </button>
                        <input
                            type="checkbox"
                            id="selectAfterUpload"
                            value="`selectAfterUpload-${name}`"
                            v-model="selectAfterUpload">
                        <label for="selectAfterUpload">{{ $t('assetsManager.selectAfterUpload') }}</label>
                    </div>
                    <button
                        @click="closeAssetsManager"
                        class="btn btn-default"
                        type="button">
                        {{ $t('assetsManager.close') }}
                    </button>
                    <button
                        @click="selectAssets"
                        class="btn btn-primary"
                        type="button">
                        {{ $t('assetsManager.selectAndClose') }}
                    </button>
                </div>
            </div>
        </modal>
    </span>
</template>

<script>
    import Redactor from './Redactor.js';
    export default Redactor;
</script>
