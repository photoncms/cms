<template>
    <div class="list-group-item">
        <div class="form-group">
            <label>{{label}}</label>
            <div>
                <input-tag
                    v-on:change="onChange"
                    :id="id"
                    :label="label"
                    :name="`filter.${name}.like`"
                    :refresh-fields="refreshFields"
                    :value="value"
                ></input-tag>
            </div>
        </div>
    </div>
</template>

<script>
    import InputTextField from './InputTextField.js';
    export default InputTextField;
</script>
