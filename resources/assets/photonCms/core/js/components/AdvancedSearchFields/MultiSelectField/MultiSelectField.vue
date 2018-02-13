<template>
    <div class="list-group-item">
        <div class="form-group">
            <div>{{label}}
                <div class="pull-right">
                    <input
                        :id="`useAnd${id}`"
                        :value="`useAnd${id}`"
                        type="checkbox"
                        v-model="useAnd">
                    <label :for="`useAnd${id}`">{{ $t('admin.useAnd') }}</label>
                </div>
            </div>
            <div>
                <select2
                    v-on:change="onChange"
                    :id="id"
                    :label="label"
                    :lazyLoading="true"
                    :multiple="true"
                    :name="select2Name"
                    :refresh-fields="refresh"
                    :relatedTableName="relatedTableName"
                    :value="fieldValue"
                ></select2>
            </div>
        </div>
    </div>
</template>

<script>
    import MultiSelectField from './MultiSelectField.js';
    export default MultiSelectField;
</script>
