<template>
    <div>
        <div :id="`${id}-container`" class="relative-container input-append date form_datetime">
            <input
                class="form-control"
                type="text"
                :disabled="disabled"
                :id="id"
                :placeholder="placeholder"
                :value="humanReadableDate"
                @input="onChange"
                readonly
                />
            <span class="add-on remove"><i class="fa fa-times"></i></span>
            <span class="add-on"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
</template>

<script>
    import BootstrapDatetimepicker from './BootstrapDatetimepicker.js';
    export default BootstrapDatetimepicker;
</script>
