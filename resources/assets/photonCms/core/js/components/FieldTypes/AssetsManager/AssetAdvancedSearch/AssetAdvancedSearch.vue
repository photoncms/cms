<template>
    <div class="sidebar">
        <div class="advanced-search">
            <div class="panel panel-default">
                <div class="arrow adv-search-arrow"></div>
                <div class="panel-heading">
                    <i class="fa fa-search"></i>
                    <span>{{ $t('admin.advancedSearch') }}</span>
                </div>

                <div class="panel-body">
                    <div class="list-group">

                        <component
                            :advanced-search-name="assetsAdvancedSearch.advancedSearchName"
                            :fieldType="field.vueComponent"
                            :id="field.id"
                            :is="`advancedSearch${field.vueComponent}Field`"
                            :key="`advanced-search-${field.id}`"
                            :label="field.label"
                            :name="field.name"
                            :related-table-name="field.relatedTableName"
                            ref="field.id"
                            v-if="!field.hidden"
                            v-for="field in fields">
                        </component>

                        <div class="list-group-item text-right">
                            <button
                                type="button"
                                class="btn btn-success"
                                style="width: 100%"
                                @click="submitSearch">
                                {{ $t('admin.search') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import AssetAdvancedSearch from './AssetAdvancedSearch.js';
    export default AssetAdvancedSearch;
</script>
