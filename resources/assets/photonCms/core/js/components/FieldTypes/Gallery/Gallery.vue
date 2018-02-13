<template>
    <div>
        <div
            class="col-lg-12 gallery-items"
            id="ui-sortable"
            v-show="galleryItems.length > 0">
            <div
                class="gallery-item"
                :data-gallery-item-id="galleryItem.id"
                v-for="galleryItem in galleryItems">
                <img :src="getAssetUrl(galleryItem.asset)"/>
                <div class="button-container">
                    <button
                        @click="deleteGalleryItem(galleryItem.id)"
                        class="btn btn-xs btn-default"
                        type="button">
                        <i class="fa fa-trash"></i>
                    </button>
                    <button
                        @click="openEntryEditor(galleryItem.id)"
                        class="btn btn-xs btn-default"
                        type="button">
                        <i class="fa fa-pencil"></i>
                    </button>
                    <a
                        :href="galleryItem.asset.file_url"
                        class="btn btn-xs btn-default"
                        target="_blank">
                        <i class="fa fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>
        <div
            v-if="!galleryDeleteConfirmationVisible"
            class="btn-group pull-right"
            style="margin-top: 10px;">
            <button
                :disabled="!galleryId"
                @click="predeleteGallery"
                class="btn btn-default"
                type="button">
                {{ $t('admin.deleteGallery') }}
            </button>
            <button
                :disabled="!galleryId"
                @click="openAssetsManager"
                class="btn btn-primary"
                type="button">
                {{ $t('admin.addImages') }}
            </button>
            <button
                :disabled="galleryId"
                class="btn btn-primary"
                @click="createGallery"
                type="button">
                {{ $t('admin.createGallery') }}
            </button>
        </div>
        <div
            v-if="galleryDeleteConfirmationVisible"
            class="btn-group pull-right"
            style="margin-top: 10px;">
            <button
                :disabled="disabled"
                @click="deleteGallery"
                class="btn btn-danger"
                type="button">
                {{ $t('admin.confirmDelete') }}
            </button>
            <button
                :disabled="disabled"
                @click="predeleteGallery"
                class="btn btn-default"
                type="button">
                {{ $t('admin.cancelDelete') }}
            </button>
        </div>
        <!-- Entry editor modal -->
        <modal
            v-if="entryEditorVisible"
            :show="entryEditorVisible"
            class="modal-entry-editor">

            <div slot="header">
                <a type="button" class="close" @click="closeEntryEditor">&times;</a>
                <h4 class="modal-title">
                    <slot name="title"><i class="fa fa-th"></i>{{ $t('admin.entryEditor') }}</slot>
                </h4>
            </div>

            <div v-if="admin.editorMode === 'edit' || admin.editorMode === 'create'" class="modal-entry-form">
                <entry-form
                    :fields="fields"
                    :disableDelete="true"
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
                        @click="selectAssets(false)"
                        class="btn btn-primary"
                        type="button">
                        {{ $t('assetsManager.select') }}
                    </button>
                    <button
                        @click="selectAssets()"
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
    import Gallery from './Gallery.js';
    export default Gallery;
</script>
