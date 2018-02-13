<template>
	<ol class="breadcrumb breadcrumb-nav">
	    <li>
	    	<router-link to="/">
	    		<i class="fa fa-home"></i>
    		</router-link>
	    </li>
	    <li v-for="item in ui.breadcrumbItems"
	        :class="{
	            'group': !item.active,
	            'active': item.active
	        }">
	        <router-link :to="item.link" class="bread-page-title">{{item.text}}</router-link>
	    </li>
	</ol>
</template>

<script>
	import Breadcrumb from './Breadcrumb.js';
	export default Breadcrumb;
</script>
