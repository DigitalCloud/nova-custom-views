<template>
    <component v-bind:is="component" :resourceName="resourceName" :resourceId="resourceId" :viaResource="viaResource" :viaResourceId="viaResourceId" :viaRelationship="viaRelationship"></component>
</template>

<script>

    import ResourceUpdate from '@nova/views/Update'

    export default {
        props: {
            resourceName: {
                type: String,
                required: true,
            },
            resourceId: {
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
            component: {
                default: ResourceUpdate,
            }
        },
    }
</script>
