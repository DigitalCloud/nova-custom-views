<template>
    <component v-bind:is="component" :resourceName="resourceName" :viaResource="viaResource" :viaResourceId="viaResourceId" :viaRelationship="viaRelationship" :relationshipType="relationshipType" :field="field"></component>
</template>

<script>

import ResourceIndex from '@nova/views/Index'

export default {
    props: {
        field: {
            type: Object,
        },
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
        component: {
            default: ResourceIndex,
        },
    }
}
</script>
