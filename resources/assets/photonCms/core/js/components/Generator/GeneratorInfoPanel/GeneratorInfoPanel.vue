<template>
    <div class="panel panel-default panel-block">
        <div class="list-group">

            <div class="list-group-item text-center">
                <button type="submit"
                    :disabled="generator.newModule"
                    @click="createNewModule"
                    class="btn btn-primary create-new-module-button">
                    <i class="fa fa-plus-square-o"></i> {{ $t('generator.createNewModule') }}
                </button>
            </div>

            <div class="list-group-item text-center">
                <button type="button"
                    :disabled="generator.newModule"
                    @click="navigateToModule(generator.selectedModule.table_name)"
                    class="btn btn-default view-module-button">
                    <i class="fa fa-eye"></i> {{ $t('generator.viewModule') }}
                </button>
            </div>


            <div v-if="!generator.newModule" class="list-group-item">
                <ul class="inline-font-icons-list date-created">
                    <li>
                        <i class="fa fa-calendar-check-o"></i>
                        {{ $t('admin.createdAt') }}: <strong>{{createdAt}}</strong>
                    </li>
                </ul>
                <br>
                <ul class="inline-font-icons-list date-created">
                    <li>
                        <i class="fa fa-calendar"></i>
                        {{ $t('admin.updatedAt') }}: <strong>{{updatedAt}}</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    import GeneratorInfoPanel from './GeneratorInfoPanel.js';
    export default GeneratorInfoPanel;
</script>

<style scoped>
    .create-new-module-button {
        min-width: 150px;
    }
    .view-module-button {
        min-width: 150px;
    }
</style>
