<template>
    <component v-bind:is="component"
               :resourceName="resourceName"
               :viaResource="viaResource"
               :viaResourceId="viaResourceId"
               :viaRelationship="viaRelationship"
               :relationshipType="relationshipType"
               :lens="lens"
    ></component>
</template>

<script>

    import Lens from '@nova/views/Lens'

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
            relationshipType: {
                type: String,
                default: '',
            },
            lens: {},
            component: {
                default: Lens,
            }
        },
    }
</script>
