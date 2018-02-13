<template>
    <div class="list-group-item">
        <div class="form-group">
            <label>{{label}}</label>
            <div>
                <boolean-field
                    v-on:change="onChange"
                    :id="id"
                    :label="label"
                    :name="`filter.${name}.equal`"
                    :refresh-fields="refreshFields"
                    :value="value"
                ></boolean-field>
            </div>
        </div>
    </div>
</template>

<script>
    import BooleanField from './BooleanField.js';
    export default BooleanField;
</script>
