<?php
declare(strict_types=1);

namespace GhostUnicorns\CrtNotify\Model;

use GhostUnicorns\CrtActivity\Api\ActivityRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class EmailNotify
{
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ActivityRepositoryInterface
     */
    private $activityRepository;

    /**
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $state
     * @param LoggerInterface $logger
     * @param ActivityRepositoryInterface $activityRepository
     * @param Config $config
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state,
        LoggerInterface $logger,
        ActivityRepositoryInterface $activityRepository,
        Config $config
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
        $this->config = $config;
        $this->logger = $logger;
        $this->activityRepository = $activityRepository;
    }

    /**
     * @param int $activityId
     * @return void
     */
    public function execute(int $activityId)
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $templateId = $this->config->getTemplateId();
        $emailToSend = $this->config->getEmailsToNotify();

        try {
            $process = $this->activityRepository->getById($activityId)->getType();
            $templateVars = [
                'process' => $process,
                'activity' => $activityId
            ];

            $storeId = $this->storeManager->getStore()->getId();

//            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend();

            $templateOptions = [
                'area' => Area::AREA_FRONTEND,
                'store' => $storeId
            ];

            foreach ($emailToSend as $toEmail) {
                $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars($templateVars)
                    ->addTo($toEmail)
                    ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }
}
