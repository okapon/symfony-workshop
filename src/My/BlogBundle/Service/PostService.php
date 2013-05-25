<?php

namespace My\BlogBundle\Service;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

use My\BlogBundle\Entity\Post;

/**
 * PostService.
 */
class PostService
{
    protected $doctrine;
    protected $logger;
    protected $mailer;

    /**
     * constructor
     *
     * @param  RegistryInterface     $doctrine
     * @param  LoggerInterface       $logger
     * @return void
     */
    public function __construct(
        RegistryInterface $doctrine,
        LoggerInterface $logger,
        $mailer
    )
    {
        $this->doctrine = $doctrine;
        $this->logger   = $logger;
        $this->mailer   = $mailer;
    }

    /**
     * savePost
     *
     * @param Post $post
     * @return void
     */
    public function savePost(Post $post)
    {
        $em = $this->doctrine->getManager();

        $em->beginTransaction();

        try {
            if (!$post->getId()) {
                // 作成者をここで入れたりとか。
                // 記事を下書き状態にしたりとか
                $post->setCreatedAt(new \DateTime());
                $em->persist($post);
            }
            $post->setUpdatedAt(new \DateTime());
            $em->flush();
            $em->commit();

        } catch (\Exception $e) {
            $em->rollback();
            $this->logger->err($e);

            throw $e;
        }

        // メール送信
        $subject = '記事の投稿が完了しました';
        $mailTo = 'Input your email'; // $user->getEmail()
        $this->mailer->send($subject,  $mailTo);
    }
}
