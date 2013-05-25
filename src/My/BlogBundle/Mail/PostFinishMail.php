<?php

namespace My\BlogBundle\Mail;

use Symfony\Component\Templating\EngineInterface;

class PostFinishMail
{
    private $mailer;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * post完了メール送信を行う
     */
    public function send($subject, $to)
    {
        // メール送信
        $body = $this->render('MyBlogBundle:Mail:post_finish.txt.twig');
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array('noreply@byblog.com' => 'MyBlog'))
            ->setTo($to)
            ->setBody($body);

        $this->mailer->send($message);
    }

    /**
     * メール本文の作成
     */
    protected function render($template, $params = array())
    {
        return $this->templating->render($template, $params);
    }
}
