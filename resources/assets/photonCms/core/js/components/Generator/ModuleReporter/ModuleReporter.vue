<template>
    <div id="modal-report" tabindex="-1" role="dialog" class="modal modal-report fade">
        <div class="modal-dialog">
           <div class="modal-content">
                <div class="modal-header">
                   <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                   <h4 class="modal-title"><i class="fa fa-save"></i><span>{{ $t('generator.pendingActions') }}</span></h4>
                </div>
                <div v-if="reportType !== 'delete'" class="modal-body scrollable">
                    <ul
                        v-for="(changeSection, changeSectionName) in report"
                        class="list-group">
                        <li v-for="changeSubject in changeSection"
                           class="list-group-item report">
                            <h5
                               class="label label-success"
                                v-if="changeSubject.change_type == 'add'">
                                <span>{{ $t('generator.create') }} {{ changeSectionName }}</span>
                            </h5>
                            <h5
                                class="label label-warning"
                                v-if="changeSubject.change_type == 'update'">
                                <span>{{ $t('generator.update') }} {{ changeSectionName }}</span>
                            </h5>
                            <h5
                                class="label label-danger"
                                v-if="changeSubject.change_type == 'delete'">
                                <span>{{ $t('generator.delete') }} {{ changeSectionName }}</span>
                            </h5>
                            <div
                                class="change"
                                v-for="(change, key) in changeSubject.data">
                                <span
                                   v-if="change.old === null"
                                   class="change-name">{{ $t('generator.set') }} {{ key | separateWords | titleCase}}:
                                </span>
                                <span
                                   v-if="change.old !== null"
                                   class="change-name">{{ $t('generator.update') }} {{ key | separateWords | titleCase}}:
                                </span>
                                <span
                                   v-if="change.old !== null"
                                   class="label label-danger">{{change.old | toString}}
                                </span>
                                <i
                                   v-if="change.old !== null"
                                   class="fa fa-arrow-right">
                                </i>
                                <span class="label label-info">{{ change.new }}</span>
                            </div>
                        </li>
                    </ul>
                </div>
               <div v-if="reportType === 'delete'" class="modal-body scrollable">
                    <ul
                        class="list-group">
                            <li class="list-group-item report">
                               <h5 class="label label-danger">
                                   <span>{{ $t('generator.deleteModule') }} <strong>{{ selectedModule.table_name }}</strong></span>
                               </h5>
                            </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ $t('generator.cancel') }}</button>
                    <button
                    v-if="reportType === 'create'"
                    @click="confirmChange"
                    type="button"
                    class="btn btn-success"
                    data-dismiss="modal">{{ $t('generator.confirmNewModuleCreation') }}</button>
                    <button
                    v-if="reportType === 'update'"
                    @click="confirmChange"
                    type="button"
                    class="btn btn-warning"
                    data-dismiss="modal">{{ $t('generator.confirmUpdate') }}</button>
                    <button
                    v-if="reportType === 'delete'"
                    @click="confirmChange"
                    type="button"
                    class="btn btn-danger"
                    data-dismiss="modal">{{ $t('generator.confirmDeletion') }}</button>
                </div>
           </div>
        </div>
   </div>
</template>

<script>
import ModuleReporter from './ModuleReporter.js';
export default ModuleReporter;
</script>
