<template>
    <component v-bind:is="component"
               :resourceName="resourceName"
               :resourceId="resourceId"
               :relatedResourceName="relatedResourceName"
               :viaResource="viaResource"
               :viaResourceId="viaResourceId"
               :viaRelationship="viaRelationship"
               :polymorphic="polymorphic"
    ></component>
</template>

<script>

    import AttachResource from '@nova/views/Attach'

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
                default: AttachResource,
            }
        },
    }
</script>
