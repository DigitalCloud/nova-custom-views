<template>
    <component v-bind:is="component"></component>
</template>

<script>

    import Error403 from '@nova/views/403'

    export default {
        props: {
            component: {
                default: Error403,
            }
        },
    }
</script>
