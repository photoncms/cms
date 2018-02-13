<template>
	<div>
        <license-expiring-notification></license-expiring-notification>

	    <main-menu></main-menu>

	    <section class="wrapper scrollable">
	        <user-menu></user-menu>
	        <title-bar></title-bar>
	        <quick-launch></quick-launch>
	        <dashboard-widgets></dashboard-widgets>
            <section class="widget-group">
		        <!-- <component :is="widget.type" :widget.sync="widget" v-for="widget in widgets"></component> -->
		    </section>
	    </section>

	    <assets></assets>
    </div>
</template>

<script>
	import Dashboard from './Dashboard.js';
	export default Dashboard;
</script>

<style scoped>
	.proton-widget.task-completion .sub-list li {
		width: 100%;
		cursor: default;
		background-color: transparent;
		color: inherit;
	}

</style>
