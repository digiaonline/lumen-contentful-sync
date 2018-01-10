<?php

return [
    /*
     * The list of content type IDs (content models in Contentful)
     */
    'content_types'   => [

    ],
    /*
     * The username webhooks should use
     */
    'webhookUsername' => env('CONTENTFUL_SYNC_WEBHOOK_USERNAME', ''),
    /*
     * The password webhooks should use
     */
    'webhookPassword' => env('CONTENTFUL_SYNC_WEBHOOK_PASSWORD', ''),
];
