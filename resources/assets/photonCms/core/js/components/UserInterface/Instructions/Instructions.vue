<template>
	<div class="col-md-6 col-lg-8">
	    <div class="panel panel-default panel-block preface">
	        <div class="list-group">
	            <div class="list-group-item button-demo">
					<div class="alert alert-dismissable alert-info fade in no-bottom-margin">
					    <span class="title"><i class="fa fa-info-circle"></i> {{title}}</span>
					    {{text}}
					</div>
	            </div>
	        </div>
	    </div>
	</div>
</template>

<script>
import Instructions from './Instructions.js';
export default Instructions;
</script>
