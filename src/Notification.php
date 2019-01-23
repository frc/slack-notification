<?php
/**
 * Created by PhpStorm.
 * User: janneaalto
 * Date: 18/06/2018
 * Time: 13.17
 */

namespace Frc\Slack;

class Notification {

    private $webhookUrl = '';
    private $slackReportChannel = '';

    protected static $instance = null;

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->webhookUrl = getenv('SLACK_WEBHOOK_URL');
        $this->slackReportChannel = getenv('SLACK_REPORT_CHANNEL');
    }

    private function checkEnv() {
        if ($this->webhookUrl == false || $this->slackReportChannel == false) {
            return false;
        }
        return true;
    }

    private function changeLineBreaks($message) {
        return preg_replace('/<br\s?\/?>/i', "\r\n", $message);
    }

    public function sendMessageToSlack($message, $icon = ':female-detective:', $username = 'PHP Notifier') {

        if (!$this->checkEnv()) {
            return false;
        }

        $message = $this->changeLineBreaks($message);
        $data = 'payload=' . json_encode([
                'channel'    => $this->slackReportChannel,
                'username'   => $username,
                'text'       => $message,
                'icon_emoji' => $icon
            ]);

        $ch = curl_init($this->webhookUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
