<?php
declare(strict_types=1);

namespace GhostUnicorns\CrtNotify\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * string
     */
    protected const CRT_NOTIFY_GENERAL_ENABLED = 'crt_notify/general/enabled';

    /**
     * string
     */
    protected const CRT_NOTIFY_GENERAL_NOTIFY_EMAIL_TEMPLATE = 'crt_notify/general/notify_email_template';

    /**
     * string
     */
    protected const CRT_NOTIFY_GENERAL_EMAIL_TO_NOTIFY = 'crt_notify/general/email_to_notify';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CRT_NOTIFY_GENERAL_ENABLED,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return string
     */
    public function getTemplateId(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::CRT_NOTIFY_GENERAL_NOTIFY_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return array
     */
    public function getEmailsToNotify(): array
    {
        $emailsString = (string)$this->scopeConfig->getValue(
            self::CRT_NOTIFY_GENERAL_EMAIL_TO_NOTIFY,
            ScopeInterface::SCOPE_WEBSITE
        );

        return explode('|', $emailsString);
    }
}
