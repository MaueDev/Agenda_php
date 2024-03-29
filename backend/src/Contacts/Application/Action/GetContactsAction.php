<?php

declare(strict_types=1);

namespace Agenda\Contacts\Application\Action;

use Agenda\Contacts\Domain\Dto\GetContactsDto;
use Agenda\Contacts\Domain\Entity\Contacts;
use Agenda\Contacts\Domain\Service\GetContactsService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetContactsAction
{
    private ContainerInterface $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $header    = $request->getHeaderLine('Authorization');
        $headerDto = GetContactsDto::fromHeader($header);
        /** @var GetContactsService $getContactsService */
        $getContactsService = $this->container->get(GetContactsService::class);
        $contacts           = $getContactsService->getContacts($headerDto);

        $response->getBody()
                ->write((string) json_encode(
                    array_map(function (Contacts $contact) {
                        return $contact->getAllValues();
                    }, (array) $contacts)
                ));
        return $response;
    }
}
