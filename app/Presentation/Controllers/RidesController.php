<?php declare(strict_types=1);

require_once __DIR__ . '/../../Infrastructure/Repositories/TripRepository.php';

class RidesController extends BaseController
{
    public function rides(): void
    {
        $cityFrom = isset($_GET['from']) ? trim((string)$_GET['from']) : '';
        $cityTo   = isset($_GET['to']) ? trim((string)$_GET['to']) : '';
        $date     = isset($_GET['date']) ? trim((string)$_GET['date']) : '';
        $sort     = isset($_GET['sort']) ? trim((string)$_GET['sort']) : 'date_asc';

        $baseLimit = 12;
        $requestedLimit = isset($_GET['limit']) ? (int)$_GET['limit'] : $baseLimit;
        if ($requestedLimit < $baseLimit) {
            $requestedLimit = $baseLimit;
        }

        $limit = $requestedLimit;
        $offset = 0;

        $searched = ($cityFrom !== '') || ($cityTo !== '') || ($date !== '');

        if (!$searched) {
            $this->renderView('trajets', [
                'pageTitle' => 'Covoiturages',
                'trips' => [],
                'totalTrips' => 0,
                'searched' => false,
                'hasMore' => false,
                'nextLimit' => $baseLimit,
                'filters' => [
                    'from' => '',
                    'to' => '',
                    'date' => '',
                    'sort' => $sort,
                    'limit' => $baseLimit,
                ],
            ]);
            return;
        }

        $repo = new TripRepository();
        $result = $repo->searchPaginated(
            $cityFrom !== '' ? $cityFrom : null,
            $cityTo !== '' ? $cityTo : null,
            $date !== '' ? $date : null,
            $sort,
            $limit,
            $offset
        );

        $trips = $result['items'];
        $total = (int)$result['total'];
        $hasMore = ($offset + count($trips)) < $total;

        $this->renderView('trajets', [
            'pageTitle' => 'Covoiturages',
            'trips' => $trips,
            'totalTrips' => $total,
            'searched' => $searched,
            'hasMore' => $hasMore,
            'nextLimit' => $limit + $baseLimit,
            'filters' => [
                'from' => $cityFrom,
                'to' => $cityTo,
                'date' => $date,
                'sort' => $sort,
                'limit' => $limit,
            ],
        ]);
    }
}
