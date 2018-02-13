<template>
    <div class="panel-group help-sidebar" v-if="pages" id="help-accordion">
        <div class="panel panel-default" v-for="page in pages">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#help-accordion" :href="`#help-${page.slug}`">
                        {{ page.title }}
                    </a>
                </h4>
            </div>
            <div :id="`help-${page.slug}`" class="panel-collapse collapse help-items-panel" :class="{ in: page.isFirst }">
                <div v-html="page.content" class="panel-body"></div>
            </div>
        </div>
    </div>
</template>

<script>
    import GeneratorHelpBlock from './GeneratorHelpBlock.js';
    export default GeneratorHelpBlock;
</script>
