<template>
    <div>
    <textarea class="form-control"
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
        :width="width"
        @input="onChange"
        @blur="onBlur">{{ value }}</textarea>
    </div>
</template>

<script>
    import Textarea from './Textarea.js';
    export default Textarea;
</script>
