<template>
    <div class="col-md-12 extended-relation-listing-entry">
        <div class="panel-group">
            <div class="panel panel-default no-border">
                <div class="panel-body no-border no-padding">
                    <div class="form-horizontal">
                        <div class="form-group available-permissions">
                            <div class="col-lg-12" v-if="permissionsAvailable" v-for="permission in availablePermissions">
                                <input
                                    :disabled="disabled"
                                    type="checkbox"
                                    :id="permission.name"
                                    :value="permission.id"
                                    v-model="selectedPermissions">
                                <label :for="permission.name">{{permission.title}}</label>
                            </div>
                            <div class="col-lg-12 text-muted" v-if="!permissionsAvailable">
                                {{ $t('admin.noPermissionsFound') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-group">
            <div class="panel panel-default no-border">
                <form id="permissions-generator-form" role="form">
                    <div class="panel-body no-border no-padding" v-if="showPermissionGenerator">
                        <permission-name-generator
                            id="PermissionNameGenerator"
                            name="PermissionNameGenerator"
                            v-on:change="updateGeneratedPermissionData"
                        ></permission-name-generator>

                        <div class="form-horizontal">
                            <div class="form-group">
                                <label for="permissionTemplatePicker" class="control-label col-lg-12">
                                Permission name
                                <i data-toggle="tooltip" data-placement="top" title="" class="fa fa-info-circle uses-tooltip" data-original-title="Permission name as it will be stored. You can use the permission generator or you can type the name manually."></i></label>
                                <div class="col-lg-12">
                                    <div>
                                        <input-text-field
                                            v-on:change="updatePermissionFieldValue"
                                            id="name"
                                            name="name"
                                            :value="permissionName"
                                            >
                                        </input-text-field>
                                        <div id="name-error" class="form-field-error"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="permissionTemplatePicker" class="control-label col-lg-12">
                                Permission title
                                <i data-toggle="tooltip" data-placement="top" title="" class="fa fa-info-circle uses-tooltip" data-original-title="Permission title as it will be stored. You can use the permission generator or you can type the title manually."></i></label>
                                <div class="col-lg-12">
                                    <div>
                                        <input-text-field
                                            v-on:change="updatePermissionFieldValue"
                                            id="title"
                                            name="title"
                                            :value="permissionTitle"
                                            >
                                        </input-text-field>
                                        <div id="title-error" class="form-field-error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="panel-body no-border no-padding">
                        <div class="form-horizontal">
                            <div v-if="serverError" class="form-group no-bottom-margin">
                                <div class="col-lg-12" v-if="showPermissionGenerator">
                                    <div class="server-error pull-right">{{serverError}}</div>
                                </div>
                            </div>
                            <div class="form-group pull-right no-bottom-margin">
                                <div class="col-lg-12" v-if="showPermissionGenerator">
                                    <button type="button" @click="setPermissionGeneratorVisibility({ visible: false })" class="btn btn-default">
                                        {{ $t('admin.cancel') }}
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        {{ $t('admin.createPermission') }}
                                    </button>
                                </div>

                                <div class="col-lg-12" v-else>
                                    <button
                                        :disabled="disabled"
                                        type="button"
                                        @click="setPermissionGeneratorVisibility({ visible: true })"
                                        class="btn btn-default">
                                        {{ $t('admin.createNewPermission') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import Permissions from './Permissions.js';
    export default Permissions;
</script>
