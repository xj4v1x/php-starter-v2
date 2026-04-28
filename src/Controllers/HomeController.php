namespace App\Controllers;

class HomeController extends BaseController {

    public function index() {
        return $this->view('home');
    }
}