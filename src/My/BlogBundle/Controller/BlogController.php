<?php

namespace My\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;

use My\BlogBundle\Entity\Post;

/**
 * @Route("/blog")
 * @Template()
 */
class BlogController extends Controller
{
    /**
     * @Route("/", name="blog_index")
     */
    public function indexAction()
    {
        $em = $this->get('doctrine')->getManager();
        $posts = $em->getRepository('MyBlogBundle:Post')->findAll();

        return array('posts' => $posts);
    }

    /**
     * @Route("/{id}/show", name="blog_show")
     */
    public function showAction($id)
    {
        $em = $this->get('doctrine')->getManager();
        $post = $em->getRepository('MyBlogBundle:Post')->find($id);

        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }

        return array('post' => $post);
    }

    /**
     * @Route("/new", name="blog_new")
     */
    public function newAction(Request $request)
    {
        // フォームの組立
        $post = new Post();
        $form = $this->createFormBuilder($post)
            ->add('title')
            ->add('body')
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            // バリデーション
            if ($form->isValid()) {
                // エンティティを永続化
                $post = $form->getData();
                $post->setCreatedAt(new \DateTime());
                $post->setUpdatedAt(new \DateTime());
                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();

                return $this->redirect($this->generateUrl('blog_index'));
            }
        }

        return array(
            'post' => $post,
            'form' => $form->createView(),
         );
    }

    /**
     * @Route("/{id}/edit", name="blog_edit")
     */
    public function editAction(Request $request, $id)
    {
        // DBから取得
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('MyBlogBundle:Post')->find($id);
        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }

        // フォームの組立
        $form = $this->createFormBuilder($post)
            ->add('title')
            ->add('body')
            ->getForm();

        // バリデーション
        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                // 更新されたエンティティをデータベースに保存
                $post = $form->getData();
                $post->setUpdatedAt(new \DateTime());
                $em->flush();

                return $this->redirect($this->generateUrl('blog_index'));
            }
        }

        return array(
            'post' => $post,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/delete", name="blog_delete")
     */
    function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $post = $em->getRepository('MyBlogBundle:Post')->find($id);
        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }
        // 削除
        $em->remove($post);
        $em->flush();

        return $this->redirect($this->generateUrl('blog_index'));
    }
}
