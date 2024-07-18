<?php

namespace SimpleAnalytics;

use SimpleAnalytics\Actions\AnalyticsCode;
use SimpleAnalytics\Enums\SettingName;
use SimpleAnalytics\Fluent\Settings\{SettingsPage, Tab};

class Plugin
{
    protected bool $shouldCollect;

    public function __construct(TrackingPolicy $trackingPolicy = new TrackingPolicy)
    {
        $this->shouldCollect = $trackingPolicy->shouldCollectAnalytics();
    }

    public function register(): void
    {
        AnalyticsCode::register($this->shouldCollect);

        SettingsPage::title('Simple Analytics')
            ->slug('simpleanalytics')
            ->tab('General', function (Tab $tab) {
                $tab->input(SettingName::CUSTOM_DOMAIN, 'Custom Domain')
                    ->autofocus()
                    ->placeholder('Enter your custom domain or leave it empty.')
                    ->description('E.g. api.example.com. Leave empty to use the default domain (most users).')
                    ->docs('https://docs.simpleanalytics.com/bypass-ad-blockers');
            })
            ->tab('Advanced', function (Tab $tab) {
                $tab->icon(get_icon('cog'));

                $tab->input(SettingName::IGNORE_PAGES, 'Ignore Pages')
                    ->description('Comma separated list of pages to ignore. E.g. /contact, /about')
                    ->placeholder('Example: /page1, /page2, /category/*')
                    ->docs('https://docs.simpleanalytics.com/ignore-pages');

                $tab->checkbox(SettingName::COLLECT_DNT, 'Collect Do Not Track')
                    ->description('If you want to collect visitors with Do Not Track enabled, turn this on.')
                    ->docs('https://docs.simpleanalytics.com/dnt');

                $tab->checkbox(SettingName::HASH_MODE, 'Hash mode')
                    ->description('If your website uses hash (#) navigation, turn this on. On most WordPress websites this is not relevant.')
                    ->docs('https://docs.simpleanalytics.com/hash-mode');

                $tab->checkbox(SettingName::MANUAL_COLLECT, 'Manually collect page views')
                    ->description('In case you don’t want to auto collect page views, but via `sa_pageview` function in JavaScript.')
                    ->docs('https://docs.simpleanalytics.com/trigger-custom-page-views#use-custom-collection-anyway');

                $tab->input(SettingName::ONLOAD_CALLBACK, 'Onload Callback')
                    ->description('JavaScript function to call when the script is loaded.')
                    ->placeholder('Example: sa_event("My event")')
                    ->docs('https://docs.simpleanalytics.com/trigger-custom-page-views#use-custom-collection-anyway');

                $tab->input(SettingName::HOSTNAME, 'Overwrite domain name')
                    ->description('Override the domain that is sent to Simple Analytics. Useful for multi-domain setups.')
                    ->placeholder('Example: example.com')
                    ->docs('https://docs.simpleanalytics.com/overwrite-domain-name');

                $tab->input(SettingName::SA_GLOBAL, 'Global variable name')
                    ->description('Change the global variable name of Simple Analytics. Default is `sa_event`.')
                    ->placeholder('Example: ba_event')
                    ->docs('https://docs.simpleanalytics.com/events#the-variable-sa_event-is-already-used');
            })
            ->tab('Events', function (Tab $tab) {
                $tab->icon(get_icon('events'));

                $tab->input(SettingName::EVENT_COLLECT, 'Auto collect downloads')
                    ->placeholder('Example: outbound,emails,downloads')
                    ->docs('https://docs.simpleanalytics.com/automated-events');

                $tab->input(SettingName::EVENT_EXTENSIONS, 'Download file extensions')
                    ->description('Comma separated list of file extensions to track as downloads. E.g. pdf, zip')
                    ->placeholder('Example: pdf, zip')
                    ->docs('https://docs.simpleanalytics.com/automated-events');

                $tab->checkbox(SettingName::EVENT_USE_TITLE, 'Use titles of page')
                    ->description('Use the title of the page as the event name. Default is the URL.')
                    ->docs('https://docs.simpleanalytics.com/automated-events');

                $tab->checkbox(SettingName::EVENT_FULL_URLS, 'Use full URLs')
                    ->description('Use full URLs instead of the path. Default is the path.')
                    ->docs('https://docs.simpleanalytics.com/automated-events');

                $tab->input(SettingName::EVENT_SA_GLOBAL, 'Override global')
                    ->description('Override the global variable name of Simple Analytics. Default is `sa_event`.')
                    ->placeholder('Example: ba_event')
                    ->docs('https://docs.simpleanalytics.com/events#the-variable-sa_event-is-already-used');
            })
            ->register();
    }
}
