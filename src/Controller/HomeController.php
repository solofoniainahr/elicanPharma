<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Form\KbisClientType;
use App\Message\EmailNotification;
use App\Services\SendMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request, SendMail $sendMail, SerializerInterface $serializer, MessageBusInterface $bus, EntityManagerInterface $em): Response
    {

        $client = new Client;

        $bus->dispatch(new EmailNotification('Look! I created a message!'));

        $form = $this->createForm(ClientType::class, $client);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
           
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('app_validation_code', ['id' => $client->getId()]);
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/autocomplete", name="app_autocomplete")
     */
    public function autocomplete(Request $request, SerializerInterface $serializer): response
    {
        if($request->isXmlHttpRequest())
        {
            $value = $request->request->get("value");
            if($value)
            {
                $data = file_get_contents("https://entreprise.data.gouv.fr/api/sirene/v1/full_text/$value");
                dd($serializer->decode($data, 'json'));
            }
            
            return $this->json([], 200);
        }
    }

    /**
     * @Route("/validation/code/{id}", name="app_validation_code")
     */
    public function checkCode(Client $client, Request $request)
    {
        if($request->isMethod('post'))
        {


            return $this->redirectToRoute('app_addKbis', ['id' => $client->getId()]);

        }

        return $this->render('home/validation_code.html.twig', [
            'client' => $client
        ]);
    }

    /**
     * @Route("/add/Kbis/{id}", name="app_addKbis")
     */
    public function addKbis( Client $client, Request $request, SerializerInterface $serializer, EntityManagerInterface $em): response
    {
        $form = $this->createForm(KbisClientType::class, $client);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $em->persist($client);
            $em->flush();
        }
        
        return $this->render('home/add_kbis.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
