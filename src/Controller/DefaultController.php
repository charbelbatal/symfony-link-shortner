<?php


namespace App\Controller;


use App\Entity\Link;
use App\Entity\LinkStatistic;
use App\Form\LinkType;
use App\Repository\LinkRepository;
use App\Repository\LinkStatisticRepository;
use App\Service\UrlShortnerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{

    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @param UrlShortnerService $urlShortnerService
     * @return Response
     */
    public function index(Request $request, UrlShortnerService $urlShortnerService): Response
    {
        $link = new Link();

        $linkShortenForm = $this->createForm(LinkType::class,$link);
        $linkShortenForm->handleRequest($request);

        if($linkShortenForm->isSubmitted() && $linkShortenForm->isValid()){
            $shortCode = $urlShortnerService->generateShortCode();

            $link->setShortCode($shortCode);
            $link->setIpAddress($request->getClientIp());

            $this->getDoctrine()->getManager()->persist($link);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('link_success', 'Link Successfully Created');

            return $this->redirectToRoute('link_info_view',['shortCode' => $link->getShortCode()]);
        }

        return $this->render('index.html.twig',[
            'linkShortenForm' => $linkShortenForm->createView()
        ]);
    }

    /**
     * @Route("/view/{shortCode}", name="link_info_view")
     * @param Request $request
     * @param Link $link
     * @return Response
     */
    public function view(Request $request,Link $link): Response
    {
        return $this->render('view.html.twig',[
            'link' => $link,
            'isEdit' => false
        ]);
    }

    /**
     * @Route("/edit/{shortCode}", name="link_info_edit")
     * @param Request $request
     * @param Link $link
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request,Link $link): Response
    {
        $linkShortenForm = $this->createForm(LinkType::class,$link);
        $linkShortenForm->handleRequest($request);

        if($linkShortenForm->isSubmitted() && $linkShortenForm->isValid()){
            $link->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->persist($link);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('link_success', 'Link Successfully Updated');

            return $this->redirectToRoute('link_info_view',[
                'shortCode' => $link->getShortCode()
            ]);
        }

        return $this->render('view.html.twig',[
            'link' => $link,
            'linkShortenForm' => $linkShortenForm->createView(),
            'isEdit' => true
        ]);
    }

    /**
     * @Route("/list", name="link_list")
     * @param Request $request
     * @param LinkRepository $linkRepository
     * @return Response
     */
    public function list(Request $request,LinkRepository $linkRepository): Response
    {
        return $this->render('list.html.twig',[
            'linkList' => $linkRepository->findAll()
        ]);
    }

    /**
     * @Route("/ajax/delete/{shortCode}", name="link_info_delete", methods={"DELETE"}, condition="request.isXmlHttpRequest()"))
     * @param Request $request
     * @param Link $link
     * @return Response
     */
    public function delete(Request $request,Link $link): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->remove($link);
            $em->flush();

            return $this->json(['status' => 'success','redirectUrl' => $this->generateUrl('homepage')]);
        }catch (\Throwable $throwable){
            return $this->json(['status' => 'error']);
        }
    }

    /**
     * @Route("/ajax/list", name="ajax_link_list", methods={"POST"}, condition="request.isXmlHttpRequest()"))
     * @param Request $request
     * @param LinkRepository $linkRepository
     * @return Response
     */
    public function ajaxList(Request $request, LinkRepository $linkRepository): Response
    {
        //Get Variables from Request sent by the DataTable
        $draw = (int) ($request->get('draw') ?? 1);
        $columns = $request->get('columns');
        $orders = $request->get('order') ?? [];
        $search = $request->get('search');
        $start = $request->get('start') ? (int) $request->get('start') : 0;
        $length = $request->get('length') ? (int) $request->get('length') : 10;

        // Ordering
        $orderColumnNumber = $orders[0]['column'];
        $orderDirection = $orders[0]['dir'];
        $orderColumn = $columns[$orderColumnNumber]['data'];
        switch ($orderColumn){
            case 'shortUrl':
                $orderColumn = 'shortCode';
                break;
            default:
                break;
        }

        //Get Link List
        $linkList = $linkRepository->findBy([],[$orderColumn => $orderDirection],$length,$start);

        // Get total number of links
        $recordsTotal = $linkRepository->count([]);

        //Prepare the Data to return as json
        $dataList = [];
        foreach($linkList as $link){
            $dataList[] = [
                'id' => $link->getId(),
                'url' => $link->getUrl(),
                'shortUrl' => $this->generateUrl('link_short', ['shortCode' => $link->getShortCode()],UrlGeneratorInterface::ABSOLUTE_URL),
                'hits' => $link->getHits(),
                'ipAddress' => $link->getIpAddress(),
                'createdAt' => $link->getCreatedAt()->setTimezone(new \DateTimeZone($this->getParameter('app.default_timezone')))->format('m/d/y H:i:s'),
                'updatedAt' => $link->getUpdatedAt() ? $link->getUpdatedAt()->setTimezone(new \DateTimeZone($this->getParameter('app.default_timezone')))->format('m/d/y H:i:s') : '',
                'actions' => [
                    'view' => $this->generateUrl('link_info_view',[
                        'shortCode' => $link->getShortCode()
                    ]),
                    'edit' => $this->generateUrl('link_info_edit',[
                        'shortCode' => $link->getShortCode()
                    ]),
                    'delete' => $this->generateUrl('link_info_delete',[
                        'shortCode' => $link->getShortCode()
                    ])
                ]
            ];
        }

        //Set the Data Array for the DataTable
        $data = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $dataList
        ];

        return $this->json($data);
    }

    /**
     * @Route("/ajax/stats/list/{shortCode}", name="ajax_link_stats_list", methods={"POST"}, condition="request.isXmlHttpRequest()"))
     * @param Request $request
     * @param Link $link
     * @param LinkStatisticRepository $linkStatisticRepository
     * @return Response
     */
    public function ajaxStatsList(Request $request,Link $link, LinkStatisticRepository $linkStatisticRepository): Response
    {
        //Get Variables from Request sent by the DataTable
        $draw = (int) ($request->get('draw') ?? 1);
        $columns = $request->get('columns');
        $orders = $request->get('order') ?? [];
        $search = $request->get('search');
        $start = $request->get('start') ? (int) $request->get('start') : 0;
        $length = $request->get('length') ? (int) $request->get('length') : 10;

        // Ordering
        $orderColumnNumber = $orders[0]['column'];
        $orderDirection = $orders[0]['dir'];
        $orderColumn = $columns[$orderColumnNumber]['data'];

        //Get Link List
        $linkStatisticList = $linkStatisticRepository->findBy(['link' => $link],[$orderColumn => $orderDirection],$length,$start);

        // Get total number of links
        $recordsTotal = $linkStatisticRepository->count(['link' => $link]);

        //Prepare the Data to return as json
        $dataList = [];
        foreach($linkStatisticList as $linkStatistic){
            $dataList[] = [
                'ipAddress' => $linkStatistic->getIpAddress(),
                'browser' => $linkStatistic->getBrowser(),
                'device' => $linkStatistic->getDevice(),
                'os' => $linkStatistic->getOs(),
                'createdAt' => $linkStatistic->getCreatedAt()->setTimezone(new \DateTimeZone($this->getParameter('app.default_timezone')))->format('m/d/y H:i:s'),
            ];
        }

        //Set the Data Array for the DataTable
        $data = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $dataList
        ];

        return $this->json($data);
    }

    /**
     * @Route("/{shortCode}", name="link_short")
     * @param Request $request
     * @param Link $link
     * @return Response
     */
    public function shortUrl(Request $request,Link $link): Response
    {
        $em = $this->getDoctrine()->getManager();

        //Add New Link Statistic
        $linkStatistic = new LinkStatistic();
        $linkStatistic->setIpAddress($request->getClientIp());
        $linkStatistic->setUserAgent($request->headers->get('User-Agent'));
        $linkStatistic->parseUA();
        $link->addLinkStatistic($linkStatistic);

        //Update Link Number of Hits
        $link->setHits($link->getHits() + 1);

        $em->persist($link);
        $em->flush();

        return $this->redirect($link->getUrl());
    }

}