<?php declare(strict_types=1);

require_once __DIR__ . '/../app/Presentation/Controllers/BaseController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/LoginController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/SignUpController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/ContactUsController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/LegalNoticesController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/RidesController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/RideDetailsController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/UsersPublicInfosController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/CreateRideController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/TripIncidentController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/AppIssueController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/CreateReviewsController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/UserDashboardController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/UserInfosController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/UserVehiculesController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/UserCreditsController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/UserRidesController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/UserReservationsController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/UserReviewsController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/EmployeeDashboardController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/EmployeeTripsValidationListController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/EmployeeTripValidationController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/EmployeeReviewsValidationController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/EmployeeTripsIncidentsManagementController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/AdminDashboardController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/AdminUsersManagementController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/AdminStatsController.php';
require_once __DIR__ . '/../app/Presentation/Controllers/AdminAppIssuesManagementController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('BASE_URL', $scriptDir);
$route = $uri;
if ($scriptDir !== '' && $scriptDir !== '/' && strpos($route, $scriptDir) === 0) {
    $route = substr($route, strlen($scriptDir));
}
$route = '/' . ltrim($route, '/');
$route = rtrim($route, '/');
if ($route === '') {
    $route = '/';
}

if ($route === '/'){
    $controller = new HomeController();
    $controller->index();
    exit;
} else if ($route === '/connexion'){
    $controller = new LoginController();
    $controller->login();
    exit;
} else if ($route === '/inscription'){
    $controller = new SignUpController();
    $controller->signUp();
    exit;
} else if ($route === '/contact'){
    $controller = new ContactUsController();
    $controller->contactUs();
    exit;
} else if ($route === '/mentions-legales'){
    $controller = new LegalNoticesController();
    $controller->legalNotices();
    exit;
} else if ($route === '/trajets'){
    $controller = new RidesController();
    $controller->rides();
    exit;
} else if ($route === '/details-trajet'){
    $controller = new RideDetailsController();
    $controller->rideDetails();
    exit;
} else if ($route === '/profils'){
    $controller = new UsersPublicInfosController();
    $controller->usersPublicInfos();
    exit;
} else if ($route === '/creer-trajet'){
    $controller = new CreateRideController();
    $controller->createRide();
    exit;
} else if ($route === '/signaler-incident'){
    $controller = new TripIncidentController();
    $controller->tripIncident();
    exit;
} else if ($route === '/signaler-probleme-technique'){
    $controller = new AppIssueController();
    $controller->appIssue();
    exit;
} else if ($route === '/rediger-avis'){
    $controller = new CreateReviewsController();
    $controller->createReviews();
    exit;
} else if ($route === '/mon-compte'){
    $controller = new UserDashboardController();
    $controller->userDashboard();
    exit;
} else if ($route === '/mes-informations'){
    $controller = new UserInfosController();
    $controller->userInfos();
    exit;
} else if ($route === '/mes-vehicules'){
    $controller = new UserVehiculesController();
    $controller->userVehicules();
    exit;
} else if ($route === '/mes-credits'){
    $controller = new UserCreditsController();
    $controller->userCredits();
    exit;
} else if ($route === '/mes-trajets'){
    $controller = new UserRidesController();
    $controller->userRides();
    exit;
} else if ($route === '/mes-reservations'){
    $controller = new UserReservationsController();
    $controller->userReservations();
    exit;
} else if ($route === '/mes-avis'){
    $controller = new UserReviewsController();
    $controller->userReviews();
    exit;
} else if ($route === '/dashboard-moderateur'){
    $controller = new EmployeeDashboardController();
    $controller->employeeDashboard();
    exit;
} else if ($route === '/liste-trajets-a-valider'){
    $controller = new EmployeeTripsValidationListController();
    $controller->employeeTripsValidationList();
    exit;
} else if ($route === '/valider-trajet'){
    $controller = new EmployeeTripValidationController();
    $controller->employeeTripValidation();
    exit;
} else if ($route === '/valider-avis'){
    $controller = new EmployeeReviewsValidationController();
    $controller->employeeReviewsValidation();
    exit;
} else if ($route === '/gestion-signalements-trajets'){
    $controller = new EmployeeTripsIncidentsManagementController();
    $controller->employeeTripsIncidentsManagement();
    exit;
} else if ($route === '/dashboard-administrateur'){
    $controller = new AdminDashboardController();
    $controller->adminDashboard();
    exit;
} else if ($route === '/gestion-comptes-utilisateurs'){
    $controller = new AdminUsersManagementController();
    $controller->adminUsersManagement();
    exit;
} else if ($route === '/statistiques'){
    $controller = new AdminStatsController();
    $controller->adminStats();
    exit;
} else if ($route === '/gestion-signalements-techniques'){
    $controller = new AdminAppIssuesManagementController();
    $controller->adminAppIssuesManagement();
    exit;
} else {
    http_response_code(404);
    echo "404 - page non trouvée";
    exit;
}
?>