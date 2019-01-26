import Dashboard from '../views/Dashboard'
import ResourceIndex from '../views/Index'
import Lens from '../views/Lens'
import ResourceDetail from '../views/Detail'
import CreateResource from '../views/Create'
import UpdateResource from '../views/Update'
import AttachResource from '../views/Attach'
import UpdateAttachedResource from '../views/UpdateAttached'
import Error403 from '../views/403'
import Error404 from '../views/404'


export default [
    {
        name: 'custom-dashboard',
        path: '/',
        component: Dashboard,
        props: true,
    },
    {
        name: 'custom-index',
        path: '/resources/:resourceName',
        component: ResourceIndex,
        props: true,
    },
    {
        name: 'custom-lens',
        path: '/resources/:resourceName/lens/:lens',
        component: Lens,
        props: true,
    },
    {
        name: 'custom-create',
        path: '/resources/:resourceName/new',
        component: CreateResource,
        props: route => {
            return {
                component: route.params.component,
                resourceName: route.params.resourceName,
                viaResource: route.query.viaResource,
                viaResourceId: route.query.viaResourceId,
                viaRelationship: route.query.viaRelationship,
            }
        },
    },
    {
        name: 'custom-edit',
        path: '/resources/:resourceName/:resourceId/edit',
        component: UpdateResource,
        props: route => {
            return {
                component: route.params.component,
                resourceName: route.params.resourceName,
                resourceId: route.params.resourceId,
                viaResource: route.query.viaResource,
                viaResourceId: route.query.viaResourceId,
                viaRelationship: route.query.viaRelationship,
            }
        },
    },
    {
        name: 'custom-attach',
        path: '/resources/:resourceName/:resourceId/attach/:relatedResourceName',
        component: AttachResource,
        props: route => {
            return {
                component: route.params.component,
                resourceName: route.params.resourceName,
                resourceId: route.params.resourceId,
                relatedResourceName: route.params.relatedResourceName,
                viaRelationship: route.query.viaRelationship,
                polymorphic: route.query.polymorphic == '1',
            }
        },
    },
    {
        name: 'custom-edit-attached',
        path:
            '/resources/:resourceName/:resourceId/edit-attached/:relatedResourceName/:relatedResourceId',
        component: UpdateAttachedResource,
        props: route => {
            return {
                component: route.params.component,
                resourceName: route.params.resourceName,
                resourceId: route.params.resourceId,
                relatedResourceName: route.params.relatedResourceName,
                relatedResourceId: route.params.relatedResourceId,
                viaRelationship: route.query.viaRelationship,
            }
        },
    },
    {
        name: 'custom-detail',
        path: '/resources/:resourceName/:resourceId',
        component: ResourceDetail,
        props: true,
    },
    {
        name: 'custom-403',
        path: '/403',
        component: Error403,
        props: true
    },
    {
        name: 'custom-404',
        path: '/404',
        component: Error404,
        props: true
    },
    {
        name: 'custom-catch-all',
        path: '*',
        component: Error404,
        props: true
    },
]
