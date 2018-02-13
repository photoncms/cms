<template>
    <div>
        <div v-if="admin.editorMode == 'create'" class="fileinput fileinput-new">
            <drop-zone
                :on-success="newFileUploaded"
                :vuexModule="vuexModule"
                class="fileinput-preview thumbnail photon-assets"
            >
                <div slot="dropzone-container">
                    <div class="dz-message"></div>
                </div>
            </drop-zone>
            <div class="btn-group pull-right" style="margin-top: 10px;">
                <button
                    class="btn btn-default"
                    id="clear-files"
                    type="button">
                    <i class="fa fa-trash-o"></i>
                    {{ $t('assetsManager.clearFiles') }}
                </button>
                <button
                    class="btn btn-primary"
                    id="upload-files"
                    type="button">
                    <i class="fa fa-upload"></i>
                    {{ $t('assetsManager.uploadFiles') }}
                </button>
            </div>
        </div>

<!--         <div v-else class="fileinput fileinput-new" data-provides="fileinput">
          <div class="fileinput-preview thumbnail" data-trigger="fileinput"></div>
          <div>
            <span class="btn btn-primary btn-file pull-right">
                <span class="fileinput-new">{{ $t('admin.selectFile') }}</span>
                <span class="fileinput-exists">{{ $t('admin.change') }}</span>
                <input
                    @change="onChange"
                    :id="id"
                    :name="name"
                    value=""
                    type="file">
            </span>
            <a href="#" class="btn btn-primary fileinput-exists pull-right" data-dismiss="fileinput">{{ $t('admin.remove') }}</a>
          </div>
        </div>
 -->    </div>
</template>

<script>
    import FileUpload from './FileUpload.js';
    export default FileUpload;
</script>
