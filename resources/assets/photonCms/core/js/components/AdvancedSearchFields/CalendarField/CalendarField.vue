<template>
    <div class="list-group-item">
        <div class="form-group">
            <label>{{label}} {{ $t('admin.advancedSearchFieldCalendarFieldFrom') }}</label>
            <div>
                <bootstrap-datetimepicker
                    v-on:change="onChange"
                    :field-type="fieldType"
                    :id="id"
                    :label="`${label} $t('admin.advancedSearchFieldCalendarFieldFrom')`"
                    :name="`filter.${name}.more_than_equal`"
                    :refresh-fields="refreshFields"
                    :value="valueFrom"
                ></bootstrap-datetimepicker>
            </div>
        </div>
        <div class="form-group">
            <label>{{label}} {{ $t('admin.advancedSearchFieldCalendarFieldTo') }}</label>
            <div>
                <bootstrap-datetimepicker
                    v-on:change="onChange"
                    :field-type="fieldType"
                    :id="id"
                    :label="`${label} $t('admin.advancedSearchFieldCalendarFieldTo')`"
                    :name="`filter.${name}.less_than_equal`"
                    :value="valueTo"
                ></bootstrap-datetimepicker>
            </div>
        </div>
    </div>
</template>

<script>
    import CalendarField from './CalendarField.js';
    export default CalendarField;
</script>
