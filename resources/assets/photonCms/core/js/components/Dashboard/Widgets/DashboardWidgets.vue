<template>
    <div>
        <section class="widget-group">
            <component
                :is="singleWidget.type"
                :key="singleWidget.id"
                :widget="singleWidget"
                v-for="singleWidget in widget.widgets">
            </component>
            <div
                v-if="userHasRole('super_administrator')"
                data-toggle="modal"
                href="#new-widget-modal"
                class="placeholder drag-placeholder new-widget-placeholder">
            </div>
            <div
                v-if="!userHasRole('super_administrator') && widget.widgets.length == 0"
                class="placeholder drag-placeholder new-widget-placeholder disabled">
            </div>
        </section>
        <div id="new-widget-modal" tabindex="-1" role="dialog" class="modal modal-new-widget fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-plus"></i><span>{{ $t('dashboard.selectNewWidgetType') }}</span></h4>
                    </div>
                    <div class="modal-body">
                        <div
                            @click="newWidget('latest-entries-widget')"
                            class="widget-screen">
                            <img src="/images/widgets/latest.jpg" alt="Latest Widget">
                        </div>
                        <div style="opacity: .2; cursor: inherit;"
                            class="widget-screen">
                            <img src="/images/widgets/stats.jpg" alt="Stats Widget">
                        </div>
                        <div style="opacity: .2; cursor: inherit;"
                            class="widget-screen">
                            <img src="/images/widgets/barchart.jpg" alt="Barchart Widget">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ $t('admin.cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import DashboardWidgets from './DashboardWidgets.js';
    export default DashboardWidgets;
</script>

