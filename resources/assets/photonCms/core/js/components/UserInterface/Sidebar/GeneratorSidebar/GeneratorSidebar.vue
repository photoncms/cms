<template>
    <div class="sidebar-flex">
        <div class="panel-body"></div>
        <div class="panel-body no-search tree-body">
            <div class="full-height-tree scrollable-both">
                <div v-if="!ui.hasNodes" class="text-muted text-center">{{ $t('admin.noEntriesFound') }}</div>
                <div id="tree"></div>
            </div>
        </div>
    </div>
</template>

<script>
    import GeneratorSidebar from './GeneratorSidebar.js';
    export default GeneratorSidebar;
</script>
