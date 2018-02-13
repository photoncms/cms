<template>
    <div class="col-md-6 col-lg-8">
        <div class="panel-group">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-horizontal">
                        <div class="btn-group pull-right" style="margin-bottom: 10px;">
                            <button
                                type="button"
                                class="btn btn-primary dropdown-toggle"
                                :disabled="!results"
                                data-toggle="dropdown">
                                {{ $t('admin.action') }} <span class="caret"></span>
                            </button>
                            <ul v-if="results" class="dropdown-menu dropdown-menu-arrow pull-right" role="menu">
                                <li><a href="javascript:;" @click="downloadFile('CSV')">{{ $t('admin.exportAsCSV') }}</a></li>
                                <li><a href="javascript:;" @click="downloadFile('XLSX')">{{ $t('admin.exportAsXLSX') }}</a></li>
                                <li><a href="javascript:;" @click="downloadFile('PDF')">{{ $t('admin.exportAsPDF') }}</a></li>
                                <li v-if="  user.meta.roles[0].name === 'administrator'
                                    || user.meta.roles[0].name === 'operator'">
                                    <a href="javascript:;" @click="showMassEditor()">{{ $t('admin.massEdit') }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <form
                        id="mass-editor-form"
                        v-show="admin.showMassEditor"
                        data-parsley-excluded="input[type=button], input[type=submit], input[type=reset]">
                        <div class="col-md-12 form-horizontal" style="margin-bottom:10px;">
                            <h4 class="section-title">{{ $t('admin.massEditor') }}</h4>
                            <component
                                :is="field.vueComponent"
                                :field="field"
                                :inline="true"
                                v-for="field in fields">
                            </component>
                            <div class="col-md-12 form-horizontal" style="margin-bottom:10px;">
                                <div class="form-group pull-right">
                                    <button type="button" class="btn btn-default" @click="hideMassEditor()">{{ $t('admin.cancel') }}</button>
                                    <button type="submit" class="btn btn-success">{{ $t('admin.applyChanges') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div v-if="results" class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="">
                                <tr>
                                    <th>{{ $t('admin.id') }}</th>
                                    <th>{{ $t('admin.entry') }}</th>
                                    <th>{{ $t('admin.updatedAt') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="entry in results" @click="navigateToEntry(entry.id)" style="cursor: pointer;">
                                    <td>{{entry.id}}</td>
                                    <td>{{entry.anchor_text}}</td>
                                    <td>{{entry.humanDate.updated}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="results" class="col-md-12 text-center dr-pagination">
                        <ul class="pagination pagination-demo">
                            <li><a href="javascript:;" @click="navigateToPage(1)">«</a></li>
                            <li v-for="page in pagination.last_page" :class=" { active: pagination.current_page == (page+1)} "><a href="javascript:;" @click="navigateToPage(page+1)">{{page+1}}</a></li>
                            <li><a href="javascript:;" @click="navigateToPage(pagination.last_page)">»</a></li>
                        </ul>
                    </div>
                    <div v-else class="text-muted text-center" style="margin-top: 8px">{{ $t('admin.noEntriesFound') }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Search from './Search.js';
export default Search;
</script>
