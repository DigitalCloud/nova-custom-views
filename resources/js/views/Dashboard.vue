<template>
    <component v-bind:is="component"></component>
</template>

<script>
    import Dashboard from '@nova/views/Dashboard'
    export default {
        props: {
            component: {
                default: Dashboard,
            }
        }
    }
</script>
