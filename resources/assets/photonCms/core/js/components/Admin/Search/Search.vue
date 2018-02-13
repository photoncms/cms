<template>
    <div class="col-md-6 col-lg-8">
        <div class="panel-group">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-horizontal">
                        <div class="btn-group pull-right" style="margin-bottom: 20px;">
                            <button
                                type="button"
                                class="btn btn-primary dropdown-toggle"
                                :disabled="!results.length > 0"
                                data-toggle="dropdown">
                                {{ $t('admin.action') }} <span class="caret"></span>
                            </button>
                            <ul v-if="results.length > 0" class="dropdown-menu dropdown-menu-arrow pull-right" role="menu">
                                <li v-if="!photonConfig.massEditingDisabled">
                                    <a
                                        href="javascript:;"
                                        @click="showMassEditor()">
                                        {{ $t('admin.massEdit') }}
                                    </a>
                                </li>
                                <li v-else class="disabled">
                                    <a href="javascript://">
                                        {{ $t('admin.massEdit') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <mass-editor></mass-editor>
                    <div v-if="results.length > 0" class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ $t('admin.id') }}</th>
                                    <th>{{ $t('admin.entry') }}</th>
                                    <th>{{ $t('admin.createdAt') }}</th>
                                    <th>{{ $t('admin.lastUpdated') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="entry in results">
                                    <td>
                                        <router-link :to="generateLink(entry)">{{entry.id}}</router-link>
                                    </td>
                                    <td>
                                        <router-link :to="generateLink(entry)">{{entry.anchor_text}}</router-link>
                                    </td>
                                    <td>
                                        <router-link :to="generateLink(entry)">{{createdAt(entry)}}</router-link>
                                    </td>
                                    <td>
                                        <router-link :to="generateLink(entry)">{{updatedAt(entry)}}</router-link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="results.length > 0" class="col-md-12 text-center dr-pagination">
                        <ul class="pagination pagination-demo">
                            <li><a href="javascript:;" @click="goToPage(1)">«</a></li>

                            <li v-for="page in trimmedPagination" :class=" { active: pagination.current_page == page} "><a href="javascript:;" @click="goToPage(page)">{{page}}</a></li>

                            <li><a href="javascript:;" @click="goToPage(pagination.last_page)">»</a></li>
                        </ul>
                    </div>
                    <div v-else class="col-md-12 text-muted text-center" style="margin-top: 8px">{{ $t('admin.noEntriesFound') }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Search from './Search.js';
export default Search;
</script>
