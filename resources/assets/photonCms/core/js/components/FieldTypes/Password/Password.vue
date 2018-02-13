<template>
    <div class="row">
        <div class="col-lg-10">
            <input
                class="form-control"
                :autocomplete="autocomplete"
                :autofocus="autofocus"
                :form="form"
                :id="id"
                :disabled="disabled"
                :maxlength="maxlength"
                :name="name"
                :readonly="readonly"
                :required="required"
                :tabindex="tabindex"
                :type="type"
                :value="localValue"
                @input="onChange"
                />
        </div>
        <div class="col-lg-2">
            <button type="button"
                @click="revealPassword"
                class="btn btn-link btn-refresh">
                <i class="fa fa-lock"></i>
            </button>
        </div>
    </div>
</template>

<script>
    import Password from './Password.js';
    export default Password;
</script>
