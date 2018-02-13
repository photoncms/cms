<template>
    <div class="sidebar-flex">
        <div class="panel-body">
            <div class="input-group">
                <quick-search :selected-module="selectedModule"></quick-search>
            </div>
            <admin-slot-below-quick-search></admin-slot-below-quick-search>
        </div>
        <div class="panel-body tree-body">
            <div class="full-height-tree scrollable-both">
                <div v-if="!ui.hasNodes" class="text-muted text-center">{{ $t('admin.noEntriesFound') }}</div>
                <div id="tree" v-show="admin.editorMode !== 'search'"></div>
                <advanced-search
                    v-if="admin.editorMode === 'search'"
                    :fields="fields">
                </advanced-search>
                <infinite-loading
                    :on-infinite="onInfinite"
                    v-if="infiniteEnabled"
                    ref="infiniteLoading">
                    <span slot="spinner">
                        <div class="spinner-icon"></div>
                    </span>
                    <span slot="no-results">
                        <div class="text-muted">{{ $t('admin.noEntriesFound') }}</div>
                    </span>
                    <span slot="no-more">
                        <div class="text-muted">{{ $t('admin.noMoreEntries') }}</div>
                    </span>
                </infinite-loading>
            </div>
        </div>
    </div>
</template>

<script>
    import AdminSidebar from './AdminSidebar.js';
    export default AdminSidebar;
</script>
