namespace App\Controllers;

class BaseController {

    protected function view($view, $data = []) {
        extract($data);
        require __DIR__ . "/../../views/$view.php";
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
}