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
        $ecoOnly = isset($_GET['eco']) && $_GET['eco'] === '1';
        $maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : null;
        if (is_int($maxPrice) && $maxPrice <= 0) $maxPrice = null;
        $maxDuration = isset($_GET['max_duration']) ? (int)$_GET['max_duration'] : null;
        if (is_int($maxDuration) && $maxDuration <= 0) $maxDuration = null;
        $minRating = isset($_GET['min_rating']) ? (float)$_GET['min_rating'] : null;
        if (is_float($minRating) && $minRating <= 0) $minRating = null;
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
            $offset,
            $ecoOnly ? true : null,
            $maxPrice,
            $maxDuration,
            $minRating
        );

        $trips = $result['items'];
        $total = (int)$result['total'];
        $suggestedDate = null;
        if ($total === 0 && $date !== '' && $cityFrom !== '' && $cityTo !== '') {
            $suggestedDate = $repo->findClosestAvailableDate(
                $cityFrom,
                $cityTo,
                $date,
                $ecoOnly ? true : null,
                $maxPrice,
                $maxDuration,
                $minRating
            );
            if ($suggestedDate !== null && $suggestedDate !== $date) {
                $result = $repo->searchPaginated(
                    $cityFrom,
                    $cityTo,
                    $suggestedDate,
                    $sort,
                    $limit,
                    $offset,
                    $ecoOnly ? true : null,
                    $maxPrice,
                    $maxDuration,
                    $minRating
                );
                $trips = $result['items'];
                $total = (int)$result['total'];
            }
        }
        $hasMore = ($offset + count($trips)) < $total;

        $this->renderView('trajets', [
            'pageTitle' => 'Covoiturages',
            'trips' => $trips,
            'totalTrips' => $total,
            'searched' => $searched,
            'hasMore' => $hasMore,
            'nextLimit' => $limit + $baseLimit,
            'suggestedDate' => $suggestedDate,
            'filters' => [
            'from' => $cityFrom,
            'to' => $cityTo,
            'date' => $date,
            'sort' => $sort,
            'limit' => $limit,
            'eco' => $ecoOnly ? '1' : '0',
            'max_price' => $maxPrice ?? '',
            'max_duration' => $maxDuration ?? '',
            'min_rating' => $minRating ?? '',
            ],
        ]);
    }
}
