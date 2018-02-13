<template>
    <div class="panel panel-default panel-block">
        <div class="list-group">

            <div class="list-group-item text-center">
                <button type="submit"
                    :disabled="admin.editorMode === 'create'
                        || admin.editorMode === 'default'
                        || !admin.selectedModule.permission_control.crud.create
                        || admin.selectedModule.type === 'single_entry'"
                    @click="createNewEntry"
                    class="btn btn-primary">
                    <i class="fa fa-plus-square-o"></i> {{ $t('admin.createNewEntry') }}
                </button>
            </div>

            <div class="list-group-item" id="date-range" v-if="admin.selectedModule.slug && admin.editorMode != 'search'">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="slug" class="col-lg-2 control-label">{{ $t('admin.slug') }}:</label>
                        <div class="col-lg-10">
                            <input-text-field
                                v-on:change="onChange"
                                :id="0"
                                :name="'slug'"
                                :placeholder="'Slug'"
                                :value="admin.editedEntry.slug"
                                ></input-text-field>
                            <div class="server-error">{{ slugError }}</div>
                        </div>
                    </div>
                </form>
            </div>

            <div v-if="subscription.subscribedUsers.length > 0" class="list-group-item alsoEditing">
                {{ $t('admin.thisEntryIsCurrentlyEditedBy') }}:
                <ul class="inline-font-icons-list date-created">
                    <li v-for="user in subscription.subscribedUsers">
                        <i class="fa fa-user-circle"></i>
                        <router-link :to="`/admin/users/${user.id}`">
                            <strong>{{user.anchor_text}}</strong>
                        </router-link>
                    </li>
                </ul>
            </div>

            <div v-if="admin.editorMode === 'edit'" class="list-group-item dimmed-info-text">
                <ul class="inline-font-icons-list date-created">
                    <li>
                        <i class="fa fa-calendar-check-o"></i>
                        {{ $t('admin.createdAt') }}: <strong>{{createdAt}}</strong>
                    </li>
                    <li>
                        <i class="fa fa-user-circle"></i>
                        {{ $t('admin.createdBy') }}: <strong v-if="admin.editedEntry.created_by">{{admin.editedEntry.created_by.anchor_text}}</strong>
                    </li>
                </ul>
                <br>
                <ul class="inline-font-icons-list date-created">
                    <li>
                        <i class="fa fa-calendar"></i>
                        {{ $t('admin.updatedAt') }}: <strong>{{updatedAt}}</strong>
                    </li>
                    <li>
                        <i class="fa fa-user-circle"></i>
                        {{ $t('admin.updatedBy') }}: <strong v-if="admin.editedEntry.updated_by">{{admin.editedEntry.updated_by.anchor_text}}</strong>
                    </li>
                </ul>
            </div>

            <div class="list-group-item dimmed-info-text">
                <ul class="inline-font-icons-list date-created">
                    <li>
                        <i class="fa fa-cog"></i>
                        {{ $t('admin.moduleType') }}: <strong>{{moduleType}}</strong>
                    </li>
                    <li v-if="parentModule">
                        <i :class="parentModule.icon"></i>
                        {{ $t('admin.parentModule') }}: <router-link :to="`/admin/${parentModule.table_name}`"><strong>{{parentModule.name}}</strong></router-link>
                    </li>
                    <li v-if="parentEntry">
                        <i class="fa fa-file-o"></i>
                        {{ $t('admin.parentEntry') }}:
                        <router-link
                            :to="`/admin/${parentEntry.url}`"
                            v-if="!isRootParentEntry">
                            <strong>{{parentEntry.anchorText}}</strong>
                        </router-link>
                        <span v-else>{{ $t('admin.root') }}</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</template>

<script>
    import InfoPanel from './InfoPanel.js';
    export default InfoPanel;
</script>
