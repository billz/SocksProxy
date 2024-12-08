<?php

/**
 * SOCKS5 Proxy Plugin
 *
 * @description A Dante SOCKS Server add-on for RaspAP 
 * @author      Bill Zimmerman <billzimmerman@gmail.com>
 * @license     https://github.com/billz/SamplePlugin/blob/master/LICENSE
 * @see         src/RaspAP/Plugins/PluginInterface.php
 * @see         src/RaspAP/UI/Sidebar.php
 */

namespace RaspAP\Plugins\SocksProxy;

use RaspAP\Plugins\PluginInterface;
use RaspAP\UI\Sidebar;

class SocksProxy implements PluginInterface
{

    private string $pluginPath;
    private string $pluginName;
    private string $templateMain;
    private string $apiKey;
    private string $serviceName;
    private string $serviceStatus;

    public function __construct(string $pluginPath, string $pluginName)
    {
        $this->pluginPath = $pluginPath;
        $this->pluginName = $pluginName;
        $this->templateMain = 'main';
        $this->serviceName = 'danted.service';
        $this->serviceStatus = $this->getServiceStatus();
        $this->apiKey = '';

        if ($loaded = self::loadData()) {
            $this->apiKey = $loaded->getApiKey();
            $this->serviceStatus = $loaded->getServiceStatus();
        }
    }

    /**
     * Initializes SamplePlugin and creates a custom sidebar item. This is the entry point
     * for creating a custom user plugin; the PluginManager will autoload the plugin code.
     *
     * Replace 'Sample Plugin' below with the label you wish to use in the sidebar.
     * You may specify any icon in the Font Awesome 6.6 free library for the sidebar item.
     * The priority value sets the position of the item in the sidebar (lower values = higher priority).
     * The page action is handled by the plugin's namespaced handlePageAction() method.
     *
     * @param Sidebar $sidebar an instance of the Sidebar
     * @see src/RaspAP/UI/Sidebar.php
     * @see https://fontawesome.com/icons
     */
    public function initialize(Sidebar $sidebar): void
    {

        $label = _('Socks Proxy');
        $icon = 'fas fa-socks';
        $action = 'plugin__'.$this->getName();
        $priority = 65;
        $service_name = $this->serviceName;

        $sidebar->addItem($label, $icon, $action, $priority);
    }

    /**
     * Handles a page action by processing inputs and rendering a plugin template.
     *
     * @param string $page the current page route
     */
    public function handlePageAction(string $page): bool
    {
        // Verify that this plugin should handle the page
        if (str_starts_with($page, "/plugin__" . $this->getName())) {

            // Instantiate a StatusMessage object
            $status = new \RaspAP\Messages\StatusMessage;

            if (!RASPI_MONITOR_ENABLED) {
                if (isset($_POST['saveSettings'])) {
                    if (isset($_POST['txtapikey'])) {
                        // Validate user data
                        $apiKey = trim($_POST['txtapikey']);
                        if (strlen($apiKey) == 0) {
                            $status->addMessage('Please enter a valid API key', 'warning');
                        } else {
                            $return = $this->saveSampleSettings($status, $apiKey);
                            $status->addMessage('Restarting '.$service_name, 'info');
                        }
                    }

                } elseif (isset($_POST['startDanteService'])) {
                    $status->addMessage('Attempting to start '.$this->serviceName, 'info');
                    exec('sudo /bin/systemctl start '.$this->serviceName, $output, $return);
                    if ($return == 0) {
                        $status->addMessage('Successfully started '.$this->serviceName, 'success');
                        $this->setServiceStatus('up');
                    } else {
                        $status->addMessage('Failed to start '.$this->serviceName, 'danger');
                        $this->setServiceStatus('down');
                    }

                } elseif (isset($_POST['stopDanteService'])) {
                    $status->addMessage('Attempting to stop '.$this->serviceName, 'info');
                    exec('sudo /bin/systemctl stop '.$this->serviceName, $output, $return);
                    if ($return == 0) {
                        $status->addMessage('Successfully stopped '.$this->serviceName, 'success');
                        $this->setServiceStatus('down');
                    } else {
                        $status->addMessage('Failed to stop '.$this->serviceName, 'danger');
                    }
                }
            }

            exec('sudo /bin/systemctl status '.$this->serviceName, $output);
            $serviceLog = implode("\n", $output);

            // Populate template data
            $__template_data = [
                'title' => _('Socks Proxy'),
                'description' => _('A Dante SOCKS Server add-on for RaspAP'),
                'author' => _('Bill Zimmerman'),
                'uri' => 'https://github.com/billz/SocksProxy',
                'icon' => 'fas fa-socks',
                'serviceStatus' => $this->getServiceStatus(),
                'serviceName' => $this->serviceName,
                'action' => 'plugin__'.$this->getName(),
                'pluginName' => $this->getName(),
                'content' => _('This is content generated by the SocksProxy Plugin.'),
                'serviceLog' => $serviceLog
            ];

            // update template data from property after processing page actions
            $__template_data['apiKey'] = $this->getApiKey();

            echo $this->renderTemplate($this->templateMain, compact(
                "status",
                "__template_data"
            ));
            return true;
        }
        return false;
    }

    /**
     * Renders a template from inside a plugin directory
     * @param string $templateName
     * @param array $__data
     */
    public function renderTemplate(string $templateName, array $__data = []): string
    {
        $templateFile = "{$this->pluginPath}/{$this->getName()}/templates/{$templateName}.php";

        if (!file_exists($templateFile)) {
            return "Template file {$templateFile} not found.";
        }
        if (!empty($__data)) {
            extract($__data);
        }

        ob_start();
        include $templateFile;
        return ob_get_clean();
    }

    /**
     * Saves SamplePlugin settings
     *
     * @param object status
     * @param string $apiKey
     */
    public function saveSampleSettings($status, $apiKey)
    {
        $status->addMessage('Saving Sample API key', 'info');
        $this->setApiKey($apiKey);
        return $status;
    }

    // Getter for apiKey
    public function getApiKey()
    {
        return $this->apiKey;
    }

    // Setter for apiKey
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->persistData();
    }

    /**
     * Returns a hypothetical service status
     * @return string $status
     */
    public function getServiceStatus()
    {
        exec('sudo /bin/systemctl status '.$service_name, $output, $return);
        if ($return == 0) {
            return 'up';
        } else {
            return 'down';
        }
    }

    // Setter for service status
    public function setServiceStatus($status)
    {
        $this->serviceStatus = $status;
        $this->persistData();
    }

    /* An example method to persist plugin data
     *
     * This writes to the volatile /tmp directory which is cleared
     * on each system boot, so should not be considered as a robust
     * method of data persistence; it's used here for demo purposes only.
     *
     * @note Plugins should avoid use of $_SESSION vars as these are
     * super globals that may conflict with other user plugins.
     */
    public function persistData()
    {
        $serialized = serialize($this);
        file_put_contents("/tmp/plugin__{$this->getName()}.data", $serialized);
    }

    // Static method to load persisted data
    public static function loadData(): ?self
    {
        $filePath = "/tmp/plugin__".self::getName() .".data";
        if (file_exists($filePath)) {
            $data = file_get_contents($filePath);
            return unserialize($data);
        }
        return null;
    }

    // Returns an abbreviated class name
    public static function getName(): string
    {
        return basename(str_replace('\\', '/', static::class));
    }

}

