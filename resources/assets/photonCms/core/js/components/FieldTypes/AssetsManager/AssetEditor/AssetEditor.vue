<template>
    <div>
        <div class="filename-preview">
            <div class="img-preview" v-if="isAssetSelected && assetsManager.editorMode === 'edit'">
                <a :href="admin.editedEntry.file_url" target="_blank">
                    <img
                        v-if="admin.editedEntry.mime_type.substring(0, 5) == 'image'"
                        :src="admin.editedEntry.file_url" />
                    <i
                        v-else
                        class="fa fa-file-o"
                        style="font-size: 60px; padding: 10px 30px;">
                    </i>
                </a>
            </div>
            <div v-if="isAssetSelected && assetsManager.editorMode === 'edit'">
                <span class="text-muted">{{ $t('assetsManager.fileSize') }}:</span> {{ formatBytes(admin.editedEntry.file_size)}}<br>
                <span class="text-muted">{{ $t('assetsManager.mimeType') }}:</span> {{admin.editedEntry.mime_type}}<br><br>
                <span class="text-muted">{{ $t('admin.createdAt') }}:</span> {{createdAt}}<br>
                <span class="text-muted">{{ $t('admin.createdBy') }}:</span> {{admin.editedEntry.created_by.anchor_text}}<br><br>
                <span class="text-muted">{{ $t('admin.updatedAt') }}:</span> {{updatedAt}}<br>
                <span class="text-muted">{{ $t('admin.updatedBy') }}:</span> {{admin.editedEntry.updated_by.anchor_text}}<br><br>
            </div>
            <div>
                <entry-form v-if="hasFields"
                    :disableCreateAnother="true"
                    :fields="fields"
                    :form-fields-reset-prop="formFieldsReset"
                    :in-sidebar="true"
                    :shouldRouterPush="false"
                    :vuexModule="assetsManager.moduleName">
                </entry-form>
            </div>
        </div>
    </div>
</template>

<script>
    import AssetEditor from './AssetEditor.js';
    export default AssetEditor;
</script>
