<?php

if (defined('PATH_THIRD')) {
    require PATH_THIRD.'store/autoload.php';
}

use Store\Action\PaymentReturnAction;

class Store_gestpay_ext
{
    public $name = 'Store GestPay.it Payment Gateway';
    public $version = '1.0.1';
    public $description = 'A custom payment gateway for Expresso Store 2.1+.';
    public $settings_exist = 'n';
    public $docs_url = 'https://www.devdemon.com';
    public $hooks    = array('store_payment_gateways', 'sessions_end');

    /**
     * This hook is used to work around the fact that some gateways (namely DPS) will not allow
     * return URLs which include a query string, which prevents us from using regular ACT URLs.
     */
    public function sessions_end($session)
    {
        if (ee()->uri->segment(1) === 'gestpay_return') {
            require_once __DIR__."/Omnipay/GestPay/sdk/GestPayCryptWS.php";

            $gestpay = new \GestPayCryptWS();
            $gestpay->setShopLogin($_GET["a"]);
            $gestpay->setEncryptedString($_GET["b"]);

            // Test Mode ?
            $query = ee()->db->select('*')->from('store_payment_methods')->where('class', 'GestPay')->limit(1)->get();
            $settings = @json_decode($query->row('settings'));
            if (isset($settings->testMode) === true && $settings->testMode == 'y') {
                $gestpay->setTestEnv(true);
            }

            $res = $gestpay->decrypt();

            if (!$gestpay->getShopTransactionID()) {
                show_error('Fatal Error! Please contact the store owner.');
            }

            // assign the session object prematurely, since EE won't need it anyway
            // (this hook runs inside the Session object constructor, which is a bit weird)
            ee()->session = $session;

            $_GET['H'] = (string) $gestpay->getShopTransactionID();
            $_GET['gestpay'] = $gestpay;

            $action = new PaymentReturnAction(ee());
            $action->perform();
        }
    }

    /**
     * This hook is called when Store is searching for available payment gateways
     * We will use it to tell Store about our custom gateway
     */
    public function store_payment_gateways($gateways)
    {
        ee()->lang->loadfile('store_gestpay');

        if (ee()->extensions->last_call !== false) {
            $gateways = ee()->extensions->last_call;
        }

        // tell Store about our new payment gateway
        // (this must match the name of your gateway in the Omnipay directory)
        $gateways[] = 'GestPay';

        // tell PHP where to find the gateway classes
        // Store will automatically include your files when they are needed
        $composer = require(PATH_THIRD.'store/autoload.php');
        $composer->add('Omnipay', __DIR__);

        return $gateways;
    }

    /**
     * Called by ExpressionEngine when the user activates the extension.
     *
     * @access      public
     * @return      void
     **/
    public function activate_extension()
    {
        foreach ($this->hooks as $hook) {
             $data = array( 'class'     =>  __CLASS__,
                            'method'    =>  $hook,
                            'hook'      =>  $hook,
                            'settings'  =>  serialize($this->settings),
                            'priority'  =>  10,
                            'version'   =>  $this->version,
                            'enabled'   =>  'y'
                );

            // insert in database
            ee()->db->insert('exp_extensions', $data);
        }
    }

    /**
     * Called by ExpressionEngine updates the extension
     *
     * @access public
     * @return void
     **/
    public function update_extension($current = '')
    {
        if ($current == $this->version) return false;

        $settings = array();

        //----------------------------------------
        // Get all existing hooks
        //----------------------------------------
        $dbexts = array();
        $query = ee()->db->select('*')->from('exp_extensions')->where('class', __CLASS__)->get();

        foreach ($query->result() as $row) {
            $dbexts[$row->hook] = $row;
            if ($row->settings) $settings = unserialize($row->settings);
        }

        //----------------------------------------
        // Add new hooks
        //----------------------------------------
        foreach ($this->hooks as $hook) {
            if (isset($dbexts[$hook]) === true) continue;

            $data = array(
                'class'     =>  __CLASS__,
                'method'    =>  $hook,
                'hook'      =>  $hook,
                'settings'  =>  serialize($settings),
                'priority'  =>  100,
                'version'   =>  $this->version,
                'enabled'   =>  'y'
            );

            // insert in database
            ee()->db->insert('exp_extensions', $data);
        }

        //----------------------------------------
        // Delete old hooks
        //----------------------------------------
        foreach ($dbexts as $hook => $ext) {
            if (in_array($hook, $this->hooks) === true) continue;

            ee()->db->where('hook', $hook);
            ee()->db->where('class', __CLASS__);
            ee()->db->delete('exp_extensions');
        }

        // Update the version number for all remaining hooks
        ee()->db->where('class', __CLASS__)->update('extensions', array('version' => $this->version));
    }

    /**
     * Called by ExpressionEngine when the user disables the extension.
     *
     * @access      public
     * @return      void
     **/
    public function disable_extension()
    {
        ee()->db->where('class', __CLASS__);
        ee()->db->delete('exp_extensions');
    }
}