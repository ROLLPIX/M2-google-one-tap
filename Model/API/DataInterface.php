<?php
declare(strict_types=1);

namespace Rollpix\GoogleOneTap\Model\API;

interface DataInterface
{
    public const XML_CLIENT_ID = 'googleonetap/general/client_id';
    public const XML_BCG_CLICK = 'googleonetap/general/background_click';
    public const XML_AUTO_SIGN_IN = 'googleonetap/general/auto_signin';
    public const XML_POSITION = 'googleonetap/general/position';
    public const XML_STATUS = 'googleonetap/module_status/status';

    // Security & Logging
    public const XML_DEBUG_LOGGING = 'googleonetap/security/debug_logging';
    public const XML_RATE_LIMIT_ENABLED = 'googleonetap/security/rate_limit_enabled';
    public const XML_RATE_LIMIT_MAX_ATTEMPTS = 'googleonetap/security/max_attempts';
    public const XML_RATE_LIMIT_TIME_WINDOW = 'googleonetap/security/time_window';
}
