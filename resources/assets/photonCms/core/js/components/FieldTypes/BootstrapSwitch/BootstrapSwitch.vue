<template>
    <div>
        <input
            :checked="value"
            :disabled="disabled"
            :id="id"
            :name="name"
            :type="type"
            :value="value">
    </div>
</template>

<script>
    import BootstrapSwitch from './BootstrapSwitch.js';
    export default BootstrapSwitch;
</script>
