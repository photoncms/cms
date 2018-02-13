<template>
    <div>
        <select
            :id="id"
            :name="name"
            :value="value"
            :multiple="multiple"
            class="form-control">
            <option v-if="!value"></option>
        </select>
    </div>
</template>

<script>
    import Select2 from './Select2.js';
    export default Select2;
</script>
