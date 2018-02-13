<template>
    <div>
        <select
            class="form-control"
            :disabled="disabledSearch">
        </select>
        <div class="input-group-btn">
            <button
                class="btn btn-default btn-search btn-search-advanced-shift"
                :disabled="disabledSearch"
                type="button">
                <i class="fa fa-search"></i>
            </button>
            <button
                :disabled="disabledAdvancedSearch"
                class="btn btn-default btn-advanced-search"
                :class="{ active: activeSearch }"
                type="button"
                @click="enterSearchMode">
                <i class="fa fa-ellipsis-v"></i>
            </button>
        </div>
    </div>
</template>

<script>
    import QuickSearch from './QuickSearch.js';
    export default QuickSearch;
</script>
