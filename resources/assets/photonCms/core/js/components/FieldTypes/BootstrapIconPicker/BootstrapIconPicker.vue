<template>
    <div class="input-group">
        <input class="form-control"
            :accept="accept"
            :accesskey="accesskey"
            :align="align"
            :alt="alt"
            :autocomplete="autocomplete"
            :autofocus="autofocus"
            :checked="checked"
            :dirname="dirname"
            :disabled="disabled"
            :form="form"
            :formaction="formaction"
            :formenctype="formenctype"
            :formmethod="formmethod"
            :formnovalidate="formnovalidate"
            :formtarget="formtarget"
            :id="id"
            :height="height"
            :list="list"
            :max="max"
            :maxlength="maxlength"
            :min="min"
            :multiple="multiple"
            :name="name"
            :pattern="pattern"
            :placeholder="placeholder"
            :readonly="readonly"
            :required="required"
            :size="size"
            :src="src"
            :step="step"
            :tabindex="tabindex"
            :type="type"
            :value="value"
            :width="width"
            @input="onChange"
            />
            <span class="input-group-btn">
                <button class="btn btn-primary-outline" :disabled="disabled"></button>
            </span>
    </div>
</template>

<script>
    import BootstrapIconPicker from './BootstrapIconPicker.js';
    export default BootstrapIconPicker;
</script>
