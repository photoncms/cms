<template>
    <section class="sidebar">
    	<div class="panel panel-default">
    	    <div class="panel-heading">
    	        <div class="clearfix">
    	            <img src="/images/logo.png" alt="proton-logo">
    	            <h5>
    	                <span class="title">
    	                    {{ ui.photonConfig.companyTitle }}
    	                </span>
    	                <span class="subtitle">
    	                    {{ ui.photonConfig.companySubtitle }}
    	                </span>
    	            </h5>
    	        </div>
    	    </div>
	        <component
                :is="sidebar.sidebarType"
                :fields="fields">
            </component>
	    </div>
    </section>
</template>

<script>
    import Sidebar from './Sidebar.js';
    export default Sidebar;
</script>
