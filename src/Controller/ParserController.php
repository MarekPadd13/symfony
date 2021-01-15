<?php

namespace App\Controller;

use App\Factory\Parser\MarathonBet\MarathonParserLink;
use App\Factory\Parser\MarathonBet\MarathonParserParent;
use App\Factory\Parser\ParserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParserController extends AbstractController
{
    private $parser;

    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @Route("/parser", name="parser")
     */
    public function index(): Response
    {
        return $this->render('parser/index.html.twig', [
            'crawlers' => $this->parser->result(new MarathonParserLink(), new MarathonParserParent()),
        ]);
    }
}
