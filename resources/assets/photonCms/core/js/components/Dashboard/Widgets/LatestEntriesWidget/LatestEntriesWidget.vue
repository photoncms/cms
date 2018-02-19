<template>
    <div
        :id="`widget.${widget.id}`"
        class="proton-widget widget-latest"
        :class="{ 'setup': setupMode }">
        <div v-if="userHasRole('super_administrator')" class="panel panel-default back">
            <div class="panel-heading">
                <i class="fa fa-cog"></i>
                <span>{{ $t('dashboard.settings') }}</span>
                <div @click="toggleSetup()" class="toggle-widget-setup">
                    <i class="fa fa-check"></i>
                    <span>{{ $t('dashboard.done') }}</span>
                </div>
            </div>

            <ul class="list-group scrollable">
                <li :class="`widget-type-text ${widget.theme}-type-text`">{{ $t('dashboard.latestEntries') }}</li>
                <li class="list-group-item">
                    <form-field
                        :disabled="field.disabled"
                        :field-type="field.vueComponent"
                        :form-fields-reset-prop="formFieldsReset"
                        :id="field.id"
                        :inline="field.inline"
                        :key="field.id"
                        :label="field.label"
                        :mutation="field.mutation"
                        :name="field.name"
                        :options-data="field.optionsData"
                        :placeholder="field.placeholder"
                        :preselect-first="field.preselectFirst"
                        :related-table-name="field.relatedTableName"
                        :required="field.required"
                        :tooltip="field.tooltip"
                        :value="field.value"
                        v-for="field in fields"
                        vuexModule="generator"
                        >
                    </form-field>
                </li>
                <li class="list-group-item">
                    <div class="form-group">
                        <button
                            type="button"
                            @click="removeWidget" class="btn btn-danger delete-widget">
                            {{ $t('dashboard.deleteWidget') }} <i class="fa fa-trash-o"></i>
                        </button>
                    </div>
                </li>
            </ul>
        </div>

        <div :class="`panel ${getWidgetTheme} front`">
            <div class="panel-heading" :class="{'with-heading-icon': widget.icon}">
                <i v-if="widget.icon" :class="widget.icon"></i>
                <span>{{widget.heading}}</span>
                <i v-if="userHasRole('super_administrator')" @click="toggleSetup" class="fa fa-cog toggle-widget-setup"></i>
            </div>
            <div>
                <ul class="list-group pending condensed">
                    <li
                        class="list-group-item"
                        :widget="widget"
                        v-for="item in items"
                        track-by="id">
                        <router-link :to="`admin/${widget.module}/${item.id}`">
                            <i v-if="item[metaData.image_field]">
                                <img :src="addThumbSuffix(item[metaData.image_field].file_url)" :alt="item.anchor_text">
                            </i>
                            <div
                                class="text-holder"
                                :class="{
                                    'no-image': !item[metaData.image_field],
                                }">
                                <span class="title-text">{{ item.anchor_text }}</span>
                            </div>
                            <span class="stat-value">{{item.timeAgo}}</span>
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    import LatestEntriesWidget from './LatestEntriesWidget.js';
    export default LatestEntriesWidget;
</script>
