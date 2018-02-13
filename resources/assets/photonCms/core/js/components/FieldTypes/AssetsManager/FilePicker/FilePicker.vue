<template>
    <div class="file-picker-holder">
        <slot name="head">
            <div class="assets-head table-responsive">
                <table class="table">
                    <tr>
                        <td>
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    :placeholder="$t('assetsManager.SearchByFilename')"
                                    type="text"
                                    :value="getSearchByFilenameValue"
                                    @keyup="search">
                                <div class="input-group-btn">
                                    <button class="btn btn-default btn-search" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="sort">
                            <select
                                class="form-control"
                                id="source"
                                style="visibility: visible"
                                @change="setSortingOption">
                                <option
                                    v-for="sortingOption in sortingOptions"
                                    v-html="sortingOption.title"
                                    :value="sortingOption.value"
                                    :selected="sortingOption.selected">
                                </option>
                            </select>
                        </td>
                        <td class="buttons">
                            <div class="btn-group">
                                <button
                                    :class="tableClassActive"
                                    @click="setActiveView('table')"
                                    class="btn btn-default"
                                    type="button">
                                    <i class="fa fa-list"></i>
                                </button>
                                <button
                                    :class="thumbnailClassActive"
                                    @click="setActiveView('thumbnail')"
                                    class="btn btn-default"
                                    type="button">
                                    <i class="fa fa-th-large"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </slot>

        <div class="file-picker-holder-body">
            <div class="assets-manager-sidebar">
                <div class="panel-group" id="file-picker-accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#file-picker-accordion" href="#accordion-assets-manager-search" id="accordion-assets-manager-search-label">
                                    {{ $t('admin.advancedSearch') }}
                                </a>
                            </h4>
                        </div>
                        <div id="accordion-assets-manager-search" class="panel-collapse collapse in">
                            <div class="panel-body">
                                <asset-advanced-search
                                    v-on:submitSearch="submitSearch"
                                    :assets-manager="assetsManager"
                                    >
                                </asset-advanced-search>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#file-picker-accordion" href="#accordion-image-editor">
                                    {{ $t('assetsManager.resizedImageFiles') }}
                                </a>
                            </h4>
                        </div>
                        <div id="accordion-image-editor" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div v-if="selectedImageSize && !resizedImageURL" class="alert alert-dismissable alert-info fade in">
                                    <span class="title"><i class="fa fa-info-circle"></i> {{ $t('assetsManager.information') }}</span>
                                    {{ $t('assetsManager.pleaseSelectTheImageToStartEditing') }}
                                </div>
                                <image-sizes
                                    v-on:change="onChange"
                                    :disabled="imageSizesOptions.disabled"
                                    :id="imageSizesOptions.id"
                                    :lazyLoading="imageSizesOptions.lazyLoading"
                                    :multiple="imageSizesOptions.multiple"
                                    :name="imageSizesOptions.name"
                                    :placeholder="imageSizesOptions.placeholder"
                                    :preloadDataConfig="imageSizesOptions.preloadDataConfig"
                                    :readonly="imageSizesOptions.readonly"
                                    :relatedTableName="imageSizesOptions.relatedTableName"
                                    :required="imageSizesOptions.required"
                                    :value="imageSizesOptions.value"></image-sizes>
                                <br>
                                <div v-if="selectedImageSize">
                                    <div class="image-size-preview-container">
                                        <img
                                            v-if="resizedImageURL"
                                            :src="resizedImageURL"
                                            class="image-size-preview" />
                                    </div>
                                    <br>
                                    <div class="text-center" v-if="selectedImageSize">
                                        <div class="btn-group pull-right">
                                            <button
                                                @click="cancelImageFraming"
                                                class="btn btn-default"
                                                type="button">
                                                {{ $t('assetsManager.close') }}
                                            </button>
                                            <button
                                                @click="revertSelection"
                                                class="btn btn-default"
                                                type="button"
                                                :disabled="!editedResizeImageData">
                                                {{ $t('assetsManager.revertSelection') }}
                                            </button>
                                            <button
                                                @click="resizeImage"
                                                class="btn btn-primary"
                                                type="button"
                                                :disabled="!editedResizeImageData">
                                                {{ $t('assetsManager.updateImage') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" id="accordion-asset-details-label" data-toggle="collapse" data-parent="#file-picker-accordion" href="#accordion-asset-details">
                                    {{ $t('assetsManager.insertEditFile') }}
                                </a>
                            </h4>
                        </div>
                        <div id="accordion-asset-details" class="panel-collapse collapse">
                            <div class="panel-body">
                                <asset-editor
                                    :assets-manager="assetsManager"
                                    :update-asset-editor="updateAssetEditor"
                                    ></asset-editor>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="fixed-scroll">
                <div class="fixed-scroll-scroller">
                    <layout
                        :active-view="activeView"
                        :assets-manager="assetsManager"
                        v-show="!imgAreaSelectActive"
                        v-on:selectedAsset="selectAsset"
                    >
                    </layout>
                    <cropper
                        :fileUrl="assetsManager.editedEntry.file_url"
                        :lockHeight="selectedImageSize.lock_height"
                        :lockWidth="selectedImageSize.lock_width"
                        :refresh="imgAreaSelectActiveRefresh"
                        :resizedImageId = "resizedImage.id"
                        :selectionHeight="resizedImage.height"
                        :selectionWidth="resizedImage.width"
                        :topX="resizedImage.top_x"
                        :topY="resizedImage.top_y"
                        v-if="imgAreaSelectActive"
                        v-on:change="onImgAreaSelect">
                    </cropper>
                    <infinite-loading
                        :on-infinite="onInfinite"
                        ref="infiniteLoading"
                        v-if="!imgAreaSelectActive">
                        <span slot="spinner">
                            <div class="spinner-icon"></div>
                        </span>
                        <span slot="no-results">
                            <div class="text-muted">{{ $t('assetsManager.noMoreEntries') }}</div>
                        </span>
                        <span slot="no-more">
                            <div class="text-muted">{{ $t('assetsManager.noMoreEntries') }}</div>
                        </span>
                    </infinite-loading>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import FilePicker from './FilePicker.js';
    export default FilePicker;
</script>
