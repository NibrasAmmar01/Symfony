<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\Contact;
use App\Form\ContactType;
class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repository): Response
    {
        $articles = $repository->findAll();
        dump($articles);
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles,

        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home() {
        return $this->render('blog/home.html.twig', [
            'title' => "Welcome to my Blog",
            'age' => 31
        ]);
    }
    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function create(Article $article = null,Request $request, EntityManagerInterface $manager) {
        /*dump($request);
        if ($request->isMethod('POST')) {
            $article = new Article();
            $article->setTitle($request->request->get('title'))
                    ->setContent($request->request->get('content'))
                    ->setImage($request->request->get('image'))
                    ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('blog_show', ['id'=> $article->getId
            ()]);
        }*/
        if (!$article) {
            $article = new Article();
        }
        //$article = new Article();
        
        //$form = $this->createFormBuilder($article) // Corrected method name
        //            ->add('title', TextType::class, [
        //                'attr' => [
        //                    'placeholder' => "Titre de l'article",
                            //'class' => 'form-control'
        //                ]
        //            ])
        //            ->add('content', TextareaType::class, [
        //                'attr' => [
        //                    'placeholder' => "Contenu de l'article",
                            //'class' => 'form-control'
        //                ]
        //            ])
        //            ->add('image', TextType::class, [
        //                'attr' => [
        //                    'placeholder' => "Image de l'article",
        //                    //'class' => 'form-control'
        //                ]
        //            ])
                    /*->add('save', SubmitType::class, [
                        'label' => 'Enregistrer'
                    ])*/
        //            ->getForm();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$article->getId()) {
                $article->setCreatedAt(new \DateTimeImmutable());
            }
            //$article->setCategory($form->get('category')->getData());
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('blog_show', ['id' => $article->getId
            ()]);
        }
        if (!$form->isSubmitted() && $article->getId()) {
            
            $article->setTitle("Titre d'example")
                    ->setContent("Le contenu de l'article");
        }
    
        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
        

    }
    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show($id) {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
    
        return $this->render('blog/show.html.twig', [ 
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
}

     /**
     * @Route("/contact", name="contact")
     */

public function contact(Request $request, EntityManagerInterface $manager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($contact);
            $manager->flush();

            return $this->redirectToRoute('blog'); 
        }

        return $this->render('blog/contact.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
    
}
