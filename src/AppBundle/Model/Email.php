<?php

namespace AppBundle\Model;

class Email
{
    /** @var  array|string */
    protected $replyTo;

    /** @var array|string */
    protected $sender;

    /** @var string|array */
    protected $recipient;

    /** @var string */
    protected $body;

    protected $plainBody;

    /** @var string */
    protected $subject;

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return array|string
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * @param array|string $replyTo
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;
    }

    /**
     * @return string|array
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param string|array $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }


    public function setPlainBody($body)
    {
        $this->plainBody = $body;
    }

    public function getPlainBody()
    {
        return $this->plainBody ? $this->plainBody : strip_tags($this->body);
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
}
