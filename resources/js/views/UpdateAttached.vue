<template>
    <component v-bind:is="component"
               :resourceName="resourceName"
               :resourceId="resourceId"
               :relatedResourceName="relatedResourceName"
               :relatedResourceId="relatedResourceId"
               :viaResource="viaResource"
               :viaResourceId="viaResourceId"
               :viaRelationship="viaRelationship"
               :polymorphic="polymorphic"
    ></component>
</template>

<script>

    import UpdateAttachedResource from '@nova/views/UpdateAttached'

    export default {
        props: {
            resourceName: {
                type: String,
                required: true,
            },
            resourceId: {
                required: true,
            },
            relatedResourceName: {
                type: String,
                required: true,
            },
            relatedResourceId: {
                required: true,
            },
            viaResource: {
                default: '',
            },
            viaResourceId: {
                default: '',
            },
            viaRelationship: {
                default: '',
            },
            polymorphic: {
                default: false,
            },
            component: {
                default: UpdateAttachedResource,
            }
        },
    }
</script>
