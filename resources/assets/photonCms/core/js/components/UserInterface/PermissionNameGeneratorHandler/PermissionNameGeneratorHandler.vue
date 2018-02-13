<template>
    <div>
        <div class="form-group" v-if="!showPermissionGenerator">
            <div class="col-lg-12 hide-permission-generator">
                <button type="button" @click="setPermissionGeneratorVisibility({ visible: true })" class="btn btn-default pull-right">
                    {{ $t('admin.showPermissionGenerator') }}
                </button>
            </div>
        </div>
        <div class="col-md-12 extended-relation-listing-entry" v-if="showPermissionGenerator">
            <permission-name-generator
                id="PermissionNameGenerator"
                name="PermissionNameGenerator"
                v-on:change="updatePermissionModelData"
            ></permission-name-generator>
            <div class="form-group">
                <div class="col-lg-12 hide-permission-generator">
                    <button type="button" @click="setPermissionGeneratorVisibility({ visible: false })" class="btn btn-default pull-right">
                        {{ $t('admin.hidePermissionGenerator') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PermissionNameGeneratorHandler from './PermissionNameGeneratorHandler.js';
    export default PermissionNameGeneratorHandler;
</script>
