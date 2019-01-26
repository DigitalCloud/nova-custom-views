<template>
    <component v-bind:is="component" :resourceName="resourceName" :resourceId="resourceId"></component>
</template>

<script>

    import ResourceDetail from '@nova/views/Detail'

    export default {
        props: {
            resourceName: {
                type: String,
                required: true,
            },
            resourceId: {
                default: '',
            },
            component: {
                default: ResourceDetail,
            }
        }
    }
</script>
