<template>
    <div>
        <div
            class="file-picker clearfix"
            v-if="activeView=='thumbnail'">
            <div
                v-for="asset in assetsManager.assets"
                :class="{'selected': isSelectedAsset(asset.id), 'active': isActiveAsset(asset.id)}"
                class="file-item">
                <div class="file-preview" @click="selectAsset(asset)">
                    <img
                        v-if="asset.mime_type.substring(0, 5) == 'image'"
                        :alt="asset.alt_text"
                        :src="getAssetThumbnail(asset)" />
                    <i
                        v-else
                        class="fa fa-file icon-file">
                    </i>
                    <a :href="asset.file_url" target="_blank">
                        <i class="view-file fa fa-search"></i>
                    </a>
                </div>
                <div class="file-name">
                    {{asset.title || asset.file_name}}
                </div>
            </div>
        </div>
        <div
            class="table-view table-responsive"
            v-if="activeView=='table'"
            >
            <table class="table">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>{{ $t('assetsManager.fileName') }}</th>
                        <th>{{ $t('assetsManager.fileSize') }}</th>
                        <th>{{ $t('assetsManager.modificationDate') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="asset in assetsManager.assets"
                        :class="{'selected': isSelectedAsset(asset.id), 'active': isActiveAsset(asset.id)}"
                        @click="selectAsset(asset)"
                        class="file-item">
                        <td class="text-center">
                            <img
                                v-if="asset.mime_type.substring(0, 5) == 'image'"
                                :alt="asset.file_name"
                                :src="getAssetThumbnail(asset)" />
                            <i
                                v-else
                                class="fa fa-file">
                            </i>
                        </td>
                        <td>
                            {{asset.file_name}}
                        </td>
                        <td>
                            {{ formatBytes(asset.file_size)}}
                        </td>
                        <td>
                            {{updatedAt(asset)}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import Layout from './Layout.js';
    export default Layout;
</script>
