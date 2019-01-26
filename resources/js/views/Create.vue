<template>
    <component v-bind:is="component" :resourceName="resourceName" :viaResource="viaResource" :viaResourceId="viaResourceId" :viaRelationship="viaRelationship"></component>
</template>

<script>
    import CreateResource from '@nova/views/Create'
    export default {
        props: {
            resourceName: {
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
            component: {
                default: CreateResource,
            }
        }
    }
</script>
