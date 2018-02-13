<template>
    <div ref="dropzone" class="dropzone">
        <slot name="dropzone-container">
            <!-- <slot name="dropzone-message">Do files here to upload</slot> -->
        </slot>
    </div>
</template>

<script>
    import DropZone from './DropZone.js';
    export default DropZone;
</script>
