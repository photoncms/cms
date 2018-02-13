<template>
    <div>
        <div class="form-group">
            <label for="parentMenu">{{ $t('admin.selectedMenu') }}</label>
            <select id="parentMenu" class="form-control"></select>
        </div>
        <div class="form-group text-muted">{{menuDescription}}</div>
    </div>
</template>

<script>
    import MenuPicker from './MenuPicker.js';
    export default MenuPicker;
</script>
