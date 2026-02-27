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

    // Enhanced One Tap config
    public const XML_CONTEXT = 'googleonetap/general/context';
    public const XML_PROMPT_PARENT_ID = 'googleonetap/general/prompt_parent_id';
    public const XML_ITP_SUPPORT = 'googleonetap/general/itp_support';
    public const XML_CLOSE_CALLBACK = 'googleonetap/general/close_callback';

    // Google Sign-In Button config
    public const XML_BUTTON_ENABLED = 'googleonetap/button/enabled';
    public const XML_BUTTON_PAGES = 'googleonetap/button/pages';
    public const XML_BUTTON_CUSTOM_SELECTOR = 'googleonetap/button/custom_selector';
    public const XML_BUTTON_THEME = 'googleonetap/button/theme';
    public const XML_BUTTON_SIZE = 'googleonetap/button/size';
    public const XML_BUTTON_SHAPE = 'googleonetap/button/shape';
    public const XML_BUTTON_TEXT = 'googleonetap/button/text';
    public const XML_BUTTON_LOGO_ALIGNMENT = 'googleonetap/button/logo_alignment';

    // Button position per page
    public const XML_BUTTON_LOGIN_POSITION = 'googleonetap/button/login_position';
    public const XML_BUTTON_REGISTER_POSITION = 'googleonetap/button/register_position';
    public const XML_BUTTON_CHECKOUT_POSITION = 'googleonetap/button/checkout_position';

    // Amasty compatibility
    public const XML_COMPAT_DISABLE_BUTTON_AMASTY = 'googleonetap/compatibility/disable_button_amasty';

    // Security & Logging
    public const XML_DEBUG_LOGGING = 'googleonetap/security/debug_logging';
    public const XML_RATE_LIMIT_ENABLED = 'googleonetap/security/rate_limit_enabled';
    public const XML_RATE_LIMIT_MAX_ATTEMPTS = 'googleonetap/security/max_attempts';
    public const XML_RATE_LIMIT_TIME_WINDOW = 'googleonetap/security/time_window';
}
