<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use App\Validation\ValidatorInterface;
use League\OAuth2\Client\Provider\GenericProvider;
use Exception;
use Slim\Interfaces\RouterInterface;
use Respect\Validation\Validator as v;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Mike42\Escpos\GdEscposImage;

use App\Entities\User;

class PrintController
{
    private $oauth;
    private $flash;
    private $gdpos;
    private $guzzle;
    private $logger;
    private $printer;
    private $qrcode;
    private $router;
    private $validator;
    private $view;

    public function __construct(Twig $view, LoggerInterface $logger, Messages $flash, ValidatorInterface $validator, GenericProvider $oauth, RouterInterface $router, Client $guzzle, Printer $printer, QrCode $qrcode, GdEscposImage $gdpos)
    {
        $this->oauth = $oauth;
        $this->flash = $flash;
        $this->gdpos = $gdpos;
        $this->guzzle = $guzzle;
        $this->logger = $logger;
        $this->printer = $printer;
        $this->qrCode = $qrcode;
        $this->router = $router;
        $this->validator = $validator;
        $this->view = $view;
    }

    public function testAction(Request $request, Response $response, array $args)
    {

        $this->qrCode->setText('123abc123')
            ->setSize(300);

        $im = imagecreatefromstring($this->qrCode->writeString(PngWriter::class));

        $qrCode = $this->gdpos;
        $qrCode->readImageFromGdResource($im);

        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text("Telva Novias\n");
        $this->printer->setEmphasis(true);
        $this->printer->setTextSize(2, 1);
        $this->printer->text("Isidro Sánchez\n");
        $this->printer->bitImage($qrCode);
        $this->printer->text("\n");
        $this->printer->feed();

        $this->printer->setEmphasis(false);
        $this->printer->setTextSize(1, 1);
        $this->printer->text("Talleres:\n");
        $this->printer->text("• Taller de lo que sea\n");
        $this->printer->text("• Otro Taller mas\n");
        $this->printer->text("\n\n\n\n");

        $this->printer->cut();
        $this->printer->close();
    }
}
