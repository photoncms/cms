<template>
    <div class="sidebar-flex">
        <div class="panel-body">
            <div class="input-group">
                <quick-search :nodes="nodes"></quick-search>
            </div>
        </div>

        <div class="panel-body menu-picker">
            <menu-picker></menu-picker>
        </div>

        <div class="panel-body tree-body">
            <div class="full-height-tree scrollable-both">
                <div v-if="!ui.hasNodes" class="text-muted text-center">{{ $t('admin.noEntriesFound') }}</div>
                <div id="tree"></div>
            </div>
        </div>
    </div>
</template>

<script>
    import MenuItemsEditorSidebar from './MenuItemsEditorSidebar.js';
    export default MenuItemsEditorSidebar;
</script>
