<template>
    <div style="height: auto; overflow: hidden">
        <div class="filename-preview" v-for="selectedAsset in selectedAssets">
            <a :href="selectedAsset.file_url" target="_blank">
                <div class="img-preview">
                    <div class="img-container">
                        <img
                            v-if="selectedAsset.mime_type.substring(0, 5) == 'image'"
                            :src="selectedAsset.resized_images[0].file_url" />
                        <i
                            v-else
                            class="fa fa-file-o"
                            style="font-size: 40px; padding: 10px 30px;">
                        </i>
                    </div>
                </div>
            </a>
            <div class="file-extended-info">
                <span class="text-muted">{{ $t('assetsManager.fileName') }}:</span> {{selectedAsset.file_name}}<br>
                <span class="text-muted">{{ $t('assetsManager.fileSize') }}:</span> {{ formatBytes(selectedAsset.file_size)}}<br>
                <span class="text-muted">{{ $t('assetsManager.mimeType') }}:</span> {{selectedAsset.mime_type}}
            </div>
        </div>
        <input type="hidden" :name="name" />
        <div
            v-show="!confirmClearAllSelectedAssets"
            class="btn-group pull-right"
            style="margin-top: 10px;">
            <button
                :disabled="clearFilesButtonDisabled"
                @click="setConfirmClearAllSelectedAssets(true)"
                class="btn btn-default"
                type="button">
                <span v-if="multiple">{{ $t('assetsManager.clearFiles') }}</span>
                <span v-else>{{ $t('assetsManager.clearFile') }}</span>
            </button>
            <button
                :disabled="disabled"
                @click="openAssetsManager"
                class="btn btn-primary"
                type="button">
                <span v-if="multiple">{{ $t('assetsManager.selectFiles') }}</span>
                <span v-else>{{ $t('assetsManager.selectFile') }}</span>
            </button>
        </div>
        <div
            v-show="confirmClearAllSelectedAssets"
            class="btn-group pull-right"
            style="margin-top: 10px;">
            <button
                :disabled="disabled"
                @click="clearAllSelectedAssets"
                class="btn btn-danger"
                type="button">
                {{ $t('admin.confirmDelete') }}
            </button>
            <button
                :disabled="disabled"
                @click="setConfirmClearAllSelectedAssets(false)"
                class="btn btn-default"
                type="button">
                {{ $t('admin.cancelDelete') }}
            </button>
        </div>

        <modal
            v-if="!disabled"
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
                        :disabled="!hasSelectedAssets"
                        class="btn btn-primary"
                        type="button">
                        {{ $t('assetsManager.selectAndClose') }}
                    </button>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
    import AssetsManager from './AssetsManager.js';
    export default AssetsManager;
</script>
