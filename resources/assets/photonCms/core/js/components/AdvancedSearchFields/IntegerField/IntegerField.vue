<template>
    <div class="list-group-item">
        <div class="form-group">
            <label>{{label}} {{ $t('admin.advancedSearchFieldCalendarFieldFrom') }}</label>
            <div>
                <input-tag
                    v-on:change="onChange"
                    :id="id"
                    :label="`${label} $t('admin.advancedSearchFieldCalendarFieldFrom')`"
                    :name="`filter.${name}.more_than_equal`"
                    :refresh-fields="refreshFields"
                    :value="valueFrom"
                ></input-tag>
            </div>
        </div>
        <div class="form-group">
            <label>{{label}} {{ $t('admin.advancedSearchFieldCalendarFieldTo') }}</label>
            <div>
                <input-tag
                    v-on:change="onChange"
                    :id="id"
                    :label="`${label} $t('admin.advancedSearchFieldCalendarFieldTo')`"
                    :name="`filter.${name}.less_than_equal`"
                    :value="valueTo"
                ></input-tag>
            </div>
        </div>
    </div>
</template>

<script>
    import IntegerField from './IntegerField.js';
    export default IntegerField;
</script>
