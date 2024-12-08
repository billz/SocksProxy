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
    private string $serviceStatus;

    public function __construct(string $pluginPath, string $pluginName)
    {
        $this->pluginPath = $pluginPath;
        $this->pluginName = $pluginName;
        $this->templateMain = 'main';
        $this->serviceStatus = 'up';
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

            /**
             * Examples of common plugin actions are handled here:
             * 1. saveSettings
             * 2. startSampleService
             * 3. stopSampleService
             *
             * Other page actions and custom functions may be added as needed.
             */
            if (!RASPI_MONITOR_ENABLED) {
                if (isset($_POST['saveSettings'])) {
                    if (isset($_POST['txtapikey'])) {
                        // Validate user data
                        $apiKey = trim($_POST['txtapikey']);
                        if (strlen($apiKey) == 0) {
                            $status->addMessage('Please enter a valid API key', 'warning');
                        } else {
                            $return = $this->saveSampleSettings($status, $apiKey);
                            $status->addMessage('Restarting sample.service', 'info');
                        }
                    }

                } elseif (isset($_POST['startSampleService'])) {
                    $status->addMessage('Attempting to start sample.service', 'info');
                    /* A dummy value is used here for demo purposes.
                     * One method of fetching a process or service status:
                     * exec('pidof some_process | wc -l', $return);
                     * $state = ($return[0] > 0);
                     * $status = $state ? "up" : "down";
                     *
                     * @note "up" and "down" correspond to the .service-status-* CSS classes
                     */
                    $this->setServiceStatus('up');

                } elseif (isset($_POST['stopSampleService'])) {
                    $status->addMessage('Attempting to stop sample.service', 'info');
                    $this->setServiceStatus('down');
                }
            }

            // Populate template data
            $__template_data = [
                'title' => _('Sample Plugin'),
                'description' => _('A sample user plugin to extend RaspAP'),
                'author' => _('A. Plugin Author'),
                'uri' => 'https://github.com/RaspAP/SamplePlugin',
                'icon' => 'fas fa-plug', // icon should be the same used for Sidebar
                'serviceStatus' => $this->getServiceStatus(), // plugin may optionally return a service status
                'serviceName' => 'sample.service', // an optional service name
                'action' => 'plugin__'.$this->getName(), // expected by Plugin Manager; do not edit
                'pluginName' => $this->getName(), // required for template rendering; do not edit
                // content may be passed in template data or used directly in the parent template and/or child tabs
                'content' => _('This is content generated by the SamplePlugin.'),
                // example service log output. this could be replaced with an actual status result such as:
                // exec("sudo systemctl status sample.service", $output, $return);
                'serviceLog' => "● sample.service - raspap-sample\n    Loaded: loaded (/lib/systemd/system/sample.service; enabled;)\n    Active: active (running)"
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
        return $this->serviceStatus;
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

