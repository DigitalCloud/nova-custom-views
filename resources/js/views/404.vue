<template>
    <component v-bind:is="component"></component>
</template>

<script>

    import Error404 from '@nova/views/404'

    export default {
        props: {
            component: {
                default: Error404,
            }
        },
    }
</script>
