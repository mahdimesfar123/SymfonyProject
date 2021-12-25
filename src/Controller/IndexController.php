<?php
namespace App\Controller;

use App\Entity\Movie;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class IndexController extends AbstractController
{
    /**
        *@Route("/",name="movie_list")
    */
    public function home()
    {
        $movies= $this->getDoctrine()->getRepository(Movie::class)->findAll();
        return $this->render('index/index.html.twig',['movies'=> $movies]);
    }
    /**
        * @Route("/movie/save")
    */
    public function save() {
        $entityManager = $this->getDoctrine()->getManager();
        $movie = new Movie();
        $movie->setMoviename('Movie 3');
        $movie->setMovieroom("MR3");
        $movie->setStarttime("12/31/2019 14:00 PM");
        $movie->setEndtime("12/31/2019 16:00 PM");
    
        $entityManager->persist($movie);
        $entityManager->flush();
        return new Response('Movie saved with id '.$movie->getId());
    }
    /**
        * @Route("/movie/new", name="new_movie")
        * Method({"GET", "POST"})
    */
    public function new(Request $request) {
        $movie = new Movie();
        $form = $this->createFormBuilder($movie)
            ->add('Movie_Name', TextType::class)
            ->add('Movie_Room', TextType::class)
            ->add('Start_Time', TextType::class)
            ->add('End_Time', TextType::class)
            ->add('save', SubmitType::class, array(
                    'label' => 'Create')
                )->getForm();
                
                
            $form->handleRequest($request);
                
            if($form->isSubmitted() && $form->isValid()) {
                $movie = $form->getData();
                
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($movie);
                $entityManager->flush();
                
                return $this->redirectToRoute('movie_list');
            }
            return $this->render('index/new.html.twig',['form' => $form->createView()]);
    }
    /**
        * @Route("/movie/{id}", name="movie_show")
    */
    public function show($id) {
        $movie = $this->getDoctrine()->getRepository(Movie::class)->find($id);
        return $this->render('index/show.html.twig',array('movie' => $movie));
        }
    /**
        * @Route("/movie/edit/{id}", name="edit_moovie")
        * Method({"GET", "POST"})
    */
    public function edit(Request $request, $id) {
        $movie = new Movie();
        $movie = $this->getDoctrine()->getRepository(Movie::class)->find($id);
    
        $form = $this->createFormBuilder($movie)
            ->add('Movie_Name', TextType::class)
            ->add('Movie_Room', TextType::class)
            ->add('Start_Time', TextType::class)
            ->add('End_Time', TextType::class)
            ->add('save', SubmitType::class, array(
                'label' => 'Save')
            )->getForm();
    
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
    
            return $this->redirectToRoute('movie_list');
        }
        return $this->render('index/edit.html.twig', ['form' => $form->createView()]);
    }
    /**
        * @Route("/movie/delete/{id}",name="delete_movie")
        * @Method({"DELETE"})
    */
    public function delete(Request $request, $id) {
        $movie = $this->getDoctrine()->getRepository(Movie::class)->find($id);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($movie);
        $entityManager->flush();
        
        $response = new Response();
        $response->send();
        return $this->redirectToRoute('movie_list');
    }
       
        
              
   
}