<?php

namespace Hocza\Sendy;

/**
 * Class Sendy
 *
 * @package Hocza\Sendy
 */
class Sendy
{
    protected $config;

    protected $installationUrl;
    protected $apiKey;
    protected $listId;

    /**
     * Sendy constructor.
     *
     * @param array $config
     *
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->setListId($config['listId']);
        $this->setInstallationUrl($config['installationUrl']);
        $this->setApiKey($config['apiKey']);

        $this->checkProperties();
    }

    /**
     * @param mixed $installationUrl
     */
    public function setInstallationUrl($installationUrl)
    {
        $this->installationUrl = $installationUrl;
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param $listId
     *
     * @return $this
     */
    public function setListId($listId)
    {
        $this->listId = $listId;

        return $this;
    }

    /**
     * Method to add a new subscriber to a list
     *
     * @param array $values
     *
     * @return array
     */
    public function subscribe(array $values)
    {
        $result = $this->buildAndSend('subscribe', $values);

        /**
         * Prepare the array to return
         */
        $notice = [
            'status' => true,
            'message' => '',
        ];

        /**
         * Handle results
         */
        switch (strval($result)) {
            case '1':
                $notice['message'] = 'Subscribed.';

                break;
            case 'Already subscribed.':
                $notice['message'] = $result;

                break;
            default:
                $notice = [
                    'status' => false,
                    'message' => $result
                ];

                break;
        }

        return $notice;
    }

    /**
     * Updating a subscriber using the email like a reference/key
     * If the email doesn't exists in the current list, this will create a new subscriber
     *
     * @param $email
     * @param array $values
     *
     * @return array
     */
    public function update($email, array $values)
    {
        $values = array_merge([
            'email' => $email
        ], $values);

        return $this->subscribe($values);
    }

    /**
     * Method to unsubscribe a user from a list
     *
     * @param $email
     *
     * @return array
     */
    public function unsubscribe($email)
    {
        $result = $this->buildAndSend('unsubscribe', ['email' => $email]);

        /**
         * Prepare the array to return
         */
        $notice = [
            'status' => true,
            'message' => '',
        ];

        /**
         * Handle results
         */
        switch (strval($result)) {
            case '1':
                $notice['message'] = 'Unsubscribed';

                break;
            default:
                $notice = [
                    'status' => false,
                    'message' => $result
                ];

                break;
        }

        return $notice;
    }

    /**
     * Method to delete a user from a list
     *
     * @param $email
     *
     * @return array
     */
    public function delete($email)
    {
        $result = $this->buildAndSend('/api/subscribers/delete.php', ['email' => $email]);

        /**
         * Prepare the array to return
         */
        $notice = [
            'status' => true,
            'message' => '',
        ];

        /**
         * Handle results
         */
        switch (strval($result)) {
            case '1':
                $notice['message'] = 'Deleted';

                break;
            default:
                $notice = [
                    'status' => false,
                    'message' => $result
                ];

                break;
        }

        return $notice;
    }


    /**
     * Method to get the current status of a subscriber.
     * Success: Subscribed, Unsubscribed, Unconfirmed, Bounced, Soft bounced, Complained
     * Error: No data passed, Email does not exist in list, etc.
     *
     * @param $email
     *
     * @return string
     */
    public function status($email)
    {
        $url = 'api/subscribers/subscription-status.php';

        return $this->buildAndSend($url, ['email' => $email]);
    }

    /**
     * Gets the total active subscriber count
     *
     * @return string
     */
    public function count()
    {
        $url = 'api/subscribers/active-subscriber-count.php';

        return $this->buildAndSend($url, []);
    }

    /**
     * Create a campaign based on the input params. See API (https://sendy.co/api#4) for parameters.
     * Bug: The API doesn't save the listIds passed to Sendy.
     *
     * @param $options
     * @param $content
     * @param bool $send : Set this to true to send the campaign
     *
     * @return string
     * @throws \Exception
     */
    public function createCampaign($options, $content, $send = false)
    {
        $url = '/api/campaigns/create.php';

        if (empty($options['from_name'])) {
            throw new \Exception('From Name is not set', 1);
        }

        if (empty($options['from_email'])) {
            throw new \Exception('From Email is not set', 1);
        }

        if (empty($options['reply_to'])) {
            throw new \Exception('Reply To address is not set', 1);
        }

        if (empty($options['subject'])) {
            throw new \Exception('Subject is not set', 1);
        }

        // 'plain_text' field can be included, but optional
        if (empty($content['html_text'])) {
            throw new \Exception('Campaign Content (HTML) is not set', 1);
        }

        if ($send) {
            if (empty($options['brand_id'])) {
                throw new \Exception('Brand ID should be set for Draft campaigns', 1);
            }
        }

        // list IDs can be single or comma separated values
        if (empty($options['list_ids'])) {
            $options['list_ids'] = $this->listId;
        }

        // should we send the campaign (1) or save as Draft (0)
        $options['send_campaign'] = ($send) ? 1 : 0;

        return $this->buildAndSend($url, array_merge($options, $content));
    }

    /**
     * @param $url
     * @param array $values
     *
     * @return string
     */
    private function buildAndSend($url, array $values)
    {
        /**
         * Merge the passed in values with the options for return
         * Passing listId too, because old API calls use list, new ones use listId
         */
        $content = array_merge($values, [
            'list' => $this->listId,
            'list_id' => $this->listId, # ¯\_(ツ)_/¯
            'api_key' => $this->apiKey,
            'boolean' => 'true',
        ]);

        /**
         * Build a query using the $content
         */
        $post_data = http_build_query($content);
        $ch = curl_init($this->installationUrl . '/' . $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Checks the properties
     *
     * @throws \Exception
     */
    private function checkProperties()
    {
        if (!isset($this->listId)) {
            throw new \Exception('[listId] is not set', 1);
        }

        if (!isset($this->installationUrl)) {
            throw new \Exception('[installationUrl] is not set', 1);
        }

        if (!isset($this->apiKey)) {
            throw new \Exception('[apiKey] is not set', 1);
        }
    }
}
