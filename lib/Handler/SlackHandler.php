<?php

namespace Productsup\Flexilog\Handler;

use \Maknz\Slack;
use \Productsup\Flexilog\Exception\HandlerException;

/**
 * Ouput to a Graylog server
 */
class SlackHandler extends AbstractHandler
{
    private $slack = null;

    /**
     * {@inheritDoc}
     *
     * @param $additionalParameters array Pass the `webhook` as a key/value array
     * Additional settings can be set as an extra $settings array with the following key/value information:
     * channel	string	The default channel that messages will be sent to
     * username	string	The default username for your bot
     * icon	string	The default icon that messages will be sent with, either :emoji: or a URL to an image
     * link_names	bool	Whether names like @regan or #accounting should be linked in the message (defaults to false)
     * unfurl_links	bool	Whether Slack should unfurl text-based URLs (defaults to false)
     * unfurl_media	bool	Whether Slack should unfurl media-based URLs, like tweets or Youtube videos (defaults to true)
     * allow_markdown	bool	Whether markdown should be parsed in messages, or left as plain text (defaults to true)
     * markdown_in_attachments	array	Which attachment fields should have markdown parsed (defaults to none)
     */
    public function __construct($minimalLevel, $verbose, $additionalParameters = array())
    {
        if (!isset($additionalParameters['webhook'])) {
            throw new HandlerException('Webhook parameter must be set inside the $additionalParameters');
        }
        $settings = [
            'username' => 'Flexilog',
            'icon' => ':memo:'
        ];
        if (isset($additionalParameters['settings'])) {
            $settings = array_replace($settings, $additionalParameters['settings']);
            unset($additionalParameters['settings']);
        }
        parent::__construct($minimalLevel, $verbose);
        $this->slack = new Slack\Client(
            $additionalParameters['webhook'],
            $settings
        );
    }

    /**
     * {@inheritDoc}
     */
    public function write($level, $message, array $splitFullMessage, array $context = array())
    {
        $color = '#808080';
        if (self::LOG_LEVELS[$level] >= 7) {
            $color = '#808080'; // gray
        } elseif (self::LOG_LEVELS[$level] >= 5) {
            $color = '#008000'; // green
        } elseif (self::LOG_LEVELS[$level] == 4) {
            $color = '#FF00FF'; // fuschia
        } elseif (self::LOG_LEVELS[$level] == 3) {
            $color = '#FFA500'; // orange
        } elseif (self::LOG_LEVELS[$level] <= 2) {
            $color = '#FF0000'; // red
        }

        $i = 1;
        foreach ($splitFullMessage as $fullMessage) {
            $slackMessage = $this->slack->createMessage();

            if ($message !== '') {
                $shortMessageToSend = sprintf('*[%s]*: %s', strtoupper($context['loglevel']), $message);
                if (count($splitFullMessage) != 1) {
                    $shortMessageToSend = $i.'/'.count($splitFullMessage).' '.$message;
                }

                $slackMessage->setText($shortMessageToSend);
            }

            $attachments = [];
            $attachmentFields = [];

            if ($this->verbose >= 1) {
                if (isset($fullMessage)) {
                    $attachments [] = new Slack\Attachment([
                        'fallback' => 'Full Message',
                        'text' => $fullMessage,
                        'color' => $color,
                        'mrkdwn_in' => ['text']
                    ]);
                }
            }

            if ($this->verbose >= 2) {
                if (isset($context) && is_array($context)) {
                    foreach ($context as $contextKey => $contextMessage) {
                        if ($contextKey == 'loglevel') {
                            continue;
                        }
                        if (is_array($contextMessage)) {
                            $contextMessage = print_r($contextMessage, true);
                        }
                        $short = false;
                        if (is_string($contextMessage)) {
                            $contextMessage = substr($contextMessage, 0, 31766);
                            if (strlen($contextMessage) <= 30) {
                                $short = true;
                            }
                        }
                        $attachmentFields[] = new Slack\AttachmentField([
                            'title' => $contextKey,
                            'value' => $contextMessage,
                            'short' => $short
                        ]);
                    }
                }
            }

            $attachments[] = new Slack\Attachment([
                'color' => $color,
                'mrkdwn_in' => ['text'],
                'fields' => $attachmentFields
            ]);
            $slackMessage->setAttachments($attachments);

            $slackMessage->send();
            $i++;
        }
    }
}
