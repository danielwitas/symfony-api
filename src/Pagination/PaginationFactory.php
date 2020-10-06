<?php


namespace App\Pagination;


use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use PHPUnit\Util\Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function createCollection(QueryBuilder $qb, Request $request, $route, $routeParams = [])
    {
        try {
            $page = $request->query->get('page', 1);

            $adapter = new QueryAdapter($qb);
            $pagerfanta = new Pagerfanta($adapter);
            $pagerfanta->setCurrentPage($page);
            $pagerfanta->getCurrentPageResults();
            $items = [];
            foreach ($pagerfanta->getCurrentPageResults() as $item) {
                $items[] = $item;
            }
            $paginatedCollection = new PaginatedCollection(
                $items,
                $pagerfanta->getNbResults()
            );
            $routeParams = array_merge($routeParams, $request->query->all());
            $createLinkUrl = function ($targetPage) use ($route, $routeParams) {
                return $this->router->generate($route, array_merge(
                    $routeParams,
                    ['page' => $targetPage]
                ));
            };
            $paginatedCollection->addLink('self', $createLinkUrl($page));
            $paginatedCollection->addLink('first', $createLinkUrl(1));
            $paginatedCollection->addLink('last', $createLinkUrl($pagerfanta->getNbPages()));

            if ($pagerfanta->hasNextPage()) {
                $paginatedCollection->addLink('next', $createLinkUrl($pagerfanta->getNextPage()));
            }
            if ($pagerfanta->hasPreviousPage()) {
                $paginatedCollection->addLink('prev', $createLinkUrl($pagerfanta->getPreviousPage()));
            }
        } catch (\Exception $exception) {
            throw new HttpException(500, 'Oops, something went wrong.');
        }
        return $paginatedCollection;
    }
}