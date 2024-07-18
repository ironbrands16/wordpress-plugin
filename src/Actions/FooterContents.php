<?php

namespace SimpleAnalytics\Actions;

use SimpleAnalytics\Enums\SettingName;

class FooterContents extends Action
{
    protected string $hook = 'wp_footer';

    public function __construct(
        protected bool $shouldCollect,
    ) {
    }

    public function handle(): void
    {
        if ($this->shouldCollect) {
            echo '<noscript><img src="https://' . get_option(SettingName::CUSTOM_DOMAIN, 'queue.simpleanalyticscdn.com') . '/noscript.gif" alt="" referrerpolicy="no-referrer-when-downgrade"></noscript>' . "\n";
        } else {
            echo "<!-- Simple Analytics: Not logging requests from admins -->\n";
        }
    }
}