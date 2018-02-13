<template>
    <div>
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
            :spellcheck="spellcheck"
            :src="src"
            :step="step"
            :tabindex="tabindex"
            :type="type"
            :value="value"
            :width="width"
            @input="onChange"
            @blur="onBlur"
            />
    </div>
</template>

<script>
    import InputTag from './InputTag.js';
    export default InputTag;
</script>
