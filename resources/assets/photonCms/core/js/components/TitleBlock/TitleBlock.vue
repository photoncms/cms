<template>
	<div id="top" class="panel panel-default panel-block panel-title-block">
	    <div class="panel-heading">
	        <div>
	            <i :class="ui.icon"></i>
	            <h1>{{ui.title}}<span class="page-title"></span><small>{{ui.subtext}}</small></h1>
	        </div>
	    </div>
	</div>
</template>

<script>
    import TitleBlock from './TitleBlock.js';
    export default TitleBlock;
</script>
